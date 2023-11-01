<?php
declare(strict_types=1);


namespace App\Enum;


enum ProjectTaskOrderStrategy: string
{
    case DIRECT = 'DIRECT';
    case SHUFFLE = 'SHUFFLE';
    case PRIORITY = 'PRIORITY';
}