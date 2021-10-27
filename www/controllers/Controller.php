<?php

namespace app\controllers;

use app\core\Application;
use app\core\Request;

class Controller
{
    public Request $request;

    public function __construct()
    {
        $this->request = Application::$app->request;
    }
}