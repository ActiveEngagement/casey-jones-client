<?php

namespace Actengage\CaseyJones\Casts;

use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<MessageGearsFolderData, MessageGearsFolderData|array<string, mixed>|string>
 */
class MessageGearsFolder implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MessageGearsFolderData
    {
        if (! is_string($value)) {
            return null;
        }

        return MessageGearsFolderData::from(json_decode($value, true));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  MessageGearsFolderData|array<string, mixed>|string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $value = MessageGearsFolderData::from($value);
        } elseif (is_string($value)) {
            $value = MessageGearsFolderData::from(json_decode($value, true));
        }

        return $value->toJson();
    }
}
