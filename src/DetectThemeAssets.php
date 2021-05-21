<?php

/**
 * DetectThemeAssets.php
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
 * Detects assets from themes
 */
class DetectThemeAssets
{

    /**
     * Detect theme public URLs from filesystem
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $sitePath, string $templatePath ): array
    {
        $files = [];

        if (is_dir($templatePath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $templatePath,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach (array_keys(iterator_to_array($iterator)) as $filename) {
                if (! is_string($filename)) {
                    continue;
                }

                $pathCrawlable =
                    FilesHelper::filePathLooksCrawlable($filename);

                // Standardise all paths to use / (Windows support)
                $filename = str_replace('\\', '/', $filename);

                $detectedFilename =
                    str_replace(
                        $sitePath,
                        '/',
                        $filename
                    );

                if (!$pathCrawlable) {
                    continue;
                }

                array_push(
                    $files,
                    $detectedFilename
                );
            }
        }

        return $files;
    }
}
