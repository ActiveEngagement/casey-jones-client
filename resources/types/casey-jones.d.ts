declare namespace Actengage {
namespace CaseyJones {
namespace Data {
export type Instance = {
id: number,
name: string,
accelerator_base_uri: string,
accelerator_api_key: string,
accelerator_account_id: string,
cloud_api_key: string,
cloud_account_id: string,
created_at: string,
updated_at: string,
deleted_at: string | null,
};
export type MessageGearsAccount = {
id: number,
name: string,
};
export type MessageGearsAudience = {
id: number,
name: string,
approximateResultCount: number,
dataType: Actengage.CaseyJones.Enums.MessageGearsAudienceDataType,
sql: string | null,
segmentationCriteria: Actengage.CaseyJones.Data.MessageGearsSegmentationCriteria[] | null,
};
export type MessageGearsCampaignAudience = {
id: number,
name: string,
dataProviderType: Actengage.CaseyJones.Enums.MessageGearsAudienceDataProviderType,
dataType: Actengage.CaseyJones.Enums.MessageGearsAudienceDataType,
};
export type MessageGearsCampaignSchedule = {
scheduleMode: Actengage.CaseyJones.Enums.MessageGearsScheduleMode,
};
export type MessageGearsFolder = {
id: number,
path: string,
name: string,
parentId: number | null,
};
export type MessageGearsFolderTree = {
id: number,
path: string,
name: string,
parentId: number | null,
children: Actengage.CaseyJones.Data.MessageGearsFolderTree[],
};
export type MessageGearsMarketingCampaign = {
id: number,
name: string,
folder: Actengage.CaseyJones.Data.MessageGearsFolder,
template: Actengage.CaseyJones.Data.MessageGearsTemplate,
audience: Actengage.CaseyJones.Data.MessageGearsCampaignAudience,
account: Actengage.CaseyJones.Data.MessageGearsAccount,
description: string | null,
category: string | null,
notificationAddress: string | null,
urlAppend: string | null,
archived: boolean,
sendProgressUpdates: boolean,
postCampaignTrigger: Actengage.CaseyJones.Data.MessageGearsPostCampaignTrigger | null,
seedlistTestingIncluded: boolean,
seedlistTestingIdentifier: string | null,
};
export type MessageGearsMarketingCampaignJobStatus = {
id: number,
subjectLine: string,
error: boolean | null,
errorMessage: string | null,
jobStatus: Actengage.CaseyJones.Enums.MessageGearsJobStatus | null,
jobActivityStatus: Actengage.CaseyJones.Enums.MessageGearsJobActivityStatus | null,
account: Actengage.CaseyJones.Data.MessageGearsAccount | null,
category: string,
startTime: number,
notificationEmailAddress: string,
deliveryCount: number,
openCount: number,
uniqueOpenCount: number,
bounceCount: number,
clickCount: number,
uniqueClickCount: number,
contentErrorCount: number,
fblCount: number,
unsubCount: number,
suppressedCount: number,
queryName: string,
recipientSql: string,
recipientCount: number,
spamAssassinScore: number,
};
export type MessageGearsMarketingCampaignNewJob = {
id: number,
subjectLine: string,
error: boolean,
jobStatus: Actengage.CaseyJones.Enums.MessageGearsJobStatus,
};
export type MessageGearsPostCampaignTrigger = {
id: number,
name: string,
};
export type MessageGearsSampleRecipient = {
id: number,
title: string,
emailAddress: string | null,
sampleRecipientXml: string | null,
};
export type MessageGearsSegmentationCriteria = {
id: number,
name: string,
label: string,
description: string,
defaultValue: string,
};
export type MessageGearsTemplate = {
id: number,
name: string,
subject: string | null,
description: string | null,
html: string | null,
text: string | null,
fromName: string | null,
fromAddress: string | null,
replyToAddress: string | null,
folder: Actengage.CaseyJones.Data.MessageGearsFolder | null,
sampleRecipients: Actengage.CaseyJones.Data.MessageGearsSampleRecipient[],
templateLibraries: Actengage.CaseyJones.Data.MessageGearsSampleRecipient[],
locked: boolean,
};
export type MessageGearsTemplateLibrary = {
id: number,
name: string,
description: string,
content: string,
folder: Actengage.CaseyJones.Data.MessageGearsFolder,
};
export type Send = {
id: string,
app_id: number,
instance_id: number,
campaign_id: number,
name: string,
status: Actengage.CaseyJones.Enums.SendStatus | null,
subject: string | null,
html: string | null,
text: string | null,
folder: Actengage.CaseyJones.Data.MessageGearsFolder | null,
from_address: string | null,
from_name: string | null,
reply_to_address: string | null,
reply_to_name: string | null,
meta: Record<string, any>,
data_variables: Record<string, string>,
mailingid: number | null,
scheduled_at: string | null,
delivered_at: string | null,
failed_at: string | null,
created_at: string,
updated_at: string,
deleted_at: string | null,
jobs: Actengage.CaseyJones.Data.SendJob[] | null,
};
export type SendJob = {
id: number,
send_id: number,
status_code: number | null,
failed: boolean | null,
mailingid: number | null,
response: string | null,
error_message: string | null,
created_at: string,
updated_at: string,
deleted_at: string | null,
};
}
namespace Enums {
export type MessageGearsAudienceDataProviderType = 'NONE' | 'STATIC' | 'QUERY' | 'URL' | 'FILE';
export type MessageGearsAudienceDataType = 'AUDIENCE' | 'CONTEXT';
export type MessageGearsJobActivityStatus = 'RUNNING' | 'PAUSED' | 'RESUMED' | 'CANCELLED' | 'COMPLETE';
export type MessageGearsJobStatus = 'INITIALIZING' | 'SENDING_RECIPIENTS' | 'SUBMITTING' | 'PROCESSING' | 'FAILED' | 'COLLECTING' | 'HALTED' | 'COMPLETED';
export type MessageGearsScheduleMode = 'ADHOC' | 'ONETIME' | 'DAILY' | 'DAYSPERWEEK' | 'MONTHLY' | 'ADVANCED';
export type SendStatus = 'draft' | 'scheduled' | 'active' | 'delivered' | 'failed' | 'queued';
}
}
}
declare namespace Illuminate {
export type CursorPaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
path: string,
per_page: number,
next_cursor: string | null,
next_page_url: string | null,
prev_cursor: string | null,
prev_page_url: string | null,
},
};
export type CursorPaginatorInterface<TKey, TValue> = Illuminate.CursorPaginator<TKey, TValue>;
export type LengthAwarePaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
total: number,
current_page: number,
first_page_url: string,
from: number | null,
last_page: number,
last_page_url: string,
next_page_url: string | null,
path: string,
per_page: number,
prev_page_url: string | null,
to: number | null,
},
};
export type LengthAwarePaginatorInterface<TKey, TValue> = Illuminate.LengthAwarePaginator<TKey, TValue>;
}
declare namespace Spatie {
namespace LaravelData {
export type CursorPaginatedDataCollection<TKey, TValue> = Illuminate.CursorPaginator<TKey, TValue>;
export type PaginatedDataCollection<TKey, TValue> = Illuminate.LengthAwarePaginator<TKey, TValue>;
}
}
