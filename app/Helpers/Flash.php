<?php

namespace App\Helpers;

/**
 * Class Flash
 *
 * Provides a simple **session-based flash messaging** system for one-time messages
 * such as success notifications, error alerts, or status updates.
 *
 * **Purpose:**
 * - Allows storing temporary messages in the session that persist across one request.
 * - Automatically clears the message after it has been retrieved.
 *
 * **Typical Use Case:**
 * - **Set a flash message in a controller**:
 *   ```php
 *   use App\Helpers\Flash;
 *   Flash::set('success', 'Your form has been submitted successfully!');
 *   ```
 * - **Retrieve and display the message in a view/layout**:
 *   ```php
 *   if ($msg = Flash::get('success')) {
 *       echo "<div class='flash-success'>{$msg}</div>";
 *   }
 *   ```
 *
 * **Notes:**
 * - Requires an active session (`session_start()` must have been called).
 * - Messages are stored in `$_SESSION['_flash']` until retrieved.
 * - Once `get()` is called, the message is removed from the session automatically.
 *
 * @package App\Helpers
 */
final class Flash
{
    /**
     * Store a flash message in the session.
     *
     * @param string $key   Identifier for the message type (e.g., 'success', 'error').
     * @param string $msg   The message text to display.
     * @return void
     */
    public static function set(string $key, string $msg): void
    {
        $_SESSION['_flash'][$key] = $msg;
    }

    /**
     * Retrieve a flash message from the session and remove it.
     *
     * @param string $key   The message identifier to retrieve.
     * @return string|null  The stored message, or null if not found.
     */
    public static function get(string $key): ?string
    {
        $v = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $v;
    }
}
