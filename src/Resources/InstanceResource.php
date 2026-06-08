<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\InstanceData;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
     * @param  array<string, mixed>|null  $query
     * @param  array<string, mixed>  $options
     * @return LengthAwarePaginator<int, InstanceData>
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function index(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get('instances', [
            'query' => $query,
        ]);

        return $this->paginate($response, $options)->through(
            fn (array $data) => InstanceData::from($data)
        );
    }

    /**
     * Get an array of all the resources.
     *
     * @param  array<string, mixed>|null  $query
     * @return Collection<int, InstanceData>
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function all(?array $query = null): Collection
    {
        $response = $this->client->get('instances/all', [
            'query' => $query,
        ]);

        return InstanceData::collect(
            $this->decode($response), Collection::class
        );
    }

    /**
     * Create a resource.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function create(array $attributes): InstanceData
    {
        $response = $this->client->post('instances', [
            'json' => $attributes,
        ]);

        return InstanceData::from($this->decode($response));
    }

    /**
     * Show the specified resource.
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function show(int $instance_id): InstanceData
    {
        $response = $this->client->get(sprintf('instances/%s', $instance_id));

        return InstanceData::from($this->decode($response));
    }

    /**
     * Update a resource.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function update(int $instance_id, array $attributes): InstanceData
    {
        $response = $this->client->put(sprintf('instances/%s', $instance_id), [
            'json' => $attributes,
        ]);

        return InstanceData::from($this->decode($response));
    }

    /**
     * Delete the specified resource.
     *
     * @throws ServerException
     * @throws ClientException
     */
    public function delete(int $instance_id): InstanceData
    {
        return InstanceData::from($this->decode(
            $this->client->delete(sprintf('instances/%s', $instance_id))
        ));
    }

    /**
     * Get a template resource.
     */
    public function templates(int $instance_id): InstanceTemplateResource
    {
        return new InstanceTemplateResource(
            client: $this->client,
            instance_id: $instance_id
        );
    }

    /**
     * Get a folder resource.
     */
    public function folders(int $instance_id): InstanceFolderResource
    {
        return new InstanceFolderResource(
            client: $this->client,
            instance_id: $instance_id
        );
    }
}
