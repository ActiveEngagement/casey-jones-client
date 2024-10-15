<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Actengage\CaseyJones\Enums\SendStatus;
use Spatie\LaravelData\Data;

/** @typescript Send */
class SendData extends Data implements Mockable
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
		/** @var Record<string,any> */
		public array $meta = [],
		/** @var Record<string,string> */
		public array $data_variables = [],
		public ?int $mailingid = null,
		public ?string $scheduled_at = null,
		public ?string $delivered_at = null,
		public ?string $failed_at = null,
		public string $created_at,
		public string $updated_at,
		public ?string $deleted_at = null,
		/** @var array<SendJobData> */
		public ?array $jobs = null
    ) {}

    /**
     * Mock an instance of the class
     *
     * @param array $attributes
     * @return static
     */
    public static function mock(array $attributes = []): static
    {
        return static::from(array_merge([
            'id' => 1,
			'app_id' => 1,
			'instance_id' => 1,
			'campaign_id' => 1,
			'template_id' => 1,
            'name' => 'Test Send',
            'status' => SendStatus::Draft,
            'subject' => 'Some Test Subject',
            'html' => '<div>test</div>',
			'text' => 'test',
			'folder' => MessageGearsFolderData::mock(),
            'from_address' => 'test@test.com',
			'from_name' => 'Test',
            'reply_to_address' => 'test@test.com',
			'reply_to_name' => 'Test',
			'data_variables' => [],
			'meta' => [],
			'mailingid' => null,
			'scheduled_at' => null,
			'failed_at' => null,
			'delivered_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ], $attributes));
    }
}