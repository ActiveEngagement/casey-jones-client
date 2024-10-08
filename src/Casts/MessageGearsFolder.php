<?php
 
namespace Actengage\CaseyJones\Casts;

use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
 
class MessageGearsFolder implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return \Actengage\CaseyJones\Data\MessageGearsFolderData
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MessageGearsFolderData
    {
        if($value === null) {
            return null;
        }

        return MessageGearsFolderData::from(json_decode($value, true));
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if($value === null) {
            return null;
        }

        if(is_array($value)) {
            $value = MessageGearsFolderData::from($value);
        }
        else if(is_string($value)) {
            $value = MessageGearsFolderData::from(json_decode($value, true));
        }

        return $value->toJson();
    }
}