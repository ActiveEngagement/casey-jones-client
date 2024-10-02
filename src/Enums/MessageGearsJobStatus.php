<?php

namespace Actengage\CaseyJones\Enums;

/** @typescript */
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