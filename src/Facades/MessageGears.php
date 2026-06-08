<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Actengage\CaseyJones\Services\MessageGears
 *
 * @method static \Actengage\CaseyJones\Services\MessageGears cloud(\Actengage\MessageGears\Cloud $instance)
 * @method static \Actengage\CaseyJones\Services\MessageGears accelerator(\Actengage\MessageGears\Accelerator $instance)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignNewJobData createMarketingCampaignJob(int $campaign_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData getMarketingCampaignJobStatus(int $campaign_id, int $job_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignJobStatusData checkMarketingCampaignJobStatus(int $campaign_id, int $job_id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator<int, \Actengage\CaseyJones\Data\MessageGearsTemplateData> getTemplates(int $page = 1, int $limit = 50)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData getTemplate(int $template_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData createTemplate(array<string, mixed> $attributes)
 * @method static \Actengage\CaseyJones\Data\MessageGearsTemplateData updateTemplate(int $template_id, array<string, mixed> $attributes)
 * @method static \GuzzleHttp\Psr7\Response deleteTemplate(int $template_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData getMarketingCampaign(int $campaign_id)
 * @method static \Actengage\CaseyJones\Data\MessageGearsMarketingCampaignData updateMarketingCampaign(int $campaign_id, array<string, mixed> $attributes)
 * @method static \Actengage\CaseyJones\Data\MessageGearsAudienceData getAudience(int $audience_id)
 * @method static \Illuminate\Pagination\LengthAwarePaginator<int, \Actengage\CaseyJones\Data\MessageGearsFolderData> getFolders(int $page = 1, int $limit = 50)
 * @method static \Illuminate\Support\Collection<int, \Actengage\CaseyJones\Data\MessageGearsFolderData> getAllFolders()
 * @method static array<int, \Actengage\CaseyJones\Data\MessageGearsFolderTreeData> getFolderTree()
 */
class MessageGears extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'casey.mg';
    }
}
