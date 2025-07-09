<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterfaceFactory;
use JaroslawZielinski\OTPComponent\Api\Data\UserSearchResultsInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserSearchResultsInterfaceFactory;
use JaroslawZielinski\OTPComponent\Api\UserRepositoryInterface;
use JaroslawZielinski\OTPComponent\Model\ResourceModel\User as ResourceUser;
use JaroslawZielinski\OTPComponent\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use JaroslawZielinski\OTPComponent\Model\UserFactory;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ResourceUser
     */
    private $resource;

    /**
     * @var UserInterfaceFactory
     */
    private $dataUserFactory;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var UserSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        ResourceUser $resource,
        UserFactory $userFactory,
        UserInterfaceFactory $dataUserFactory,
        UserCollectionFactory $userCollectionFactory,
        UserSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->userFactory = $userFactory;
        $this->dataUserFactory = $dataUserFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        UserInterface $user
    ) {
        $userData = $this->extensibleDataObjectConverter->toNestedArray(
            $user,
            [],
            UserInterface::class
        );

        $userModel = $this->userFactory->create()->setData($userData);

        try {
            $this->resource->save($userModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the User: %1',
                $exception->getMessage()
            ));
        }
        return $userModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($userId)
    {
        $user = $this->userFactory->create();
        $this->resource->load($user, $userId);
        if (!$user->getUserId()) {
            throw new NoSuchEntityException(__('User with id "%1" does not exist.', $userId));
        }
        return $user->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->userCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            UserInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        /** @var UserSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        UserInterface $user
    ) {
        try {
            $userModel = $this->userFactory->create();
            $this->resource->load($userModel, $user->getUserId());
            $this->resource->delete($userModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the User: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($userId)
    {
        return $this->delete($this->get($userId));
    }
}
