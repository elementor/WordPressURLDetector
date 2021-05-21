<?php

/**
 * FilesHelper.php
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
 * Helper methods for working with a WordPress site's filesystem
 */
class FilesHelper
{

    /**
     * Get public URLs for all files in a local directory.
     *
     * @return array<string> list of relative, urlencoded URLs
     */
    public static function getListOfLocalFilesByDir( string $dir ): array
    {
        $files = [];

        // TODO: move to arg
        $sitePath = SiteInfo::getPath('site');

        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $dir,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach (array_keys(iterator_to_array($iterator)) as $filename) {
                if (! is_string($filename)) {
                    continue;
                }

                $pathCrawlable = self::filePathLooksCrawlable($filename);

                if (!$pathCrawlable) {
                    continue;
                }

                $url = str_replace($sitePath, '/', $filename);

                $files[] = $url;
            }
        }

        return $files;
    }

    /**
     * Ensure a given filepath has an allowed filename and extension.
     *
     * @return bool  True if the given file does not have a disallowed filename
     *               or extension.
     */
    public static function filePathLooksCrawlable( string $filename ): bool
    {
        // TODO: get from DetectorConfig
        $filenamesToIgnore = [];

        $filenameMatches = 0;

        str_ireplace($filenamesToIgnore, '', $filename, $filenameMatches);

        // If we found matches we don't need to go any further
        if ($filenameMatches) {
            return false;
        }

        /*
          Prepare the file extension list for regex:
          - Add prepending (escaped) \ for a literal . at the start of
            the file extension
          - Add $ at the end to match end of string
          - Add i modifier for case insensitivity
        */
        foreach ((new DetectorConfig())->fileExtensionIgnorePatterns as $extension) {
            if ((bool)preg_match("/\\{$extension}$/i", $filename)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clean all detected URLs before use. Accepts relative and absolute URLs
     * both with and without starting or trailing slashes.
     *
     * @param array<string> $urls list of absolute or relative URLs
     * @return array<string> list of relative URLs
     */
    // TODO: use thephpleague/uri to simplify
    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public static function cleanDetectedURLs( array $urls, string $homeURL ): array
    {
        return array_map(
            // trim hashes/query strings
            static function ( $url ) use ( $homeURL ): string {
                // NOTE: 2 x str_replace's significantly faster than
                // 1 x str_replace with search/replace arrays of 2 length
                $url = str_replace(
                    $homeURL,
                    '/',
                    $url
                );

                // TODO: this looks like a cause for malformed URLs http:/something
                // we should be looking for a host by parsing URL
                $url = str_replace(
                    '//',
                    '/',
                    $url
                );

                $url = strtok($url, '#');

                if (! is_string($url)) {
                    return '';
                }

                $url = strtok($url, '?');

                if (! is_string($url)) {
                    return '';
                }

                return $url;
            },
            $urls
        );
    }
}
