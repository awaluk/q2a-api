<?php

namespace Q2aApi\Dto;

use Q2aApi\Helper\PostHelper;

class PostDto implements DtoInterface
{
    const TYPE_QUESTION = 'Q';
    const TYPE_ANSWER = 'A';
    const TYPE_COMMENT = 'C';

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
        return (int)$this->data['postid'];
    }

    public function getType(): string
    {
        return $this->data['basetype'];
    }

    public function getVotesSum(): int
    {
        return (int)$this->data['netvotes'];
    }

    public function isQueued(): bool
    {
        return PostHelper::isQueued($this->getType());
    }

    public function isHidden(): bool
    {
        return $this->data['hidden'] === '1';
    }

    public function getContent(): string
    {
        return $this->data['content'];
    }

    public function hasHtmlContent(): bool
    {
        return $this->data['format'] === 'html';
    }

    public function getCreatedDate(): string
    {
        return date('c', $this->data['created']);
    }
}
