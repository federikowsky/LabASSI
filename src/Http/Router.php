<?php

namespace App\HTTP;

use App\Core\ServiceContainer;

use App\Exceptions\HTTP\ {
    NotFoundException
};

class Router
{
    protected $routes = [];
    protected $container;
    protected $cacheFile = __DIR__ . '/../../storage/cache/routes_cache.php';

    public function __construct(ServiceContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Load the routes from the configuration files
     * @param array $routeFiles
     */
    public function load_routes(array $routeFiles): void
    {
        $cacheFileMtime = file_exists($this->cacheFile) ? filemtime($this->cacheFile) : 0;
        $shouldUpdateCache = false;

        // Check if any route configuration file has been modified after the cache
        foreach ($routeFiles as $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("Route file '$file' not found.");
            }
            if (filemtime($file) > $cacheFileMtime) {
                $shouldUpdateCache = true;
                break;
            }
        }

        // Load the routes from the cache file if it exists and it's up to date
        if (file_exists($this->cacheFile) && !$shouldUpdateCache) {
            $this->routes = require $this->cacheFile;
        } else {
            // Load the routes from the configuration files
            foreach ($routeFiles as $file) {
                if (file_exists($file)) {
                    $routeConfig = require $file;
                    $this->add_routes($routeConfig['routes'], $routeConfig['middleware'] ?? []);
                } else {
                    throw new \InvalidArgumentException("Route file '$file' not found.");
                }
            }

            // Save the routes to the cache file
            file_put_contents($this->cacheFile, '<?php return ' . var_export($this->routes, true) . ';');
        }
    }

    /**
     * Add the routes to the router and configurations
     * @param array $routes
     * @param array $middleware
     */
    protected function add_routes(array $routes, array $groupMiddleware): void
    {
        foreach ($routes as $route => $config) {
            $this->routes[$route] = [
                'controller' => $config['controller'],
                'method' => $config['method'],
                'name' => $config['name'] ?? null,
                'middleware' => array_unique(array_merge($groupMiddleware, $config['middleware'] ?? []))
            ];
        }
    }

    public function get_routes(?string $route = null): ?array
    {
        if ($route) {
            return $this->routes[$route] ?? null;
        }
        return $this->routes;
    }

    /**
     * Handle the request and call the appropriate controller method
     * @param string $uri
     * @throws NotFoundException se la rotta non viene trovata
     */
    public function route(string $uri): void
    {
        $routeConfig = $this->find_route($uri);

        if ($routeConfig) {
            $controllerName = $routeConfig['controller'];
            $method = $routeConfig['method'];
            $params = $routeConfig['params'] ?? []; // Found dynamic parameters
            $middlewareStack = $routeConfig['middleware'];
            
            // Execute the middleware and controller method
            $response = $this->execute_middleware_stack($middlewareStack, function() use ($controllerName, $method, $params) {
                return $this->execute_controller($controllerName, $method, $params);
            });
            $response->send();

        } else {
            throw new NotFoundException("Route '$uri' not found.");
        }
    }

    /**
     * Find the route, even if it contains dynamic parameters like /user/{id}
     * @param string $uri
     * @return array|null
     */
    public function find_route(string $uri)
    {
        // First check a match in the static routes
        if (isset($this->routes[$uri])) {
            return $this->routes[$uri];
        }
    
        // Check for dynamic routes
        foreach ($this->routes as $route => $config) {
            // Skip static routes
            if (strpos($route, '{') === false) {
                continue;
            }

            // Replace dynamic parts with regex
            $pattern = preg_replace('/\{[^\}]+\}/', '([a-zA-Z0-9_-]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Remove the first element
                $config['params'] = $matches; // Save the dynamic parameters
                return $config;
            }
        }
        return null;
    }

    /**
     * Run the middleware stack
     * @param array $middlewareStack
     * @param callable $next
     */
    protected function execute_middleware_stack(array $middlewareStack, callable $next)
    {
        if (empty($middlewareStack)) {
            return $next();
        }

        $middlewareName = array_shift($middlewareStack);

        $middlewareClass = "App\\Middlewares\\{$middlewareName}";

        if (class_exists($middlewareClass)) {

            $middleware = $this->container->getLazy($middlewareClass);

            if (method_exists($middleware, 'handle')) {
                return $middleware->handle(function() use ($middlewareStack, $next) {
                   return $this->execute_middleware_stack($middlewareStack, $next);
                });
            } else {
                throw new \Exception("Method 'handle' not found in middleware class '$middlewareClass'.");
            }

        } else {
            throw new \Exception("Middleware class '$middlewareClass' not found.");
        }
    }

    /**
     * Run the controller and method
     * @param string $controllerName
     * @param string $method
     * @param array $params
     */
    protected function execute_controller(string $controllerName, string $method, array $params = [])
    {
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (class_exists($controllerClass)) {
            $controller = $this->container->getLazy($controllerClass);

            if (method_exists($controller, $method)) {
                // Pass dynamic parameters to the controller method
                return call_user_func_array([$controller, $method], $params);
            } else {
                throw new NotFoundException("Method '$method' not found in controller '$controllerClass'.");
            }
        } else {
            throw new NotFoundException("Controller '$controllerClass' not found.");
        }
    }
}