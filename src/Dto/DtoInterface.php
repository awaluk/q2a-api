<?php

namespace Q2aApi\Dto;

interface DtoInterface
{
    public function hasOriginal(string $key);

    public function getOriginal(string $key);

    public function getOriginals(): array;
}
