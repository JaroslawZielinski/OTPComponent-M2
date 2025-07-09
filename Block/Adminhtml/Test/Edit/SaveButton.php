<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Adminhtml\Test\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        $url = $this->getUrl('jaroslawzielinski_otpcomponent/form_otp/preprocess');

        return [
            'label' => __('Submit'),
            'class' => 'save primary',
            'on_click' => "setLocation('" . $url . "')",
            'sort_order' => 90,
        ];
    }
}
