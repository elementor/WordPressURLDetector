<?php

/**
 * DetectAuthorPaginationURLsTest.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectAuthorPaginationURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectAuthorPaginationURLsTest extends \PHPUnit\Framework\TestCase
{

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testDetect()
    {
        $siteURL = 'https://foo.com/';

        // Set the WordPress pagination base
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_rewrite;
        // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        $wp_rewrite = (object)[ 'pagination_base' => '/page' ];

        // Set pagination to 3 posts per page
        \WP_Mock::userFunction(
            'get_option',
            [
                'times' => 1,
                'args' => [ 'posts_per_page' ],
                'return' => 3,
            ]
        );
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

            \WP_Mock::userFunction(
                'count_user_posts',
                [
                    'times' => 1,
                    'args' => [ $i, 'post', true ],
                    'return' => '10',
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
            'count_user_posts',
            [
                'times' => 1,
                'args' => [ 4, 'post', true ],
                'return' => '10',
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
            '/users/1/page/1/',
            '/users/1/page/2/',
            '/users/1/page/3/',
            '/users/1/page/4/',
            '/users/2/page/1/',
            '/users/2/page/2/',
            '/users/2/page/3/',
            '/users/2/page/4/',
            '/users/3/page/1/',
            '/users/3/page/2/',
            '/users/3/page/3/',
            '/users/3/page/4/',
        ];

        $actual = DetectAuthorPaginationURLs::detect($siteURL);
        $this->assertEquals($expected, $actual);
    }
}
