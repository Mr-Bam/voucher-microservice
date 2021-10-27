<?php

namespace app\core;

use app\lib\Database\Database;

class Application
{
    public Auth $auth;
    public Database $db;
    public Response $response;
    public Router $router;
    public Request $request;
    public static Application $app;

    public function __construct()
    {
        self::$app = $this;
//        $this->auth = new Auth();
        $this->db = Database::getInstance();
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);
    }

    public function run()
    {
        $this->router->resolve();
    }
}