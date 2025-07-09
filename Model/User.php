<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterfaceFactory;
use JaroslawZielinski\OTPComponent\Model\ResourceModel\User\Collection;

class User extends AbstractModel
{
    /**
     * @var UserInterfaceFactory
     */
    protected $userDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'otpc_users';

    /**
     * @inheritDoc
     */
    public function __construct(
        UserInterfaceFactory $userDataFactory,
        DataObjectHelper $dataObjectHelper,
        Context $context,
        Registry $registry,
        ResourceModel\User $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->userDataFactory = $userDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getDataModel(): UserInterface
    {
        $userData = $this->getData();

        $userDataObject = $this->userDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $userDataObject,
            $userData,
            User::class
        );

        return $userDataObject;
    }
}
