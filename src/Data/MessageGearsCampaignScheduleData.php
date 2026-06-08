<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsScheduleMode;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript(name: 'MessageGearsCampaignSchedule')]
class MessageGearsCampaignScheduleData extends Data implements Mockable
{
    public function __construct(
        public MessageGearsScheduleMode $scheduleMode
    ) {}

    /**
     * Mock an instance of the class
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'scheduleMode' => MessageGearsScheduleMode::Adhoc,
        ], $attributes));
    }
}
