<?php

use Actengage\CaseyJones\Models\Send;
use Actengage\CaseyJones\Models\SendJob;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    expect((new SendJob)->send())->toBeInstanceOf(BelongsTo::class);
});

it('broadcasts on itself and its send', function () {
    $channels = (new SendJob)->broadcastOn('created');

    expect($channels)->toHaveCount(2)
        ->and($channels[0])->toBeInstanceOf(SendJob::class);
});

it('assigns a uuid to the uuid column and keeps an auto-incrementing id', function () {
    $job = SendJob::factory()->create(['status_code' => 200]);

    expect($job->getIncrementing())->toBeTrue()
        ->and($job->id)->toBeInt()
        ->and($job->uuid)->toBeString();
});

it('compiles the no-argument query scopes', function (string $scope) {
    expect(SendJob::query()->{$scope}()->count())->toBe(0);
})->with(['failed', 'success', 'pending']);

it('compiles the mailingid scope', function () {
    expect(SendJob::mailingid(5)->count())->toBe(0);
});

it('derives the failed flag from the status code when saving', function (int $statusCode, bool $failed) {
    expect(SendJob::factory()->create(['status_code' => $statusCode])->failed)->toBe($failed);
})->with([
    'success' => [200, false],
    'server error' => [500, true],
]);

it('marks the job as failed from a generic exception', function () {
    $send = Send::factory()->create(['mailingid' => 42]);
    $job = SendJob::factory()->create(['send_id' => $send->id, 'status_code' => 200]);

    $job->fail(new Exception('something broke'));

    expect($job->failed)->toBeTrue()
        ->and($job->mailingid)->toBe(42)
        ->and($job->error_message)->toBe('something broke');
});

it('marks the job as failed from a bad response exception', function () {
    $send = Send::factory()->create(['mailingid' => 7]);
    $job = SendJob::factory()->create(['send_id' => $send->id, 'status_code' => 200]);

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
