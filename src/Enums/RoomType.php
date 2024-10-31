<?php

declare(strict_types=1);

namespace App\Enums;

enum RoomType: string
{
    case CABANA = 'Cabana';
    case VILLA = 'Villa';
    case PENTHOUSE = 'Penthouse'; 
}
