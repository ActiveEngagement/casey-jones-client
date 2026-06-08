<?php

namespace Actengage\CaseyJones\Database\Factories;

use Actengage\CaseyJones\Enums\SendStatus;
use Actengage\CaseyJones\Models\Send;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Send>
 */
class SendFactory extends Factory
{
    /**
     * @var class-string<Send>
     */
    protected $model = Send::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_id' => 1,
            'instance_id' => 1,
            'campaign_id' => 1,
            'name' => fake()->sentence(3),
            'status' => SendStatus::Active,
        ];
    }
}
