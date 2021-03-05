<?php

/**
 * DetectCategories.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Detects Category URLs
 */
class DetectCategories
{

    /**
     * Detect Category URLs
     *
     * @return array<string> list of URLs
     */
    public static function detect(): array
    {
        $args = [ 'public' => true ];

        $taxonomies = get_taxonomies($args, 'objects');

        $categoryURLs = [];

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

                $permalink = trim($termLink);

                $categoryURLs[] = $permalink;
            }
        }

        return $categoryURLs;
    }
}
