<?php

namespace App\Enums;

enum BrowserType: string
{
    case CHROME = 'CHROME';
    case SAFARI = 'SAFARI';
    case FIREFOX = 'FIREFOX';
    case EDGE = 'EDGE';
    case OPERA = 'OPERA';
    case OTHER = 'OTHER';
}