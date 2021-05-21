<?php

/**
 * DetectPages.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects Page URLs
 */
class DetectPages
{

    /**
     * Detect Page URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        global $wpdb;

        $pageIDs = $wpdb->get_col(
            "SELECT ID
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
            AND post_type = 'page'"
        );

        return array_filter(
            array_map(
                static function ($pageID): string {
                    return get_page_link($pageID);
                },
                $pageIDs
            ),
            static function ($permalink): bool {
                return strpos($permalink, '?post_type') === false;
            }
        );
    }
}
