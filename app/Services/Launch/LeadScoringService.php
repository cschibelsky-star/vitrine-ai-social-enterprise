<?php

namespace App\Services\Launch;

use App\Data\LaunchLeadData;

final class LeadScoringService
{
    public function score(LaunchLeadData $lead): int
    {
        $score = 40;
        $score += $lead->company ? 20 : 0;
        $score += $lead->whatsapp !== '' ? 20 : 0;
        $score += $lead->utmCampaign ? 10 : 0;
        $score += $lead->utmSource ? 10 : 0;

        return min(100, $score);
    }
}
