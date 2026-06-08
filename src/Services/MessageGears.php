<?php

namespace Actengage\CaseyJones\Services;

use Actengage\CaseyJones\Data\MessageGearsAudienceData;
use Actengage\CaseyJones\Data\MessageGearsFolderData;
use Actengage\CaseyJones\Data\MessageGearsFolderTreeData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignNewJobData;
use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasDeliveries;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasOpens;
use Actengage\CaseyJones\Exceptions\JobHasAlreadyBeenCompleted;
use Actengage\CaseyJones\Exceptions\JobIsCurrentlyBeingCollected;
use Actengage\MessageGears\Accelerator;
use Actengage\MessageGears\Cloud;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MessageGears
{
    /**
     * The current accelerator instance.
     */
    public Cloud $cloud;

    /**
     * The current accelerator instance.
     */
    public Accelerator $accelerator;

    /**
     * Set the Cloud instance.
     */
    public function cloud(Cloud $instance): static
    {
        $this->cloud = $instance;

        return $this;
    }

    /**
     * Set the Accelerator instance.
     */
    public function accelerator(Accelerator $instance): static
    {
        $this->accelerator = $instance;

        return $this;
    }

    /**
     * Create the marketing campaign job.
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function createMarketingCampaignJob(int $campaign_id): MessageGearsMarketingCampaignNewJobData
    {
        $response = $this->accelerator->post([
            'campaign/marketing/%d/job', (string) $campaign_id,
        ]);

        return MessageGearsMarketingCampaignNewJobData::from(json_decode($response->getBody()));
    }

    /**
     * Get the job status.
     *
     * @throws ServerException
     */
    public function getMarketingCampaignJobStatus(int $campaign_id, int $job_id): MessageGearsMarketingCampaignJobStatusData
    {
        $response = $this->accelerator->get([
            'campaign/marketing/%d/job/%d', (string) $campaign_id, (string) $job_id,
        ]);

        return MessageGearsMarketingCampaignJobStatusData::from(json_decode($response->getBody()));
    }

    /**
     * Check the job status for potential conflicts and throws exceptions.
     *
     * @throws ServerException
     * @throws JobAlreadyHasDeliveries
     * @throws JobAlreadyHasOpens
     * @throws JobHasAlreadyBeenCompleted
     * @throws JobIsCurrentlyBeingCollected
     */
    public function checkMarketingCampaignJobStatus(int $campaign_id, int $job_id): MessageGearsMarketingCampaignJobStatusData
    {
        $response = $this->getMarketingCampaignJobStatus($campaign_id, $job_id);

        if ($response->jobStatus === MessageGearsJobStatus::Completed) {
            throw new JobHasAlreadyBeenCompleted(
                sprintf('The job (%s) has already been completed.', $job_id)
            );
        }

        if ($response->jobStatus === MessageGearsJobStatus::Collecting) {
            throw new JobIsCurrentlyBeingCollected(
                sprintf('The job (%s) is currently being collected.', $job_id)
            );
        }

        if ($response->deliveryCount !== 0) {
            throw new JobAlreadyHasDeliveries(
                sprintf('The job (%s) already has %s deliveries.', $job_id, $response->deliveryCount)
            );
        }

        if ($response->openCount !== 0) {
            throw new JobAlreadyHasOpens(
                sprintf('The job (%s) already has %s opens.', $job_id, $response->openCount)
            );
        }

        return $response;
    }

    /**
     * Decode a paginated API response body into its component parts.
     *
     * @return array{content: list<array<array-key, mixed>>, totalElements: int, size: int, last: bool}
     */
    protected function decodePage(string $body): array
    {
        $data = json_decode($body, true);

        $content = is_array($data) && isset($data['content']) && is_array($data['content'])
            ? array_values(array_filter($data['content'], is_array(...)))
            : [];

        return [
            'content' => $content,
            'totalElements' => is_array($data) && isset($data['totalElements']) && is_int($data['totalElements']) ? $data['totalElements'] : 0,
            'size' => is_array($data) && isset($data['size']) && is_int($data['size']) ? $data['size'] : 0,
            'last' => is_array($data) && isset($data['last']) && is_bool($data['last']) ? $data['last'] : true,
        ];
    }

    /**
     * Get a list of template.
     *
     * @return LengthAwarePaginator<int, MessageGearsTemplateData>
     *
     * @throws ServerException
     */
    public function getTemplates(int $page = 1, int $limit = 50): LengthAwarePaginator
    {
        $response = $this->accelerator->get('template', [
            'query' => [
                'page' => max(0, $page - 1),
                'size' => $limit,
            ],
        ]);

        $data = $this->decodePage($response->getBody()->getContents());

        $items = array_map(
            MessageGearsTemplateData::from(...),
            $data['content']
        );

        return new LengthAwarePaginator(
            $items, $data['totalElements'], $data['size'], $page
        );
    }

    /**
     * Get a template.
     *
     * @throws ServerException
     */
    public function getTemplate(int $template_id): MessageGearsTemplateData
    {
        $response = $this->accelerator->get([
            'template/%s', (string) $template_id,
        ]);

        return MessageGearsTemplateData::from(json_decode($response->getBody()));
    }

    /**
     * Creates a template.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function createTemplate(array $attributes): MessageGearsTemplateData
    {
        $response = $this->accelerator->post('template', [
            'json' => $attributes,
        ]);

        return MessageGearsTemplateData::from(json_decode($response->getBody()));
    }

    /**
     * Updates a template.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function updateTemplate(int $template_id, array $attributes): MessageGearsTemplateData
    {
        $response = $this->accelerator->request('patch', [
            'template/%d', (string) $template_id,
        ], [
            'json' => $attributes,
        ]);

        return MessageGearsTemplateData::from(json_decode((string) $response->getBody()));
    }

    /**
     * Deletes a template.
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function deleteTemplate(int $template_id): Response
    {
        return $this->accelerator->request('delete', [
            'template/%s', (string) $template_id,
        ]);
    }

    /**
     * Get a marketing campaign.
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function getMarketingCampaign(int $campaign_id): MessageGearsMarketingCampaignData
    {
        $response = $this->accelerator->get([
            'campaign/marketing/%d', (string) $campaign_id,
        ]);

        return MessageGearsMarketingCampaignData::from(json_decode($response->getBody()));
    }

    /**
     * Update a marketing campaign.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function updateMarketingCampaign(int $campaign_id, array $attributes): MessageGearsMarketingCampaignData
    {
        $response = $this->accelerator->request('patch', [
            'campaign/marketing/%d', (string) $campaign_id,
        ], [
            'json' => $attributes,
        ]);

        return MessageGearsMarketingCampaignData::from(json_decode((string) $response->getBody()));
    }

    /**
     * Get an audience.
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function getAudience(int $audience_id): MessageGearsAudienceData
    {
        $response = $this->accelerator->get(['audience/query/%s', (string) $audience_id]);

        return MessageGearsAudienceData::from(json_decode($response->getBody()));
    }

    /**
     * Get a list of paginated folders.
     *
     * @return LengthAwarePaginator<int, MessageGearsFolderData>
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function getFolders(int $page = 1, int $limit = 50): LengthAwarePaginator
    {
        $response = $this->accelerator->get('template/folder', [
            'query' => [
                'page' => max(0, $page - 1),
                'size' => $limit,
            ],
        ]);

        $data = $this->decodePage($response->getBody()->getContents());

        $items = array_map(
            MessageGearsFolderData::from(...),
            $data['content']
        );

        return new LengthAwarePaginator(
            $items, $data['totalElements'], $data['size'], $page
        );
    }

    /**
     * Get the all folders.
     *
     * @return Collection<int, MessageGearsFolderData>
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function getAllFolders(): Collection
    {
        return Cache::remember("{$this->accelerator->apiKey}-folders", now()->addHour(), function (): Collection {
            $folders = [];

            $page = 0;

            do {
                $response = $this->accelerator->get('template/folder', [
                    'query' => [
                        'page' => $page,
                        'size' => 100,
                    ],
                ]);

                $data = $this->decodePage($response->getBody()->getContents());

                foreach ($data['content'] as $folder) {
                    $folders[] = MessageGearsFolderData::from($folder);
                }

                $page++;
            } while (! $data['last']);

            return collect($folders);
        });
    }

    /**
     * Get the folder tree.
     *
     * @return array<int, MessageGearsFolderTreeData>
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function getFolderTree(): array
    {
        return Cache::remember("{$this->accelerator->apiKey}-folder-tree", now()->addHour(), function (): array {
            $folders = $this->getAllFolders()->map(
                fn (MessageGearsFolderData $folder): MessageGearsFolderTreeData => MessageGearsFolderTreeData::from($folder)
            );

            $keyed = $folders->keyBy('id');

            foreach ($folders as $index => $folder) {
                if (! isset($folder->parentId)) {
                    continue;
                }

                $parent = $keyed->get($folder->parentId);

                if ($parent instanceof MessageGearsFolderTreeData) {
                    $parent->children[] = $folder;
                    $folders->forget($index);
                }
            }

            $root = $folders->first();

            return $root instanceof MessageGearsFolderTreeData ? $root->children : [];
        });
    }
}
