<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Contracts;

interface Mockable
{
    /**
     * Mock an instance of the class.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function mock(array $attributes = []): static;
}
