<?php

/**
 * SitemapInvalidURLTest.php
 *
 * @package WordPressURLDetector
 * @author  Leon Stafford <me@ljs.dev>
 * @license The Unlicense
 * @link    https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class SitemapInvalidURLTest
 *
 * @package WordPressURLDetector
 */
class SitemapInvalidURLTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testInvalidURL( $url )
    {
        $this->expectException('WordPressURLDetector\Exception');
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
