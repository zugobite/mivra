<?php

namespace App\Helpers;

/**
 * Class Seo
 *
 * A lightweight SEO helper for managing and rendering
 * HTML head tags, Open Graph tags, Twitter cards,
 * and JSON-LD structured data.
 *
 * This class follows a fluent interface pattern, allowing
 * chainable configuration of SEO attributes.
 *
 * @package App\Helpers
 */
final class SEO
{
    /**
     * Internal SEO data store.
     *
     * @var array{
     *     title: string|null,
     *     description: string|null,
     *     canonical: string|null,
     *     robots: string|null,
     *     image: string|null,
     *     locale: string|null,
     *     site_name: string|null,
     *     twitter: string|null,
     *     jsonld: array<int,array<string,mixed>>,
     *     extras: array<int,string>
     * }
     */
    private array $data = [
        'title'       => null,
        'description' => null,
        'canonical'   => null,
        'robots'      => 'index,follow',
        'image'       => null,
        'locale'      => 'en_ZA',
        'site_name'   => 'Mivra',
        'twitter'     => '@yoursite',
        'jsonld'      => [],
        'extras'      => [],
    ];

    /**
     * Create a new SEO instance with optional overrides.
     *
     * @param array<string,mixed> $overrides
     * @return self
     */
    public static function make(array $overrides = []): self
    {
        $seo = new self();
        $seo->data = array_replace($seo->data, $overrides);
        return $seo;
    }

    /**
     * Set the page title.
     *
     * @param string $v
     * @return self
     */
    public function title(string $v): self
    {
        $this->data['title'] = $v;
        return $this;
    }

    /**
     * Set the page description (meta description).
     *
     * @param string $v
     * @return self
     */
    public function description(string $v): self
    {
        $this->data['description'] = $v;
        return $this;
    }

    /**
     * Set the canonical URL.
     *
     * @param string $v
     * @return self
     */
    public function canonical(string $v): self
    {
        $this->data['canonical'] = $v;
        return $this;
    }

    /**
     * Set the robots meta tag value.
     *
     * @param string $v
     * @return self
     */
    public function robots(string $v): self
    {
        $this->data['robots'] = $v;
        return $this;
    }

    /**
     * Set the preview image URL.
     *
     * @param string|null $v
     * @return self
     */
    public function image(?string $v): self
    {
        $this->data['image'] = $v;
        return $this;
    }

    /**
     * Set the Open Graph locale.
     *
     * @param string $v
     * @return self
     */
    public function locale(string $v): self
    {
        $this->data['locale'] = $v;
        return $this;
    }

    /**
     * Set the Open Graph site name.
     *
     * @param string $v
     * @return self
     */
    public function siteName(string $v): self
    {
        $this->data['site_name'] = $v;
        return $this;
    }

    /**
     * Set the Twitter handle.
     *
     * @param string $v
     * @return self
     */
    public function twitter(string $v): self
    {
        $this->data['twitter'] = $v;
        return $this;
    }

    /**
     * Add a JSON-LD schema object.
     *
     * @param array<string,mixed> $schema
     * @return self
     */
    public function addJsonLd(array $schema): self
    {
        $this->data['jsonld'][] = $schema;
        return $this;
    }

    /**
     * Add a raw HTML tag string to be included in head.
     *
     * @param string $tagHtml
     * @return self
     */
    public function addExtra(string $tagHtml): self
    {
        $this->data['extras'][] = $tagHtml;
        return $this;
    }

    /**
     * Render all configured SEO tags as a string of HTML.
     *
     * @return string
     */
    public function toHeadHtml(): string
    {
        $e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        $d = $this->data;

        $out = [];

        // Basic meta
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

        // JSON-LD
        foreach ($d['jsonld'] as $schema) {
            $out[] = '<script type="application/ld+json">' .
                json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
                '</script>';
        }

        // Custom extras
        foreach ($d['extras'] as $raw) $out[] = $raw;

        return implode("\n    ", $out);
    }
}
