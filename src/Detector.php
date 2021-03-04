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

/**
 * Detects URLs in a WordPress site
 */
class Detector
{
    protected function __construct()
    {
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
                    $config->detectWPIncludesAssets ? DetectWPIncludesAssets::detect(
                        $siteInfo::getPath('includes'),
                        $siteInfo::getUrl('includes'),
                        $siteInfo::getUrl('home'),
                    ) : [],
                    $config->detectThirdPartyAssets ?  detectThirdPartyAssets::detect(
                            $siteInfo::getURL('site'),
                            $siteInfo::getPath('content'),
                            $siteInfo::getURL('content'),
                        ) : [],
                    $config->detectPostPagination ? DetectPostPagination::detect($siteInfo::getURL('site')) : [],
                    $config->detectArchive ? DetectArchive::detect() : [],
                    $config->detectCategories ? DetectCategories::detect() : [],
                    $config->detectCategoryPagination ? DetectCategoryPagination::detect() : [],
                    $config->detectAuthors ? DetectAuthors::detect() : [],
                    $config->detectAuthorPagination ? DetectAuthorPagination::detect($siteInfo::getUrl('site')) : [],
                ),
                $siteInfo::getUrl('home')
            )
        );
    }
}
