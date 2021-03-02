<?php

/**
 * Detector.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Detects URLs in a WordPress site
 */
class Detector
{
    public Logger $log;

    protected function __construct()
    {
        $this->log = new Logger('detector');
        $this->log->pushHandler(new StreamHandler(__DIR__ . '/detector.log', Logger::DEBUG));
        $this->log->info('Instantiated Detector');
    }

    /**
     * Detect URLs within site
     *
     * @return array<string> of URLs
     */
    public function detectURLs(DetectorConfig $config, SiteInfo $siteInfo): array
    {
        $this->log->info('Starting to detect WordPress site URLs.');

        return array_unique(
            FilesHelper::cleanDetectedURLs(
                array_merge(
                    // TODO: move these into detectCommon or such
                    [
                        '/',
                        '/robots.txt',
                        '/favicon.ico',
                        '/sitemap.xml', // TODO: should be redundant with recent Sitemap detector
                    ],
                    $config->detectPosts ? DetectPosts::detect() : [],
                    $config->detectPages ? DetectPages::detect() : [],
                    $config->detectCustomPostTypes ? DetectCustomPostType::detect() : [],
                    $config->detectUploads ? FilesHelper::getListOfLocalFilesByDir($siteInfo::getPath('uploads')) : [],
                    $config->detectSitemaps ? DetectSitemaps::detect($siteInfo::getURL('site')) : [],
                    $config->detectParentThemeAssets ? DetectThemeAssets::detect('parent') : [],
                    $config->detectChildThemeAssets ? DetectThemeAssets::detect('child') : [],
                    $config->detectPluginAssets ? DetectPluginAssets::detect() : [],
                    $config->detectWPIncludesAssets ? DetectWPIncludesAssets::detect() : [],
                    $config->detectVendorFiles ? DetectVendorFiles::detect($siteInfo::getURL('site')) : [],
                    $config->detectPostPagination ? DetectPostPagination::detect($siteInfo::getURL('site')) : [],
                    $config->detectArchive ? DetectArchive::detect() : [],
                    $config->detectCategory ? DetectCategory::detect() : [],
                    $config->detectCategoryPagination ? DetectCategoryPagination::detect() : [],
                    $config->detectAuthors ? DetectAuthors::detect() : [],
                    $config->detectAuthorPagination ? DetectAuthorPagination::detect($siteInfo::getUrl('site')) : [],
                ),
                $siteInfo::getUrl('home')
            )
        );
    }
}
