<?php

use Illuminate\Support\Facades\Cache;

it('broadcasts a restart signal and records the time', function () {
    Cache::forget('casey:restart');

    $this->artisan('casey:restart')
        ->expectsOutputToContain('Broadcasting Casey Jones restart signal.')
        ->assertExitCode(0);

    expect(Cache::get('casey:restart'))->not->toBeNull();
});
