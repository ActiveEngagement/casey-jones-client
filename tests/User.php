<?php

namespace Tests;

use Actengage\Sanitize\Casts\Email;
use Actengage\Sanitize\Casts\Phone;
use Actengage\Sanitize\Casts\Zip;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'zip'
    ];

    protected $casts = [
        'email' => Email::class,
        'phone' => Phone::class,
        'zip' => Zip::class,
    ];
}