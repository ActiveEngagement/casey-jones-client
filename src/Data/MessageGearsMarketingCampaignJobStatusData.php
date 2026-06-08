<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\MessageGearsJobActivityStatus;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Spatie\LaravelData\Data;

/** @typescript MessageGearsMarketingCampaignJobStatus */
class MessageGearsMarketingCampaignJobStatusData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $subjectLine,
        public ?bool $error = false,
        public ?string $errorMessage = null,
        public ?MessageGearsJobStatus $jobStatus = null,
        public ?MessageGearsJobActivityStatus $jobActivityStatus = null,
        public ?MessageGearsAccountData $account = null,
        public string $category = '',
        public int $startTime = 0,
        public string $notificationEmailAddress = '',
        public int $deliveryCount = 0,
        public int $openCount = 0,
        public int $uniqueOpenCount = 0,
        public int $bounceCount = 0,
        public int $clickCount = 0,
        public int $uniqueClickCount = 0,
        public int $contentErrorCount = 0,
        public int $fblCount = 0,
        public int $unsubCount = 0,
        public int $suppressedCount = 0,
        public string $queryName = '',
        public string $recipientSql = '',
        public int $recipientCount = 0,
        public float $spamAssassinScore = 0.0
    ) {}

    /**
     * Mock an instance of the class.
     *
     * @param  array<string, mixed>  $attributes
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
                'name' => 'Active Engagement Accelerator',
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
            'spamAssassinScore' => 0.0,
        ], $attributes));
    }
}
