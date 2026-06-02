<?php

class Router {
    private array $routes = [];

    public function get(string $path, string $handler): void {
        $this->routes['GET'][] = [$path, $handler];
    }

    public function post(string $path, string $handler): void {
        $this->routes['POST'][] = [$path, $handler];
    }

    public function dispatch(?string $uri = null): void {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($uri === null) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = rtrim($uri, '/') ?: '/';
            if (defined('BASE_URL') && BASE_URL !== '' && str_starts_with($uri, BASE_URL)) {
                $uri = substr($uri, strlen(BASE_URL)) ?: '/';
            }
        }

        if (!isset($this->routes[$method])) {
            http_response_code(405);
            echo '405 Method Not Allowed';
            return;
        }

        foreach ($this->routes[$method] as [$pattern, $handler]) {
            $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                [$controllerName, $action] = explode('@', $handler);

                $file = __DIR__ . '/../controllers/' . $controllerName . '.php';
                if (!file_exists($file)) {
                    http_response_code(500);
                    echo "Controller $controllerName not found";
                    return;
                }

                require_once $file;
                $instance = new $controllerName();
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array([$instance, $action], $params);
                return;
            }
        }

        http_response_code(404);
        echo '<div class="container mt-5"><div class="alert alert-warning">404 — Página no encontrada</div></div>';
    }
}
