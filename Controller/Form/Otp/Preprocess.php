<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Form\Otp;

use JaroslawZielinski\OTPComponent\Block\Container\Form;
use JaroslawZielinski\OTPComponent\Controller\Form\Otp;
use JaroslawZielinski\OTPComponent\Model\Config;
use JaroslawZielinski\OTPComponent\Model\EmailFacade;
use JaroslawZielinski\OTPComponent\Model\FormFactory;
use JaroslawZielinski\OTPComponent\Model\ReCaptcha;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Preprocess extends Otp
{
    /**
     * @var EmailFacade
     */
    private $emailFacade;

    /**
     * @inheritDoc
     */
    public function __construct(
        EmailFacade $emailFacade,
        CustomerSession $customerSession,
        Config $config,
        FormFactory $formFactory,
        EventManager $eventManager,
        ReCaptcha $reCaptcha,
        LoggerInterface $logger,
        FormKey $formKey,
        ResultJsonFactory $resultJsonFactory,
        Context $context
    ) {
        $this->emailFacade = $emailFacade;
        parent::__construct(
            $customerSession,
            $config,
            $formFactory,
            $eventManager,
            $reCaptcha,
            $logger,
            $formKey,
            $resultJsonFactory,
            $context
        );
    }

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
            if ($this->config->isReCaptchaEnable()
                && !$this->reCaptcha->isValid($params['g-recaptcha-response'] ?? null)) {
                throw new LocalizedException(__('ReCaptcha validation failed.'));
            }
            $this->validationEventDispatch($request);
            // Save Tuple to the server session who is waiting for authentication
            $codeArray = [rand() % 10, rand() % 10, rand() % 10, rand() % 10, rand() % 10, rand() % 10];
            $temporaryTuple = array_merge($params, [
                'otp6_value' => implode('', $codeArray),
            ]);
            $suffixEncoded = $this->generateCRCCode();
            $suffix = $this->getCRCCode($suffixEncoded);
            $this->customerSession->getData(self::TUPLE_PREFIX . $suffix, $clear = true);
            $this->customerSession->setData(self::TUPLE_PREFIX . $suffix, $temporaryTuple);
            $params['crc_c'] = $suffixEncoded;
            // Prepare email to the User's email with OTP code
            $templateVars['codes'] = $temporaryTuple['otp6_value'];
            $templateVars['name'] = $params['name'];
            $templateId = $this->config->getEmailsEmailTemplate();
            $fromEmail = $this->config->getEmailsEmail();
            $fromName = $this->config->getEmailsName();
            $toEmail = $params['email'];
            $toEmailName = sprintf('%s %s', $params['name'], $params['surname']);
            // Send email to the User's email with OTP code
            $this->emailFacade->sendEmail($templateId, $templateVars, $fromEmail, $fromName, $toEmail, $toEmailName);
            /** @var Form $formBlock */
            $formBlock = $this->formFactory->create(
                $this->_view->getLayout(),
                array_merge($params, [
                    'submit_url' => '/otpcomponent/form_otp/process',
                    'disable_fields' => true,
                    'show_codes' => true
                ]),
                [],
                false
            );
            $status = self::STATUS_OK;
            $message = __('An email with OTP code has been sent.');
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
