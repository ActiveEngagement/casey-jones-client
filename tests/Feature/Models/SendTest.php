<?php

use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Actengage\CaseyJones\Enums\SendStatus;
use Actengage\CaseyJones\Models\Send;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config()->set('database.connections.sqlite.database', ':memory:');
    DB::purge('sqlite');
    DB::setDefaultConnection('sqlite');

    Schema::create('sends', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->integer('instance_id')->nullable();
        $table->integer('campaign_id')->nullable();
        $table->string('name')->nullable();
        $table->string('status')->nullable();
        $table->json('folder')->nullable();
        $table->json('data_variables')->nullable();
        $table->json('meta')->nullable();
        $table->integer('mailingid')->nullable();
        $table->timestamp('scheduled_at')->nullable();
        $table->timestamp('failed_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('send_jobs', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('send_id')->nullable();
        $table->integer('status_code')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
});

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
    $send = new Send(['folder' => null]);

    expect($send->getAttributes()['folder'])->toBeNull();
});

it('serializes meta arrays through the array object cast', function () {
    $send = Send::create(['name' => 'Test', 'status' => SendStatus::Active, 'meta' => ['a' => 1]]);

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
    $send = Send::create([
        'name' => 'Test',
        'status' => SendStatus::Draft,
        'scheduled_at' => now(),
    ]);

    expect($send->scheduled_at)->toBeNull();
});

it('nullifies failed_at when saved as scheduled', function () {
    $send = Send::create([
        'name' => 'Test',
        'status' => SendStatus::Scheduled,
        'scheduled_at' => now(),
        'failed_at' => now(),
    ]);

    expect($send->failed_at)->toBeNull()
        ->and($send->scheduled_at)->not->toBeNull();
});

describe('query scopes', function () {
    it('compiles the status scopes', function () {
        expect(Send::status(SendStatus::Draft, SendStatus::Active)->count())->toBe(0)
            ->and(Send::draft()->count())->toBe(0)
            ->and(Send::scheduled()->count())->toBe(0)
            ->and(Send::active()->count())->toBe(0)
            ->and(Send::failed()->count())->toBe(0)
            ->and(Send::queued()->count())->toBe(0);
    });

    it('compiles the instance and campaign scopes', function () {
        expect(Send::instance(10)->count())->toBe(0)
            ->and(Send::query()->campaignId(20)->count())->toBe(0);
    });

    it('compiles the scheduledAt scope from a string and a Carbon instance', function () {
        expect(Send::scheduledAt('2026-01-01 12:00:00')->count())->toBe(0)
            ->and(Send::scheduledAt(Carbon::parse('2026-01-01 12:00:00'))->count())->toBe(0);
    });

    it('compiles the readyToSend and activeTooLong scopes', function () {
        expect(Send::readyToSend()->count())->toBe(0)
            ->and(Send::activeTooLong()->count())->toBe(0);
    });
});
