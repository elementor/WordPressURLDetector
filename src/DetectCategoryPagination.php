<?php

/**
 * DetectCategoryPagination.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectCategoryPagination
 *
 * @package WordPressURLDetector
 */
class DetectCategoryPagination
{

    /**
     * Detect Category Pagination URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        global $wp_rewrite, $wpdb;

        // first we get each category with total posts as an array
        // similar to getting regular category URLs, but with extra
        // info we need to get correct pagination URLs
        $args = [ 'public' => true ];

        $categoryLinks = [];
        $urlsToInclude = [];
        $taxonomies = get_taxonomies($args, 'objects');
        $paginationBase = $wp_rewrite->pagination_base;
        $postsPerPage = get_option('posts_per_page');

        foreach ($taxonomies as $taxonomy) {
            /** @var list<\WP_Term> $terms */
            $terms = get_terms(
                $taxonomy->name,
                [ 'hide_empty' => true ]
            );

            foreach ($terms as $term) {
                $termLink = get_term_link($term);

                if (! is_string($termLink)) {
                    continue;
                }

                $categoryLinks[trim($termLink)] = $term->count;
            }
        }

        foreach ($categoryLinks as $term => $totalPosts) {
            $totalPages = ceil($totalPosts / $postsPerPage);

            for ($page = 1; $page <= $totalPages; $page += 1) {
                $urlsToInclude[] =
                    "{$term}{$paginationBase}/{$page}/";
            }
        }

        return $urlsToInclude;
    }
}
