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
     * @param array|null $query
     * @param array $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get(sprintf(
            'instances/%s/templates', $this->instance_id
        ), [
            'query' => $query
        ]);

        return $this->paginate($response, $options)->through(
            fn (array $template) => MessageGearsTemplateData::from($template)
        );
    }

    /**
     * Create a resource.
     *
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
     */
    public function create(array $attributes): MessageGearsTemplateData
    {
        $response = $this->client->post(sprintf(
            'instances/%s/templates', $this->instance_id
        ), [
            'json' => $attributes
        ]);

        return MessageGearsTemplateData::from($this->decode($response));
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
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
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
     */
    public function update(int $id, array $attributes): MessageGearsTemplateData
    {
        $response = $this->client->put(sprintf(
            'instances/%s/templates/%s', $this->instance_id, $id
        ), [
            'json' => $attributes
        ]);

        return MessageGearsTemplateData::from($this->decode($response));
    }

    /**
     * Delete the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $this->client->delete(sprintf(
            'instances/%s/templates/%s', $this->instance_id, $id
        ));
    }    
}