<?php

/**
 * DetectPosts.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects Post URLs
 */
class DetectPosts
{

    /**
     * Detect Post URLs
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
            AND post_type = 'post'"
        );

        foreach ($postIDs as $postID) {
            $permalink = get_permalink($postID);

            if ($permalink === false) {
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
