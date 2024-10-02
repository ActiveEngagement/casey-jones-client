<?php

use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;

it('can be mocked', function() {
    expect(MessageGearsMarketingCampaignData::mock())->toBeInstanceOf(MessageGearsMarketingCampaignData::class);
});