<?php

/**
 * DetectAuthorPagination.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects Author pagination URLs
 */
class DetectAuthorPagination
{

    /**
     * Detect Author Pagination URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL ): array
    {
        $public = true;
        $authorURLs = [];
        $urlsToInclude = [];
        $users = get_users();
        $paginationBase = SiteInfo::getPaginationBase();
        $defaultPostsPerPage = get_option('posts_per_page');

        foreach ($users as $author) {
            $authorLink = get_author_posts_url($author->ID);

            $authorURL = str_replace(
                $wpSiteURL,
                '',
                trim($authorLink)
            );

            $authorURLs[$authorURL] = count_user_posts($author->ID, 'post', $public);
        }

        foreach ($authorURLs as $author => $totalPosts) {
            $totalPages = ceil($totalPosts / $defaultPostsPerPage);

            for ($page = 1; $page <= $totalPages; $page += 1) {
                $urlsToInclude[] =
                    "/{$author}{$paginationBase}/{$page}/";
            }
        }

        return $urlsToInclude;
    }
}
