<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Recovery scan: delayed jobs are an optimization; the database owns the schedule.
\Illuminate\Support\Facades\Schedule::call(function () {
    if (! config('publishing.enabled', false)) { return; }
    \App\Models\ContentProject::query()->where('status', 'scheduled')
        ->whereNotNull('approved_at')->whereNotNull('scheduled_at')->where('scheduled_at', '<=', now())
        ->whereNull('published_at')->chunkById(100, function ($projects) {
            foreach ($projects as $project) { \App\Jobs\PublishScheduledContent::dispatch($project->id); }
        });
})->name("social-publication-due-scan")->everyMinute()->withoutOverlapping();

Artisan::command('publisher:attach {project} {file}', function () {
    $project = \App\Models\ContentProject::query()->findOrFail((int) $this->argument('project'));
    app(\App\Services\Publishing\ApprovedAssetService::class)->attach($project, $this->argument('file'));
    $this->info('Arquivo anexado para revisão. A aprovação deve ser feita pelo cliente.');
})->purpose('Importar arquivo final local sem gerar mídia nem publicar');

Artisan::command('publisher:reconcile {project}', function () {
    $project = \App\Models\ContentProject::query()->findOrFail((int) $this->argument('project'));
    $result = app(\App\Services\Publishing\MetaPublisherService::class)->reconcile($project);
    $this->info('Status: '.($result['status'] ?? 'UNKNOWN'));
})->purpose('Consultar tentativa existente sem reenviar');
