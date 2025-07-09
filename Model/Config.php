<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Encryption\Encryptor;

class Config
{
    public const CONFIG_PATH_SETTINGS_ENABLE = 'jaroslawzielinski_otpcomponent/settings/enable';

    public const CONFIG_PATH_SETTINGS_MENU_ENABLED = 'jaroslawzielinski_otpcomponent/settings/menu_enabled';

    public const CONFIG_PATH_OTP_CONFIGURATION_NAME_ENABLED = 'jaroslawzielinski_otpcomponent/otp_form_configuration/name_enabled';

    public const CONFIG_PATH_OTP_CONFIGURATION_NAME_REQUIRED = 'jaroslawzielinski_otpcomponent/otp_form_configuration/name_required';

    public const CONFIG_PATH_OTP_CONFIGURATION_SURNAME_ENABLED = 'jaroslawzielinski_otpcomponent/otp_form_configuration/surname_enabled';

    public const CONFIG_PATH_OTP_CONFIGURATION_SURNAME_REQUIRED = 'jaroslawzielinski_otpcomponent/otp_form_configuration/surname_required';

    public const CONFIG_PATH_RE_CAPTCHA_ENABLE = 'jaroslawzielinski_otpcomponent/reCaptcha/enable';

    public const CONFIG_PATH_RE_CAPTCHA_SITEKEY = 'jaroslawzielinski_otpcomponent/reCaptcha/sitekey';

    public const CONFIG_PATH_RE_CAPTCHA_SECRETKEY = 'jaroslawzielinski_otpcomponent/reCaptcha/secretkey';

    public const CONFIG_PATH_EMAILS_NAME = 'jaroslawzielinski_otpcomponent/emails/name';

    public const CONFIG_PATH_EMAILS_EMAIL = 'jaroslawzielinski_otpcomponent/emails/email';

    public const CONFIG_PATH_EMAILS_EMAIL_TEMPLATE = 'jaroslawzielinski_otpcomponent/emails/email_template';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Encryptor
     */
    private $encryptor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Encryptor $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    public function isModuleEnable($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_SETTINGS_ENABLE, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function isModuleMenuEnabled($scopeCode = null): bool
    {
        $isEnabled = $this->isModuleEnable($scopeCode);
        return $isEnabled && $this->scopeConfig
                ->isSetFlag(self::CONFIG_PATH_SETTINGS_MENU_ENABLED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }


    public function isNameEnabled($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_OTP_CONFIGURATION_NAME_ENABLED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }


    public function isNameRequired($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_OTP_CONFIGURATION_NAME_REQUIRED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }


    public function isSurnameEnabled($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_OTP_CONFIGURATION_SURNAME_ENABLED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }


    public function isSurnameRequired($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_OTP_CONFIGURATION_SURNAME_REQUIRED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function isReCaptchaEnable($scopeCode = null): bool
    {
        return $this->scopeConfig
            ->isSetFlag(self::CONFIG_PATH_RE_CAPTCHA_ENABLE, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getReCaptchaSiteKey($scopeCode = null): string
    {
        return (string)$this->scopeConfig
            ->getValue(self::CONFIG_PATH_RE_CAPTCHA_SITEKEY, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    /**
     * @throws \Exception
     */
    public function getReCaptchaSecretKey($scopeCode = null): string
    {
        $secretKey = (string)$this->scopeConfig
            ->getValue(self::CONFIG_PATH_RE_CAPTCHA_SECRETKEY, ScopeInterface::SCOPE_STORE, $scopeCode);
        return $this->encryptor->decrypt($secretKey);
    }

    public function getEmailsName($scopeCode = null): string
    {
        return (string)$this->scopeConfig
            ->getValue(self::CONFIG_PATH_EMAILS_NAME, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getEmailsEmail($scopeCode = null): string
    {
        return (string)$this->scopeConfig
            ->getValue(self::CONFIG_PATH_EMAILS_EMAIL, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getEmailsEmailTemplate($scopeCode = null): string
    {
        return (string)$this->scopeConfig
            ->getValue(self::CONFIG_PATH_EMAILS_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE, $scopeCode);
    }
}
