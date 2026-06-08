<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum MessageGearsScheduleMode: string
{
    case Adhoc = 'ADHOC';
    case OneTime = 'ONETIME';
    case Daily = 'DAILY';
    case DaysPerWeek = 'DAYSPERWEEK';
    case Monthly = 'MONTHLY';
    case Advanced = 'ADVANCED';
}
