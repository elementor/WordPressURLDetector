<?php

/**
 * DetectPageURLsTest.php
 *
 * @package WordPressURLDetector
 * @author  Leon Stafford <me@ljs.dev>
 * @license The Unlicense
 * @link    https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

use Mockery;

/**
 * Class DetectPageURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectPageURLsTest extends \PHPUnit\Framework\TestCase
{

    public function testDetect()
    {
        global $wpdb;
        $siteURL = 'https://foo.com/';

        // Create 3 attachments
        // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        $wpdb = Mockery::mock('\WPDB');
        $wpdb->shouldReceive('get_col')
            ->once()
            ->andReturn([ 1, 2, 3 ]);
        $wpdb->posts = 'wp_posts';

        // And URLs for them
        for ($i = 1; $i <= 3; $i += 1) {
            \WP_Mock::userFunction(
                'get_page_link',
                [
                    'times' => 1,
                    'args' => [ $i ],
                    'return' => "{$siteURL}page/$i/",
                ]
            );
        }

        $expected = [
            "{$siteURL}page/1/",
            "{$siteURL}page/2/",
            "{$siteURL}page/3/",
        ];
        $actual = DetectPageURLs::detect();
        $this->assertEquals($expected, $actual);
    }
}
