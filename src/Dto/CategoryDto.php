<?php

namespace Q2aApi\Dto;

use Q2aApi\Helper\CategoryHelper;

class CategoryDto implements DtoInterface
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function hasOriginal(string $key)
    {
        return array_key_exists($key, $this->data);
    }

    public function getOriginal(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function getOriginals(): array
    {
        return $this->data;
    }

    public function getId(): int
    {
        return (int)$this->data['categoryid'];
    }

    public function getName(): string
    {
        return $this->data['title'];
    }

    public function getSlug(): string
    {
        return $this->data['tags'];
    }

    public function getPath(): string
    {
        return CategoryHelper::changeBackPathToPath($this->data['backpath']);
    }

    public function getDescription(): string
    {
        return $this->data['content'];
    }

    public function getPosition(): int
    {
        return (int)$this->data['position'];
    }

    public function getQuestionsCount(): int
    {
        return (int)$this->data['qcount'];
    }
}
