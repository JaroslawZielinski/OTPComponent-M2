<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Test extends Action
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('JaroslawZielinski_OTPComponent::menu');
        $resultPage->getConfig()->getTitle()->prepend(__('OTP Component Test'));
        $resultPage->addBreadcrumb(__('OTP Component Test'), __('Backend Test'));

        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('JaroslawZielinski_OTPComponent::testBackend');
    }
}
