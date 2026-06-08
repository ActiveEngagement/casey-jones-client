<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsAudienceDataType;
use Actengage\CaseyJones\Exceptions\MissingSegmentationCriteria;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'MessageGearsAudience')]
class MessageGearsAudienceData extends Data implements Mockable
{
    /**
     * @param  array<int, MessageGearsSegmentationCriteriaData>|null  $segmentationCriteria
     */
    public function __construct(
        public int $id,
        public string $name,
        public int $approximateResultCount,
        public MessageGearsAudienceDataType $dataType,
        public ?string $sql = null,
        #[DataCollectionOf(MessageGearsSegmentationCriteriaData::class)]
        public ?array $segmentationCriteria = [],
    ) {}

    /**
     * Transpose the stored database format into the format used in HTTP requests.
     *
     * @param  array<string, mixed>  $value
     * @return array<int, array{id: int, value: mixed}>
     */
    public function transposeSegmentationCriteria(array $value): array
    {
        $keyedCriteria = collect($this->segmentationCriteria)->keyBy('name');

        return collect($value)->map(function ($value, $key) use ($keyedCriteria) {
            if (! $match = $keyedCriteria->get($key)) {
                throw new MissingSegmentationCriteria(
                    "The key \"{$key}\" is missing from the available segmentation criteria: \"{$keyedCriteria->keys()->implode(', ')}\""
                );
            }

            return ['id' => $match->id, 'value' => $value];
        })->values()->all();
    }

    /**
     * Mock an instance of the class
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'id' => 1,
            'name' => 'Sample Audience',
            'approximateResultCount' => 1,
            'dataType' => MessageGearsAudienceDataType::Audience,
            'segmentationCriteria' => [
                MessageGearsSegmentationCriteriaData::mock(),
            ],
        ], $attributes));
    }
}
