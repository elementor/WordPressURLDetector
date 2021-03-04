<?php

/**
 * DetectorConfig.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Configurable options for the Detector
 */
class DetectorConfig
{

    public bool $detectArchiveURLs = true;
    public bool $detectAuthorPaginationURLs = true;
    public bool $detectAuthorsURLs = true;
    public bool $detectCategoryPaginationURLs = true;
    public bool $detectCategoryURLs = true;
    public bool $detectChildThemeAssets = true;
    public bool $detectCustomPostTypeURLs = true;
    public bool $detectPageURLs = true;
    public bool $detectParentThemeAssets = true;
    public bool $detectPluginAssets = true;
    public bool $detectPostURLs = true;
    public bool $detectPostsPaginationURLs = true;
    public bool $detectSitemapsURLs = true;
    public bool $detectThirdPartyAssets = true;
    public bool $detectWPIncludesAssets = true;

    public array $filenameIgnorePatterns = [
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
        'LICENSE',
        'Makefile',
        'node_modules',
        'package.json',
        'pb_backupbuddy',
        'README',
        '/tests/',
        'thumbs.db',
        'tinymce',
        'wc-logs',
        'wpallexport',
        'wpallimport',
        'yarn-error.log',
        'yarn.lock',
    ];

    public array $fileExtensionIgnorePatterns = [
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

    protected function __construct()
    {
    }
}
