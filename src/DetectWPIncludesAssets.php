<?php

/**
 * DetectWPIncludesAssets.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Class DetectWPIncludesAssets
 *
 * @package WordPressURLDetector
 */
class DetectWPIncludesAssets
{

    /**
     * Detect assets within wp-includes path
     *
     * @return array<string> list of URLs
     * @throw WordPressURLDetectorException
     */
    public static function detect( string $includesPath, string $includesURL, string $homeURL ): array
    {
        if (! is_dir($includesPath)) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $includesPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        // return non-empty, rewritten URLs
        return array_filter(
            array_map(
                static function ($filename) use ($homeURL, $includesPath, $includesURL) {
                    $pathCrawlable =
                    FilesHelper::filePathLooksCrawlable($filename);

                    // TODO: adjust to array_filter vs abusing this
                    if (!$pathCrawlable) {
                        return '';
                    }

                    // TODO: dubious, use WP helper or RecursiveDirectoryIterator UNIX_PATHS
                    // Standardise all paths to use / (Windows support)
                    $filename = str_replace('\\', '/', $filename);

                    $detectedFilename = str_replace($homeURL, '', str_replace($includesPath, $includesURL, $filename));

                    // TODO: adjust to array_filter first vs abusing this
                    if (! is_string($detectedFilename) || !$pathCrawlable) {
                        return '';
                    }

                    return '/' . $detectedFilename;
                },
                array_keys($iterator)
            )
        );
    }
}
