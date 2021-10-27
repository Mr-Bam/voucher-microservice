<?php

namespace app\core\Http;

class Post implements IMethod
{
    public function getParams(): array
    {
        if (!$_POST) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        return $_POST;
    }
}