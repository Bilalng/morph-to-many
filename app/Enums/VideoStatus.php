<?php

namespace App\Enums;

enum VideoStatus : string
{
    case WAITING = 'waiting';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function description(): string
    {
        return match($this) {
            self::WAITING => 'Waiting for approval',
            self::ACTIVE => 'Active and visible to users',
            self::INACTIVE => 'Inactive and hidden from users',
        };
    }
}
