<?php

/**
 * WPDB.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class WPDB
 *
 * Handles querying the WordPress database
 *
 * Methods should do minimal transformation outside of MySQL
 *
 * @package WordPressURLDetector
 */
class WPDB
{

    /**
     * Detect published post types
     *
     * @return array<string> list of post types
     */
    public function uniquePublishedPostTypes(): array
    {
        global $wpdb;

        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID,post_type AS postType
                 FROM %s
                 WHERE post_status = 'publish'
                 AND post_type NOT IN ('revision','nav_menu_item')",
                $wpdb->posts,
            )
        );

        return array_unique(
            array_map(
                static function ($post) {
                        return $post->postType;
                },
                $posts
            )
        );
    }

    public function totalPublishedForPostType( string $postType ): int
    {
        global $wpdb;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM %s WHERE post_status = 'publish' AND post_type = %s",
                $wpdb->posts,
                $postType
            )
        );
    }
}
