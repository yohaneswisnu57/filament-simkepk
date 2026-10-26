<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlSanitizer
{
    /**
     * Sanitize rich text HTML with an element/attribute allowlist to prevent XSS.
     */
    public static function sanitize(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $config = (new HtmlSanitizerConfig)
            ->allowElement('p')
            ->allowElement('b')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('em')
            ->allowElement('span')
            ->allowElement('div')
            ->allowElement('h1')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('h5')
            ->allowElement('h6')
            ->allowElement('a', ['href'])
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        return (new SymfonyHtmlSanitizer($config))->sanitize($html);
    }
}
