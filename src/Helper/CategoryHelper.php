<?php

declare(strict_types=1);

namespace Q2aApi\Helper;

class CategoryHelper
{
    public static function pathToSlugs(string $path): array
    {
        return explode('/', $path);
    }
}
