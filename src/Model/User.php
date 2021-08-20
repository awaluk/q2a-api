<?php

namespace Q2aApi\Model;

class User implements ModelInterface
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

    public function getPointsTitle(): ?string
    {
        return qa_get_points_title_html(
            $this->getPoints(),
            qa_get_points_to_titles()
        );
    }

    public function getLevel(): int
    {
        return (int)$this->data['level'];
    }
}
