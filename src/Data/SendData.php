<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Enums\SendStatus;
use Spatie\LaravelData\Data;

/** @typescript */
class SendData extends Data
{
    public function __construct(
		public int $id,
		public int $app_id,
		public int $instance_id,
		public int $campaign_id,
		public string $name,
		public SendStatus $status,
		public string $subject,
		public string $html,
		public string $text,
		public MessageGearsFolderData $folder,
		public string $from_address,
		public string $from_name,
		public string $reply_to_address,
		public string $reply_to_name,
		public int $mailingid,
		public ?string $scheduled_at = null,
		public ?string $delivered_at = null,
		public ?string $failed_at = null,
		public string $created_at,
		public string $updated_at,
		public ?string $deleted_at = null,
		/** @var array<SendJobData> */
		public ?array $jobs
    ) {}
}
