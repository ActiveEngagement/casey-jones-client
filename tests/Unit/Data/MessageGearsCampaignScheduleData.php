<?php

use Actengage\CaseyJones\Data\MessageGearsCampaignScheduleData;

it('can be mocked', function() {
    expect(MessageGearsCampaignScheduleData::mock())->toBeInstanceOf(MessageGearsCampaignScheduleData::class);
});