<?php

namespace App\Support;

use Illuminate\Support\Str;

class EditorContentProcessor
{
    /**
     * 허용 HTML 태그 목록
     *
     * @var array<int, string>
     */
    private array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a',
    ];

    public function sanitizeForStorage(string $content): string
    {
        return $this->sanitizeHtml($this->normalizeUtf8($content));
    }

    public function toRenderableHtml(string $content): string
    {
        $content = $this->normalizeUtf8($content);

        if ($this->looksLikeHtml($content)) {
            return $this->sanitizeHtml($content);
        }

        $markdownHtml = Str::markdown($content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $this->sanitizeHtml((string) $markdownHtml);
    }

    private function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\s*\/?\s*(p|br|strong|b|em|i|u|s|h[1-6]|ul|ol|li|blockquote|pre|code|a)\b/i', $value);
    }

    private function sanitizeHtml(string $html): string
    {
        $html = trim($this->normalizeUtf8($html));

        if ($html === '') {
            return '';
        }

        if (! class_exists(\DOMDocument::class)) {
            return strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');
        }

        $wrappedHtml = '<!doctype html><html><body>' . $html . '</body></html>';
        $doc = new \DOMDocument('1.0', 'UTF-8');

        $previousInternalErrors = libxml_use_internal_errors(true);
        $loadHtml = '<?xml encoding="UTF-8">' . $wrappedHtml;
        if (function_exists('mb_convert_encoding')) {
            $loadHtml = mb_convert_encoding($loadHtml, 'HTML-ENTITIES', 'UTF-8');
        }

        $doc->loadHTML($loadHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousInternalErrors);

        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//processing-instruction("xml")') as $xmlNode) {
            $xmlNode->parentNode?->removeChild($xmlNode);
        }

        foreach ($xpath->query('//*') as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (! in_array($tag, $this->allowedTags, true) && $tag !== 'html' && $tag !== 'body') {
                $node->parentNode?->removeChild($node);
                continue;
            }

            if ($node->hasAttributes()) {
                $attrsToRemove = [];

                foreach ($node->attributes as $attribute) {
                    if (! $attribute instanceof \DOMAttr) {
                        continue;
                    }

                    $attrName = strtolower($attribute->name);

                    if (str_starts_with($attrName, 'on')) {
                        $attrsToRemove[] = $attribute->name;
                        continue;
                    }

                    if ($tag !== 'a' || $attrName !== 'href') {
                        $attrsToRemove[] = $attribute->name;
                        continue;
                    }

                    $href = trim((string) $attribute->value);
                    if ($href === '' || preg_match('/^\s*javascript:/i', $href) || preg_match('/^\s*data:/i', $href)) {
                        $attrsToRemove[] = $attribute->name;
                    }
                }

                foreach ($attrsToRemove as $attrName) {
                    $node->removeAttribute($attrName);
                }
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }

        $result = '';
        foreach ($body->childNodes as $childNode) {
            $result .= $doc->saveHTML($childNode);
        }

        return trim($result);
    }

    private function normalizeUtf8(string $value): string
    {
        if (! $this->looksLikeMojibake($value)) {
            return $value;
        }

        if (! function_exists('mb_convert_encoding')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
        if (! is_string($converted) || $converted === '') {
            return $value;
        }

        if (function_exists('mb_check_encoding') && ! mb_check_encoding($converted, 'UTF-8')) {
            return $value;
        }

        return $this->looksLikeMojibake($converted) ? $value : $converted;
    }

    private function looksLikeMojibake(string $value): bool
    {
        return (bool) preg_match('/(?:Ã.|Â.|ì.|ë.|ê.|í.|î.|ð.|�)/u', $value);
    }
}
