<?php

namespace App\Services\Publishing;

use App\Models\SocialPublicationOperation;
use Illuminate\Support\Facades\DB;

/**
 * Persistence-only claim gate. No HTTP, Meta API, queue dispatch or retry side effects.
 * A single conditional UPDATE is atomic on supported SQL databases.
 */
final class PublicationOperationClaim
{
    public function claim(int $operationId, int $clientId): bool
    {
        return DB::table('social_publication_operations')
            ->where('id', $operationId)
            ->where('client_id', $clientId)
            ->where('status', SocialPublicationOperation::QUEUED)
            ->where('attempts', 0)
            ->update([
                'status' => SocialPublicationOperation::PUBLISHING,
                'attempts' => 1,
                'last_attempted_at' => now(),
                'updated_at' => now(),
            ]) === 1;
    }

    public function markUncertain(int $operationId, int $clientId): bool
    {
        return DB::table('social_publication_operations')
            ->where('id', $operationId)
            ->where('client_id', $clientId)
            ->where('status', SocialPublicationOperation::PUBLISHING)
            ->update([
                'status' => SocialPublicationOperation::PENDING_CONFIRMATION,
                'updated_at' => now(),
            ]) === 1;
    }
}
