<?php

namespace Q2aApi\Model\Post;

class Comment extends Post
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
