<?php
namespace App\Services\Publishing;
use App\Models\ContentProject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
class PublicationScheduleService
{
    public function schedule(ContentProject $project, Carbon $at): void
    {
        DB::transaction(function () use ($project, $at) {
            $fresh = ContentProject::query()->lockForUpdate()->findOrFail($project->id);
            if ($at->isPast() || ! in_array($fresh->status, ['ready', 'scheduled'], true)
                || ! $fresh->approved_at || $fresh->published_at
                || ! hash_equals($fresh->approved_fingerprint ?? '', (new PublicationContract)->fingerprint($fresh))
                || DB::table('publication_attempts')->where('content_project_id', $fresh->id)->exists()) {
                throw new RuntimeException('Agendamento exige arquivo aprovado, sem envio anterior e data futura.');
            }
            (new ApprovedAssetService)->verify($fresh);
            $fresh->forceFill([
                'scheduled_at' => $at->copy()->utc(),
                'status' => 'scheduled',
                'schedule_generation' => ((int) $fresh->schedule_generation) + 1,
            ])->save();
        });
    }
    public function withdraw(ContentProject $project, bool $revision = false): void
    {
        DB::transaction(function () use ($project, $revision) {
            $fresh = ContentProject::query()->lockForUpdate()->findOrFail($project->id);
            if ($fresh->published_at || DB::table('publication_attempts')->where('content_project_id', $fresh->id)->exists()) {
                throw new RuntimeException('Envio existente; não é possível alterar a peça.');
            }
            if (! $revision && ! in_array($fresh->status, ['ready', 'scheduled'], true)) {
                throw new RuntimeException('Peça não aprovada.');
            }
            $changes = [
                'scheduled_at' => null,
                'status' => $revision ? 'editing' : 'ready',
                'schedule_generation' => ((int) $fresh->schedule_generation) + 1,
            ];
            if ($revision) { $changes += ['approved_at' => null, 'approved_by' => null, 'approved_fingerprint' => null]; }
            $fresh->forceFill($changes)->save();
        });
    }
}
