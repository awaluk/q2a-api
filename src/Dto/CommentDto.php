<?php

namespace Q2aApi\Dto;

class CommentDto extends PostDto
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function getParentId()
    {
      return (int)$this->data['parentid'];
    }
}
