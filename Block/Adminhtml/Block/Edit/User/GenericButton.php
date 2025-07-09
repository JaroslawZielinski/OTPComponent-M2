<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Adminhtml\Block\Edit\User;

use JaroslawZielinski\OTPComponent\Api\UserRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     */
    public function __construct(
        Context $context,
        UserRepositoryInterface $userRepository
    ) {
        $this->context = $context;
        $this->userRepository = $userRepository;
    }

    public function getUserId(): ?int
    {
        try {
            return (int)$this->userRepository->get(
                (int)$this->context->getRequest()->getParam('user_id')
            )->getUserId();
        } catch (NoSuchEntityException|LocalizedException $e) {
        }
        return null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
