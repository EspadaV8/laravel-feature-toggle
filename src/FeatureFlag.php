<?php
declare(strict_types=1);

namespace Kirschbaum\LaravelFeatureFlag;

use Illuminate\Support\Collection;

class FeatureFlag
{
    /**
     * @var string
     */
    protected $featureId;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $enabled = false;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function isEnabled(string $featureId): bool
    {
        return $this
            ->setFeatureId($featureId)
            ->findEnvironmentSettingOrUseDefault()
            ->takeExceptionIfNoSettingIsFound()
            ->getEnabled();
    }

    public function getJavascriptFlags(): array
    {
        $settings = new Collection(config('feature-flags'));
        $settings = $settings->where('js_export', true);

        $results = [];
        foreach ($settings as $key => $setting) {
            $results[$key] = $this->isEnabled($key);
        }

        return $results;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFeatureId(): string
    {
        return $this->featureId;
    }

    public function setFeatureId(string $featureId): self
    {
        $this->featureId = $featureId;

        return $this;
    }

    private function findEnvironmentSettingOrUseDefault(): self
    {
        $setting = config("feature-flags.{$this->getFeatureId()}.environments.{$this->environment}");

        if (null === $setting) {
            $setting = config("feature-flags.{$this->getFeatureId()}.environments.default");
        }

        $this->setEnabled($setting);

        return $this;
    }

    private function takeExceptionIfNoSettingIsFound(): self
    {
        if (null === $this->getEnabled()) {
            throw new \Exception(
                sprintf(
                    'FeatureFlag: Cannot find a setting for the feature ID %s',
                    $this->getFeatureId()
                )
            );
        }

        return $this;
    }
}
