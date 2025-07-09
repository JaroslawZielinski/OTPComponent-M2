<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Plugin\Backend\Model\Menu;

use JaroslawZielinski\OTPComponent\Model\Config;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\Builder;

class BuilderPlugin
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function afterGetResult(Builder $subject, Menu $result): Menu
    {
        if (!$this->config->isModuleMenuEnabled()) {
            $result->remove('JaroslawZielinski_OTPComponent::menu');
        }
        return $result;
    }
}
