<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    foreach ([
        app_path('Listeners/CaseyTestListener.php'),
        base_path('stubs/listener.stub'),
    ] as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

it('generates a listener class from the package stub', function () {
    $this->artisan('casey:listener', ['name' => 'CaseyTestListener'])
        ->assertExitCode(0);

    $path = app_path('Listeners/CaseyTestListener.php');

    expect(File::exists($path))->toBeTrue()
        ->and(File::get($path))->toContain('class CaseyTestListener')
        ->and(File::get($path))->toContain('use Actengage\CaseyJones\Events\StreamEventReceived;');
});

it('prefers a published stub when one exists in the application', function () {
    File::ensureDirectoryExists(base_path('stubs'));
    File::put(base_path('stubs/listener.stub'), "<?php\n\nnamespace {{ namespace }};\n\nclass {{ class }}\n{\n    // published stub\n}\n");

    $this->artisan('casey:listener', ['name' => 'CaseyTestListener', '--force' => true])
        ->assertExitCode(0);

    expect(File::get(app_path('Listeners/CaseyTestListener.php')))->toContain('// published stub');
});
