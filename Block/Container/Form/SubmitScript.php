<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Container\Form;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * @method getFormId(): string
 * @method getRecaptchaBlock(): string
 */
class SubmitScript extends Template
{
    /**
     * @var string
     */
    private $templatePath;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        array $data = [],
        string $templatePath = 'JaroslawZielinski_OTPComponent::container/form/submitScript.phtml'
    ) {
        $this->templatePath = $templatePath;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setTemplate($this->templatePath);
        parent::_construct();
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
