<?php

declare(strict_types=1);

namespace Q2aApi\Helper;

class QuestionHelper
{
    public static function titleToSlug(string $title): string
    {
        return explode('/', qa_q_request(0, $title))[1];
    }

    public static function tagsStringToArray(string $tags): array
    {
        return explode(',', $tags ?? '');
    }
}
