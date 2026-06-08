<?php

namespace Actengage\CaseyJones\Database\Factories;

use Actengage\CaseyJones\Models\Send;
use Actengage\CaseyJones\Models\SendJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SendJob>
 */
class SendJobFactory extends Factory
{
    /**
     * @var class-string<SendJob>
     */
    protected $model = SendJob::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'send_id' => Send::factory(),
            'status_code' => 200,
        ];
    }
}
