<?php

/**
 * DetectArchiveURLsTest.php
 *
 * @package           WordPressURLDetector
 * @author            Leon Stafford <me@ljs.dev>
 * @license           The Unlicense
 * @link              https://unlicense.org
 */

declare(strict_types=1);

namespace WordPressURLDetector;

/**
 * Class DetectArchiveURLsTest
 *
 * @package WordPressURLDetector
 */
final class DetectArchiveURLsTest extends \PHPUnit\Framework\TestCase
{

    // phpcs:ignore NeutronStandard.Functions.LongFunction.LongFunction
    public function testDetect()
    {
        $siteURL = 'https://foo.com/';

        \WP_Mock::userFunction(
            'wp_get_archives',
            [
                'times' => 1,
                'args' => [
                    [
                        'type' => 'yearly',
                        'echo' => 0,
                    ],
                ],
                'return' =>
                    "<li><a href='{$siteURL}archives/2020/'>2020</a></li>
                    <li><a href='{$siteURL}archives/2019/'>2019</a></li>
                    <li><a href='{$siteURL}archives/2018/'>2018</a></li>
                    <li><a href='{$siteURL}archives/2017/'>2017</a></li>",
            ]
        );

        \WP_Mock::userFunction(
            'wp_get_archives',
            [
                'times' => 1,
                'args' => [
                    [
                        'type' => 'monthly',
                        'echo' => 0,
                    ],
                ],
                'return' =>
                    "<li><a href='{$siteURL}archives/2020/08/'>August 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/07/'>July 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/06/'>June 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/05/'>May 2020</a></li>",
            ]
        );

        \WP_Mock::userFunction(
            'wp_get_archives',
            [
                'times' => 1,
                'args' => [
                    [
                        'type' => 'daily',
                        'echo' => 0,
                    ],
                ],
                'return' =>
                    "<li><a href='{$siteURL}archives/2020/08/20/'>August 20, 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/08/17/'>August 17, 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/08/16/'>August 16, 2020</a></li>
                    <li><a href='{$siteURL}archives/2020/08/15/'>August 15, 2020</a></li>",
            ]
        );

        $expected = [
            "{$siteURL}archives/2020/",
            "{$siteURL}archives/2019/",
            "{$siteURL}archives/2018/",
            "{$siteURL}archives/2017/",
            "{$siteURL}archives/2020/08/",
            "{$siteURL}archives/2020/07/",
            "{$siteURL}archives/2020/06/",
            "{$siteURL}archives/2020/05/",
            "{$siteURL}archives/2020/08/20/",
            "{$siteURL}archives/2020/08/17/",
            "{$siteURL}archives/2020/08/16/",
            "{$siteURL}archives/2020/08/15/",
        ];
        $actual = DetectArchiveURLs::detect();
        $this->assertEquals($expected, $actual);
    }
}
