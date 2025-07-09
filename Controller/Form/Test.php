<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Test extends Action
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('OTP Component Test'));
        return $resultPage;
    }
}
