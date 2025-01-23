<?php

namespace App\Utils\Enums;

use App\Utils\Enums\EnumAction;

enum TermOutType: string
{
    use EnumAction;

    case INFO = 'info';
    case WARN = 'warn';
    case ERR = 'err';
}
