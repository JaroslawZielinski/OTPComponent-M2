<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model\ResourceModel\User;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use JaroslawZielinski\OTPComponent\Model\User;
use Vendor\OTPComponent\Model\ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'user_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            User::class,
            \JaroslawZielinski\OTPComponent\Model\ResourceModel\User::class
        );
    }
}
