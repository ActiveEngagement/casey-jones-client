<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsScheduleMode;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript MessageGearsCampaignSchedule */
class MessageGearsCampaignScheduleData extends Data implements Mockable
{
    public function __construct(
        public MessageGearsScheduleMode $scheduleMode
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
            'scheduleMode' => MessageGearsScheduleMode::Adhoc
        ], $attributes));
    }
}
