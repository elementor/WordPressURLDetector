<?php

/**
 * SiteInfo.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
    Singleton instance to allow instantiating once and allow reading
    static properties throughout
*/
class SiteInfo
{

    /** @var \WordPressURLDetector\SiteInfo */
    private static $instance = null;

    /**
     * Site info.
     *
     * @var array<mixed>
     */
    private static $info = [];

    /**
     * Set site info using trailingslashed paths/URLs.
     *
     * @see https://codex.wordpress.org/Determining_Plugin_and_Content_Directories
     */
    public function __construct()
    {
        $uploadPathAndURL = wp_upload_dir();
        $siteURL = trailingslashit(site_url());

        // properties which should not change during plugin execution
        self::$info = [
            // Core
            'site_path' => ABSPATH,
            'site_url' => $siteURL,
            'home_url' => trailingslashit(get_home_url()),
            'includes_path' => trailingslashit(ABSPATH . WPINC),
            'includes_url' => includes_url(),

            /*
                TODO: Q on subdir:

                Does it matter?
                'subdirectory' => $this->isSiteInstalledInSubDirectory(),

                A: It shouldn't, but current mechanism for rewriting URLs
                has some cases that require knowledge of it...
            */

            // Content
            'content_path' => trailingslashit(WP_CONTENT_DIR),
            'content_url' => trailingslashit(content_url()),
            'uploads_path' =>
                trailingslashit($uploadPathAndURL['basedir']),
            'uploads_url' => trailingslashit($uploadPathAndURL['baseurl']),

            // Plugins
            'plugins_path' => trailingslashit(WP_PLUGIN_DIR),
            'plugins_url' => trailingslashit(plugins_url()),

            // Themes
            'themes_root_path' => trailingslashit(get_theme_root()),
            'themes_root_url' => trailingslashit(get_theme_root_uri()),
            'parent_theme_path' => trailingslashit(get_template_directory()),
            'parent_theme_url' =>
                trailingslashit(get_template_directory_uri()),
            'child_theme_path' => trailingslashit(get_stylesheet_directory()),
            'child_theme_url' =>
                trailingslashit(get_stylesheet_directory_uri()),
        ];
    }

    /**
     * Get Path via name
     *
     * @throws \WordPressURLDetector\Exception
     */
    public static function getPath( string $name ): string
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        // TODO: Move trailingslashit() here ???
        $key = $name . '_path';

        if (! array_key_exists($key, self::$info)) {
            $err = 'Attempted to access missing SiteInfo path';
            WsLog::l($err);
            throw new \WordPressURLDetector\Exception($err);
        }

        // Standardise all paths to use / (Windows support)
        return str_replace('\\', '/', self::$info[$key]);
    }

    /**
     * Get URL via name
     *
     * @throws \WordPressURLDetector\Exception
     */
    public static function getURL( string $name ): string
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        $key = $name . '_url';

        if (! array_key_exists($key, self::$info)) {
            $err = 'Attempted to access missing SiteInfo URL';
            WsLog::l($err);
            throw new \WordPressURLDetector\Exception($err);
        }

        return self::$info[$key];
    }

    public static function permalinksAreCompatible(): bool
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        $structure = get_option('permalink_structure');

        return strlen($structure) && strcmp($structure[-1], '/') === 0;
    }

    public static function getPermalinks(): string
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        return get_option('permalink_structure');
    }

    /**
     * Get Site URL host
     *
     * @throws \WordPressURLDetector\Exception
     */
    public static function getSiteURLHost(): string
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        $urlHost = parse_url(self::$info['site_url'], PHP_URL_HOST);

        if (! is_string($urlHost)) {
            $err = 'Failed to get hostname from Site URL';
            WsLog::l($err);
            throw new \WordPressURLDetector\Exception($err);
        }

        return $urlHost;
    }

    public function debug(): void
    {
        var_export(self::$info);
    }

    /**
     *  Get all WP site info
     *
     *  @return array<mixed>
     */
    public static function getAllInfo(): array
    {
        if (self::$instance === null) {
             self::$instance = new SiteInfo();
        }

        return self::$info;
    }

    /*
     * get pagination base from rewrite patterns in WP database
     */
    public static function getPaginationBase(): string
    {
        return
            explode(
                '/',
                key(
                    array_filter(
                        get_option('rewrite_rules'),
                        static function ($rule) {
                            return strpos($rule, 'index.php?&paged=$matches[1]') !== false;
                        }
                    )
                )
            )[0];
    }
}
