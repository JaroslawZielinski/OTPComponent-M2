<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form\Otp;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form\Otp;
use JaroslawZielinski\OTPComponent\Model\Config;
use JaroslawZielinski\OTPComponent\Model\EmailFacade;
use JaroslawZielinski\OTPComponent\Model\User\DataProvider;
use JaroslawZielinski\OTPComponent\Model\UserFactory;
use Magento\Backend\Model\Auth\Session as BackendSession;

class Preprocess extends Otp
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var EmailFacade
     */
    private $emailFacade;

    /**
     * @inheritDoc
     */
    public function __construct(
        UserFactory $userFactory,
        EmailFacade $emailFacade,
        BackendSession $backendSession,
        DataPersistorInterface $dataPersistor,
        Config $config,
        EventManager $eventManager,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->userFactory = $userFactory;
        $this->emailFacade = $emailFacade;
        parent::__construct($backendSession, $dataPersistor, $config, $eventManager, $logger, $context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $request = $this->getRequest();
        $params = $request->getParams();
        try {
            $data = array_intersect_key(
                $params,
                array_flip([
                    UserInterface::USER_ID,
                    UserInterface::NAME,
                    UserInterface::SURNAME,
                    UserInterface::EMAIL,
                    UserInterface::DOB
                ])
            );
            $this->dataPersistor->set(DataProvider::OTPFORM_USER, $data);
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(__('Invalid form key. Please refresh the page.'));
            }
            $this->validationEventDispatch($request);
            // Save Tuple to the server session who is waiting for authentication
            $codeArray = [rand() % 10, rand() % 10, rand() % 10, rand() % 10, rand() % 10, rand() % 10];
            $temporaryTuple = [
                'otp6_value' => implode('', $codeArray),
            ];
            $this->backendSession->getData(self::TUPLE_PREFIX, $clear = true);
            $this->backendSession->setData(self::TUPLE_PREFIX, $temporaryTuple);
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
            $this->messageManager->addSuccessMessage(__('An email with OTP code has been sent.'));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($message, $e->getTrace());
            $this->messageManager->addErrorMessage($message);
            return $this->_redirect('jaroslawzielinski_otpcomponent/form/test');
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('JaroslawZielinski_OTPComponent::menu');
        $resultPage->getConfig()->getTitle()->prepend(__('OTP Component Test'));
        $resultPage->addBreadcrumb(__('OTP Component Test'), __('Backend Test'));

        return $resultPage;
    }
}
