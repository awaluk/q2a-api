<?php

declare(strict_types=1);

namespace Q2aApi\Helper;

class CategoryHelper
{
    public static function pathToSlugs(string $path): array
    {
        return explode('/', $path);
    }

    public static function changeBackPathToPath(string $backPath): string
    {
        return implode('/', array_reverse(explode('/', $backPath)));
    }
}
