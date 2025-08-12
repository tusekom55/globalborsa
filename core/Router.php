<?php
/**
 * URL Router and Request Handler
 */

class Router
{
    private $routes = [];
    private $currentRoute = null;

    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertToPattern($path)
        ];
    }

    private function convertToPattern($path)
    {
        // Convert {param} to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $this->currentRoute = $route;
                array_shift($matches); // Remove full match
                $this->executeHandler($route['handler'], $matches);
                return;
            }
        }

        // No route found
        $this->handleNotFound();
    }

    private function executeHandler($handler, $params = [])
    {
        if (strpos($handler, '@') !== false) {
            list($controllerName, $method) = explode('@', $handler);
            
            // Try to find controller in modules
            $controllerFile = $this->findController($controllerName);
            
            if ($controllerFile && file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $method)) {
                        call_user_func_array([$controller, $method], $params);
                        return;
                    }
                }
            }
        }

        // Handler not found
        $this->handleNotFound();
    }

    private function findController($controllerName)
    {
        // Extract module name from controller name
        $moduleName = strtolower(str_replace('Controller', '', $controllerName));
        
        $possiblePaths = [
            MODULES_PATH . '/' . $moduleName . '/' . $controllerName . '.php',
            CORE_PATH . '/' . $controllerName . '.php'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function handleNotFound()
    {
        http_response_code(404);
        
        // Try to load 404 controller
        $errorController = $this->findController('ErrorController');
        if ($errorController && file_exists($errorController)) {
            require_once $errorController;
            $controller = new ErrorController();
            $controller->notFound();
        } else {
            // Fallback 404 page
            echo $this->getDefault404Page();
        }
    }

    private function getDefault404Page()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>404 - Page Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #e74c3c; }
            </style>
        </head>
        <body>
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for could not be found.</p>
            <a href="/">Go Home</a>
        </body>
        </html>';
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    public function url($path, $params = [])
    {
        $url = SITE_URL . $path;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }

    public function redirect($path, $statusCode = 302)
    {
        $url = $this->url($path);
        header("Location: $url", true, $statusCode);
        exit;
    }

    public function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: $referer");
        exit;
    }
}
