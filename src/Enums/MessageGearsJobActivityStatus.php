<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum MessageGearsJobActivityStatus: string
{
    case Running = 'RUNNING';
    case Paused = 'PAUSED';
    case Resumed = 'RESUMED';
    case Cancelled = 'CANCELLED';
    case Complete = 'COMPLETE';
}
