<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsAudienceDataProviderType;
use Actengage\CaseyJones\Enums\MessageGearsAudienceDataType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript */
class MessageGearsCampaignAudienceData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $name,
        public MessageGearsAudienceDataProviderType $dataProviderType,
        public MessageGearsAudienceDataType $dataType,
    ) {}

    /**
     * Mock an instance of the class
     *
     * @param array $attributes
     * @return static
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'id' => 1,
            'name' => 'Sample Audience',
            'dataProviderType' => MessageGearsAudienceDataProviderType::Query,
            'dataType' => MessageGearsAudienceDataType::Audience
        ], $attributes));
    }
}
