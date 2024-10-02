<?php

use Actengage\CaseyJones\Data\MessageGearsTemplateLibraryData;

it('can be mocked', function() {
    expect(MessageGearsTemplateLibraryData::mock())->toBeInstanceOf(MessageGearsTemplateLibraryData::class);
});