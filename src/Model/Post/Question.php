<?php

namespace Q2aApi\Model\Post;

use Q2aApi\Helper\QuestionHelper;
use Q2aApi\Model\Category;

class Question extends Post
{
    private $categoryDto;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->categoryDto = new Category([
            'categoryid' => $this->data['categoryid'],
            'title' => $this->data['categoryname'],
            'backpath' => $this->data['categorybackpath']
        ]);
    }

    public function getTitle(): string
    {
        return $this->data['title'];
    }

    public function getSlug(): string
    {
        return QuestionHelper::titleToSlug($this->data['title']);
    }

    public function getAnswersCount(): int
    {
        return (int)$this->data['acount'];
    }

    public function getViewsNumber(): int
    {
        return (int)$this->data['views'];
    }

    public function isFavouriteForLoggedUser(): bool
    {
        return isset($this->data['userfavoriteq']) && $this->data['userfavoriteq'] === '1';
    }

    public function isClosed(): bool
    {
        return $this->data['closedbyid'] !== null;
    }

    public function hasBestAnswer(): bool
    {
        return $this->data['selchildid'] !== null;
    }

    public function getBestAnswerId(): ?int
    {
        return $this->data['selchildid'];
    }

    public function getTags(): array
    {
        return QuestionHelper::tagsStringToArray($this->data['tags']);
    }

    public function getCategory(): Category
    {
        return $this->categoryDto;
    }
}
