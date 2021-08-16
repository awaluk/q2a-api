<?php

declare(strict_types=1);

namespace Q2aApi\Service;

use Q2aApi\Dto\PostDto;
use Q2aApi\Dto\QuestionDto;

class PostService
{
    public function getLatestActionType(PostDto $post): string
    {
        $actions = [
            'C_Y' => 'answer_changed_to_comment',
            'C_M' => 'comment_moved',
            'A_S' => 'answer_selected',
            'Q_A' => 'question_category_updated',
            'Q_T' => 'question_tags_updated',
            'C_E' => 'comment_updated',
            'A_E' => 'answer_updated',
            'Q_E' => 'question_updated',
            'C' => 'comment_created',
            'A' => 'answer_created',
            'Q' => 'question_created',
        ];

        $type = $post->getOriginal('obasetype') ?? $post->getType();
        $updateType = $post->getOriginal('oupdatetype') ?? $post->getOriginal('updatetype');

        if (!empty($updateType)) {
            $type .= '_' . $updateType;
        }

        if ($type === 'Q_C' && $post instanceof QuestionDto) {
            return $post->isClosed() ? 'question_closed' : 'question_reopened';
        }

        if (in_array($type, ['Q_H', 'A_H', 'C_H'])) {
            $suffix = $post->isHidden() ? '_hidden' : '_restored';
            return ($type === 'Q_H' ? 'question' : ($type === 'A_H' ? 'answer' : 'comment')) . $suffix;
        }

        return $actions[$type];
    }
}
