<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumUtils;

enum CompanyStatusEnum: string
{
    use EnumUtils;

    case Created = 'created';
    case Updated = 'updated';
    case Duplicate = 'duplicate';
}
