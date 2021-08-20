<?php

namespace Q2aApi\Model;

interface ModelInterface
{
    public function hasOriginal(string $key);

    public function getOriginal(string $key);

    public function getOriginals(): array;
}
