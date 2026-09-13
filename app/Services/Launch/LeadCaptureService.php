<?php

namespace App\Services\Launch;

use App\Data\LaunchLeadData;
use Illuminate\Support\Facades\DB;

final class LeadCaptureService
{
    public function capture(LaunchLeadData $lead): object
    {
        $existing = DB::table('waitlist_leads')->where('email', $lead->email)->first();
        $now = now();
        $values = [
            'name' => $lead->name,
            'whatsapp' => $lead->whatsapp,
            'company' => $lead->company,
            'source' => $lead->source,
            'consent' => true,
            'joined_at' => $now,
            'updated_at' => $now,
        ];

        if ($existing) {
            DB::table('waitlist_leads')->where('id', $existing->id)->update($values);
        } else {
            DB::table('waitlist_leads')->insert($values + ['email' => $lead->email, 'created_at' => $now]);
        }

        return DB::table('waitlist_leads')->where('email', $lead->email)->first();
    }
}
