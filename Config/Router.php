<?php
namespace API\Config;

class Router
{
    private $routes = [];

    public function addRoute($method, $path, $controller, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($path, $controller, $action)
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post($path, $controller, $action)
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function put($path, $controller, $action)
    {
        $this->addRoute('PUT', $path, $controller, $action);
    }

    public function delete($path, $controller, $action)
    {
        $this->addRoute('DELETE', $path, $controller, $action);
    }

    public function route($method, $path)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['path'], $path, $matches)) {
                $controller = new $route['controller']();
                $action = $route['action'];
                array_shift($matches);

                if ($method === 'POST' && isset($_FILES['file'])) {
                    $matches[] = $_FILES['file'];
                } else {
                    header('Content-Type: application/json');
                }

                return call_user_func_array([$controller, $action], $matches);
            }
        }
        http_response_code(404);
        header('Content-Type: application/json');
        return json_encode(['error' => 'Not Found']);
    }
}