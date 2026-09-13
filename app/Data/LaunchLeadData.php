<?php

namespace App\Data;

final readonly class LaunchLeadData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $whatsapp,
        public ?string $company,
        public string $source,
        public ?string $utmSource,
        public ?string $utmMedium,
        public ?string $utmCampaign,
        public ?string $utmContent,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) $data['name']),
            email: mb_strtolower(trim((string) $data['email'])),
            whatsapp: trim((string) $data['whatsapp']),
            company: isset($data['company']) && $data['company'] !== '' ? trim((string) $data['company']) : null,
            source: trim((string) ($data['source'] ?? 'landing_lista_vip')) ?: 'landing_lista_vip',
            utmSource: self::nullableString($data['utm_source'] ?? null),
            utmMedium: self::nullableString($data['utm_medium'] ?? null),
            utmCampaign: self::nullableString($data['utm_campaign'] ?? null),
            utmContent: self::nullableString($data['utm_content'] ?? null),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
