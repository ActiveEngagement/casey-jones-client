<?php

namespace Tests\Unit\Rules;

use Actengage\Sanitize\Rules\Zip;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ZipTest extends TestCase
{
    public function test_validating_zip_codes()
    {
        $rules = ['zip' => [new Zip]];

        // $this->assertTrue(Validator::make(['zip' => '22801'], $rules)->passes());
        // $this->assertTrue(Validator::make(['zip' => '22801-4567'], $rules)->passes());
        $this->assertFalse(Validator::make(['zip' => '00000'], $rules)->passes());
        $this->assertFalse(Validator::make(['zip' => '22801-0000'], $rules)->passes());
        
        $this->assertSame([
            'The zip must be a valid zip code.'
        ], Validator::make(['zip' => '00000'], $rules)->errors()->get('zip'));
    }
}