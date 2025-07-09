<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;

class User extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('otpc_users', UserInterface::USER_ID);
    }
}
