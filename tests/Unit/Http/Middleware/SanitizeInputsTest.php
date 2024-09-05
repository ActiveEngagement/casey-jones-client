<?php

namespace Tests\Unit\Http\Middleware;

use Actengage\Sanitize\Http\Middleware\SanitizeInputs;
use Illuminate\Http\Request;
use Tests\TestCase;

class SanitizeInputsTest extends TestCase
{
    public function test_sanitizing_inputs_using_middleware()
    {
        $request = request()->merge([
            'email' => ' TEST @test.com ',
            'phone' => '1-800-567-1234',
            'zip' => ' 80863 ',
            'name' => 'Foo Bar'
        ]);
        
        $middleware = new SanitizeInputs;

        $middleware->handle($request, function(Request $request) {
            $this->assertEquals('test@test.com', $request->email);
            $this->assertEquals('8005671234', $request->phone);
            $this->assertEquals('80863', $request->zip);
            $this->assertEquals('Foo Bar', $request->name);

            return response()->make();
        });
    }
}