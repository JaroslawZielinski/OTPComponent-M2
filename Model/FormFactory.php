<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use JaroslawZielinski\OTPComponent\Block\Container;
use JaroslawZielinski\OTPComponent\Block\Container\Form;
use JaroslawZielinski\OTPComponent\Block\Container\Form\Fields;
use JaroslawZielinski\OTPComponent\Block\Container\Form\HiddenExtraFields;
use JaroslawZielinski\OTPComponent\Block\Container\Form\Otp;
use JaroslawZielinski\OTPComponent\Block\Container\Form\ReCaptcha;
use JaroslawZielinski\OTPComponent\Block\Container\Form\SubmitScript;
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

    /**
     * @var SubmitScript
     */
    private $submitScript;

    /**
     * @var Fields
     */
    private $extraFields;

    /**
     */
    public function __construct(
        OtpView $otpView,
        Config $config,
        SubmitScript $submitScript,
        Fields $extraFields
    ) {
        $this->otpView = $otpView;
        $this->config = $config;
        $this->submitScript = $submitScript;
        $this->extraFields = $extraFields;
    }

    /**
     */
    public function create(
        LayoutInterface $layout,
        array $params = [],
        array $fields = [],
        bool $withContainer = true,
        bool $submitScript = false
    ): Template {
        $formHtmlId = $params['form_id'];
        $otp = $params['otp'] ?? '';
        $crcCode = $params['crc_c'] ?? '';
        /** @var HiddenExtraFields $hiddenExtraFieldsBlock */
        $hiddenExtraFieldsBlock = $layout
            ->createBlock(HiddenExtraFields::class, null, ['data' => [ 'fields' => $fields ]]);
        /** @var ExtraFields $extraFieldsBlock */
        $extraFieldsBlock = $layout
            ->createBlock(Fields::class, null, ['data' =>
                    ['template' => $this->extraFields->getTemplatePath()]
            ]);
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
        $recaptchaBlockName = 'recaptcha.v2.invisible';
        $submitScriptTemplatePath = $submitScript ?
            $this->submitScript->getTemplatePath() :
            'JaroslawZielinski_OTPComponent::container/form/submitScript.phtml';
        $submitScriptBlock = $layout
            ->createBlock(SubmitScript::class, null, ['data' => [
                'template' => $submitScriptTemplatePath,
                'form_id' => $formHtmlId,
                'recaptcha_block' => $recaptchaBlockName
            ]])->addData($this->submitScript->getData());
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
            $formBlock->setChild($recaptchaBlockName, $reCaptchaBlock);
        }
        $formBlock->setChild('submit-script', $submitScriptBlock);
        /** @var Container $container */
        $container = $layout
            ->createBlock(Container::class, null, ['data' => [
                'form_id' => $formHtmlId
            ]]);
        $container->setChild('otp-form-form', $formBlock);
        return $withContainer ? $container : $formBlock;
    }
}
