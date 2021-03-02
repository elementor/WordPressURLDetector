<?php

declare(strict_types=1);

namespace WordPressURLDetector;

class SitemapInvalidURLTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testInvalidURL( $url )
    {
        $this->expectException('WordPressURLDetector\WordPressURLDetectorException');
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('WordPressURLDetector\SitemapParser', $parser);
        $parser->parse($url);
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
                'htt://www.example.c/',
            ],
            [
                'http:/www.example.com/',
            ],
            [
                'https//www.example.com/',
            ],
        ];
    }
}
