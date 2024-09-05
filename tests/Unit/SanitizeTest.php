<?php

namespace Tests\Unit\Sanitizers;

use Actengage\Sanitize\Facades\Sanitize;
use Tests\TestCase;
use Tests\User;

class SanitizeTest extends TestCase
{
    public function test_sanitizing_inputs()
    {
        $request = Sanitize::request(request()->merge([
            'name' => 'Foo Bar',
            'email' => ' TEST @test.com ',
            'phone' => '1-800-567-1234',
            'zip' => ' 80863 ',
        ]));

        $this->assertEquals('Foo Bar', $request->name);
        $this->assertEquals('test@test.com', $request->email);
        $this->assertEquals('8005671234', $request->phone);
        $this->assertEquals('80863', $request->zip);
    }

    public function test_sanitizing_email()
    {
        $this->assertEquals('test@test.com', Sanitize::email(' TEST @test.com '));
        $this->assertEquals('testtest@gmail.com', Sanitize::email('test.test@gmail.com'));
        $this->assertEquals(null, Sanitize::email('test'));
        $this->assertEquals(null, Sanitize::email(' '));
        $this->assertEquals(null, Sanitize::email(null));
    }

    public function test_sanitizing_phone()
    {
        $this->assertEquals('8881231234', Sanitize::phone('(888) 123-1234'));
        $this->assertEquals('8005671234', Sanitize::phone('0-800-567-1234'));
        $this->assertEquals('8005671234', Sanitize::phone('1-800-567-1234 x1234'));
        $this->assertNull(Sanitize::phone('567123'));
        $this->assertNull(Sanitize::phone('8005551212'));
        $this->assertNull(Sanitize::phone('800-555-1212'));
    }
    
    public function test_sanitizing_zip()
    {
        $this->assertEquals('02801', Sanitize::zip('2801'));
        $this->assertEquals('22801', Sanitize::zip('22801'));
        $this->assertEquals('22801-1234', Sanitize::zip('228011234'));
        $this->assertEquals('22801-1234', Sanitize::zip('22801-1234'));
        $this->assertNull(Sanitize::zip('00000'));
        $this->assertNull(Sanitize::zip('228011'));
        $this->assertNull(Sanitize::zip('22801-12345'));
        $this->assertNull(Sanitize::zip('22801-0000'));
    }

    public function test_sanitizing_cast_attributes()
    {
        $user = User::make([
            'name' => 'Foo Bar',
            'email' => ' TEST @test.com ',
            'phone' => '1-800-567-1234',
            'zip' => ' 80863 ',
        ]);

        $this->assertEquals('Foo Bar', $user->name);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals('8005671234', $user->phone);
        $this->assertEquals('80863', $user->zip);
    }

    public function test_custom_sanitizer_macro()
    {
        Sanitize::macro('number', function(?string $value) {
            return is_numeric($value) ? $value : null;
        });

        $this->assertEquals('123', Sanitize::number('123'));
        $this->assertEquals(null, Sanitize::number('test'));
    }
}
