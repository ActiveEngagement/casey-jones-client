<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Spatie\LaravelData\Data;

/** @typescript */
class MessageGearsMarketingCampaignNewJobData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $subjectLine,
        public bool $error,
        public MessageGearsJobStatus $jobStatus,
    ) {}

    /**
     * Mock an instance of the class.
     *
     * @param array $attributes
     * @return static
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'id' => 1,
            'subjectLine' => 'some subject line',
            'error' => false,
            'jobStatus' => MessageGearsJobStatus::Initializing,
        ], $attributes));
    }
}
