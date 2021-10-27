<?php

namespace app\core;

use app\controllers\ContactController;

class Router
{
    public Request $request;
    public Response $response;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function resolve()
    {
        $data = $this->getPerformer();

        $controller = $data['controller'];
        $action = $data['action'];
        $params = [];
        if (isset($data['params'])) {
            $params = $data['params'];
        }

        $controller = new $controller();
        $result = call_user_func_array([$controller, $action], $params);

        $this->render($result);
    }

    public function getPerformer(): array
    {
        $routes = $this->getRoutes();
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        if (!isset($routes[$path])) {
            return $this->getPerformerByDefault();
        }

        $route = $routes[$path];
        $routeSettings = [];
        $method = strtoupper($method);
        foreach ($route as $item) {
            if (isset($item['method']) && $item['method'] === $method) {
                $routeSettings = $item;
                break;
            }
        }

        if (!$routeSettings) {
            return $this->getPerformerByDefault();
        }

        $controller = $this->getController($routeSettings);
        $action = $method . $this->getAction($routeSettings);


        if (!$this->checkClassExists($controller)) {
            $this->throwNotFoundException();
        }

        if (!$this->checkClassMethodExists($controller, $action)) {
            $this->throwException("Action for `$path` endpoint doesn't exist");
        }

        return ['controller' => $controller, 'action' => $action];
    }

    private function getController($settings)
    {
        if (!isset($settings['controller'])) {
            $controller = $this->getDefaultController();
        } else {
            $controller = $this->getControllerNamespace() . $settings['controller'];
        }

        return $controller . 'Controller';
    }

    private function getAction($settings)
    {
        if (!isset($settings['action'])) {
            $action = $this->getDefaultAction();
        } else {
            $action = '_' . $settings['action'];
        }

        return $action . 'Action';
    }

    private function getPerformerByDefault(): array
    {
        $method = $this->request->getMethod();
        $path = $this->request->getEndpoint();
//        $params = $this->getParams();
        $controller = $this->getDefaultController() . 'Controller';
        if (!$this->checkClassExists($controller)) {
            $this->throwNotFoundException();
        }
        $action = strtoupper($method) . $this->getDefaultAction() . 'Action';
        if (!$this->checkClassMethodExists($controller, $action)) {
            $this->throwException("Action for `$path` endpoint doesn't exist");
        }

        $data = [
            'controller' => $controller,
            'action' => $action
        ];
//        if ($params) {
//            $data['params'] = $params;
//        }

        return $data;
    }

    private function getControllerNamespace(): string
    {
        return CONTROLLER_NAMESPACE . '\\';
    }

    private function getDefaultController(): string
    {
        return $this->getControllerNamespace() . ucfirst($this->request->getPath());
    }

    private function getDefaultAction(): string
    {
        return "_index";
    }

    private function checkClassExists($class): bool
    {
        return class_exists($class);
    }

    private function checkClassMethodExists($class, $method): bool
    {
        $methods = get_class_methods($class);
        return in_array($method, $methods, true);
    }

    private function getRoutes()
    {
        return require CONFIG_DIR . '/routes.php';
    }

    public function throwNotFoundException(): void
    {
        $this->response->setStatusCode(404);
        echo $this->getJson(['status' => 'ERROR', 'message' => 'Not Found']);
        exit;
    }

    public function throwException($message): void
    {
        $this->response->setStatusCode(400);
        echo $this->getJson(['status' => 'ERROR', 'message' => $message]);
        exit;
    }

    public function getJson($data)
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function prepareResult($data)
    {
        if (is_array($data) && !isset($data['status'])) {
            $result = $data;
            $result = array_merge(['status' => 'SUCCESS'], $result);
        } else {
            $result = $data;
        }

        return $this->getJson($result);
    }

    public function render($data): void
    {
        echo $this->getJson($data['data']);
        exit;
    }
}