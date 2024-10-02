<?php

namespace Actengage\CaseyJones\Data;

use Actengage\CaseyJones\Contracts\Mockable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/** @typescript */
class MessageGearsTemplateData extends Data implements Mockable
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $subject,
        public ?string $description,
        public ?string $html,
        public ?string $text,
        public ?string $fromName,
        public ?string $fromAddress,
        public ?string $replyToAddress,
        public MessageGearsFolderData $folder,
        #[DataCollectionOf(MessageGearsSampleRecipientData::class)]
        public ?array $sampleRecipients,
        #[DataCollectionOf(MessageGearsTemplateLibraryData::class)]
        public ?array $templateLibraries,
        public bool $locked,
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
            'name' => 'Test Template',
            'subject' => 'Some Subject',
            'description' => 'Some Description',
            'html' => '<div>test</div>',
            'text' => 'text',
            'fromName' => 'Test',
            'fromAddress' => 'test@test.com',
            'replyToAddress' => 'test@test.com',
            'folder' => MessageGearsFolderData::mock(),
            'sampleRecipients' => [MessageGearsSampleRecipientData::mock()],
            'templateLibraries' => [MessageGearsTemplateLibraryData::mock()],
            'locked' => false,
        ], $attributes));
    }
}
