<?php

declare(strict_types=1);

namespace WordPressURLDetector;

class DetectAuthorsURLs
{

    /**
     * Detect Authors URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        global $wp_rewrite, $wpdb;

        $authors_urls = [];
        $users = get_users();

        foreach ($users as $author) {
            $author_link = get_author_posts_url($author->ID);

            if (! is_string($author_link)) {
                continue;
            }

            $permalink = trim($author_link);

            $authors_urls[] = $permalink;
        }

        return $authors_urls;
    }
}
