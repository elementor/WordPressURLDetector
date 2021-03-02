<?php

namespace WordPressURLDetector;

use PHPUnit\Framework\TestCase;

class SitemapInvalidURLTest extends TestCase {

    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testInvalidURL( $url ) {
        $this->expectException( 'WordPressURLDetector\WordPressURLDetectorException' );
        $parser = new SitemapParser( 'SitemapParser' );
        $this->assertInstanceOf( 'WordPressURLDetector\SitemapParser', $parser );
        $parser->parse( $url );
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest() {
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
