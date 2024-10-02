<?php

namespace Actengage\CaseyJones\Enums;

/** @typescript */
enum MessageGearsScheduleMode: string
{
    case Adhoc = 'ADHOC';
    case OneTime = 'ONETIME';
    case Daily = 'DAILY';
    case DaysPerWeek = 'DAYSPERWEEK';
    case Monthly = 'MONTHLY';
    case Advanced = 'ADVANCED';
}