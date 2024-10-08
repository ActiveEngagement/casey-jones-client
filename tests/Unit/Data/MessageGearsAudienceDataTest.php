<?php

use Actengage\CaseyJones\Data\MessageGearsAudienceData;
use Actengage\CaseyJones\Data\MessageGearsSegmentationCriteriaData;
use Actengage\CaseyJones\Exceptions\MissingSegmentationCriteria;

it('can be mocked', function() {
    expect(MessageGearsAudienceData::mock())->toBeInstanceOf(MessageGearsAudienceData::class);
});

it('transposes segmentation criteria', function() {
    expect(MessageGearsAudienceData::mock([
        'segmentationCriteria' => [
            MessageGearsSegmentationCriteriaData::mock([
                'id' => 1,
                'name' => 'forceFail',
                'label' => 'Force Fail',
                'description' => 'Force the SQL query to fail',
                'defaultValue' => 'NA',
            ]),
            MessageGearsSegmentationCriteriaData::mock([
                'id' => 2,
                'name' => 'emails',
                'label' => 'Emails',
                'description' => 'Send to a list of emails',
                'defaultValue' => 'test@test.com,test2@test.com',
            ])
        ]
    ])->transposeSegmentationCriteria([
        'forceFail' => 'YES',
        'emails' => 'test@test.com'
    ]))->toBe([
        ['id' => 1, 'value' => 'YES'],
        ['id' => 2, 'value' => 'test@test.com'],
    ]);
});

it('throws an errow if a segmentation criteria is missing while transposing', function() {
    expect(MessageGearsAudienceData::mock([
        'segmentationCriteria' => [
            MessageGearsSegmentationCriteriaData::mock([
                'id' => 1,
                'name' => 'forceFail',
                'label' => 'Force Fail',
                'description' => 'Force the SQL query to fail',
                'defaultValue' => 'NA',
            ])
        ]
    ])->transposeSegmentationCriteria([
        'forceFail' => 'YES',
        'emails' => 'test@test.com'
    ]))->toBe([
        ['id' => 1, 'value' => 'YES'],
        ['id' => 2, 'value' => 'test@test.com'],
    ]);
})->throws(MissingSegmentationCriteria::class, 'The key "emails" is missing from the available segmentation criteria: "forceFail"');