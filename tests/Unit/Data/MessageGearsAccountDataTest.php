<?php

use Actengage\CaseyJones\Data\MessageGearsAccountData;

it('can be mocked', function() {
    expect(MessageGearsAccountData::mock())->toBeInstanceOf(MessageGearsAccountData::class);
});