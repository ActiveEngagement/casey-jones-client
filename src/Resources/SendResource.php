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
     * @param string $id
     * @return array
     */
    public function show(string $id): array
    {
        $response = $this->client->get(sprintf('sends/%s', $id));

        return $this->decode($response);
    }

    /**
     * Update the specified resource.
     *
     * @param string $id
     * @param array $attributes
     * @return array
     */
    public function update(string $id, array $attributes): array
    {
        $response = $this->client->put(sprintf('sends/%s', $id), [
            'form_params' => $attributes
        ]);

        return $this->decode($response);
    }

    /**
     * Delete the specified resource.
     *
     * @param string $id
     * @return array
     */
    public function delete(string $id): array
    {
        return $this->decode(
            $this->client->delete(sprintf('sends/%s', $id))
        );
    }

    /**
     * Show the specified resource.
     *
     * @param string $id
     * @return array
     */
    public function audience(string $id): array
    {
        $response = $this->client->get(sprintf('sends/%s/audience', $id));

        return $this->decode($response);
    }

    /**
     * Show the specified resource.
     *
     * @param string $id
     * @return array
     */
    public function campaign(string $id): array
    {
        $response = $this->client->get(sprintf('sends/%s/campaign', $id));

        return $this->decode($response);
    }

}