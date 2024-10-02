<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript */
class MessageGearsFolderData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $path,
        public string $name,
        public ?int $parentId,
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
            'path' => '/',
            'name' => 'Test Template',
        ], $attributes));
    }
}
