<?php

session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require BASE_PATH . '/config/routes.php';

use App\Core\Controller;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

if (isset($routes[$path])) {
    [$controllerName, $method] = $routes[$path];
    $controllerClass = "App\\Controllers\\{$controllerName}";
    if (class_exists($controllerClass)) {
        /** @var Controller $controller */
        $controller = new $controllerClass();
        if (method_exists($controller, $method)) {
            call_user_func([$controller, $method]);
            exit;
        }
    }
}

http_response_code(404);
echo 'Página não encontrada.';
