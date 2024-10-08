<?php

namespace Actengage\CaseyJones\Services;

use Actengage\CaseyJones\Data\MessageGearsAudienceData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignNewJobData;
use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasDeliveries;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasOpens;
use Actengage\CaseyJones\Exceptions\JobHasAlreadyBeenCompleted;
use Actengage\CaseyJones\Exceptions\JobIsCurrentlyBeingCollected;
use Actengage\MessageGears\Accelerator;
use Actengage\MessageGears\Cloud;
use GuzzleHttp\Psr7\Response;

class MessageGears
{
    /**
     * The current accelerator instance.
     *
     * @var \Actengage\MessageGears\Cloud
     */
    public Cloud $cloud;

    /**
     * The current accelerator instance.
     *
     * @var \Actengage\MessageGears\Accelerator
     */
    public Accelerator $accelerator;

    /**
     * Set the Cloud instance.
     *
     * @param \Actengage\MessageGears\Cloud $instance
     * @return static
     */
    public function cloud(Cloud $instance): static
    {
        $this->cloud = $instance;

        return $this;
    }

    /**
     * Set the Accelerator instance.
     *
     * @param \Actengage\MessageGears\Accelerator $accelerator
     * @return static
     */
    public function accelerator(Accelerator $instance): static
    {
        $this->accelerator = $instance;

        return $this;
    }

    /**
     * Create the marketing campaign job.
     *
     * @param integer $campaign_id
     * @return \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function createMarketingCampaignJob(int $campaign_id): MessageGearsMarketingCampaignNewJobData
    {
        $response = $this->accelerator->post([
            'campaign/marketing/%d/job', $campaign_id
        ]);

        return MessageGearsMarketingCampaignNewJobData::from(json_decode($response->getBody()));
    }

    /**
     * Get the job status.
     *
     * @param integer $campaign_id
     * @param integer $mailingid
     * @return MessageGearsMarketingCampaignJobStatusData
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function getMarketingCampaignJobStatus(int $campaign_id, int $job_id): MessageGearsMarketingCampaignJobStatusData
    {
        $response = $this->accelerator->get([
            'campaign/marketing/%d/job/%d', $campaign_id, $job_id
        ]);

        return MessageGearsMarketingCampaignJobStatusData::from(json_decode($response->getBody()));
    }

    /**
     * Check the job status for potential conflicts and throws exceptions.
     *
     * @param integer $campaign_id
     * @param integer $mailingid
     * @return MessageGearsMarketingCampaignJobStatusData
     * @throws \GuzzleHttp\Exception\ServerException
     * @throws \Actengage\CaseyJones\Exceptions\JobAlreadyHasDeliveries
     * @throws \Actengage\CaseyJones\Exceptions\JobAlreadyHasOpens
     * @throws \Actengage\CaseyJones\Exceptions\JobHasAlreadyBeenCompleted
     * @throws \Actengage\CaseyJones\Exceptions\JobIsCurrentlyBeingCollected
     */
    public function checkMarketingCampaignJobStatus(int $campaign_id, int $job_id): MessageGearsMarketingCampaignJobStatusData
    {
        $response = $this->getMarketingCampaignJobStatus($campaign_id, $job_id);

        if($response->jobStatus === MessageGearsJobStatus::Completed) {
            throw new JobHasAlreadyBeenCompleted(
                sprintf('The job (%s) has already been completed.', $job_id)
            );
        }

        if($response->jobStatus === MessageGearsJobStatus::Collecting) {
            throw new JobIsCurrentlyBeingCollected(
                sprintf('The job (%s) is currently being collected.', $job_id)
            );
        }

        if($response->deliveryCount) {
            throw new JobAlreadyHasDeliveries(
                sprintf('The job (%s) already has %s deliveries.', $job_id, $response->deliveryCount)
            );
        }

        if($response->openCount) {
            throw new JobAlreadyHasOpens(
                sprintf('The job (%s) already has %s opens.', $job_id, $response->openCount)
            );
        }

        return $response;
    }

    /**
     * Get a template.
     *
     * @param integer $template_id
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function getTemplate(int $template_id): MessageGearsTemplateData
    {
        $response = $this->accelerator->get([
            'template/%s', $template_id
        ]);

        return MessageGearsTemplateData::from(json_decode($response->getBody()));
    }

    /**
     * Creates a template.
     *
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function createTemplate(array $attributes): MessageGearsTemplateData
    {
        $response = $this->accelerator->post('template/', [
            'json' => $attributes
        ]);

        return MessageGearsTemplateData::from(json_decode($response->getBody()));
    }

    /**
     * Updates a template.
     *
     * @param int $template_id
     * @param array $attributes
     * @return \Actengage\CaseyJones\Data\MessageGearsTemplateData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function updateTemplate(int $template_id, array $attributes): MessageGearsTemplateData
    {
        $response = $this->accelerator->patch([
            'template/%d', $template_id
        ], [
            'json' => $attributes
        ]);

        return MessageGearsTemplateData::from(json_decode($response->getBody()));
    }

    /**
     * Deletes a template.
     *
     * @param int $template_id
     * @return \GuzzleHttp\Psr7\Response
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function deleteTemplate(int $template_id): Response
    {
        return $this->accelerator->delete([
            'template/%s', $template_id
        ]);
    }

    /**
     * Get a marketing campaign.
     *
     * @param integer $campaign_id
     * @return MessageGearsMarketingCampaignData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function getMarketingCampaign(int $campaign_id): MessageGearsMarketingCampaignData
    {
        $response = $this->accelerator->get([
            'campaign/marketing/%d', $campaign_id
        ]);

        return MessageGearsMarketingCampaignData::from(json_decode($response->getBody()));
    }

    /**
     * Update a marketing campaign.
     *
     * @param integer $campaign_id
     * @param array $attributes
     * @return MessageGearsCampaignData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function updateMarketingCampaign(int $campaign_id, array $attributes): MessageGearsMarketingCampaignData
    {
        $response = $this->accelerator->patch([
            'campaign/marketing/%d', $campaign_id
        ], [
            'json' => $attributes
        ]);

        return MessageGearsMarketingCampaignData::from(json_decode($response->getBody()));
    }

    /**
     * Get an audience.
     *
     * @param integer $audience_id
     * @return MessageGearsAudienceData
     * @throws \GuzzleHttp\Exception\ClientException
     * @throws \GuzzleHttp\Exception\ServerException
     */
    public function getAudience(int $audience_id): MessageGearsAudienceData
    {
        $response = $this->accelerator->get(['audience/query/%s', $audience_id]);

        return MessageGearsAudienceData::from(json_decode($response->getBody()));
    }
}