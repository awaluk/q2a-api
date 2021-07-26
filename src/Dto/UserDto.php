<?php

namespace Q2aApi\Dto;

class UserDto implements DtoInterface
{
    protected $data;
    protected $q2aOptions;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->q2aOptions = qa_post_html_options($this->data);
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
        return (int)$this->data['userid'];
    }

    public function getName(): string
    {
        return $this->data['handle'];
    }

    public function getPoints(): int
    {
        return (int)$this->data['points'];
    }

    public function getPointsTitle()
    {
        return qa_get_points_title_html($this->data['points'], $this->q2aOptions['pointstitle']);
    }

    public function getLevel(): int
    {
        return (int)$this->data['level'];
    }
}
