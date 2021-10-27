<?php

namespace app\core;

use app\core\Http\Get;
use app\core\Http\Post;

class Request
{
    public Get $get;
    public Post $post;

    public function __construct()
    {
        $this->get = new Get();
        $this->post = new Post();
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        if ($path !== '/') {
            $path = trim($path, '/');
        }

        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getEndpoint()
    {
        return $this->getPath();
    }
}