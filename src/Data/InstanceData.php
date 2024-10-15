<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Data;

/** @typescript Instance */
class InstanceData extends Data implements Mockable
{
    public function __construct(
		public int $id,
		public string $name,
		public string $accelerator_base_uri,
		public string $accelerator_api_key,
		public string $accelerator_account_id,
		public string $cloud_api_key,
		public string $cloud_account_id,
		public string $created_at,
		public string $updated_at,
		public ?string $deleted_at = null,
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
            'name' => 'Test Account',
            'accelerator_base_uri' => 'https://localhost:8000',
            'accelerator_api_key' => '12345',
            'accelerator_account_id' => '1',
            'cloud_api_key' => '12345',
            'cloud_account_id' => '1',
            'created_at' => now(),
            'updated_at' => now()
        ], $attributes));
    }
}
