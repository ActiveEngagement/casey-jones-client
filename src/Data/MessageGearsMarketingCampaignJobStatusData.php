<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsJobActivityStatus;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Spatie\LaravelData\Data;

/** @typescript */
class MessageGearsMarketingCampaignJobStatusData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $subjectLine,
        public bool $error = false,
        public ?string $errorMessage = null,
        public MessageGearsJobStatus $jobStatus,
        public ?MessageGearsJobActivityStatus $jobActivityStatus = null,
        public MessageGearsAccountData $account,
        public string $category,
        public int $startTime,
        public string $notificationEmailAddress,
        public int $deliveryCount,
        public int $openCount,
        public int $uniqueOpenCount,
        public int $bounceCount,
        public int $clickCount,
        public int $uniqueClickCount,
        public int $contentErrorCount,
        public int $fblCount,
        public int $unsubCount,
        public int $suppressedCount,
        public string $queryName,
        public string $recipientSql,
        public int $recipientCount,
        public float $spamAssassinScore
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
            'id' => 100000,
            'subjectLine' => 'Test Subject',
            'error' => false,
            'jobStatus' => MessageGearsJobStatus::Completed,
            'jobActivityStatus' => MessageGearsJobActivityStatus::Complete,
            'account' => MessageGearsAccountData::from([
                'id' => config('services.mg.accelerator_account_id', 1),
                'name' => 'Active Engagement Accelerator'
            ]),
            'category' => 'AE',
            'startTime' => time(),
            'notificationEmailAddress' => 'test@test.com',
            'deliveryCount' => 1,
            'openCount' => 1,
            'uniqueOpenCount' => 1,
            'bounceCount' => 0,
            'clickCount' => 0,
            'uniqueClickCount' => 0,
            'contentErrorCount' => 0,
            'fblCount' => 0,
            'unsubCount' => 0,
            'suppressedCount' => 0,
            'queryName' => 'Alchemy Test Audience',
            'recipientSql' => '',
            'recipientCount' => 0,
            'spamAssassinScore' => 0.0
        ], $attributes));
    }
}
