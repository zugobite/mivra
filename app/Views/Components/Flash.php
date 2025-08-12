<?php

/**
 * Flash message component.
 *
 * Displays one-time session-based notifications (e.g., success or error messages)
 * retrieved from the `Flash` helper. Messages are automatically cleared
 * after being fetched.
 *
 * How it works:
 * - `Flash::get('success')` returns the success message if set, or null otherwise.
 * - `Flash::get('error')` returns the error message if set, or null otherwise.
 * - Each message is wrapped in a styled <div> with corresponding classes for styling.
 *
 * Usage:
 * This file is typically included in your main layout:
 * ```php
 * <?php include __DIR__ . '/../Components/Flash.php'; ?>
 * ```
 *
 * Expected CSS classes:
 * - `.flash`: Base flash message styling.
 * - `.flash-success`: Styling for success messages.
 * - `.flash-error`: Styling for error messages.
 *
 * @uses \App\Helpers\Flash
 */

use App\Helpers\Flash;

// Retrieve one-time flash messages from session
$success = Flash::get('success');
$error   = Flash::get('error');
?>

<?php if ($success): ?>
    <div class="flash flash-success container">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="flash flash-error container">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>