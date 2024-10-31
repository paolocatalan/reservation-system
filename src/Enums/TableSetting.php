<?php

declare(strict_types=1);

namespace App\Enums;

enum TableSetting: string
{
    case Informal = 'Informal Table Setting';
    case Formal = 'Formal Table Setting';
    case FiveCourse = 'Five Course Table Settings';
}
