<?php

/**
 * Contact page view.
 *
 * This file defines the HTML structure and SEO metadata for the contact page.
 * Includes a CSRF-protected form with fields for name, email, and message.
 * Designed to support progressive enhancement — works with standard form POST
 * and can also be used with AJAX for smoother user interaction.
 *
 * Features:
 * - CSRF protection via hidden `_csrf` token.
 * - Accessible form markup with `label` elements.
 * - SEO metadata and canonical URL setup.
 * - AJAX-friendly result container (`#contact-result`) for dynamic responses.
 *
 * Usage:
 * This view is rendered via the `ContactController@show` method.
 *
 * @uses \App\Helpers\Seo    For building SEO metadata and head tags.
 * @uses \App\Helpers\Url    For generating canonical page URL.
 * @uses \App\Middleware\Csrf For generating a secure CSRF token.
 */

use App\Helpers\Seo;
use App\Helpers\Url;
use App\Middleware\Csrf;

// Load base application layout function
require __DIR__ . '/Layouts/AppLayout.php';

// Create SEO metadata for the contact page
$seo = Seo::make()
  ->title('Contact | Mivra')
  ->description('Get in touch.')
  ->canonical(Url::current());

// Capture the page's main content
ob_start(); ?>
<section class="container">
  <h1>Contact</h1>
  <form id="contact-form" action="/contact" method="post" class="stack">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <label>Name <input type="text" name="name" required></label>
    <label>Email <input type="email" name="email" required></label>
    <label>Message <textarea name="message" required></textarea></label>
    <button class="btn" type="submit">Send</button>
  </form>
  <div id="contact-result" class="mt"></div>
</section>
<?php
$content = ob_get_clean();

// Render the page with the App layout
renderAppLayout('Contact', $content, $seo);
