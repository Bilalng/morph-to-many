<?php

namespace App\Enums;

enum PostStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case WAITING = 'waiting';

    public function description(): string {
        return match ($this) {
            self::ACTIVE => 'Video şuan yayında',
            self::INACTIVE => 'Video yayından kaldırıldı',
            self::WAITING => 'Video Beklemede',
        };
    }
}
