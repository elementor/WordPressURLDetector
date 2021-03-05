<?php

/**
 * DetectCommon.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects common WordPress site URLs
 */
class DetectCommon
{

    /**
     * Detect Post URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        return [
            '/',
            '/robots.txt',
            '/favicon.ico',
        ];
    }
}
