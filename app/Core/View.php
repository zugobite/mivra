<?php

namespace App\Core;

/**
 * View Renderer.
 *
 * Provides a simple static method to render PHP view templates.
 * Templates are located in the `app/Views/` directory and are
 * included with extracted variables for use within the view.
 *
 * @package App\Core
 */
final class View
{
    /**
     * Render a PHP view template.
     *
     * Extracts variables into the local scope and includes the view file.
     * If the view file does not exist, a `RuntimeException` is thrown.
     *
     * Example:
     * ```php
     * View::render('Home', ['user' => $user]);
     * ```
     * This will load `app/Views/Home.php` with `$user` available inside it.
     *
     * @param string               $view The view name without extension
     *                                   (e.g., `'Home'` → `app/Views/Home.php`).
     * @param array<string, mixed> $vars Variables to extract into the view scope.
     *
     * @throws \RuntimeException If the specified view file does not exist.
     *
     * @return void
     */
    public static function render(string $view, array $vars = []): void
    {
        extract($vars, EXTR_SKIP);

        $path = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        require $path;
    }
}
