<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form\Otp;

use Magento\Framework\Exception\LocalizedException;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form\Otp;
use JaroslawZielinski\OTPComponent\Model\User\DataProvider;

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
            $data = array_intersect_key(
                $params,
                array_flip([
                    UserInterface::USER_ID,
                    UserInterface::NAME,
                    UserInterface::SURNAME,
                    UserInterface::EMAIL,
                    UserInterface::DOB,
                    'otp6_value'
                ])
            );
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(__('Invalid form key. Please refresh the page.'));
            }
            $this->validationEventDispatch($request);
            // Get Tuple from the server session who is waiting for authentication
            $temporaryTuple = $this->backendSession->getData(self::TUPLE_PREFIX, $clear = true);
            if (!isset($temporaryTuple['otp6_value'])) {
                throw new LocalizedException(__('The OTP code has expired. Try again later.'));
            }
            if ($params['otp6_value'] !== $temporaryTuple['otp6_value']) {
                throw new LocalizedException(__('The OTP code does not match. Try again later.'));
            }
            $message = __('Successfully processed.');
            $totals = [
                'message' => $message
            ];
            $this->processAuthenticatedEventDispatch($data, $totals);
            $this->dataPersistor->clear(DataProvider::OTPFORM_USER);
            $this->messageManager->addSuccessMessage($totals['message']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->logger->error($message, $e->getTrace());
            $this->messageManager->addErrorMessage($message);
            $this->dataPersistor->set(DataProvider::OTPFORM_USER, $data);
        }
        return $this->_redirect('jaroslawzielinski_otpcomponent/form/test');
    }
}
