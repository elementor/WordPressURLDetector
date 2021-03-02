<?php

declare(strict_types=1);

namespace WordPressURLDetector;

class SitemapStringTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testString( $url )
    {
        $parser = new SitemapParser('SitemapParser', [ 'strict' => false ]);
        $this->assertInstanceOf('WordPressURLDetector\SitemapParser', $parser);
        $parser->parse($url);
        $this->assertTrue(is_array($parser->getSitemaps()));
        $this->assertTrue(is_array($parser->getURLs()));
        $this->assertTrue(count($parser->getSitemaps()) > 1);
        $this->assertTrue(count($parser->getURLs()) >= 1000);
        foreach ($parser->getSitemaps() as $url => $tags) {
            $this->assertTrue(is_string($url));
            $this->assertTrue(is_array($tags));
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
        foreach ($parser->getURLs() as $url => $tags) {
            $this->assertTrue(is_string($url));
            $this->assertTrue(is_array($tags));
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'https://www.xml-sitemaps.com/urllist.txt',
            ],
        ];
    }
}
