<?php

namespace Actengage\CaseyJones\Enums;

/** @typescript */
enum SendStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Queued = 'queued';
}
