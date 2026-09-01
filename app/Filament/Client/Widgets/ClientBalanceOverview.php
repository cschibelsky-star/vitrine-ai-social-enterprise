<?php

namespace App\Filament\Client\Widgets;

use App\Models\ClientBalance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientBalanceOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $clientId = $user?->client_id;

        $balances = ClientBalance::query()
            ->where('client_id', $clientId)
            ->get()
            ->keyBy('balance_type');

        $content = $balances->get('content_credit');
        $video = $balances->get('video_second');
        $avatar = $balances->get('avatar_second');

        return [
            Stat::make('Conteúdo', $this->formatNumber($content?->available).' créditos disponíveis')
                ->description('de '.$this->formatNumber($content?->granted).' no período'),
            Stat::make('Vídeos', $this->formatNumber($video?->available).'s disponíveis')
                ->description('de '.$this->formatNumber($video?->granted).'s no período'),
            Stat::make('Avatar', $this->formatNumber($avatar?->available).'s disponíveis')
                ->description('de '.$this->formatNumber($avatar?->granted).'s no período'),
        ];
    }

    private function formatNumber(mixed $value): string
    {
        if ($value === null) {
            return '0';
        }

        $number = (float) $value;

        return fmod($number, 1.0) === 0.0
            ? number_format($number, 0, ',', '.')
            : number_format($number, 2, ',', '.');
    }
}
