<?php

use Actengage\CaseyJones\Data\MessageGearsCampaignScheduleData;
use Actengage\CaseyJones\Data\MessageGearsFolderTreeData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignNewJobData;
use Actengage\CaseyJones\Data\SendJobData;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Actengage\CaseyJones\Enums\MessageGearsScheduleMode;

it('mocks the campaign schedule data', function () {
    $data = MessageGearsCampaignScheduleData::mock();

    expect($data)->toBeInstanceOf(MessageGearsCampaignScheduleData::class)
        ->and($data->scheduleMode)->toBe(MessageGearsScheduleMode::Adhoc);
});

it('mocks the folder tree data', function () {
    $data = MessageGearsFolderTreeData::mock(['parentId' => 5]);

    expect($data)->toBeInstanceOf(MessageGearsFolderTreeData::class)
        ->and($data->parentId)->toBe(5)
        ->and($data->children)->toBe([]);
});

it('mocks the marketing campaign new job data', function () {
    $data = MessageGearsMarketingCampaignNewJobData::mock();

    expect($data)->toBeInstanceOf(MessageGearsMarketingCampaignNewJobData::class)
        ->and($data->jobStatus)->toBe(MessageGearsJobStatus::Initializing)
        ->and($data->error)->toBeFalse();
});

it('builds send job data from an array', function () {
    $data = SendJobData::from([
        'id' => 1,
        'send_id' => 2,
        'status_code' => 200,
        'failed' => false,
        'mailingid' => 99,
        'response' => '{}',
        'error_message' => null,
        'created_at' => '2026-01-01 00:00:00',
        'updated_at' => '2026-01-01 00:00:00',
    ]);

    expect($data)->toBeInstanceOf(SendJobData::class)
        ->and($data->id)->toBe(1)
        ->and($data->send_id)->toBe(2);
});
