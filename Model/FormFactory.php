<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use JaroslawZielinski\OTPComponent\Block\Container;
use JaroslawZielinski\OTPComponent\Block\Container\Form;
use JaroslawZielinski\OTPComponent\Block\Container\Form\ExtraFields;
use JaroslawZielinski\OTPComponent\Block\Container\Form\HiddenExtraFields;
use JaroslawZielinski\OTPComponent\Block\Container\Form\Otp;
use JaroslawZielinski\OTPComponent\Block\Container\Form\ReCaptcha;
use JaroslawZielinski\OTPComponent\ViewModel\Otp as OtpView;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class FormFactory
{
    /**
     * @var OtpView
     */
    private $otpView;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        OtpView $otpView,
        Config $config
    ) {
        $this->otpView = $otpView;
        $this->config = $config;
    }

    /**
     */
    public function create(
        LayoutInterface $layout,
        array $params = [],
        array $fields = [],
        $withContainer = true
    ): Template {
        $formHtmlId = $params['form_id'];
        $otp = $params['otp'];
        $crcCode = $params['crc_c'] ?? '';
        /** @var HiddenExtraFields $hiddenExtraFieldsBlock */
        $hiddenExtraFieldsBlock = $layout
            ->createBlock(HiddenExtraFields::class, null, ['data' => [ 'fields' => $fields ]]);
        /** @var ExtraFields $extraFieldsBlock */
        $extraFieldsBlock = $layout
            ->createBlock(ExtraFields::class);
        /** @var Otp $otpWidgetBlock */
        $otpWidgetBlock = $layout
            ->createBlock(Otp::class, null, ['data' => [
                'crc_c' => $crcCode,
                'config' => [
                    'name' => 'otp6_value',
                    'initialValue' => $otp
                ],
                'view_model' => $this->otpView
            ]]);
        /** @var Form $formBlock */
        $formBlock = $layout
            ->createBlock(Form::class, null,
                [
                    'data' => [
                        'form_id' => $formHtmlId,
                        'disable_fields' => false,
                        'show_codes' => false,
                        'submit_url' => '/otpcomponent/form_otp/preprocess'
                    ]
                ]
            )
            ->addData($params);
        $formBlock->setChild('hidden_extra_fields', $hiddenExtraFieldsBlock);
        $formBlock->setChild('extra_fields', $extraFieldsBlock);
        $formBlock->setChild('otp_widget', $otpWidgetBlock);
        if ($this->config->isReCaptchaEnable()) {
            /** @var ReCaptcha $reCaptchaBlock */
            $reCaptchaBlock = $layout
                ->createBlock(ReCaptcha::class, null, ['data' => [
                    'form_id' => $formHtmlId,
                    'class' => 'otp-form-submit',
                    'is_submit' => true,
                    'submit_label' => __('Send')
                ]]);
            $formBlock->setChild('recaptcha.v2.invisible', $reCaptchaBlock);
        }
        /** @var Container $container */
        $container = $layout
            ->createBlock(Container::class, null, ['data' => [
                'form_id' => $formHtmlId
            ]]);
        $container->setChild('otp-form-form', $formBlock);
        return $withContainer ? $container : $formBlock;
    }
}
