<?php

/**
 * Base layout template for all pages.
 *
 * Responsibilities:
 * - Injects SEO metadata into the <head> section using the `Seo` helper.
 * - Outputs a consistent site header and footer.
 * - Includes CSS and JavaScript assets with cache-busting via `Asset` helper.
 * - Displays any flash messages from the `Flash` helper.
 *
 * Usage example:
 * ```php
 * require __DIR__ . '/Layouts/AppLayout.php';
 * $seo = Seo::make()->title('My Page Title');
 * ob_start();
 *     echo '<p>Page content here</p>';
 * $content = ob_get_clean();
 * renderAppLayout('My Page Title', $content, $seo);
 * ```
 *
 * @param string      $title         Fallback page title if no `Seo` instance is provided.
 * @param string      $content       Fully rendered HTML content for the page body.
 * @param Seo|null    $seo           Optional `Seo` object for head tag generation.
 * @param string      $extraHead     Optional raw HTML to inject into the <head> (e.g., extra meta tags, stylesheets).
 * @param string      $extraScripts  Optional raw HTML to inject before </body> (e.g., inline scripts, third-party libraries).
 *
 * @return void
 */

use App\Helpers\Seo;
use App\Helpers\Asset;
use App\Helpers\Flash;

function renderAppLayout(
    string $title,
    string $content,
    ?Seo $seo = null,
    string $extraHead = '',
    string $extraScripts = ''
): void {
    if (!$seo) {
        $seo = Seo::make(['title' => $title]);
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?= $seo->toHeadHtml() . "\n" ?>
        <link rel="stylesheet" href="<?= Asset::url('/assets/css/app.css') ?>">
        <?= $extraHead ?>
    </head>

    <body>
        <?php include __DIR__ . '/../Components/Header.php'; ?>
        <?php include __DIR__ . '/../Components/Flash.php'; ?>

        <main class="app-main container">
            <?= $content ?>
        </main>

        <?php include __DIR__ . '/../Components/Footer.php'; ?>

        <script src="<?= Asset::url('/assets/js/app.js') ?>"></script>
        <?= $extraScripts ?>
    </body>

    </html>
<?php
}
