<?php

/**
 * DetectCommonCache.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector\ThirdParty;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class DetectVendorFiles
 *
 * @package WordPressURLDetector
 */
class DetectCommonCache
{

    /**
     * Detect vendor cache URLs from filesystem
     * commonly used by Autoptimize and other plugins/themes
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL, string $wpContentPath, string $wpContentURL ): array
    {
        // cache dir used by Autoptimize and other themes/plugins
        $vendorCacheDir = $wpContentPath . 'cache/';

        if (! is_dir($vendorCacheDir)) {
            return [];
        }

        // get difference between home and wp-contents URL
        $prefix = str_replace($wpSiteURL, '/', $wpContentURL);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $vendorCacheDir,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        $files = [];

        foreach (array_keys($iterator) as $filename) {
            $pathCrawlable = FilesHelper::filePathLooksCrawlable($filename);

            // Standardise all paths to use / (Windows support)
            $filename = str_replace('\\', '/', $filename);

            if (! is_string($filename)) {
                continue;
            }

            if (!$pathCrawlable) {
                continue;
            }

            array_push(
                $files,
                $prefix .
                home_url(str_replace($wpContentPath, '', $filename))
            );
        }

        return $files;
    }
}
