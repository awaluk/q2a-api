<?php

namespace Q2aApi\Http;

use Q2aApi\Exceptions\HttpException;

class Request
{
    private $url;
    private $path;
    private $body;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->path = substr($url, strlen(API_URL) + 1);
        $this->body = file_get_contents('php://input');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getContentType(): string
    {
        return strtolower($_SERVER['CONTENT_TYPE'] ?? '');
    }

    public function get(string $key)
    {
        if ($this->getContentType() === 'application/json') {
            $data = json_decode($this->body, true);
            if ($data === null) {
                throw new HttpException(qa_lang('q2a_api/response_bad_request'), Response::STATUS_BAD_REQUEST);
            }
            if (isset($data[$key])) {
                return $data[$key];
            }
        }

        return $_REQUEST[$key] ?? null;
    }
}
