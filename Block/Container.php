<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block;

use Magento\Framework\View\Element\Template;

/**
 * @method getFormId(): string
 */
class Container extends Template
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->setTemplate('JaroslawZielinski_OTPComponent::container.phtml');
        parent::_construct();
    }
}
