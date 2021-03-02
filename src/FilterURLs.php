<?php

declare(strict_types=1);

namespace WordPressURLDetector;

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
     * @return array<string>|array<null> list of relative URLs
     * @throws \WordPressURLDetector\WordPressURLDetectorException
     */
    public static function filter( array $urls, string $home_url ): array
    {
        if ($home_url === '') {
            // TODO: parse URL for validity
            $err = 'Home URL not defined ';
            throw new \WordPressURLDetector\WordPressURLDetectorException($err);
        }

        $cleaned_urls = array_map(
            // trim hashes/query strings
            static function ( $url ) use ( $home_url ) {
                if (! $url) {
                    return;
                }

                // NOTE: 2 x str_replace's significantly faster than
                // 1 x str_replace with search/replace arrays of 2 length
                $url = str_replace(
                    $home_url,
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

                if (! is_string($url)) {
                    return;
                }

                $url = strtok($url, '#');

                if (! $url) {
                    return;
                }

                $url = strtok($url, '?');

                if (! $url) {
                    return;
                }

                return $url;
            },
            $urls
        );

        if (empty($cleaned_urls)) {
            return [];
        }

        return $cleaned_urls;
    }
}
