<?php
namespace App\Services\Publishing;
use App\Models\ContentProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use RuntimeException;
class ApprovedAssetService
{
    // Trusted local files only; no remote URL fetching or AI generation.
    public function attach(ContentProject $project, string $file): void
    {
        if (! is_file($file) || filesize($file) > 100 * 1024 * 1024) {
            throw new RuntimeException('Arquivo ausente ou maior que 100 MB.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file);
        if (! in_array($mime, ['image/png', 'image/jpeg', 'video/mp4'], true)) {
            throw new RuntimeException('Use PNG, JPEG ou MP4.');
        }
        $placement = (new PublicationContract)->placement($project->format);
        $binary = file_get_contents($file);
        $metadata = [];
        if ($mime !== 'video/mp4') {
            $size = getimagesize($file);
            if (! $size || $size[0] * $size[1] > 40000000) {
                throw new RuntimeException('Imagem inválida ou grande demais.');
            }
            $metadata = ['width' => $size[0], 'height' => $size[1]];
            if ($project->channel === 'instagram' && $mime === 'image/png') {
                if (! function_exists('imagecreatefrompng')) {
                    throw new RuntimeException('A conversão PNG para JPEG requer GD.');
                }
                $image = imagecreatefrompng($file);
                $canvas = imagecreatetruecolor($size[0], $size[1]);
                imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
                imagecopy($canvas, $image, 0, 0, 0, 0, $size[0], $size[1]);
                ob_start(); imagejpeg($canvas, null, 95); $binary = ob_get_clean();
                imagedestroy($image); imagedestroy($canvas);
                $mime = 'image/jpeg';
            }
            if ($placement === 'reels') {
                throw new RuntimeException('Reels exige MP4.');
            }
            $ratio = $size[0] / $size[1];
            if ($placement === 'stories' && abs($ratio - 9 / 16) > 0.01) {
                throw new RuntimeException('Stories exige arquivo vertical 9:16 nesta versão.');
            }
            if ($project->channel === 'instagram' && strlen($binary) > 8 * 1024 * 1024) {
                throw new RuntimeException('Imagem do Instagram excede 8 MB.');
            }
            if ($placement === 'feed' && $project->channel === 'instagram' && ($ratio < 0.8 || $ratio > 1.91)) {
                throw new RuntimeException('Proporção incompatível com Feed do Instagram.');
            }
        } else {
            if ($placement === 'feed') { throw new RuntimeException('Use Reels para MP4.'); }
            $probe = new Process(['ffprobe', '-v', 'error', '-show_streams', '-show_format', '-of', 'json', $file]);
            $probe->setTimeout(20); $probe->mustRun();
            $data = json_decode($probe->getOutput(), true, 512, JSON_THROW_ON_ERROR);
            $videos = array_values(array_filter($data['streams'] ?? [], fn ($s) => ($s['codec_type'] ?? '') === 'video'));
            $v = $videos[0] ?? [];
            $duration = (float) ($data['format']['duration'] ?? 0);
            $width = (int) ($v['width'] ?? 0); $height = (int) ($v['height'] ?? 0);
            $fpsParts = explode('/', $v['avg_frame_rate'] ?? '0/1');
            $fps = (float) $fpsParts[0] / max(1, (float) ($fpsParts[1] ?? 1));
            if ($width < 540 || $height < 960 || abs($width / max(1, $height) - 9 / 16) > 0.01
                || $duration < 4 || $duration > 60 || $fps < 23 || $fps > 60
                || ! in_array($v['codec_name'] ?? '', ['h264', 'hevc'], true)) {
                throw new RuntimeException('MP4 fora do perfil conservador: 9:16, 4–60s, 23–60fps, H264/HEVC.');
            }
            foreach ($data['streams'] ?? [] as $stream) {
                if (($stream['codec_type'] ?? '') === 'audio' && ($stream['codec_name'] ?? '') !== 'aac') {
                    throw new RuntimeException('Use áudio AAC.');
                }
            }
            $metadata = compact('width', 'height', 'duration', 'fps');
        }
        $ext = match ($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', default => 'mp4' };
        $path = 'publisher/assets/'.Str::uuid().'.'.$ext;
        Storage::disk('local')->put($path, $binary);
        try {
            DB::transaction(function () use ($project, $path, $binary, $mime, $metadata) {
                $fresh = ContentProject::query()->lockForUpdate()->findOrFail($project->id);
                if ($fresh->published_at || DB::table('publication_attempts')->where('content_project_id', $fresh->id)->exists()) {
                    throw new RuntimeException('A tentativa existente precisa ser reconciliada antes de alterar a peça.');
                }
                $fresh->forceFill([
                    'publishing_asset' => ['path' => $path, 'mime' => $mime, 'sha256' => hash('sha256', $binary)] + $metadata,
                    'approved_fingerprint' => null, 'approved_at' => null, 'approved_by' => null,
                    'scheduled_at' => null, 'status' => 'review',
                ])->save();
            });
        } catch (\Throwable $e) { Storage::disk('local')->delete($path); throw $e; }
    }
    public function approve(ContentProject $project, int $userId): void
    {
        DB::transaction(function () use ($project, $userId) {
            $fresh = ContentProject::query()->lockForUpdate()->findOrFail($project->id);
            if ($fresh->published_at || DB::table('publication_attempts')->where('content_project_id', $fresh->id)->exists()) {
                throw new RuntimeException('Peça já enviada ou aguardando reconciliação.');
            }
            (new PublicationContract)->payload($fresh);
            $this->verify($fresh);
            $fresh->forceFill(['approved_fingerprint' => (new PublicationContract)->fingerprint($fresh),
                'approved_by' => $userId, 'approved_at' => now(), 'status' => 'ready', 'scheduled_at' => null])->save();
        });
    }
    public function verify(ContentProject $project): void
    {
        $a = (array) $project->publishing_asset;
        if (empty($a['path']) || ! Storage::disk('local')->exists($a['path'])
            || ! hash_equals($a['sha256'] ?? '', hash('sha256', Storage::disk('local')->get($a['path'])))) {
            throw new RuntimeException('Arquivo final ausente ou alterado.');
        }
    }
}
