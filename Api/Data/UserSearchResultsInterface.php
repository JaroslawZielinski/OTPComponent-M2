<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface UserSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return UserInterface[]
     */
    public function getItems();

    /**
     * @param UserInterface[] $items
     * @return UserSearchResultsInterface
     */
    public function setItems(array $items);
}
