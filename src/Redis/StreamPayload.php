<?php

namespace Actengage\CaseyJones\Redis;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class StreamPayload implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * Construct the Redis stream payload.
     *
     * @param string $name
     * @param array $payload
     */
    public function __construct(
        public readonly string $token,
        public readonly string $name,
        public readonly string $payload,
        public readonly string $id = '*'
    ) {
        //  
    }

    /**
     * Cast the class to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'payload' => $this->payload
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Json serializes the object.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->toJson(1);
    }
}