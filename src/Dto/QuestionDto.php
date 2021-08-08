<?php

namespace Q2aApi\Dto;

use Q2aApi\Helper\QuestionHelper;

class QuestionDto extends PostDto
{
    private $categoryDto;
    private $authorDto;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->categoryDto = new CategoryDto([
            'categoryid' => $this->data['categoryid'],
            'title' => $this->data['categoryname'],
            'backpath' => $this->data['categorybackpath']
        ]);
        $this->authorDto = new UserDto($this->data);
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

    public function getTags(): array
    {
        return QuestionHelper::tagsStringToArray($this->data['tags']);
    }

    public function getCategory(): CategoryDto
    {
        return $this->categoryDto;
    }

    public function getAuthor(): UserDto
    {
        return $this->authorDto;
    }

    public function getUserVote (): int 
    {
        return $this->data['uservote'] ?? 0;
    }
}
