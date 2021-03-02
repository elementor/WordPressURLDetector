<?php

declare(strict_types=1);

namespace WordPressURLDetector;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Detector
{
    public Logger $log;

    protected function __construct()
    {
        $this->log = new Logger('detector');
        $this->log->pushHandler(new StreamHandler(__DIR__.'/detector.log', Logger::DEBUG));
        $this->log->info('Instantiated Detector');
    }

    /**
     * Detect URLs within site
     *
     * @return string[] of URLs
     */
    public static function detectURLs(DetectorConfig $config, SiteInfo $siteInfo): array
    {
        $this->log->info('Starting to detect WordPress site URLs.');

        $arrays_to_merge = [];

        // TODO: detect robots.txt, etc before adding
        $arrays_to_merge[] = [
            '/',
            '/robots.txt',
            '/favicon.ico',
            '/sitemap.xml',
        ];

        if ($config->detectPosts) {
            $arrays_to_merge[] = DetectPostURLs::detect();
        }

        if ($config->detectPages) {
            $arrays_to_merge[] = DetectPageURLs::detect();
        }

        if ($config->detectCustomPostTypes) {
            $arrays_to_merge[] = DetectCustomPostTypeURLs::detect();
        }

        if ($config->detectUploads) {
            $arrays_to_merge[] =
                FilesHelper::getListOfLocalFilesByDir($siteInfo::getPath('uploads'));
        }

        if ($config->detectSitemaps) {
            $arrays_to_merge[] = DetectSitemapsURLs::detect($siteInfo::getURL('site'));
        }


        if ($config->detectParentThemeAssets) {
            $arrays_to_merge[] = DetectThemeAssets::detect('parent');
        }


        if ($config->detectChildThemeAssets) {
            $arrays_to_merge[] = DetectThemeAssets::detect('child');
        }


        if ($detect_plugin_assets) {
            $arrays_to_merge[] = DetectPluginAssets::detect();
        }


        if ($detect_wpinc_assets) {
            $arrays_to_merge[] = DetectWPIncludesAssets::detect();
        }


        if ($detect_vendor_cache) {
            $arrays_to_merge[] = DetectVendorFiles::detect($siteInfo::getURL('site'));
        }


        if ($detect_posts_pagination) {
            $arrays_to_merge[] = DetectPostsPaginationURLs::detect($siteInfo::getURL('site'));
        }


        if ($detect_archives) {
            $arrays_to_merge[] = DetectArchiveURLs::detect();
        }


        if ($detect_categories) {
            $arrays_to_merge[] = DetectCategoryURLs::detect();
        }


        if ($detect_category_pagination) {
            $arrays_to_merge[] = DetectCategoryPaginationURLs::detect();
        }


        if ($detect_authors) {
            $arrays_to_merge[] = DetectAuthorsURLs::detect();
        }


        if ($detect_authors_pagination) {
            $arrays_to_merge[] = DetectAuthorPaginationURLs::detect($siteInfo::getUrl('site'));
        }

        $url_queue = call_user_func_array('array_merge', $arrays_to_merge);

        $url_queue = FilesHelper::cleanDetectedURLs($url_queue);

        $unique_urls = array_unique($url_queue);

        CrawlQueue::addUrls($unique_urls);

        $total_detected = (string)count($unique_urls);

        $this->log->info(
            "Detection complete. $total_detected URLs added to Crawl Queue."
        );

        return $unique_urls;
    }
}
