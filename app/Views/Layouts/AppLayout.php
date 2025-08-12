<?php

use App\Helpers\SEO;

/**
 * Render the main application layout.
 *
 * This function wraps the provided content within a consistent HTML
 * structure, including the `<head>` section, shared header and footer
 * components, and optional extra head/scripts content.
 *
 * It also ensures SEO metadata is properly rendered using the Seo helper.
 *
 * @param string      $title            Page title (legacy parameter; SEO->title is the actual source of truth)
 * @param string      $content          Rendered HTML body content for the page
 * @param SEO|null    $seo              Optional SEO helper instance; if not provided, one will be created
 * @param string      $extraHead        Additional HTML to include in the <head> (e.g., custom meta tags or styles)
 * @param string      $extraScripts     Additional HTML to include before closing </body> (e.g., extra JS scripts)
 *
 * @return void
 */
function renderAppLayout($title, $content, $seo = null, $extraHead = '', $extraScripts = '')
{
    if (!$seo) {
        $seo = SEO::make(['title' => $title]);
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?= $seo->toHeadHtml() ?>

        <link rel="stylesheet" href="/css/base.css">
        <?= $extraHead ?>
    </head>

    <body>
        <?php include __DIR__ . '/../Components/Header.php'; ?>

        <main class="app-main">
            <?= $content ?>
        </main>

        <?php include __DIR__ . '/../Components/Footer.php'; ?>

        <script src="/js/app.js"></script>
        <?= $extraScripts ?>
    </body>

    </html>
<?php
}
