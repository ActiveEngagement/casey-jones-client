<?php

use Actengage\CaseyJones\Data\MessageGearsPostCampaignTriggerData;

it('can be mocked', function() {
    expect(MessageGearsPostCampaignTriggerData::mock())->toBeInstanceOf(MessageGearsPostCampaignTriggerData::class);
});