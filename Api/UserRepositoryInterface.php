<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserSearchResultsInterface;

interface UserRepositoryInterface
{
    /**
     * @param UserInterface $user
     * @return UserInterface
     * @throws LocalizedException
     */
    public function save(
        UserInterface $user
    );

    /**
     * @param string $userId
     * @return UserInterface
     * @throws LocalizedException
     */
    public function get($userId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return UserSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param UserInterface $user
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        UserInterface $user
    );

    /**
     * @param string $userId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($userId);
}
