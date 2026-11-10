<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    //case INACTIVE = 'inactive';(not use)
    case SUSPENDED = 'suspended';
}
