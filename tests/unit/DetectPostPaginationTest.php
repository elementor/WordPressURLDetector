<?php

declare(strict_types=1);

namespace WordPressURLDetector;

use Mockery;

final class DetectPostPaginationTest extends \PHPUnit\Framework\TestCase
{

    public function testDetectWithoutPostsPage()
    {
        // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        $wpdb = Mockery::mock('\WordPressURLDetector\WPDB');
        $wpdb->shouldreceive('uniquePublishedPostTypes')
            ->once()
            ->andReturn([
            'post',
            'page',
            'attachment',
            'mycustomtype',
            'nonexistant',
            'noobjecttype',
            'spacednametype',
        ]);

        $wpdb->shouldreceive('totalPublishedForPostType')
            ->once()
            ->andReturn(10);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'post'")
            ->once()
            ->andReturn(15);

        \WP_Mock::userFunction(
            'get_post_type_object',
            [
                'post' => (object)[ 'labels' => [ 'name' => 'Posts' ] ],
                'page' => (object)[ 'labels' => [ 'name' => 'Pages' ] ],
                'attachment' => (object)[ 'labels' => [ 'name' => 'Attachments' ] ],
                'mycustomtype' => (object)[ 'labels' => [ 'name' => 'MyCustomTypes' ] ],
                'nonexistant' => null,
                'noobjecttype' => null,
                'spacednametype' => (object)[ 'labels' => [ 'name' => 'With Space' ] ],
            ]
        );

        \WP_Mock::userFunction(
            'get_option',
            [
                'times' => 1,
                'args' => 'page_for_posts',
                'return' => '0',
            ]
        );

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'page'")
            ->once()
            ->andReturn(9);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'attachment'")
            ->once()
            ->andReturn(13);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'mycustomtype'")
            ->once()
            ->andReturn(21);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'nonexistant'")
            ->once()
            ->andReturn(null);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'noobjecttype'")
            ->once()
            ->andReturn(1);

        $wpdb->shouldReceive('get_var')
            ->with("SELECT COUNT(*) FROM wp_posts WHERE" .
            " post_status = 'publish' AND post_type = 'spacednametype'")
            ->once()
            ->andReturn(1);

        $expected = [
            '/page/1/',
            '/page/2/',
            '/page/3/',
            '/page/4/',
            '/page/5/',
            '/attachments/page/1/',
            '/attachments/page/2/',
            '/attachments/page/3/',
            '/attachments/page/4/',
            '/attachments/page/5/',
            '/mycustomtype/page/1/',
            '/mycustomtype/page/2/',
            '/mycustomtype/page/3/',
            '/mycustomtype/page/4/',
            '/mycustomtype/page/5/',
            '/mycustomtype/page/6/',
            '/mycustomtype/page/7/',
        ];


        $actual = DetectPostPagination::detect('https://foo.com/', $wpdb, 'page', 3);
        $this->assertEquals($expected, $actual);
    }

    //public function testDetectWithPostsPage()
    //{
    //    global $wpdb;
    //    // Set the WordPress pagination base
    //    global $wp_rewrite;
    //    $site_url = 'https://foo.com/';

    //    // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    //    $wp_rewrite = (object)[ 'pagination_base' => 'page' ];

    //    // Create 1 post object
    //    // @phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    //    $wpdb = Mockery::mock('\WPDB');
    //    // set table name
    //    $wpdb->posts = 'wp_posts';
    //    $query_string = "
    //        SELECT ID,post_type
    //        FROM $wpdb->posts
    //        WHERE post_status = 'publish'
    //        AND post_type NOT IN ('revision','nav_menu_item')";

    //    $posts = [
    //        (object)[
    //            'ID' => '1',
    //            'post_type' => 'post',
    //        ],
    //    ];

    //    $wpdb->shouldReceive('get_results')
    //        ->with($query_string)
    //        ->once()
    //        ->andReturn($posts);

    //    // Set pagination to 3 posts per page
    //    \WP_Mock::userFunction(
    //        'get_option',
    //        [
    //            'times' => 1,
    //            'args' => [ 'posts_per_page' ],
    //            'return' => 3,
    //        ]
    //    );

    //    $posts_query = "SELECT COUNT(*) FROM $wpdb->posts WHERE" .
    //        " post_status = 'publish' AND post_type = 'post'";

    //    $wpdb->shouldReceive('get_var')
    //        ->with($posts_query)
    //        ->once()
    //        ->andReturn(15);

    //    $post_type_object = (object)[ 'labels' => [ 'name' => 'Posts' ] ];

    //    \WP_Mock::userFunction(
    //        'get_post_type_object',
    //        [
    //            'times' => 1,
    //            'args' => 'post',
    //            'return' => $post_type_object,
    //        ]
    //    );

    //    \WP_Mock::userFunction(
    //        'get_option',
    //        [
    //            'times' => 5,
    //            'args' => 'page_for_posts',
    //            'return' => '10',
    //        ]
    //    );

    //    \WP_Mock::userFunction(
    //        'get_post_type_archive_link',
    //        [
    //            'times' => 5,
    //            'args' => 'post',
    //            'return' => $site_url . 'blog',
    //        ]
    //    );

    //    $expected = [
    //        '/blog/page/1/',
    //        '/blog/page/2/',
    //        '/blog/page/3/',
    //        '/blog/page/4/',
    //        '/blog/page/5/',
    //    ];
    //    // getting '/blog//page/1/'...

    //    $actual = DetectPostPagination::detect($site_url);
    //    $this->assertEquals($expected, $actual);
    //}
}
