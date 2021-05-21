<?php

/**
 * SitemapDownloadTest.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class SitemapDownloadTest
 *
 * @package WordPressURLDetector
 */
class SitemapDownloadTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testDownload( $url )
    {
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('WordPressURLDetector\SitemapParser', $parser);
        $parser->parse($url);
        $this->assertTrue(is_array($parser->getSitemaps()));
        $this->assertTrue(is_array($parser->getURLs()));
        $this->assertTrue(count($parser->getSitemaps()) > 0 || count($parser->getURLs()) > 0);
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
                'http://www.google.com/sitemap.xml',
            ],
            [
                'http://php.net/sitemap.xml',
            ],
            [
                'https://www.yahoo.com/news/sitemaps/news-sitemap_index_US_en-US.xml.gz',
            ],
        ];
    }
}
