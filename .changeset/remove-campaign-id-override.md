---
"casey-jones": major
---

Remove the non-production `campaign_id` override from `Send`. The model now returns and stores its own `campaign_id` in every environment, and no longer reads `services.mg.campaign_id`. Apps that relied on the override to force a test campaign outside production must make sure their non-production data uses test campaigns.

`MessageGearsMarketingCampaignJobStatusData::mock()` no longer reads `services.mg.accelerator_account_id`. Its account ID is `1`. Pass `account` to `mock()` to set a different one.
