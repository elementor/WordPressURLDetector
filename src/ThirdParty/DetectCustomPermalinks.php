<?php

/**
 * DetectCustomPermalinks.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector\ThirdParty;

/**
 * Class DetectCustomPermalinks
 *
 * @package WordPressURLDetector
 */
class DetectCustomPermalinks
{

    /**
     * Detect URLs from the Custom Permalinks Plugin
     * https://wordpress.org/plugins/custom-permalinks/
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL ): array
    {
        if (! class_exists('Custom_Permalinks')) {
            return [];
        }

        global $wpdb;

        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_value AS metaValue
                 FROM %s
                 WHERE meta_key = 'custom_permalink'
                ",
                $wpdb->postmeta,
            )
        );

        if (! $posts) {
            return [];
        }

        return array_map(
            static function ($post) use ($wpSiteURL) {
                return $wpSiteURL . $post->metaValue;
            },
            $posts
        );
    }
}
