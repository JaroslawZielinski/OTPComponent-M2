<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface UserInterface extends ExtensibleDataInterface
{
    public const USER_ID = 'user_id';

    public const NAME = 'name';

    public const SURNAME = 'surname';

    public const EMAIL = 'email';

    public const DOB = 'dob';

    public const SOURCE = 'source';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const FRONTEND_SOURCE = false;

    public const BACKEND_SOURCE = true;

    /**
     * @return ?int
     */
    public function getUserId();

    /**
     * @param ?int $userId
     * @return UserInterface
     */
    public function setUserId($userId): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return UserInterface
     */
    public function setName(string $name): self;

    /**
     * @return ?string
     */
    public function getSurname(): ?string;

    /**
     * @param ?string $surname
     * @return UserInterface
     */
    public function setSurname(?string $surname): self;


    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     * @return UserInterface
     */
    public function setEmail(string $email): self;


    /**
     * @return string
     */
    public function getDob(): string;

    /**
     * @param string $dob
     * @return UserInterface
     */
    public function setDob(string $dob): self;

    /**
     * @return bool
     */
    public function getSource(): bool;

    /**
     * @param bool $source
     * @return UserInterface
     */
    public function setSource(bool $source): self;

    /**
     * @return ?string
     */
    public function getCreatedAt(): ?string;

    /**
     * @param ?string $createdAt
     * @return UserInterface
     */
    public function setCreatedAt(?string $createdAt): self;

    /**
     * @return ?string
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param ?string $updatedAt
     * @return UserInterface
     */
    public function setUpdatedAt(?string $updatedAt): self;

    /**
     * @return \JaroslawZielinski\OTPComponent\Api\Data\UserExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \JaroslawZielinski\OTPComponent\Api\Data\UserExtensionInterface $extensionAttributes
     * @return UserInterface
     */
    public function setExtensionAttributes(
        \JaroslawZielinski\OTPComponent\Api\Data\UserExtensionInterface $extensionAttributes
    );
}
