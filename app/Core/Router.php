<?php
/**
 * Simple Router Class
 * Handles URL routing and dispatches to controllers
 */

class Router {
    
    private $routes = [];
    private $notFoundHandler = null;
    
    /**
     * Register a GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Register a POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Register a route for any method
     */
    public function any($path, $handler) {
        $this->addRoute('ANY', $path, $handler);
    }
    
    /**
     * Add a route to the routing table
     */
    private function addRoute($method, $path, $handler) {
        // Convert route parameters to regex pattern
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    /**
     * Set 404 handler
     */
    public function notFound($handler) {
        $this->notFoundHandler = $handler;
    }
    
    /**
     * Dispatch the request to the appropriate handler
     */
    public function dispatch($requestMethod = null, $requestUri = null) {
        $requestMethod = $requestMethod ?: $_SERVER['REQUEST_METHOD'];
        $requestUri = $requestUri ?: $this->getRequestUri();
        
        // Try to match a route
        foreach ($this->routes as $route) {
            // Check method
            if ($route['method'] !== 'ANY' && $route['method'] !== $requestMethod) {
                continue;
            }
            
            // Check path pattern
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return $this->callHandler($route['handler'], $params);
            }
        }
        
        // No route matched - 404
        if ($this->notFoundHandler) {
            return $this->callHandler($this->notFoundHandler, []);
        }
        
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    /**
     * Call the route handler
     */
    private function callHandler($handler, $params) {
        // If handler is a string like 'Controller@method'
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            
            // Include controller file
            $controllerFile = APP_PATH . '/Controllers/' . $controller . '.php';
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller not found: {$controller}");
            }
            require_once $controllerFile;
            
            // Instantiate and call method
            $instance = new $controller();
            return call_user_func_array([$instance, $method], $params);
        }
        
        // If handler is a closure
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        throw new Exception("Invalid route handler");
    }
    
    /**
     * Get the request URI without query string and base path
     */
    private function getRequestUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove base path using BASE_URL from env.php
        $basePath = defined('BASE_URL') ? BASE_URL : '/project-akhir/public';
        if (!empty($basePath) && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Ensure leading slash
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        // Remove trailing slash (except for root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        return $uri;
    }
    
    /**
     * Generate URL for a given path (uses BASE_URL from env.php)
     */
    public static function url($path) {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/project-akhir/public';
        return $baseUrl . $path;
    }
}
