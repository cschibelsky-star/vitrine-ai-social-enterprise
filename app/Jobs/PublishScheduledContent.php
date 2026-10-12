<?php

namespace App\Jobs;

use App\Models\ContentProject;
use App\Services\Publishing\MetaPublisherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 90;

    public function __construct(public int $projectId)
    {
    }

    public function handle(MetaPublisherService $publisher): void
    {
        $project = ContentProject::query()->find($this->projectId);

        if (! $project || $project->status !== 'scheduled' || $project->published_at !== null) {
            return;
        }

        if ($project->scheduled_at && $project->scheduled_at->isFuture()) {
            $this->release(max(1, (int) ceil(now()->diffInSeconds($project->scheduled_at))));

            return;
        }

        $publisher->publish($project);
    }
}

