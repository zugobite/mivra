<?php

namespace App\Helpers;

/**
 * Class Seo
 *
 * SEO metadata builder for PHP views.
 *
 * Generates `<title>`, `<meta>` tags, Open Graph (OG) tags, Twitter Cards,
 * JSON-LD schema, and any additional custom head tags.
 *
 * **Supported features:**
 * - Title, description, canonical URL, robots meta
 * - Open Graph title/description/URL/site_name/locale/image
 * - Twitter Card metadata (summary or summary_large_image)
 * - Multiple JSON-LD schema objects
 * - Arbitrary extra head tags
 *
 * **Usage Example:**
 * ```php
 * use App\Helpers\Seo;
 *
 * $seo = Seo::make()
 *   ->title('Home — My Site')
 *   ->description('Welcome to my site.')
 *   ->canonical('https://example.com')
 *   ->image('https://example.com/img/cover.jpg')
 *   ->twitter('@myhandle')
 *   ->addJsonLd([
 *       '@context' => 'https://schema.org',
 *       '@type'    => 'WebSite',
 *       'name'     => 'My Site',
 *       'url'      => 'https://example.com'
 *   ]);
 *
 * echo $seo->toHeadHtml();
 * ```
 *
 * @package App\Helpers
 */
final class Seo
{
    /** @var array<string,mixed> Default SEO data structure. */
    private array $data = [
        'title'       => null,
        'description' => null,
        'canonical'   => null,
        'robots'      => 'index,follow',
        'image'       => null,
        'locale'      => 'en_ZA',
        'site_name'   => 'Mivra Micro',
        'twitter'     => '@yoursite',
        'jsonld'      => [],
        'extras'      => [],
    ];

    /**
     * Factory method to create a Seo instance with optional overrides.
     *
     * @param array<string,mixed> $overrides Key-value overrides for default SEO data.
     * @return self
     */
    public static function make(array $overrides = []): self
    {
        $s = new self();
        $s->data = array_replace($s->data, $overrides);
        return $s;
    }

    /** @return $this */ public function title(string $v): self
    {
        $this->data['title'] = $v;
        return $this;
    }
    /** @return $this */ public function description(string $v): self
    {
        $this->data['description'] = $v;
        return $this;
    }
    /** @return $this */ public function canonical(string $v): self
    {
        $this->data['canonical'] = $v;
        return $this;
    }
    /** @return $this */ public function robots(string $v): self
    {
        $this->data['robots'] = $v;
        return $this;
    }
    /** @return $this */ public function image(?string $v): self
    {
        $this->data['image'] = $v;
        return $this;
    }
    /** @return $this */ public function locale(string $v): self
    {
        $this->data['locale'] = $v;
        return $this;
    }
    /** @return $this */ public function siteName(string $v): self
    {
        $this->data['site_name'] = $v;
        return $this;
    }
    /** @return $this */ public function twitter(string $v): self
    {
        $this->data['twitter'] = $v;
        return $this;
    }
    /** @return $this */ public function addJsonLd(array $schema): self
    {
        $this->data['jsonld'][] = $schema;
        return $this;
    }
    /** @return $this */ public function addExtra(string $tagHtml): self
    {
        $this->data['extras'][] = $tagHtml;
        return $this;
    }

    /**
     * Render all SEO tags as a string of HTML.
     *
     * @return string HTML for <head> section.
     */
    public function toHeadHtml(): string
    {
        $e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        $d = $this->data;
        $out = [];

        // Basic meta tags
        if ($d['title'])       $out[] = '<title>' . $e($d['title']) . '</title>';
        if ($d['description']) $out[] = '<meta name="description" content="' . $e($d['description']) . '">';
        if ($d['robots'])      $out[] = '<meta name="robots" content="' . $e($d['robots']) . '">';
        if ($d['canonical'])   $out[] = '<link rel="canonical" href="' . $e($d['canonical']) . '">';

        // Open Graph
        if ($d['title'])       $out[] = '<meta property="og:title" content="' . $e($d['title']) . '">';
        if ($d['description']) $out[] = '<meta property="og:description" content="' . $e($d['description']) . '">';
        if ($d['canonical'])   $out[] = '<meta property="og:url" content="' . $e($d['canonical']) . '">';
        if ($d['site_name'])   $out[] = '<meta property="og:site_name" content="' . $e($d['site_name']) . '">';
        if ($d['locale'])      $out[] = '<meta property="og:locale" content="' . $e($d['locale']) . '">';
        if ($d['image']) {
            $out[] = '<meta property="og:image" content="' . $e($d['image']) . '">';
            $out[] = '<meta property="og:image:alt" content="' . $e($d['title'] ?? 'Preview') . '">';
        }

        // Twitter
        $out[] = '<meta name="twitter:card" content="' . ($d['image'] ? 'summary_large_image' : 'summary') . '">';
        if ($d['twitter'])     $out[] = '<meta name="twitter:site" content="' . $e($d['twitter']) . '">';
        if ($d['title'])       $out[] = '<meta name="twitter:title" content="' . $e($d['title']) . '">';
        if ($d['description']) $out[] = '<meta name="twitter:description" content="' . $e($d['description']) . '">';
        if ($d['image'])       $out[] = '<meta name="twitter:image" content="' . $e($d['image']) . '">';

        // JSON-LD scripts
        foreach ($d['jsonld'] as $schema) {
            $out[] = '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                . '</script>';
        }

        // Extra raw tags
        foreach ($d['extras'] as $raw) {
            $out[] = $raw;
        }

        return implode("\n    ", $out);
    }
}
