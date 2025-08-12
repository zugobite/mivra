<?php

namespace App\Core;

/**
 * HTTP Request Wrapper.
 *
 * A minimal abstraction around PHP superglobals (`$_GET`, `$_POST`, `$_FILES`, `$_COOKIE`, `$_SERVER`).
 * Provides convenient methods for:
 * - Retrieving the HTTP method.
 * - Getting the request path (without query string).
 * - Accessing input values from POST or GET with a fallback.
 *
 * @package App\Core
 */
final class Request
{
    /**
     * Query parameters from `$_GET`.
     *
     * @var array<string, mixed>
     */
    public array $get;

    /**
     * Form data from `$_POST`.
     *
     * @var array<string, mixed>
     */
    public array $post;

    /**
     * Uploaded files from `$_FILES`.
     *
     * @var array<string, mixed>
     */
    public array $files;

    /**
     * Cookies from `$_COOKIE`.
     *
     * @var array<string, mixed>
     */
    public array $cookies;

    /**
     * Server and environment information from `$_SERVER`.
     *
     * @var array<string, mixed>
     */
    public array $server;

    /**
     * Private constructor to enforce use of `fromGlobals()`.
     *
     * @param array<string, mixed> $get
     * @param array<string, mixed> $post
     * @param array<string, mixed> $files
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     */
    private function __construct(array $get, array $post, array $files, array $cookies, array $server)
    {
        $this->get     = $get;
        $this->post    = $post;
        $this->files   = $files;
        $this->cookies = $cookies;
        $this->server  = $server;
    }

    /**
     * Create a `Request` instance from PHP superglobals.
     *
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_FILES, $_COOKIE, $_SERVER);
    }

    /**
     * Get the HTTP request method.
     *
     * @return string HTTP method (e.g., GET, POST, PUT, DELETE).
     */
    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get the URL path (excluding query string).
     *
     * @return string The request path (e.g., `/contact`).
     */
    public function path(): string
    {
        return (string) parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    /**
     * Retrieve an input value from POST, falling back to GET.
     *
     * @param string $key     Input field name.
     * @param mixed  $default Default value if not found.
     *
     * @return mixed The input value or the default if not found.
     */
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }
}
