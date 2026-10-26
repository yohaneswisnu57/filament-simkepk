<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_keeps_allowed_markup(): void
    {
        $result = HtmlSanitizer::sanitize('<p><strong>Halo</strong> <a href="https://example.com">link</a></p>');

        $this->assertStringContainsString('<strong>Halo</strong>', $result);
        $this->assertStringContainsString('href="https://example.com"', $result);
    }

    public function test_it_strips_script_and_event_handlers(): void
    {
        $result = HtmlSanitizer::sanitize('<p onclick="alert(1)">x</p><script>alert(1)</script><img src=x onerror=alert(1)>');

        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringNotContainsString('<script', $result);
        $this->assertStringNotContainsString('onerror', $result);
        $this->assertStringNotContainsString('<img', $result);
    }

    public function test_it_blocks_dangerous_link_schemes(): void
    {
        $javascript = HtmlSanitizer::sanitize('<a href="java&#115;cript:alert(1)">x</a>');
        $data = HtmlSanitizer::sanitize('<a href="data:text/html,<script>alert(1)</script>">x</a>');

        $this->assertStringNotContainsStringIgnoringCase('javascript:', $javascript);
        $this->assertStringNotContainsString('data:', $data);
    }

    public function test_it_returns_empty_string_for_empty_input(): void
    {
        $this->assertSame('', HtmlSanitizer::sanitize(null));
        $this->assertSame('', HtmlSanitizer::sanitize(''));
    }
}
