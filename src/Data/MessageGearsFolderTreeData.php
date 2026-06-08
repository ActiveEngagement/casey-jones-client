<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript MessageGearsFolderTree */
class MessageGearsFolderTreeData extends Data implements Mockable
{
    /**
     * @param  array<int, MessageGearsFolderTreeData>  $children
     */
    public function __construct(
        public int $id,
        public string $path,
        public string $name,
        public ?int $parentId,
        public array $children = []
    ) {}

    /**
     * Mock an instance of the class
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'id' => 1,
            'path' => '/',
            'name' => 'Test Template',
            'children' => [],
        ], $attributes));
    }
}
