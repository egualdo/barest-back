<?php

namespace App\Enums;

enum SettingsSectionEnum: string
{
    case privacy = 'privacy';
    case cookies = 'cookies';
    case conditions = 'conditions';
}