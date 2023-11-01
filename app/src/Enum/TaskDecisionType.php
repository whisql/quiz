<?php

namespace App\Enum;

enum TaskDecisionType: string
{
    case SINGLE_SELECT = 'SINGLE_SELECT';
    case MULTI_SELECT = 'MULTI_SELECT';
    case INPUT_TEXT = 'INPUT_TEXT';
}
