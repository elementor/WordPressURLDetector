<?php

/**
 * DetectPluginAssets.php
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
 * Class DetectPluginAssets
 *
 * @package WordPressURLDetector
 */
class DetectPluginAssets
{

    /**
     * Detect Plugin asset URLs
     *
     * @return array<string> list of URLs
     */
    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public static function detect(): array
    {
        $files = [];

        $pluginsPath = SiteInfo::getPath('plugins');
        $pluginsURL = SiteInfo::getURL('plugins');

        if (is_dir($pluginsPath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $pluginsPath,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            $activePlugins = get_option('active_plugins');

            if (is_multisite()) {
                $activeSitewidePlugins = get_site_option('active_sitewide_plugins');
                $activeSitewidePlugins = array_keys($activeSitewidePlugins);

                $activePlugins = array_unique(array_merge($activePlugins, $activeSitewidePlugins));
            }

            $activePluginDirs = array_map(
                static function ($activePlugin): string {
                    return explode('/', $activePlugin)[0];
                },
                $activePlugins
            );

            foreach (array_keys(iterator_to_array($iterator)) as $filename) {
                $pathCrawlable =
                    FilesHelper::filePathLooksCrawlable($filename);

                if (! $pathCrawlable) {
                    continue;
                }

                $matchesActivePluginDir =
                    ( str_replace($activePluginDirs, '', $filename) !== $filename );

                if (! $matchesActivePluginDir) {
                    continue;
                }

                // Standardise all paths to use / (Windows support)
                $filename = str_replace('\\', '/', $filename);

                $detectedFilename =
                    str_replace(
                        $pluginsPath,
                        $pluginsURL,
                        $filename
                    );

                $detectedFilename =
                    str_replace(
                        get_home_url(),
                        '',
                        $detectedFilename
                    );

                array_push(
                    $files,
                    $detectedFilename
                );
            }
        }

        return $files;
    }
}
