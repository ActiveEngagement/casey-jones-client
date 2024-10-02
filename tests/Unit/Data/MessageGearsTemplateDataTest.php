<?php

use Actengage\CaseyJones\Data\MessageGearsTemplateData;

it('can be mocked', function() {
    expect(MessageGearsTemplateData::mock())->toBeInstanceOf(MessageGearsTemplateData::class);
});