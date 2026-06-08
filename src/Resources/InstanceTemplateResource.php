<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Illuminate\Pagination\LengthAwarePaginator;

class InstanceTemplateResource
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
     * @return LengthAwarePaginator<int, MessageGearsTemplateData>
     */
    public function index(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get(sprintf(
            'instances/%s/templates', $this->instance_id
        ), [
            'query' => $query,
        ]);

        return $this->paginate($response, $options)->through(
            fn (array $template) => MessageGearsTemplateData::from($template)
        );
    }

    /**
     * Create a resource.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): MessageGearsTemplateData
    {
        $response = $this->client->post(sprintf(
            'instances/%s/templates', $this->instance_id
        ), [
            'json' => $attributes,
        ]);

        return MessageGearsTemplateData::from($this->decode($response));
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id): MessageGearsTemplateData
    {
        $response = $this->client->get(sprintf(
            'instances/%s/templates/%s', $this->instance_id, $id
        ));

        return MessageGearsTemplateData::from($this->decode($response));
    }

    /**
     * Update a resource.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $id, array $attributes): MessageGearsTemplateData
    {
        $response = $this->client->put(sprintf(
            'instances/%s/templates/%s', $this->instance_id, $id
        ), [
            'json' => $attributes,
        ]);

        return MessageGearsTemplateData::from($this->decode($response));
    }

    /**
     * Delete the specified resource.
     */
    public function delete(int $id): void
    {
        $this->client->delete(sprintf(
            'instances/%s/templates/%s', $this->instance_id, $id
        ));
    }
}
