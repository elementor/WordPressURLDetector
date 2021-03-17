<?php

/**
 * DetectPostPagination.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectPostPagination
 *
 * @package WordPressURLDetector
 */
class DetectPostPagination
{

    /**
     * Detect Post pagination URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect( string $wpSiteURL ): array
    {
        $postTypes = WPDB::uniquePublishedPostTypes();
        $paginationBase = SiteInfo::getPaginationBase();
        $defaultPostsPerPage = get_option('posts_per_page');

        $urlsToInclude = [];

        // TODO: should be combined into above query
        foreach ($postTypes as $postType) {
            $postTypeTotal = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM %s WHERE post_status = 'publish' AND post_type = %s",
                    $wpdb->posts,
                    $postType
                )
            );

            if (! $postTypeTotal) {
                continue;
            }

            $postTypeObj = get_post_type_object($postType);

            if (! $postTypeObj) {
                continue;
            }

            // cast WP's object back to array
            $postTypeLabels = (array)$postTypeObj->labels;

            $pluralForm = strtolower($postTypeLabels['name']);

            // skip post type names containing spaces
            if (strpos($pluralForm, ' ') !== false) {
                continue;
            }

            $totalPages = ceil($postTypeTotal / $defaultPostsPerPage);

            for ($page = 1; $page <= $totalPages; $page += 1) {
                // TODO: skipping page pagination here, but is it covered elsewhere?
                if ($postType === 'page') {
                    continue;
                }

                if ($postType === 'post') {
                    $postArchiveSlug = '';

                    // check if a Posts page has been set in Settings > Reading
                    if (get_option('page_for_posts') !== '0') {
                        // get FQURL to Posts Page
                        $postArchiveLink = get_post_type_archive_link('post');

                        if ($postArchiveLink) {
                            $postArchiveSlug = str_replace(
                                $wpSiteURL,
                                '',
                                trailingslashit($postArchiveLink)
                            );
                        }
                    }

                    $urlsToInclude[] = "/{$postArchiveSlug}{$paginationBase}/{$page}/";
                } else {
                    $urlsToInclude[] =
                        "/{$pluralForm}/{$paginationBase}/{$page}/";
                }
            }
        }

        return $urlsToInclude;
    }
}
