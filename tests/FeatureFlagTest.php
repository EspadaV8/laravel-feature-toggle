<?php
declare(strict_types=1);

namespace Kirschbaum\Tests\LaravelFeatureFlag;

use Kirschbaum\LaravelFeatureFlag\FeatureFlag;
use PHPUnit\Framework\TestCase;

class FeatureFlagTest extends TestCase
{
    /**
     * @dataProvider data
     *
     * @param string $environment
     * @param array  $config
     * @param array  $expected
     */
    public function testFeatureFlags(string $environment, array $config, array $expected)
    {
        $featureFlag = new FeatureFlag($environment, $config);

        foreach ($config as $key => $settings) {
            $result = $featureFlag->isEnabled($key);

            $this->assertEquals($expected[$key], $result);
        }
    }

    public function testJSExports()
    {
        $config = [
            'feature-1' => [
                'environments' => [
                    'local' => true,
                    'default' => false,
                ],
                'js_export' => true,
            ],
            'feature-2' => [
                'environments' => [
                    'local' => true,
                    'default' => true,
                ],
                'js_export' => false,
            ],
            'feature-3' => [
                'environments' => [
                    'local' => false,
                    'default' => false,
                ],
                'js_export' => false,
            ],
            'feature-4' => [
                'environments' => [
                    'local' => false,
                    'default' => false,
                ],
            ],
            'feature-5' => [
                'environments' => [
                    'local' => false,
                    'default' => false,
                ],
                'js_export' => true,
            ],
        ];

        $expected = [
            'feature-1' => true,
            'feature-5' => false,
        ];

        $featureFlag = new FeatureFlag('local', $config);

        $result = $featureFlag->getJavascriptFlags();

        $this->assertEquals($expected, $result);
    }

    public function testItThrowsExceptionWhenWithNoDefault()
    {
        $config = [
            'feature-1' => [
                'environments' => [
                    'local' => true,
                ],
            ],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('FeatureFlag: Cannot find default setting for feature ID - feature-1');

        new FeatureFlag('unknown', $config);
    }

    public function data()
    {
        $defaultConfig = [
            'feature-1' => [
                'environments' => [
                    'local' => true,
                    'dev' => true,
                    'production' => true,
                    'default' => false,
                ],
                'js_export' => true,
            ],
            'feature-2' => [
                'environments' => [
                    'local' => true,
                    'dev' => true,
                    'production' => false,
                    'default' => false,
                ],
                'js_export' => false,
            ],
            'feature-3' => [
                'environments' => [
                    'local' => true,
                    'dev' => false,
                    'production' => false,
                    'default' => false,
                ],
                'js_export' => true,
            ],
            'feature-4' => [
                'environments' => [
                    'local' => false,
                    'dev' => false,
                    'production' => false,
                    'default' => false,
                ],
            ],
        ];

        return [
            'default config local' => [
                'environment' => 'local',
                'config' => $defaultConfig,
                'expected' => [
                    'feature-1' => true,
                    'feature-2' => true,
                    'feature-3' => true,
                    'feature-4' => false,
                ],
            ],
            'default config dev' => [
                'environment' => 'dev',
                'config' => $defaultConfig,
                'expected' => [
                    'feature-1' => true,
                    'feature-2' => true,
                    'feature-3' => false,
                    'feature-4' => false,
                ],
            ],
            'default config production' => [
                'environment' => 'production',
                'config' => $defaultConfig,
                'expected' => [
                    'feature-1' => true,
                    'feature-2' => false,
                    'feature-3' => false,
                    'feature-4' => false,
                ],
            ],
            'default config unknown' => [
                'environment' => 'unknown',
                'config' => $defaultConfig,
                'expected' => [
                    'feature-1' => false,
                    'feature-2' => false,
                    'feature-3' => false,
                    'feature-4' => false,
                ],
            ],
        ];
    }
}
