<?php

/**
 * DetectThirdPartyAssets.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectThirdPartyAssets
 *
 * @package WordPressURLDetector
 */
class DetectThirdPartyAssets
{

    /**
     * Detect vendor URLs from filesystem
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL, string $wpContentPath, string $wpContentURL ): array
    {
        return array_merge(
            ThirdParty\DetectCommonCache::detect($wpSiteURL, $wpContentPath, $wpContentURL),
            ThirdParty\DetectCustomPermalinks::detect($wpSiteURL),
        );
    }
}
