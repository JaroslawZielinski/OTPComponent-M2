<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Ui\Component\Form\Field;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use JaroslawZielinski\OTPComponent\Ui\Component\Form\Field\Otp as OtpComponent;

class Otp extends Field
{
    public const CRC_FIELD_NAME = 'otpform_user_crc_c';

    /**
     * @var DataPersistorInterface|ContextInterface
     */
    private $dataPersistor;

    /**
     * @inheritDoc
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(): array
    {
        $configuration = parent::getConfiguration();
        $crcC = $this->dataPersistor->get(OtpComponent::CRC_FIELD_NAME);
        $configuration['crcC'] = $crcC['value'] ?? '';
        return $configuration;
    }
}
