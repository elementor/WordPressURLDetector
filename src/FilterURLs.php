<?php

/**
 * FilterURLs.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class FilterURLs
 *
 * @package WordPressURLDetector
 */
class FilterURLs
{

    /**
     * Filters detected URLs
     *
     *   - forces site root relative
     *   - removes any trailing hashes or query strings (#. or ?.)
     *   - removes empty strings
     *
     * @param array<string> $urls list of absolute or relative URLs
     * @return array<string> list of relative URLs
     */
    public static function filter( array $urls, string $homeURL ): array
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
