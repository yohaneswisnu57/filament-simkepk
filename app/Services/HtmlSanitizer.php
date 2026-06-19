<?php

namespace App\Services;

class HtmlSanitizer
{
    /**
     * Sanitize a rich text HTML content to prevent XSS attacks.
     *
     * @param string|null $html
     * @return string
     */
    public static function sanitize(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Strip tags not on the allowlist
        $allowedTags = '<p><b><i><u><ul><ol><li><a><br><strong><em><span><h1><h2><h3><h4><h5><h6><div>';
        $cleaned = strip_tags($html, $allowedTags);

        // 2. Remove event attributes (on*) and javascript: links to prevent attribute injection XSS
        $cleaned = preg_replace('/on\w+\s*=\s*".*?"/i', '', $cleaned);
        $cleaned = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $cleaned);
        $cleaned = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $cleaned);
        $cleaned = preg_replace('/javascript\s*:/i', '', $cleaned);

        return $cleaned;
    }
}
