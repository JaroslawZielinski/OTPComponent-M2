<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Observer\Adminhtml;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use JaroslawZielinski\OTPComponent\Api\Data\UserInterface;
use JaroslawZielinski\OTPComponent\Api\UserRepositoryInterface;
use JaroslawZielinski\OTPComponent\Model\UserFactory;

class Action implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     */
    public function __construct(
        LoggerInterface $logger,
        UserFactory $userFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->logger = $logger;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     * @throws \DateMalformedStringException
     */
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        /** @var Http $request */
        $tuple = $event->getTuple();
        $message = $event->getMessage();
        $model = $this->userFactory->create();
        $data = array_intersect_key(
            $tuple,
            array_flip([UserInterface::NAME, UserInterface::SURNAME, UserInterface::EMAIL, UserInterface::DOB])
        );
        $data['source'] = UserInterface::BACKEND_SOURCE;
        $model->setData($data);
        $modelData = $model->getDataModel();
        $modelData = $this->userRepository->save($modelData);
        $otp6Value = $tuple['otp6_value'];
        $newMessage = sprintf(
            '%s [for \'%s\']. User has been created [id=%s].',
            $message,
            $otp6Value,
            $modelData->getUserId()
        );
        $this->logger->info('Backend action peformed!', ['message' => $newMessage]);
        $event->setMessage($newMessage);
    }
}
