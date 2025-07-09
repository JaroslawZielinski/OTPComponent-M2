<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Adminhtml\Container\Form;

use Magento\Backend\Block\Template;

/**
 * @method getHtmlId(): string
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
