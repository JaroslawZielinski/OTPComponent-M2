<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Container;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use JaroslawZielinski\OTPComponent\Model\Config;

/**
 * @method setFormId(string $formId): self
 * @method getFormId(): string
 * @method setName(string $name): string
 * @method getName(): string
 * @method setSurname(string $surname): self
 * @method getSurname(): string
 * @method setEmail(string $email): self
 * @method getEmail(): string
 * @method setShowCodes(bool $showCodes): self
 * @method getShowCodes(): bool
 * @method setDisableFields(bool $disableFields): self
 * @method getDisableFields(): bool
 * @method setSubmitUrl(string $submitUrl): self
 * @method getSubmitUrl(): string
 * @method setParentId(string $parentId): self
 * @method getParentId(): string
 * @method setOtp(string $otp): self
 * @method getOtp(): string
 */
class Form extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @inheritDoc
     */
    public function __construct(
        Config $config,
        FormKey $formKey,
        Context $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }

    /**
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    public function getRequired(bool $isRequired = true, string $requiredMessage = 'This is a required field.'): string
    {
        return sprintf(
            'data-msg-required=\'%s\' data-validate=\'{"required":%s}\'',
            __($requiredMessage),
            $isRequired ? 'true' : 'false'
        );
    }

    public function getRequiredEmail(
        string $requiredMessage = 'This is a required field.',
        string $emailMessage = 'Please enter a valid email address (Ex: johndoe@domain.com).'
    ): string {
        return sprintf(
            'data-msg-required=\'%s\' data-msg-email=\'%s\' data-validate=\'{"required":true, "validate-email":true}\'',
            __($requiredMessage),
            __($emailMessage)
        );
    }

    public function isNameEnabled(): bool
    {
        return $this->config->isNameEnabled();
    }

    public function isNameRequired(): bool
    {
        return $this->config->isNameRequired();
    }

    public function isSurnameEnabled(): bool
    {
        return $this->config->isSurnameEnabled();
    }

    public function isSurnameRequired(): bool
    {
        return $this->config->isSurnameRequired();
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->setTemplate('JaroslawZielinski_OTPComponent::container/form.phtml');

        parent::_construct();
    }
}
