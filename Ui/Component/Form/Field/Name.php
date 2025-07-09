<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Ui\Component\Form\Field;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use JaroslawZielinski\OTPComponent\Model\Config;

class Name extends Field
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @inheritDoc
     */
    public function __construct(
        Config $config,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(): array
    {
        $configuration = parent::getConfiguration();
        $configuration['visible'] = $this->config->isNameEnabled();
        $configuration['validation']['required-entry'] = $this->config->isNameRequired();
        return $configuration;
    }
}
