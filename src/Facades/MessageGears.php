<?php

namespace Actengage\CaseyJones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Actengage\CaseyJones\Services\MessageGears
 * @method static \Actengage\CaseyJones\Services\MessageGears cloud(\Actengage\MessageGears\Cloud $instance)
 * @method static \Actengage\CaseyJones\Services\MessageGears accelerator(\Actengage\MessageGears\Accelerator $instance)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData getMarketingCampaignJobStatus(int $campaign_id, int $job_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData checkMarketingCampaignJobStatus(int $campaign_id, int $job_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData getTemplate(int $template_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData createTemplate(array $attributes)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData updateTemplate(int $template_id, array $attributes)
 * @method static \GuzzleHttp\Psr7\Response deleteTemplate(int $template_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData getMarketingCampaign(int $campaign_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData updateMarketingCampaign(int $campaign_id, array $attributes)
 */
class MessageGears extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'casey.mg';
    }
}