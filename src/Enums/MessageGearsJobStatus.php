<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum MessageGearsJobStatus: string
{
    case Initializing = 'INITIALIZING';
    case SendingRecipients = 'SENDING_RECIPIENTS';
    case Submitting = 'SUBMITTING';
    case Processing = 'PROCESSING';
    case Failed = 'FAILED';
    case Collecting = 'COLLECTING';
    case Halted = 'HALTED';
    case Completed = 'COMPLETED';
}
