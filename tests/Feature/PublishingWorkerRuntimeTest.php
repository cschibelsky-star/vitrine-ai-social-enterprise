<?php
namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class PublishingWorkerRuntimeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('PUBLISHING_RUNTIME_VALIDATION') !== '1') {
            $this->markTestSkipped('Requires the private validation Compose and its dedicated Redis.');
        }
        $this->assertSame('validation_redis', config('database.redis.default.host'));
        $this->assertFalse((bool) config('publishing.enabled'));
    }

    public function test_real_redis_worker_recovers_synthetic_failure_without_duplicate_result(): void
    {
        $queue = 'publisher-validation-'.Str::uuid();
        $path = storage_path('framework/testing/'.$queue.'.json');
        if (! is_dir(dirname($path))) { mkdir(dirname($path), 0770, true); }
        $connection = Queue::connection('redis');
        try {
            $connection->push(new \Tests\Fixtures\PublicationRuntimeProbe($path), '', $queue);
            $this->assertSame(1, $connection->size($queue));
            foreach ([1, 2] as $attempt) {
                $worker = new Process([PHP_BINARY, 'artisan', 'queue:work', 'redis', '--queue='.$queue,
                    '--once', '--tries=2', '--backoff=0', '--timeout=15', '--sleep=0'], base_path());
                $worker->setTimeout(25);
                $worker->mustRun();
                $record = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
                $this->assertSame($attempt, $record['attempts']);
                $this->assertSame($attempt === 2, $record['completed']);
            }
            $this->assertSame(0, $connection->size($queue));
            $worker = new Process([PHP_BINARY, 'artisan', 'queue:work', 'redis', '--queue='.$queue,
                '--once', '--tries=2', '--timeout=15', '--sleep=0'], base_path());
            $worker->setTimeout(25); $worker->mustRun();
            $this->assertSame(2, json_decode(file_get_contents($path), true)['attempts']);
        } finally {
            $connection->clear($queue);
            if (is_file($path)) { unlink($path); }
        }
    }

    public function test_scheduler_executor_runs_only_due_synthetic_event(): void
    {
        $schedule = new Schedule('UTC');
        $ran = 0; $future = 0;
        $schedule->call(function () use (&$ran) { $ran++; })->everyMinute();
        $schedule->call(function () use (&$future) { $future++; })->when(fn () => false)->everyMinute();
        $this->app->instance(Schedule::class, $schedule);
        $this->assertSame(0, Artisan::call('schedule:run'));
        $this->assertSame(1, $ran);
        $this->assertSame(0, $future);
    }
}
