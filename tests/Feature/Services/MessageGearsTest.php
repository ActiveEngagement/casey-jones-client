<?php

use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData;
use Actengage\CaseyJones\Data\MessageGearsMarketingCampaignNewJobData;
use Actengage\CaseyJones\Data\MessageGearsTemplateData;
use Actengage\CaseyJones\Enums\MessageGearsJobStatus;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasDeliveries;
use Actengage\CaseyJones\Exceptions\JobAlreadyHasOpens;
use Actengage\CaseyJones\Exceptions\JobHasAlreadyBeenCompleted;
use Actengage\CaseyJones\Exceptions\JobIsCurrentlyBeingCollected;
use Actengage\CaseyJones\Facades\MessageGears;
use Actengage\MessageGears\Facades\Accelerator;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

describe('MessageGears job status requests', function() {
    it('will get the job status.', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'id' => 1
                ])->toJson())
            ])
        );

        $response = $service->getMarketingCampaignJobStatus(1, 1);

        expect($response)->toBeInstanceOf(MessageGearsMarketingCampaignJobStatusData::class);
        expect($response->id)->toBe(1);
        expect($response->jobStatus)->toBe(MessageGearsJobStatus::Completed);
    });

    it('will create a marketing campaign job', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], json_encode([
                    'id' => 1,
                    'subjectLine' => 'Test Subject',
                    'error' => false,
                    'jobStatus' => MessageGearsJobStatus::Initializing
                ]))
            ])
        );

        $response = $service->createMarketingCampaignJob(1);

        expect($response)->toBeInstanceOf(MessageGearsMarketingCampaignNewJobData::class);
    });

    it('will throws an exception if checking for a job that doesn\'t exist.', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new ServerException(
                    'Server error: `GET https://ae.listelixr.net/beta/campaign/marketing/1/job/1` resulted in a `500 Internal Server Error` response: "Job not found"',
                    new Request('GET', 'https://ae.listelixr.net/beta/campaign/marketing/1/job/1'),
                    new Response(500, [], 'Server error: `GET https://ae.listelixr.net/beta/campaign/marketing/1/job/1` resulted in a `500 Internal Server Error` response: "Job not found"')
                )
            ])
        );
        
        $service->getMarketingCampaignJobStatus(1, 1);
    })->throws(ServerException::class);

    it('will throw an exception if the status is "COMPLETED".', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'jobStatus' => MessageGearsJobStatus::Completed
                ])->toJson())
            ])
        );

        $service->checkMarketingCampaignJobStatus(1, 1);
    })->throws(JobHasAlreadyBeenCompleted::class);

    it('will throw an exception if the status is "COLLECTING".', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'jobStatus' => MessageGearsJobStatus::Collecting
                ])->toJson())
            ])
        );

        $service->checkMarketingCampaignJobStatus(1, 1);
    })->throws(JobIsCurrentlyBeingCollected::class);

    it('will throw an exception if it has deliveries".', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'jobStatus' => MessageGearsJobStatus::Initializing,
                    'deliveryCount' => 1
                ])->toJson())
            ])
        );

        $service->checkMarketingCampaignJobStatus(1, 1);
    })->throws(JobAlreadyHasDeliveries::class);

    it('will throw an exception if it has opens".', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'jobStatus' => MessageGearsJobStatus::Initializing,
                    'deliveryCount' => 0,
                    'openCount' => 1
                ])->toJson())
            ])
        );

        $service->checkMarketingCampaignJobStatus(1, 1);
    })->throws(JobAlreadyHasOpens::class);

    it('will pass the status check', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignJobStatusData::mock([
                    'jobStatus' => MessageGearsJobStatus::Initializing,
                    'deliveryCount' => 0,
                    'openCount' => 0
                ])->toJson())
            ])
        );

        $service->checkMarketingCampaignJobStatus(1, 1);
    })->throwsNoExceptions();
});

describe('MessageGears template requests', function() {
    it('will get a template', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsTemplateData::mock()->toJson())
            ])
        );

        $response = $service->getTemplate(1);

        expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
    });

    it('will create a template', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsTemplateData::mock()->toJson())
            ])
        );

        $response = $service->createTemplate([
            'name' => 'Test Template'
        ]);

        expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
    });

    it('will update a template', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsTemplateData::mock()->toJson())
            ])
        );

        $response = $service->updateTemplate(1, [
            'name' => 'Test Template'
        ]);

        expect($response)->toBeInstanceOf(MessageGearsTemplateData::class);
    });

    it('will delete a template', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsTemplateData::mock()->toJson())
            ])
        );

        $response = $service->deleteTemplate(1);

        expect($response)->toBeInstanceOf(Response::class);
    });
});

describe('MessageGears campaign requests', function() {
    it('will get a marketing campaign', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignData::mock()->toJson())
            ])
        );

        $response = $service->getMarketingCampaign(1);

        expect($response)->toBeInstanceOf(MessageGearsMarketingCampaignData::class);
    });

    it('will update a marketing campaign', function() {
        $service = MessageGears::accelerator(
            Accelerator::mock([
                new Response(200, [], MessageGearsMarketingCampaignData::mock()->toJson())
            ])
        );

        $response = $service->updateMarketingCampaign(1, [
            'name' => 'test'
        ]);

        expect($response)->toBeInstanceOf(MessageGearsMarketingCampaignData::class);
    });
});