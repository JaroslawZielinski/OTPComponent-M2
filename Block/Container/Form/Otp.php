<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Container\Form;

use Magento\Framework\View\Element\Template;

/**
 * @method getCrcC(): string
 */
class Otp extends Template
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->setTemplate('JaroslawZielinski_OTPComponent::container/form/otp.phtml');
        parent::_construct();
    }

    /**
     */
    public function getConfig(): array
    {
        return $this->getData('config') ?? [];
    }
}
