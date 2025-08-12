<?php

namespace App\Core;

/**
 * HTTP Router.
 *
 * A minimal yet capable router supporting:
 * - HTTP verbs: GET, POST, PUT, DELETE
 * - Route parameters with named placeholders (e.g., `/users/{id}`)
 * - Named routes with URL generation
 * - Global and per-route middleware
 * - 405 Method Not Allowed handling
 * - Simple route cache dump/load for performance
 *
 * Middleware signature:
 * ```php
 * function (Request $req, callable $next) {
 *     return $next($req);
 * }
 * ```
 *
 * @package App\Core
 */
class Router
{
    /**
     * Routes grouped by HTTP method.
     *
     * @var array<string, array<int, array>>
     */
    private array $routes = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];

    /**
     * Named routes for URL generation.
     *
     * @var array<string, array{method:string, path:string, keys:array}>
     */
    private array $named = [];

    /**
     * Global middleware stack.
     *
     * @var array<int, callable>
     */
    private array $middleware = [];

    /**
     * Temporarily stores the name for the next registered route.
     *
     * @var string|null
     */
    private ?string $pendingName = null;

    /* ===== Route Registration ===== */

    /** Map a GET route. */
    public function get(string $path, $action, array $mw = []): self
    {
        return $this->map('GET', $path, $action, $mw);
    }

    /** Map a POST route. */
    public function post(string $path, $action, array $mw = []): self
    {
        return $this->map('POST', $path, $action, $mw);
    }

    /** Map a PUT route. */
    public function put(string $path, $action, array $mw = []): self
    {
        return $this->map('PUT', $path, $action, $mw);
    }

    /** Map a DELETE route. */
    public function delete(string $path, $action, array $mw = []): self
    {
        return $this->map('DELETE', $path, $action, $mw);
    }

    /**
     * Name the most recently added route.
     *
     * @param string $name Route name for URL generation.
     * @return self
     */
    public function name(string $name): self
    {
        $this->pendingName = $name;
        return $this;
    }

    /**
     * Register a global middleware.
     *
     * @param callable $mw Middleware function.
     * @return void
     */
    public function use(callable $mw): void
    {
        $this->middleware[] = $mw;
    }

    /**
     * Generate a URL from a named route.
     *
     * @param string               $name   Named route identifier.
     * @param array<string, mixed> $params Route parameters to substitute.
     *
     * @return string Generated URL path.
     *
     * @throws \InvalidArgumentException If the named route does not exist.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->named[$name])) {
            throw new \InvalidArgumentException("Route '{$name}' not found");
        }

        $path = $this->named[$name]['path'];
        foreach ($params as $k => $v) {
            $path = str_replace('{' . $k . '}', rawurlencode((string) $v), $path);
        }

        return $path;
    }

    /* ===== Dispatching ===== */

    /**
     * Dispatch the current HTTP request to the matching route.
     *
     * - Matches by HTTP method and URI.
     * - Supports route parameters.
     * - Returns 405 if method is not allowed but path exists.
     * - Returns 404 if no route matches.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri     = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $allowed = [];

        foreach ($this->routes as $m => $group) {
            foreach ($group as $route) {
                if (preg_match($route['regex'], $uri, $mres)) {
                    if ($m === $method) {
                        $params = array_intersect_key($mres, array_flip($route['keys']));
                        $this->runThroughMiddleware($route['action'], $params, $route['mw']);
                        return;
                    }
                    $allowed[] = $m;
                }
            }
        }

        if ($allowed) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            http_response_code(405);
            include __DIR__ . '/../Views/Error/405.php';
            return;
        }

        http_response_code(404);
        include __DIR__ . '/../Views/Error/404.php';
    }

    /* ===== Route Caching ===== */

    /**
     * Dump all routes and named routes to a PHP cache file.
     *
     * @param string $file Path to the cache file.
     * @return void
     */
    public function dumpCache(string $file): void
    {
        $data = ['routes' => $this->routes, 'named' => $this->named];
        file_put_contents($file, '<?php return ' . var_export($data, true) . ';');
    }

    /**
     * Load routes from a PHP cache file.
     *
     * @param string $file Path to the cache file.
     * @return bool True if cache loaded successfully, false otherwise.
     */
    public function loadCache(string $file): bool
    {
        if (!is_file($file)) {
            return false;
        }

        $data = require $file;
        if (!is_array($data) || !isset($data['routes'], $data['named'])) {
            return false;
        }

        $this->routes = $data['routes'];
        $this->named  = $data['named'];
        return true;
    }

    /* ===== Internal Helpers ===== */

    /**
     * Register a route for a given method.
     *
     * @param string               $method HTTP method.
     * @param string               $path   Route path pattern.
     * @param mixed                $action Controller@method string or callable.
     * @param array<int, callable> $mw     Per-route middleware stack.
     *
     * @return self
     */
    private function map(string $method, string $path, $action, array $mw): self
    {
        [$regex, $keys] = $this->compile($path);
        $route = [
            'regex'  => $regex,
            'keys'   => $keys,
            'action' => $action,
            'mw'     => $mw,
            'path'   => $path
        ];

        $this->routes[$method][] = $route;

        if ($this->pendingName) {
            $this->named[$this->pendingName] = [
                'method' => $method,
                'path'   => $path,
                'keys'   => $keys
            ];
            $this->pendingName = null;
        }

        return $this;
    }

    /**
     * Compile a route path into a regex and extract parameter keys.
     *
     * @param string $path Route path pattern.
     *
     * @return array{0:string, 1:array} Array containing the compiled regex and parameter keys.
     */
    private function compile(string $path): array
    {
        $keys = [];
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function ($m) use (&$keys) {
                $keys[] = $m[1];
                return '(?P<' . $m[1] . '>[^/]+)';
            },
            $path
        );

        return ['~^' . $regex . '$~', $keys];
    }

    /**
     * Execute global and per-route middleware, then call the route action.
     *
     * @param mixed                $action  Controller@method string or callable.
     * @param array<string, mixed> $params  Route parameters.
     * @param array<int, callable> $routeMw Per-route middleware stack.
     *
     * @return void
     */
    private function runThroughMiddleware($action, array $params, array $routeMw): void
    {
        $stack = array_merge($this->middleware, $routeMw);

        $next = function (Request $req) use ($action, $params) {
            return $this->callAction($action, $params, $req);
        };

        while ($mw = array_pop($stack)) {
            $prev = $next;
            $next = function (Request $req) use ($mw, $prev) {
                return $mw($req, $prev);
            };
        }

        $next(Request::fromGlobals());
    }

    /**
     * Call the matched route's action.
     *
     * @param mixed                $action  Controller@method string or callable.
     * @param array<string, mixed> $params  Route parameters.
     * @param Request              $request Current HTTP request instance.
     *
     * @return void
     *
     * @throws \RuntimeException If the action is invalid.
     */
    private function callAction($action, array $params, Request $request): void
    {
        if (is_callable($action)) {
            $action($request, $params);
            return;
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action, 2);
            $fqcn = "\\App\\Controllers\\{$controller}";

            if (!class_exists($fqcn)) {
                $file = __DIR__ . "/../Controllers/{$controller}.php";
                if (is_file($file)) {
                    require_once $file;
                }
            }

            $obj = new $fqcn();
            $obj->$method($request, $params);
            return;
        }

        throw new \RuntimeException('Invalid route action');
    }
}
