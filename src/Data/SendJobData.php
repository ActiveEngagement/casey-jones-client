<?php

namespace Actengage\CaseyJones\Data;

use Spatie\LaravelData\Data;

/** @typescript SendJob */
class SendJobData extends Data
{
    public function __construct(
		public int $id,
		public int $send_id,
		public ?int $status_code = null,
		public ?bool $failed = null,
		public ?int $mailingid = null,
		public ?string $response = null,
		public ?string $error_message = null,
		public string $created_at,
		public string $updated_at,
		public ?string $deleted_at = null
    ) {}
}
