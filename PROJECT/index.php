<?php
// Front Controller - Handles all requests

// Basic routing
$url = $_GET['url'] ?? 'user/index';
$urlParts = explode('/', $url);

$controllerName = ucfirst($urlParts[0] ?? 'User') . 'Controller';

$action = $urlParts[1] ?? 'index';
$id = $urlParts[2] ?? null;

// Load controller
$controllerFile = "app/controllers/UserController.php";
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $controller = new $controllerName();
    
    if (method_exists($controller, $action)) {
        $controller->$action($id);
    } else {
        http_response_code(404);
        echo "Action not found";
    }
} else {
    http_response_code(404);
    echo "Controller not found";
}
?>