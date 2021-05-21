<?php

/**
 * DetectCustomPostTypeURLsTest.php
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
 * Class DetectCustomPostTypeURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectCustomPostTypeURLsTest extends \PHPUnit\Framework\TestCase
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
        \WP_Mock::userFunction(
            'get_post_permalink',
            [
                'times' => 1,
                'args' => [ 1 ],
                'return' => "{$siteURL}?post_type=attachment&p=1/",
            ]
        );
        \WP_Mock::userFunction(
            'get_post_permalink',
            [
                'times' => 1,
                'args' => [ 2 ],
                'return' => "{$siteURL}custom-post-type/foo/",
            ]
        );
        \WP_Mock::userFunction(
            'get_post_permalink',
            [
                'times' => 1,
                'args' => [ 3 ],
                'return' => "{$siteURL}2020/10/08/bar/",
            ]
        );

        // the attachment should not skipped
        $expected = [
            "{$siteURL}custom-post-type/foo/",
            "{$siteURL}2020/10/08/bar/",
        ];
        $actual = DetectCustomPostTypeURLs::detect();
        $this->assertEquals($expected, $actual);
    }
}
