<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Form\FormKey;
use JaroslawZielinski\OTPComponent\Model\ReCaptcha;
use Magento\Framework\Event\ManagerInterface as EventManager;
use JaroslawZielinski\OTPComponent\Model\Config;
use JaroslawZielinski\OTPComponent\Model\FormFactory;
use Magento\Customer\Model\Session as CustomerSession;

abstract class Otp extends Action
{
    public const NON_DATA_ATTRIBUTES = ['form_id', 'otp'];

    public const TUPLE_PREFIX = 'otpcomponent_tuple_';

    public const STATUS_OK = 'OK';

    public const STATUS_FAILED = 'Failed';

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ReCaptcha
     */
    protected $reCaptcha;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var ResultJsonFactory
     */
    private $resultJsonFactory;

    /**
     * @inheritDoc
     */
    public function __construct(
        CustomerSession $customerSession,
        Config $config,
        FormFactory $formFactory,
        EventManager $eventManager,
        ReCaptcha $reCaptcha,
        LoggerInterface $logger,
        FormKey $formKey,
        ResultJsonFactory $resultJsonFactory,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->formFactory = $formFactory;
        $this->eventManager = $eventManager;
        $this->reCaptcha = $reCaptcha;
        $this->logger = $logger;
        $this->formKey = $formKey;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     */
    public function executeJson(array $input): Json
    {
        $result = $this
            ->resultJsonFactory
            ->create()
            ->setHeader('X-Robots-Tag', 'noindex,nofollow');
        return $result->setData($input);
    }

    protected function generateCRCCode(): string
    {
        $now = new \DateTime();
        $number = rand(1, 1000);
        $crc = sprintf('otp_%s_%s', $now->getTimestamp(), $number);
        return base64_encode($crc);
    }

    protected function getCRCCode(string $crcCode): string
    {
        return base64_decode($crcCode);
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
    protected function processAuthenticatedEventDispatch(array $tuple, &$message, string &$status): void
    {
        $this->eventManager->dispatch('otpcomponent_controller_authenticated', [
            'tuple' => $tuple,
            'status' => &$status,
            'message' => &$message,
        ]);
    }
}
