<?php

/**
 * DetectAuthorsURLsTest.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectAuthorsURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectAuthorsURLsTest extends \PHPUnit\Framework\TestCase
{

    public function testDetect()
    {
        $siteURL = 'https://foo.com/';
        $users = [];

        // Create some virtual users
        for ($i = 1; $i <= 3; $i += 1) {
            // Add the user
            $users[] = (object)[ 'ID' => $i ];

            // Create an author URL for this user
            \WP_Mock::userFunction(
                'get_author_posts_url',
                [
                    'times' => 1,
                    'args' => [ $i ],
                    'return' => "{$siteURL}users/{$i}",
                ]
            );
        }

        // create user missing author URL
        $users[] = (object)[ 'ID' => 4 ];
        \WP_Mock::userFunction(
            'get_author_posts_url',
            [
                'times' => 1,
                'args' => [ 4 ],
                'return' => null,
            ]
        );

        \WP_Mock::userFunction(
            'get_users',
            [
                'times' => 1,
                'return' => $users,
            ]
        );

        $expected = [
            "{$siteURL}users/1",
            "{$siteURL}users/2",
            "{$siteURL}users/3",
        ];
        $actual = DetectAuthorsURLs::detect();
        $this->assertEquals($expected, $actual);
    }
}
