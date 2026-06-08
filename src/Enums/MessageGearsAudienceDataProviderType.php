<?php

declare(strict_types=1);

namespace Actengage\CaseyJones\Enums;

enum MessageGearsAudienceDataProviderType: string
{
    case None = 'NONE';
    case Static = 'STATIC';
    case Query = 'QUERY';
    case Url = 'URL';
    case File = 'FILE';
}
