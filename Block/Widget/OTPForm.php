<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use JaroslawZielinski\OTPComponent\Model\FormFactory;

/**
 * @method getHtmlId(): string
 * @method getOtp(): string
 */
class OTPForm extends Template implements BlockInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @inheritDoc
     */
    public function __construct(
        FormFactory $formFactory,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->setTemplate('JaroslawZielinski_OTPComponent::widget/otpform.phtml');
        parent::_construct();
    }

    /**
     */
    public function getContainer(string $formId, string $otp): Template
    {
        return $this->formFactory->create(
            $this->_layout,
            ['form_id' => $formId, 'otp' => $otp],
            ['dob' => 'dob'],
            $withContainer = true
        );
    }
}
