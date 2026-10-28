<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPublicationOperation extends Model
{
    public const QUEUED = 'queued';
    public const PUBLISHING = 'publishing';
    public const PENDING_CONFIRMATION = 'pending_confirmation';
    public const PUBLISHED = 'published';
    public const RETRYABLE_ERROR = 'retryable_error';
    public const FAILED = 'failed';
    public const REQUIRES_RECONNECT = 'requires_reconnect';

    protected $fillable = [
        'content_project_id',
        'client_id',
        'channel',
        'content_version',
        'idempotency_key',
        'status',
        'provider_container_id',
        'external_id',
        'last_error_code',
        'last_error_message',
        'attempts',
        'last_attempted_at',
        'confirmed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'last_attempted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ContentProject::class, 'content_project_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function idempotencyKey(
        int $clientId,
        int $projectId,
        string $channel,
        string $contentVersion
    ): string {
        return hash('sha256', implode('|', [
            'social-publication-v1',
            $clientId,
            $projectId,
            $channel,
            $contentVersion,
        ]));
    }
}
