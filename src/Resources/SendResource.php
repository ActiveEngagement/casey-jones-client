<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Illuminate\Pagination\LengthAwarePaginator;

class SendResource
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
        $response = $this->client->get('sends', [
            'query' => $query
        ]);

        return $this->paginate($response, $options);
    }

    /**
     * Create a resource.
     *
     * @param array $attributes
     * @return array
     */
    public function create(array $attributes): array
    {
        $response = $this->client->post('sends', [
            'form_params' => $attributes
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
        $response = $this->client->get(sprintf('sends/%s', $id));

        return $this->decode($response);
    }

    /**
     * Update the specified resource.
     *
     * @param int $id
     * @param array $attributes
     * @return array
     */
    public function update(int $id, array $attributes): array
    {
        $response = $this->client->put(sprintf('sends/%s', $id), [
            'form_params' => $attributes
        ]);

        return $this->decode($response);
    }

    /**
     * Delete the specified resource.
     *
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        return $this->decode(
            $this->client->delete(sprintf('sends/%s', $id))
        );
    }

}