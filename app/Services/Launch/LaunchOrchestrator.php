<?php

namespace App\Services\Launch;

use App\Data\LaunchLeadData;
use App\Events\LaunchLeadCaptured;

final class LaunchOrchestrator
{
    public function __construct(
        private readonly LeadCaptureService $capture,
        private readonly LeadScoringService $scoring,
    ) {
    }

    public function captureLead(array $payload): object
    {
        $lead = LaunchLeadData::fromArray($payload);
        $record = $this->capture->capture($lead);
        $score = $this->scoring->score($lead);

        event(new LaunchLeadCaptured(
            leadId: (int) $record->id,
            name: $lead->name,
            email: $lead->email,
            whatsapp: $lead->whatsapp,
            company: $lead->company,
            source: $lead->source,
            utmSource: $lead->utmSource,
            utmMedium: $lead->utmMedium,
            utmCampaign: $lead->utmCampaign,
            utmContent: $lead->utmContent,
            leadScore: $score,
            capturedAt: now()->toIso8601String(),
        ));

        return $record;
    }
}
