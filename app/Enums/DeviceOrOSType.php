<?php

namespace App\Enums;

enum DeviceOrOSType: string
{
    case ANDROID = 'ANDROID';
    case IOS = 'IOS';
    case WINDOWS = 'WINDOWS';
    case MACOS = 'MACOS';
    case LINUX = 'LINUX';
    case OTHER = 'OTHER';
}