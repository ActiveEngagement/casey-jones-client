<?php

namespace Actengage\CaseyJones\Models;

use Actengage\CaseyJones\Enums\SendStatus;
use Actengage\CaseyJones\Model;

class Send extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SendStatus::class,
            'scheduled_at' => 'datetime',
            'failed_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function resource(): string
    {
        return 'sends';
    }
}