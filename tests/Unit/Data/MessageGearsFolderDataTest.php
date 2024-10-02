<?php

use Actengage\CaseyJones\Data\MessageGearsFolderData;

it('can be mocked', function() {
    expect(MessageGearsFolderData::mock())->toBeInstanceOf(MessageGearsFolderData::class);
});