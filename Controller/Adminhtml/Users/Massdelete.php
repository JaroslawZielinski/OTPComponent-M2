<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Adminhtml\Users;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use JaroslawZielinski\OTPComponent\Api\UserRepositoryInterface;
use JaroslawZielinski\OTPComponent\Model\ResourceModel\User\CollectionFactory;
use JaroslawZielinski\OTPComponent\Model\User;

class Massdelete extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @inheritDoc
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        UserRepositoryInterface $userRepository,
        Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->userRepository = $userRepository;
        parent::__construct($context);
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;
        /** @var User $item */
        foreach ($collection as $item) {
            try {
                $itemData = $item->getDataModel();
                $this->userRepository->delete($itemData);
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                continue;
            }
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('JaroslawZielinski_OTPComponent::users');
    }
}
