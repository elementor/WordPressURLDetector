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
     * @throw Exception
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
                static function ($filename) use ($homeURL, $includesPath, $includesURL): string {
                    if (! is_string($filename)) {
                        return '';
                    }

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

                    return '/' . $detectedFilename;
                },
                array_keys(iterator_to_array($iterator))
            )
        );
    }
}
