<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Data;

/** @typescript */
class MessageGearsMarketingCampaignData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $name,
        public MessageGearsFolderData $folder,
        public MessageGearsTemplateData $template,
        public MessageGearsCampaignAudienceData $audience,
        public MessageGearsAccountData $account,
        public ?string $description = null,
        public ?string $category = null,
        public ?string $notificationAddress = null,
        public ?string $urlAppend = null,
        public bool $archived = false,
        public bool $sendProgressUpdates = false,
        public ?MessageGearsPostCampaignTriggerData $postCampaignTrigger = null,
        public bool $seedlistTestingIncluded = false,
        public ?string $seedlistTestingIdentifier = null,
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
            'folder' => MessageGearsFolderData::mock(),
            'template' => MessageGearsTemplateData::mock(),
            'audience' => MessageGearsCampaignAudienceData::mock(),
            'account' => MessageGearsAccountData::mock()
        ], $attributes));
    }
}