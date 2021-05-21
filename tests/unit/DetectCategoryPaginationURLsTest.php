<?php

/**
 * DetectArchiveURLsTest.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectCategoryPaginationURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectCategoryPaginationURLsTest extends \PHPUnit\Framework\TestCase
{

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testDetect()
    {
        $siteURL = 'https://foo.com/';
        $taxonomies = [
            (object)[ 'name' => 'category' ],
            (object)[ 'name' => 'post_tag' ],
        ];
        $terms = [
            'category' => [
                'category1' => (object)[
                    'name' => 'category1',
                    'count' => 1,
                ],
                'category2' => (object)[
                    'name' => 'category2',
                    'count' => 3,
                ],
                'category3' => (object)[
                    'name' => 'category3',
                    'count' => 4,
                ],
                'category4' => (object)[
                    'name' => 'category4',
                    'count' => 7,
                ],
            ],
            'post_tag' => [
                'post_tag1' => (object)[
                    'name' => 'post_tag1',
                    'count' => 14,
                ],
            ],
        ];
        $termLinkss = [
            'category1' => "{$siteURL}category/1",
            'category2' => "{$siteURL}category/2",
            'category3' => "{$siteURL}category/3",
            // empty term link should be skipped
            'category4' => null,
            'post_tag1' => "{$siteURL}tags/foo/bar",
        ];

        // Set the WordPress pagination base
        // @phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_rewrite;
        // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        // @phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
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
        // Set up our custom taxonomies
        \WP_Mock::userFunction(
            'get_taxonomies',
            [
                'times' => 1,
                'args' => [
                    [ 'public' => true ],
                    'objects',
                ],
                'return' => $taxonomies,
            ]
        );
        foreach ($taxonomies as $taxonomy) {
            // And the terms within those taxonomies
            \WP_Mock::userFunction(
                'get_terms',
                [
                    'times' => 1,
                    'args' => [
                        $taxonomy->name,
                        [ 'hide_empty' => true ],
                    ],
                    'return' => $terms[$taxonomy->name],
                ]
            );

            // ...and the links for those terms
            foreach ($terms[$taxonomy->name] as $term) {
                \WP_Mock::userFunction(
                    'get_term_link',
                    [
                        'times' => 1,
                        'args' => [ $term ],
                        'return' => $termLinkss[$term->name],
                    ]
                );
            }
        }

        $expected = [
            "{$siteURL}category/1/page/1/",
            "{$siteURL}category/2/page/1/",
            "{$siteURL}category/3/page/1/",
            "{$siteURL}category/3/page/2/",
            "{$siteURL}tags/foo/bar/page/1/",
            "{$siteURL}tags/foo/bar/page/2/",
            "{$siteURL}tags/foo/bar/page/3/",
            "{$siteURL}tags/foo/bar/page/4/",
            "{$siteURL}tags/foo/bar/page/5/",
        ];
        $actual = DetectCategoryPaginationURLs::detect();
        $this->assertEquals($expected, $actual);
    }
}
