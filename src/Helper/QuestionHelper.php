<?php

declare(strict_types=1);

namespace Q2aApi\Helper;

class QuestionHelper
{
    public static function titleToSlug(string $title): string
    {
        $slug = str_replace(
            ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'],
            ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'],
            $title
        );
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    public static function tagsStringToArray(string $tags): array
    {
        return explode(',', $tags ?? '');
    }
}
