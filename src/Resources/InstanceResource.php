<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Illuminate\Pagination\LengthAwarePaginator;

class InstanceResource
{
    use InteractsWithResponses;
    
    public function __construct(
        protected readonly Client $client
    ) {
        //
    }

    /**
     * Get a paginated listing of resources.
     *
     * @param array|null $query
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function list(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get('instances', [
            'query' => $query
        ]);

        return $this->paginate($response, $options);
    }

    /**
     * Get an array of all the resources.
     *
     * @param array|null $query
     * @return array
     */
    public function all(?array $query = null): array
    {
        $response = $this->client->get('instances/all', [
            'query' => $query
        ]);

        return $this->decode($response);
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return array
     */
    public function show(int $id): array
    {
        $response = $this->client->get(sprintf('instances/%s', $id));

        return $this->decode($response);
    }
    
}