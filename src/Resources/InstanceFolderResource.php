<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InstanceFolderResource
{
    use InteractsWithResponses;

    public function __construct(
        protected readonly Client $client,
        protected readonly int $instance_id
    ) {
        //
    }

    /**
     * Get a paginated listing of resources.
     *
     * @param  array<string, mixed>|null  $query
     * @param  array<string, mixed>  $options
     * @return LengthAwarePaginator<int, MessageGearsFolderData>
     */
    public function index(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get(sprintf(
            'instances/%s/folders', $this->instance_id
        ), [
            'query' => $query,
        ]);

        return $this->paginate($response, $options)->through(
            fn (array $folder) => MessageGearsFolderData::from($folder)
        );
    }

    /**
     * Get an array of all the resources.
     *
     * @return Collection<int, MessageGearsFolderData>
     */
    public function all(): Collection
    {
        $response = $this->client->get(sprintf(
            'instances/%s/folders/all', $this->instance_id
        ));

        return MessageGearsFolderData::collect($this->decode($response), Collection::class);
    }

    /**
     * Get the folder tree.
     *
     * @return array<array-key, mixed>
     */
    public function tree(): array
    {
        $response = $this->client->get(sprintf(
            'instances/%s/folders/tree', $this->instance_id
        ));

        return $this->decode($response);
    }
}
