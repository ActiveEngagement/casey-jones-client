<?php

use Actengage\CaseyJones\Data\MessageGearsSampleRecipientData;

it('can be mocked', function() {
    expect(MessageGearsSampleRecipientData::mock())->toBeInstanceOf(MessageGearsSampleRecipientData::class);
});