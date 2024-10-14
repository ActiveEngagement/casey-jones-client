<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript MessageGearsSegmentationCriteria */
class MessageGearsSegmentationCriteriaData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public string $description,
        public string $defaultValue
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
            'name' => 'some_data_variable',
            'label' => 'Some Data Variable',
            'description' => 'This is a mock data variable',
            'defaultValue' => 'NA'
        ], $attributes));
    }
}
