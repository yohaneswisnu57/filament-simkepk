<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_has_descriptive_title_and_meta_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>SIMKEPK UKWMS - Pengajuan Etik Penelitian Online | KEPK Widya Mandala</title>', false);
        $response->assertSee('<link rel="canonical" href="https://simkepk.ukwms.ac.id/">', false);
        $response->assertSee('<meta property="og:locale" content="id_ID">', false);
        $response->assertSee('<meta property="og:site_name" content="SIMKEPK UKWMS">', false);
        $response->assertSee('<link rel="icon" href="'.asset('favicon.ico').'" sizes="any">', false);
        $response->assertSee('<link rel="apple-touch-icon" href="'.asset('apple-touch-icon.png').'">', false);
        $response->assertSee('<meta property="og:image" content="'.asset('images/og-image.jpg').'">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertDontSee('property="twitter:', false);
        $response->assertDontSee('SIMKEP UKWMS', false);
    }

    public function test_public_pages_use_bundled_assets_instead_of_runtime_cdns(): void
    {
        foreach (['/', '/privacy-policy'] as $uri) {
            $response = $this->get($uri);

            $response->assertOk();
            $response->assertDontSee('cdn.tailwindcss.com', false);
            $response->assertDontSee('unika.widyamandala.ac.id/wp-content', false);
            $response->assertSee(asset('images/logo-ukwms-128.png'), false);
        }
    }

    public function test_seo_image_assets_exist(): void
    {
        foreach (['favicon.ico', 'apple-touch-icon.png', 'images/og-image.jpg', 'images/logo-ukwms-128.png'] as $path) {
            $this->assertFileExists(public_path($path));
            $this->assertGreaterThan(0, filesize(public_path($path)), "{$path} is empty");
        }

        [$width, $height] = getimagesize(public_path('images/og-image.jpg'));

        $this->assertSame([1200, 630], [$width, $height]);
    }

    public function test_landing_page_has_exactly_one_h1(): void
    {
        $content = $this->get('/')->getContent();

        $this->assertSame(1, preg_match_all('/<h1[\s>]/i', $content));
    }

    public function test_landing_page_has_organization_structured_data_without_faqs(): void
    {
        $structuredData = $this->extractStructuredData($this->get('/')->getContent());

        $types = array_column($structuredData, '@type');

        $this->assertContains('Organization', $types);
        $this->assertNotContains('FAQPage', $types);
    }

    public function test_landing_page_has_faq_structured_data_for_active_faqs_only(): void
    {
        Faq::create([
            'question' => 'Berapa lama proses telaah etik?',
            'answer' => '<p>Sekitar <strong>14 hari</strong> kerja &amp; tergantung jenis telaah.</p>',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Faq::create([
            'question' => 'Pertanyaan nonaktif',
            'answer' => 'Tidak boleh muncul.',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $structuredData = $this->extractStructuredData($this->get('/')->getContent());
        $faqPage = collect($structuredData)->firstWhere('@type', 'FAQPage');

        $this->assertNotNull($faqPage);
        $this->assertCount(1, $faqPage['mainEntity']);
        $this->assertSame('Berapa lama proses telaah etik?', $faqPage['mainEntity'][0]['name']);
        $this->assertSame(
            'Sekitar 14 hari kerja & tergantung jenis telaah.',
            $faqPage['mainEntity'][0]['acceptedAnswer']['text'],
        );
    }

    public function test_structured_data_escapes_script_tags_in_faq_content(): void
    {
        Faq::create([
            'question' => 'Pertanyaan </script><script>alert(1)</script>',
            'answer' => 'Jawaban',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $content = $this->get('/')->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $content);
        $this->assertNotNull(collect($this->extractStructuredData($content))->firstWhere('@type', 'FAQPage'));
    }

    public function test_robots_txt_references_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://simkepk.ukwms.ac.id/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Disallow: /verify/', $robots);
        $this->assertDoesNotMatchRegularExpression('/^Disallow: \/$/m', $robots);
    }

    public function test_sitemap_is_valid_xml_listing_public_pages(): void
    {
        $sitemap = simplexml_load_file(public_path('sitemap.xml'));

        $this->assertNotFalse($sitemap);

        $locations = [];
        foreach ($sitemap->url as $url) {
            $locations[] = (string) $url->loc;
        }

        $this->assertSame([
            'https://simkepk.ukwms.ac.id/',
            'https://simkepk.ukwms.ac.id/privacy-policy',
        ], $locations);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractStructuredData(string $content): array
    {
        $this->assertSame(1, preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches));

        $decoded = json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);

        $this->assertIsArray($decoded);

        return $decoded;
    }
}
