<?php

declare(strict_types=1);

namespace WordPressURLDetector;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FilesHelper
{

    /**
     * Get public URLs for all files in a local directory.
     *
     * @return array<string> list of relative, urlencoded URLs
     */
    public static function getListOfLocalFilesByDir( string $dir ): array
    {
        $files = [];

        $site_path = SiteInfo::getPath('site');

        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $dir,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach ($iterator as $filename => $file_object) {
                $path_crawlable = self::filePathLooksCrawlable($filename);

                if (!$path_crawlable) {
                    continue;
                }

                if (!is_string($site_path)) {
                    continue;
                }

                $url = str_replace($site_path, '/', $filename);

                if (!is_string($url)) {
                    continue;
                }

                $files[] = $url;
            }
        }

        return $files;
    }

    /**
     * Ensure a given filepath has an allowed filename and extension.
     *
     * @return bool  True if the given file does not have a disallowed filename
     *               or extension.
     */
    public static function filePathLooksCrawlable( string $file_name ): bool
    {
        $filenames_to_ignore = [
            '__MACOSX',
            '.babelrc',
            '.git',
            '.gitignore',
            '.gitkeep',
            '.htaccess',
            '.php',
            '.svn',
            '.travis.yml',
            'backwpup',
            'bower_components',
            'bower.json',
            'composer.json',
            'composer.lock',
            'config.rb',
            'current-export',
            'Dockerfile',
            'gulpfile.js',
            'latest-export',
            'LICENSE',
            'Makefile',
            'node_modules',
            'package.json',
            'pb_backupbuddy',
            'plugins/wp2static',
            'previous-export',
            'README',
            'static-html-output-plugin',
            '/tests/',
            'thumbs.db',
            'tinymce',
            'wc-logs',
            'wpallexport',
            'wpallimport',
            'wp-static-html-output', // exclude earlier version exports
            'wp2static-addon',
            'wp2static-crawled-site',
            'wp2static-processed-site',
            'wp2static-working-files',
            'yarn-error.log',
            'yarn.lock',
        ];

        $filenames_to_ignore =
            apply_filters(
                'wp2static_filenames_to_ignore',
                $filenames_to_ignore
            );

        $filename_matches = 0;

        str_ireplace($filenames_to_ignore, '', $file_name, $filename_matches);

        // If we found matches we don't need to go any further
        if ($filename_matches) {
            return false;
        }

        $file_extensions_to_ignore = [
            '.bat',
            '.crt',
            '.DS_Store',
            '.git',
            '.idea',
            '.ini',
            '.less',
            '.map',
            '.md',
            '.mo',
            '.php',
            '.PHP',
            '.phtml',
            '.po',
            '.pot',
            '.scss',
            '.sh',
            '.sql',
            '.SQL',
            '.tar.gz',
            '.tpl',
            '.txt',
            '.yarn',
            '.zip',
        ];

        $file_extensions_to_ignore =
            apply_filters(
                'wp2static_file_extensions_to_ignore',
                $file_extensions_to_ignore
            );

        /*
          Prepare the file extension list for regex:
          - Add prepending (escaped) \ for a literal . at the start of
            the file extension
          - Add $ at the end to match end of string
          - Add i modifier for case insensitivity
        */
        foreach ($file_extensions_to_ignore as $extension) {
            if (preg_match("/\\{$extension}$/i", $file_name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clean all detected URLs before use. Accepts relative and absolute URLs
     * both with and without starting or trailing slashes.
     *
     * @param array<string> $urls list of absolute or relative URLs
     * @return array<string>|array<null> list of relative URLs
     * @throws \WordPressURLDetector\WordPressURLDetectorException
     */
    public static function cleanDetectedURLs( array $urls ): array
    {
        $home_url = SiteInfo::getUrl('home');

        if (! is_string($home_url)) {
            $err = 'Home URL not defined ';
            WsLog::l($err);
            throw new \WordPressURLDetector\WordPressURLDetectorException($err);
        }

        $cleaned_urls = array_map(
            // trim hashes/query strings
            static function ( $url ) use ( $home_url ) {
                if (! $url) {
                    return;
                }

                // NOTE: 2 x str_replace's significantly faster than
                // 1 x str_replace with search/replace arrays of 2 length
                $url = str_replace(
                    $home_url,
                    '/',
                    $url
                );

                // TODO: this looks like a cause for malformed URLs http:/something
                // we should be looking for a host by parsing URL
                $url = str_replace(
                    '//',
                    '/',
                    $url
                );

                if (! is_string($url)) {
                    return;
                }

                $url = strtok($url, '#');

                if (! $url) {
                    return;
                }

                $url = strtok($url, '?');

                if (! $url) {
                    return;
                }

                return $url;
            },
            $urls
        );

        if (empty($cleaned_urls)) {
            $err = 'No valid URLs left after cleaning';
            WsLog::l($err);
            return [];
        }

        return $cleaned_urls;
    }
}
