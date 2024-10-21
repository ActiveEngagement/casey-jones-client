<?php

namespace Actengage\CaseyJones\Resources;

use Actengage\CaseyJones\Client;
use Actengage\CaseyJones\Concerns\InteractsWithResponses;
use Actengage\CaseyJones\Data\SendData;
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
     * @return \Illuminate\Pagination\LengthAwarePaginator<Actengage\CaseyJones\Data\SendData>
     */
    public function index(?array $query = null, array $options = []): LengthAwarePaginator
    {
        $response = $this->client->get('sends', [
            'query' => $query
        ]);

        return $this->paginate($response, $options)->through(
            fn (array $send) => SendData::from($send)
        );
    }

    /**
     * Create a resource.
     *
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\SendData
     */
    public function create(array $attributes): SendData
    {
        $response = $this->client->post('sends', [
            'json' => $attributes
        ]);

        return SendData::from($this->decode($response));
    }

    /**
     * Show the specified resource.
     *
     * @param string $send_id
     * @return \Actengage\CaseyJones\Data\SendData
     */
    public function show(string $send_id): SendData
    {
        $response = $this->client->get(sprintf('sends/%s', $send_id));

        return SendData::from($this->decode($response));
    }

    /**
     * Update the specified resource.
     *
     * @param string $send_id
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\SendData
     */
    public function update(string $send_id, array $attributes): SendData
    {
        $response = $this->client->put(sprintf('sends/%s', $send_id), [
            'json' => $attributes
        ]);

        return SendData::from($this->decode($response));
    }

    /**
     * Delete the specified resource.
     *
     * @param string $send_id
     * @return \Actengage\CaseyJones\Data\SendData
     */
    public function delete(string $send_id): SendData
    {
        return SendData::from($this->decode(
            $this->client->delete(sprintf('sends/%s', $send_id))
        ));
    }

    /**
     * Get the send audience resource.
     *
     * @param string $send_id
     * @return \Actengage\CaseyJones\Resources\SendAudienceResource
     */
    public function audience(string $send_id): SendAudienceResource
    {
        return new SendAudienceResource(
            client: $this->client,
            send_id: $send_id
        );
    }

    /**
     * Get the send campaign resource.
     *
     * @param string $send_id
     * @return \Actengage\CaseyJones\Resources\SendCampaignResource
     */
    public function campaign(string $send_id): SendCampaignResource
    {
        return new SendCampaignResource(
            client: $this->client,
            send_id: $send_id
        );
    }

}