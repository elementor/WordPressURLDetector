<?php

declare(strict_types=1);

namespace WordPressURLDetector;

class DetectorConfig
{

    public bool $detectArchiveURLs = true;
    public bool $detectAuthorPaginationURLs = true;
    public bool $detectAuthorsURLs = true;
    public bool $detectCategoryPaginationURLs = true;
    public bool $detectCategoryURLs = true;
    public bool $detectCustomPostTypeURLs = true;
    public bool $detectPageURLs = true;
    public bool $detectPluginAssets = true;
    public bool $detectPostURLs = true;
    public bool $detectPostsPaginationURLs = true;
    public bool $detectSitemapsURLs = true;
    public bool $detectParentThemeAssets = true;
    public bool $detectChildThemeAssets = true;
    public bool $detectVendorFiles = true;
    public bool $detectWPIncludesAssets = true;

    protected function __construct()
    {
    }
}
