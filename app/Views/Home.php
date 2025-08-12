<?php

use App\Helpers\SEO;

$seo = SEO::make()
    ->title('Home — Mivra')
    ->description('A barebones, lightweight PHP framework for tiny, fast sites.')
    ->canonical('https://mivra.webtra.co.za/')
    ->image('https://mivra.webtra.co.za/images/og/home.png')
    ->addJsonLd([
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'Mivra',
        'url'      => 'https://mivra.webtra.co.za/',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => 'https://mivra.webtra.co.za/search?q={query}',
            'query-input' => 'required name=query'
        ]
    ]);

ob_start(); ?>
<h1>Welcome to Mivra</h1>
<p>This is your home page.</p>
<?php
$content = ob_get_clean();

require_once __DIR__ . '/Layouts/AppLayout.php';
renderAppLayout('Home', $content, $seo);
