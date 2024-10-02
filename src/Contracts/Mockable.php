<?php

namespace Actengage\CaseyJones\Contracts;

interface Mockable
{
    /**
     * Mock an instance of the class.
     *
     * @param array $attributes
     * @return static
     */
    public static function mock(array $attributes = []): static;
}