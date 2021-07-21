<?php

namespace Q2aApi\Http;

use Q2aApi\Exception\HttpException;

class Request
{
    private $url;
    private $path;
    private $body;
    private $data;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->initialize();
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
        return strtolower(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]);
    }

    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return !empty($this->data[$key]);
    }

    private function initialize()
    {
        $this->path = substr($this->url, strlen(API_URL) + 1);
        $this->body = file_get_contents('php://input');

        $this->data = $_REQUEST;
        if ($this->getContentType() === 'application/json' && !empty($this->body)) {
            $jsonData = json_decode($this->body, true);
            if ($jsonData === null) {
                throw new HttpException(qa_lang('q2a_api/response_bad_request'), Response::STATUS_BAD_REQUEST);
            }
            $this->data = array_merge($this->data, $jsonData);
        }
    }
}
