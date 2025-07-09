<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Container\Form;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use JaroslawZielinski\OTPComponent\Model\Config;

/**
 * @see https://developers.google.com/recaptcha/docs/display
 *
 * @method getFormId(): string
 * @method getIsSubmit(): bool
 * @method getSubmitLabel(): string
 * @method getHtmlTitle(): string
 * @method getHtmlName(): string
 * @method getClass(): string
 */
class ReCaptcha extends Template
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Config
     */
    private $config;

    /**
     * @@inheritDoc
     */
    public function __construct(
        Config $config,
        Context $context,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
        $this->id = str_replace('.', '', (string)microtime(true));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSiteKey(): string
    {
        return $this->config->getReCaptchaSiteKey();
    }

    public function getHtmlClass(?string $oldClass = null): string
    {
        return sprintf(
            '%sg-recaptcha g-recaptcha-%s',
            !empty($oldClass) ? $oldClass . ' ' : '',
            $this->getId()
        );
    }

    /**
     */
    public function getApiUrlWithCallback(string $callback = null): string
    {
        if (empty($callback)) {
            return \JaroslawZielinski\OTPComponent\Model\ReCaptcha::GOOGLE_RECAPTCHA_API_URL;
        }
        return sprintf(
            '%s?onload=%s&render=explicit',
            \JaroslawZielinski\OTPComponent\Model\ReCaptcha::GOOGLE_RECAPTCHA_API_URL,
            $callback
        );
    }

    /**
     */
    public function jsFailedReset(string $formId): string
    {
        return <<<EOT
grecaptcha.reset(window.reCaptchaIds['{$formId}']);
EOT;
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->setTemplate('JaroslawZielinski_OTPComponent::container/form/reCaptcha.phtml');

        parent::_construct();
    }
}
