<?php

namespace app\core\Http;

class Get implements IMethod
{
    public function getParams(): array
    {
        return $_GET;
    }
}