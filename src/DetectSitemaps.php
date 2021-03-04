<?php

/**
 * DetectSitemaps.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

use WordPressURLDetectorGuzzleHttp\Client;
use WordPressURLDetectorGuzzleHttp\Psr7\Request;

/**
 * Detects Sitemap URLs in a WordPress site
 */
class DetectSitemaps
{

    /**
     * Detect Sitemaps
     *
     * @return array<string> list of URLs
     * @throws \WordPressURLDetector\WordPressURLDetectorException
     */
    public static function detect( string $wpSiteURL ): array
    {
        $sitemapsURLs = [];
        $parser = new SitemapParser('leonstafford/WordPressURLDetector', [ 'strict' => false ]);

        $sitePath = rtrim(SiteInfo::getURL('site'), '/');

        $portOverride = apply_filters(
            'wp2static_curl_port',
            null
        );

        $baseURI = $sitePath;

        if ($portOverride) {
            $baseURI = "{$baseURI}:{$portOverride}";
        }

        $client = new Client(
            [
                'base_uri' => $baseURI,
                'verify' => false,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 1,
                    // required to get effective_url
                    'track_redirects' => true,
                ],
                'connect_timeout' => 0,
                'timeout' => 600,
                'headers' => [
                    'User-Agent' => apply_filters(
                        'wp2static_curl_user_agent',
                        'WordPressURLDetector.com',
                    ),
                ],
            ]
        );

        $headers = [];

        $authUser = CoreOptions::getValue('basicAuthUser');

        if ($authUser) {
            $authPassword = CoreOptions::getValue('basicAuthPassword');

            if ($authPassword) {
                $headers['auth'] = [ $authUser, $authPassword ];
            }
        }

        $request = new Request('GET', '/robots.txt', $headers);

        $response = $client->send($request);

        $robotsExists = $response->getStatusCode() === 200;

        try {
            $sitemaps = [];

            // if robots exists, parse for possible sitemaps
            if ($robotsExists) {
                $parser->parseRecursive($wpSiteURL . 'robots.txt');
                $sitemaps = $parser->getSitemaps();
            }

            // if no sitemaps add known sitemaps
            if ($sitemaps === []) {
                $sitemaps = [
                    // we're assigning empty arrays to match sitemaps library
                    '/sitemap.xml' => [], // normal sitemap
                    '/sitemap_index.xml' => [], // yoast sitemap
                    '/wp_sitemap.xml' => [], // wp 5.5 sitemap
                ];
            }

            foreach (array_keys($sitemaps) as $sitemap) {
                if (! is_string($sitemap)) {
                    continue;
                }

                $request = new Request('GET', $sitemap, $headers);

                $response = $client->send($request);

                $statusCode = $response->getStatusCode();

                if ($statusCode !== 200) {
                    continue;
                }

                $parser->parse($wpSiteURL . $sitemap);

                $sitemapsURLs[] = '/' . str_replace(
                    $wpSiteURL,
                    '',
                    $sitemap
                );

                $extractSitemaps = $parser->getSitemaps();

                foreach (array_keys($extractSitemaps) as $url) {
                    $sitemapsURLs[] = '/' . str_replace(
                        $wpSiteURL,
                        '',
                        $url
                    );
                }
            }
        } catch (\WordPressURLDetector\WordPressURLDetectorException $e) {
            WsLog::l($e->getMessage());
            throw new \WordPressURLDetector\WordPressURLDetectorException($e->getMessage(), 0, $e);
        }

        return $sitemapsURLs;
    }
}
