<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model\Cache;

use Magento\Framework\App\CacheInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class ArrayPersistor implements DataPersistorInterface
{
    public const MAX_TIME = 3600;

    public const ARRAY_PERSISTOR_TAG = 'array_custom_data';

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        CacheInterface $cache,
        AuthSession $authSession
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->cache = $cache;
        $this->authSession = $authSession;
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function set($key, $data)
    {
        $this->validate($data);
        $userId = $this->authSession->getUser()->getId();
        $cacheKey = $key . $userId;
        $this->cache->save(
            $this->jsonSerializer->serialize($data),
            $cacheKey,
            [self::ARRAY_PERSISTOR_TAG],
            self::MAX_TIME
        );
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        $userId = $this->authSession->getUser()->getId();
        $cacheKey = $key . $userId;
        $data = $this->cache->load($cacheKey);
        return $data ? $this->jsonSerializer->unserialize($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function clear($key)
    {
        $userId = $this->authSession->getUser()->getId();
        $cacheKey = $key . $userId;
        $this->cache->remove($cacheKey);
    }

    /**
     * @throws LocalizedException
     */
    private function validate($data): void
    {
        if (!is_array($data)) {
            throw new LocalizedException(__('Invalid data provided.'));
        }
    }
}
