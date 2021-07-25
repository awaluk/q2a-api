<?php

declare(strict_types=1);

namespace Q2aApi\Helper;

class PostHelper
{
    public static function isQueued(string $type): bool
    {
        return substr($type, 1) === '_QUEUED';
    }
}
