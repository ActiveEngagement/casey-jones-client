<?php

use Actengage\CaseyJones\Data\MessageGearsCampaignAudienceData;

it('can be mocked', function() {
    expect(MessageGearsCampaignAudienceData::mock())->toBeInstanceOf(MessageGearsCampaignAudienceData::class);
});