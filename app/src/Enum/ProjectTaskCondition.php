<?php
declare(strict_types=1);


namespace App\Enum;


enum ProjectTaskCondition: string
{
    case PENDING = 'PENDING';
    case ACTIVE = 'ACTIVE';
    case COMPLETED = 'COMPLETED';
}