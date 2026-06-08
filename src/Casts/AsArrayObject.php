<?php

namespace Actengage\CaseyJones\Casts;

use ArrayObject;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class AsArrayObject implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array<mixed>  $arguments
     * @return CastsAttributes<ArrayObject<array-key, mixed>, iterable<array-key, mixed>>
     */
    public static function castUsing(array $arguments)
    {
        /**
         * @implements CastsAttributes<ArrayObject<array-key, mixed>, iterable<array-key, mixed>>
         */
        return new class implements CastsAttributes
        {
            /**
             * @param  array<string, mixed>  $attributes
             * @return ArrayObject<array-key, mixed>|null
             */
            public function get(Model $model, string $key, mixed $value, array $attributes): ?ArrayObject
            {
                if (! isset($attributes[$key])) {
                    return null;
                }

                $data = Json::decode($attributes[$key]);

                return is_array($data) ? new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS) : null;
            }

            /**
             * @param  array<string, mixed>  $attributes
             * @return array<string, mixed>
             */
            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                if (empty($value)) {
                    $value = (object) [];
                }

                return [$key => Json::encode($value)];
            }

            /**
             * @param  ArrayObject<array-key, mixed>  $value
             * @param  array<string, mixed>  $attributes
             * @return array<array-key, mixed>|object
             */
            public function serialize(Model $model, string $key, mixed $value, array $attributes): array|object
            {
                return empty($value = $value->getArrayCopy()) ? (object) $value : $value;
            }
        };
    }
}
