<?php

use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Actengage\CaseyJones\Enums\SendStatus;
use Actengage\CaseyJones\Models\Send;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('casts meta and data_variables to array objects', function () {
    $send = new Send([
        'meta' => ['foo' => 'bar'],
        'data_variables' => ['baz' => 'qux'],
    ]);

    expect($send->meta)->toBeInstanceOf(ArrayObject::class)
        ->and($send->meta['foo'])->toBe('bar')
        ->and($send->data_variables['baz'])->toBe('qux');
});

it('casts empty meta to an empty object', function () {
    expect((new Send(['meta' => []]))->getAttributes()['meta'])->toBe('{}');
});

it('casts the folder attribute from data, an array and a json string', function () {
    expect((new Send(['folder' => MessageGearsFolderData::mock(['id' => 5])]))->folder->id)->toBe(5)
        ->and((new Send(['folder' => MessageGearsFolderData::mock(['id' => 7])->toArray()]))->folder->id)->toBe(7)
        ->and((new Send(['folder' => MessageGearsFolderData::mock(['id' => 9])->toJson()]))->folder->id)->toBe(9)
        ->and((new Send)->folder)->toBeNull();
});

it('returns null from the array object cast when the attribute is missing', function () {
    $send = new Send;
    $send->setRawAttributes(['meta' => null]);

    expect($send->meta)->toBeNull();
});

it('stores null when the folder is set to null', function () {
    expect((new Send(['folder' => null]))->getAttributes()['folder'])->toBeNull();
});

it('assigns a uuid to the uuid column and keeps an auto-incrementing id', function () {
    $send = Send::factory()->create();

    expect($send->getKeyName())->toBe('id')
        ->and($send->getIncrementing())->toBeTrue()
        ->and($send->id)->toBeInt()
        ->and($send->uuid)->toBeString()
        ->and($send->uuid)->toMatch('/^[0-9a-f-]{36}$/');
});

it('serializes meta arrays through the array object cast', function () {
    $send = Send::factory()->create(['meta' => ['a' => 1]]);

    expect($send->fresh()->meta['a'])->toBe(1);
});

it('forces the campaign id to the configured value outside production', function () {
    config()->set('services.mg.campaign_id', 99);

    expect((new Send(['campaign_id' => 12345]))->campaign_id)->toBe(99);
});

it('defines the jobs relationship', function () {
    expect((new Send)->jobs())->toBeInstanceOf(HasMany::class);
});

it('determines if it is one of the given statuses', function () {
    $send = new Send(['status' => SendStatus::Active]);

    expect($send->isStatus(SendStatus::Active))->toBeTrue()
        ->and($send->isStatus(SendStatus::Draft))->toBeFalse()
        ->and($send->isStatus(SendStatus::Draft, SendStatus::Active))->toBeTrue();
});

it('broadcasts on the model and a private channel', function () {
    $send = new Send(['status' => SendStatus::Active]);

    $channels = $send->broadcastOn('created');

    expect($channels[0])->toBe($send)
        ->and($channels[1])->toBeInstanceOf(PrivateChannel::class);
});

it('nullifies scheduled_at when saved as a draft', function () {
    $send = Send::factory()->create([
        'status' => SendStatus::Draft,
        'scheduled_at' => now(),
    ]);

    expect($send->scheduled_at)->toBeNull();
});

it('nullifies failed_at when saved as scheduled', function () {
    $send = Send::factory()->create([
        'status' => SendStatus::Scheduled,
        'scheduled_at' => now(),
        'failed_at' => now(),
    ]);

    expect($send->failed_at)->toBeNull()
        ->and($send->scheduled_at)->not->toBeNull();
});

it('compiles the no-argument query scopes', function (string $scope) {
    expect(Send::query()->{$scope}()->count())->toBe(0);
})->with([
    'draft',
    'scheduled',
    'active',
    'failed',
    'queued',
    'readyToSend',
    'activeTooLong',
]);

it('compiles the status scope with explicit statuses', function () {
    expect(Send::status(SendStatus::Draft, SendStatus::Active)->count())->toBe(0);
});

it('compiles the instance and campaign scopes', function () {
    expect(Send::instance(10)->count())->toBe(0)
        ->and(Send::query()->campaignId(20)->count())->toBe(0);
});

it('compiles the scheduledAt scope from a string and a Carbon instance', function () {
    expect(Send::scheduledAt('2026-01-01 12:00:00')->count())->toBe(0)
        ->and(Send::scheduledAt(Carbon::parse('2026-01-01 12:00:00'))->count())->toBe(0);
});
