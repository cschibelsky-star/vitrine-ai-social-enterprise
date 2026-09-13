<?php

namespace App\Events;

final readonly class LaunchLeadCaptured
{
    public function __construct(
        public int $leadId,
        public string $name,
        public string $email,
        public string $whatsapp,
        public ?string $company,
        public string $source,
        public ?string $utmSource,
        public ?string $utmMedium,
        public ?string $utmCampaign,
        public ?string $utmContent,
        public int $leadScore,
        public string $capturedAt,
    ) {
    }
}
