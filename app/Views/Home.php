<?php

/**
 * Home page view.
 *
 * This file defines the HTML content and SEO metadata for the home page.
 * Uses the `AppLayout` layout for consistent structure and styles across the site.
 *
 * Features:
 * - Sets SEO metadata (title, description, canonical URL) using the `Seo` helper.
 * - Adds JSON-LD structured data for the website schema.
 * - Outputs a hero section with a call-to-action button linking to the contact page.
 *
 * Usage:
 * This view is rendered via the `HomeController@index` method.
 *
 * @uses \App\Helpers\Seo   For building SEO metadata and head tags.
 * @uses \App\Helpers\Url   For generating the current and base site URLs.
 */

use App\Helpers\Seo;

// Load base application layout function
require __DIR__ . '/Layouts/AppLayout.php';

// Create SEO metadata for the home page
$seo = Seo::make()
  ->title('Home | Mivra')
  ->description('A zero-dependency PHP micro-site framework.')
  ->canonical(\App\Helpers\Url::current())
  ->addJsonLd([
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => 'Mivra',
    'url'      => \App\Helpers\Url::base(),
  ]);

// Capture the page's main content
ob_start(); ?>
<section class="hero container">
  <h1>Mivra Micro</h1>
  <p>Build landing pages and portfolios with <strong>no Composer, no NPM</strong>.</p>
  <a class="btn" href="/contact">Contact</a>
</section>
<?php
$content = ob_get_clean();

// Render the page with the App layout
renderAppLayout('Home', $content, $seo);
