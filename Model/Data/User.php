<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model\Data;

use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class User extends AbstractExtensibleObject implements UserInterface
{
    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->_get(UserInterface::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId($userId): UserInterface
    {
        return $this->setData(UserInterface::USER_ID, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->_get(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): UserInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getSurname(): ?string
    {
        return $this->_get(self::SURNAME);
    }

    /**
     * @inheritDoc
     */
    public function setSurname(?string $surname): UserInterface
    {
        return $this->setData(self::SURNAME, $surname);
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->_get(UserInterface::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): UserInterface
    {
        return $this->setData(UserInterface::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getDob(): string
    {
        return $this->_get(UserInterface::DOB);
    }

    /**
     * @inheritDoc
     */
    public function setDob(string $dob): UserInterface
    {
        return $this->setData(UserInterface::DOB, $dob);
    }

    /**
     * @inheritDoc
     */
    public function getSource(): bool
    {
        return $this->_get(UserInterface::SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource(bool $source): UserInterface
    {
        return $this->setData(UserInterface::SOURCE, $source);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->_get(UserInterface::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(?string $createdAt): UserInterface
    {
        return $this->setData(UserInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->_get(UserInterface::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(?string $updatedAt): UserInterface
    {
        return $this->setData(UserInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \JaroslawZielinski\OTPComponent\Api\Data\UserExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
