<?php

/**
 * DetectAuthors.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects Author URLs
 */
class DetectAuthors
{

    /**
     * Detect Authors URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        $authorURLs = [];
        $authors = get_users();

        foreach ($authors as $author) {
            $authorLink = get_author_posts_url($author->ID);

            // TODO: check for valid URL here, a bad filter could return anything
            if (! is_string($authorLink)) {
                continue;
            }

            $authorURLs[] = trim($authorLink);
        }

        return $authorURLs;
    }
}
