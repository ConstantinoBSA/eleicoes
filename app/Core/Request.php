<?php

namespace App\Core;

class Request
{
    protected $query;
    protected $request;
    protected $server;
    protected $headers;
    protected $body;

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->server = $_SERVER;
        $this->headers = $this->getAllHeaders();
        $this->body = file_get_contents('php://input');
    }

    public static function capture()
    {
        return new static();
    }

    public function get($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function post($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function server($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function all()
    {
        return array_merge($this->query, $this->request);
    }

    protected function getAllHeaders()
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[strtolower($headerName)] = $value;
            }
        }
        return $headers;
    }

    public function body()
    {
        return $this->body;
    }
}
