<?php
declare(strict_types=1);

namespace App\Enum;

enum ProjectCondition: string
{
    case ACTIVE = 'ACTIVE';
    case COMPLETED = 'COMPLETED';
}