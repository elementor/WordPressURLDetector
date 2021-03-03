<?php

/**
 * DetectVendorFiles.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectVendorFiles
 *
 * @package WordPressURLDetector
 */
class DetectVendorFiles
{

    /**
     * Detect vendor URLs from filesystem
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL ): array
    {
        $vendorFiles = [];

        // cache dir used by Autoptimize and other themes/plugins
        $vendorCacheDir =
            SiteInfo::getPath('content') . 'cache/';

        if (is_dir($vendorCacheDir)) {
            $siteURL = SiteInfo::getUrl('site');
            $contentURL = SiteInfo::getUrl('content');

            // get difference between home and wp-contents URL
            $prefix = str_replace(
                $siteURL,
                '/',
                $contentURL
            );

            $vendorCacheURLs = DetectVendorCache::detect(
                $vendorCacheDir,
                SiteInfo::getPath('content'),
                $prefix
            );

            $vendorFiles = array_merge($vendorFiles, $vendorCacheURLs);
        }

        // TODO: move into own detector class
        if (class_exists('Custom_Permalinks')) {
            global $wpdb;

            $query = "
                SELECT meta_value AS metaValue
                FROM %s
                WHERE meta_key = '%s'
                ";

            $customPermalinks = [];

            $posts = $wpdb->get_results(
                sprintf(
                    $query,
                    $wpdb->postmeta,
                    'custom_permalink'
                )
            );

            if ($posts) {
                foreach ($posts as $post) {
                    $customPermalinks[] = $wpSiteURL . $post->metaValue;
                }

                $vendorFiles = array_merge(
                    $vendorFiles,
                    $customPermalinks
                );
            }
        }

        return $vendorFiles;
    }
}
