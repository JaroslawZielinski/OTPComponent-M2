<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Form\Otp;

use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Exception\LocalizedException;
use JaroslawZielinski\OTPComponent\Block\Container\Form;
use JaroslawZielinski\OTPComponent\Controller\Form\Otp;

class Process extends Otp
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $params = $request->getParams();
        try {
            if (false === $request->isXmlHttpRequest()) {
                throw new LocalizedException(__('It is not an ajax call.'));
            }
            $formKey = $request->getParam('form_key', null);
            if (false === ($formKey && Security::compareStrings($formKey, $this->formKey->getFormKey()))) {
                throw new LocalizedException(__('Invalid form key. Please refresh the page.'));
            }
            if ($this->config->isReCaptchaEnable()
                && !$this->reCaptcha->isValid($params['g-recaptcha-response'] ?? null)) {
                throw new LocalizedException(__('ReCaptcha validation failed.'));
            }
            $crcCode = $params['crc_c'] ?? null;
            if (empty($crcCode)) {
                throw new LocalizedException(__('OTP form is invalid.'));
            }
            $this->validationEventDispatch($request);
            // Fetch Tuple from the server session who is waiting for authentication
            $suffix = $this->getCRCCode($crcCode);
            $temporaryTuple = $this->customerSession->getData(self::TUPLE_PREFIX . $suffix, $clear = true);
            if (!isset($temporaryTuple['otp6_value'])) {
                throw new LocalizedException(__('The OTP code has expired. Try again later.'));
            }
            if ($params['otp6_value'] !== $temporaryTuple['otp6_value']) {
                throw new LocalizedException(__('The OTP code does not match. Try again later.'));
            }
            $newParams = array_diff_key($params, array_flip(self::NON_DATA_ATTRIBUTES));
            $resetParams = array_fill_keys(array_keys($newParams), '');
            /** @var Form $formBlock */
            $formBlock = $this->formFactory->create(
                $this->_view->getLayout(),
                array_merge($params, $resetParams, [
                    'submit_url' => '/otpcomponent/form_otp/preprocess',
                    'disable_fields' => false,
                    'show_codes' => false
                ]),
                [],
                false
            );
            $status = self::STATUS_OK;
            $message = __('Successfully processed.');
            $this->processAuthenticatedEventDispatch($temporaryTuple, $message, $status);
            $results = $formBlock->toHtml();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            $status = self::STATUS_FAILED;
            $message = __('Error happened [%1]', $e->getMessage());
            $results = '';
        }
        return $this->executeJson([
            'status' => $status,
            'message' => $message,
            'results' => $results
        ]);
    }
}
