<?php

use Actengage\CaseyJones\Enums\SendStatus;
use Actengage\CaseyJones\Models\Send;
use Actengage\CaseyJones\Models\SendJob;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config()->set('database.connections.sqlite.database', ':memory:');
    DB::purge('sqlite');
    DB::setDefaultConnection('sqlite');

    Schema::create('sends', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name')->nullable();
        $table->string('status')->nullable();
        $table->json('meta')->nullable();
        $table->json('data_variables')->nullable();
        $table->integer('mailingid')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('send_jobs', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('send_id')->nullable();
        $table->integer('status_code')->nullable();
        $table->boolean('failed')->nullable();
        $table->integer('mailingid')->nullable();
        $table->json('response')->nullable();
        $table->text('error_message')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
});

it('casts its attributes', function () {
    $job = new SendJob([
        'failed' => 1,
        'status_code' => '200',
        'mailingid' => '5',
        'response' => ['ok' => true],
    ]);

    expect($job->failed)->toBeTrue()
        ->and($job->status_code)->toBe(200)
        ->and($job->mailingid)->toBe(5)
        ->and($job->response)->toBe(['ok' => true]);
});

it('belongs to a send', function () {
    expect((new SendJob())->send())->toBeInstanceOf(BelongsTo::class);
});

it('broadcasts on itself and its send', function () {
    $channels = (new SendJob())->broadcastOn('created');

    expect($channels)->toHaveCount(2)
        ->and($channels[0])->toBeInstanceOf(SendJob::class);
});

describe('query scopes', function () {
    it('compiles the failed, success and pending scopes', function () {
        expect(SendJob::failed()->count())->toBe(0)
            ->and(SendJob::success()->count())->toBe(0)
            ->and(SendJob::pending()->count())->toBe(0)
            ->and(SendJob::mailingid(5)->count())->toBe(0);
    });
});

it('derives the failed flag from the status code when saving', function () {
    $success = SendJob::create(['status_code' => 200]);
    $failure = SendJob::create(['status_code' => 500]);

    expect($success->failed)->toBeFalse()
        ->and($failure->failed)->toBeTrue();
});

it('marks the job as failed from a generic exception', function () {
    $send = Send::create(['name' => 'Test', 'status' => SendStatus::Active, 'mailingid' => 42]);
    $job = new SendJob(['status_code' => 200]);
    $job->send_id = $send->id;
    $job->save();

    $job->fail(new Exception('something broke'));

    expect($job->failed)->toBeTrue()
        ->and($job->mailingid)->toBe(42)
        ->and($job->error_message)->toBe('something broke');
});

it('marks the job as failed from a bad response exception', function () {
    $send = Send::create(['name' => 'Test', 'status' => SendStatus::Active, 'mailingid' => 7]);
    $job = new SendJob(['status_code' => 200]);
    $job->send_id = $send->id;
    $job->save();

    $exception = new BadResponseException(
        'Unprocessable',
        new Request('POST', '/'),
        new Response(422, [], json_encode(['errorMessage' => 'Validation failed']))
    );

    $job->fail($exception);

    expect($job->failed)->toBeTrue()
        ->and($job->status_code)->toBe(422)
        ->and($job->error_message)->toBe('Validation failed')
        ->and($job->response)->toMatchArray(['errorMessage' => 'Validation failed']);
});
