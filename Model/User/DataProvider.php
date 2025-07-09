<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model\User;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use JaroslawZielinski\OTPComponent\Model\ResourceModel\User\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public const OTPFORM_USER = 'otpform_user';

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @see https://magento.stackexchange.com/questions/352671/magento-2-4-constructing-controller-url-adminbackend-in-javascript#answer-352675
     *
     * @inheritDoc
     */
    public function __construct(
        CollectionFactory $groupCollectionFactory,
        DataPersistorInterface $dataPersistor,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $groupCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $userData = $this->dataPersistor->get(self::OTPFORM_USER);
        $this->dataPersistor->clear(self::OTPFORM_USER);
        if (!empty($userData)) {
            $this->loadedData[$userData['user_id'] ?? ''] = $userData;
        } else {
            return [];
        }
        return $this->loadedData;
    }
}
