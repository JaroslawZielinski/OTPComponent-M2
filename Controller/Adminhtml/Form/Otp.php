<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Adminhtml\Form;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface;
use JaroslawZielinski\OTPComponent\Model\Config;

abstract class Otp extends Action
{
    public const TUPLE_PREFIX = 'adminhtml_otpcomponent_tuple';

    public const STATUS_OK = 'OK';

    public const STATUS_FAILED = 'Failed';

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritDoc
     */
    public function __construct(
        BackendSession $backendSession,
        DataPersistorInterface $dataPersistor,
        Config $config,
        EventManager $eventManager,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->backendSession = $backendSession;
        $this->dataPersistor = $dataPersistor;
        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('JaroslawZielinski_OTPComponent::testBackend');
    }

    /**
     * Validation
     */
    protected function validationEventDispatch(\Magento\Framework\App\RequestInterface $request): void
    {
        $this->eventManager->dispatch('otpcomponent_controller_validation', [
            'request' => $request
        ]);
    }

    /**
     * Action
     */
    protected function processAuthenticatedEventDispatch(array $tuple, &$message, string $status = 'OK'): void
    {
        $this->eventManager->dispatch('otpcomponent_controller_authenticated', [
            'tuple' => $tuple,
            'status' => $status,
            'message' => &$message,
        ]);
    }
}
