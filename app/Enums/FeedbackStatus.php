<?php

namespace App\Enums;

enum FeedbackStatus: string
{
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case IN_PROGRESS = 'IN_PROGRESS';
    case IMPLEMENTED = 'IMPLEMENTED';
    case CLOSED = 'CLOSED';
}