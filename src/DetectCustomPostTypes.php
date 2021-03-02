<?php

/**
 * DetectCustomPostTypes.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects custom post type URLs
 */
class DetectCustomPostTypes
{

    /**
     * Detect Custom Post Type URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        global $wpdb;

        $postURLs = [];

        $postIDs = $wpdb->get_col(
            "SELECT ID
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
            AND post_type NOT IN ('revision','nav_menu_item')"
        );

        foreach ($postIDs as $postID) {
            $permalink = get_post_permalink($postID);

            if (! is_string($permalink)) {
                continue;
            }

            if (strpos($permalink, '?post_type') !== false) {
                continue;
            }

            $postURLs[] = $permalink;
        }

        return $postURLs;
    }
}
