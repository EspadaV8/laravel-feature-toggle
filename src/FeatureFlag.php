<?php
declare(strict_types=1);

namespace Kirschbaum\LaravelFeatureFlag;

class FeatureFlag
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var array
     */
    protected $flags;

    public function __construct(string $environment, array $config = [])
    {
        $this->environment = $environment;

        $this->processConfig($config);
    }

    public function isEnabled(string $featureId): bool
    {
        if (array_key_exists($featureId, $this->flags)) {
            return $this->flags[$featureId];
        }

        throw new \Exception(
            sprintf(
                'FeatureFlag: Cannot find a setting for the feature ID %s',
                $featureId
            )
        );
    }

    public function getJavascriptFlags(): array
    {
        $results = [];

        foreach ($this->flags as $key => $settings) {
            if ($settings['js_export'] === true) {
                $results[$key] = $settings['is_enabled'];
            }
        }

        return $results;
    }

    private function processConfig(array $config)
    {
        $this->flags = [];

        foreach ($config as $key => $settings) {
            $isEnabled = $this->getEnabledForKey($key, $settings);

            $this->flags[$key] = [
                'is_enabled' => $isEnabled,
                'js_export' => $settings['js_export'] ?? false,
            ];
        }

        return $this;
    }

    /**
     * @param string $key
     * @param array  $settings
     *
     * @return bool
     *
     * @throws \Exception
     */
    private function getEnabledForKey(string $key, array $settings): bool
    {
        $environments = $settings['environments'];

        if (array_key_exists('default', $environments) === false) {
            throw new \Exception(
                sprintf(
                    'FeatureFlag: Cannot find default setting for feature ID - %s',
                    $key
                )
            );
        }

        if (array_key_exists($this->environment, $environments)) {
            return (bool) $environments[$this->environment];
        }

        return (bool) $environments['default'];
    }
}
