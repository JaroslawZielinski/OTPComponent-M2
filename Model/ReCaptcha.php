<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Model;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;

class ReCaptcha
{
    public const GOOGLE_RECAPTCHA_API_URL = 'https://www.google.com/recaptcha/api.js';

    public const GOOGLE_RECAPTCHA_API_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        RemoteAddress $remoteAddress,
        JsonSerializer $jsonSerializer,
        GuzzleClient $guzzleClient,
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->remoteAddress = $remoteAddress;
        $this->jsonSerializer = $jsonSerializer;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @throws \Exception
     */
    public function isValid(?string $gResponse): bool
    {
        if (empty($gResponse)) {
            return false;
        }
        $secret = $this->config->getReCaptchaSecretKey();
        $remoteIp = $this->remoteAddress->getRemoteAddress();
        $recaptcha['success'] = false;
        try {
            /** @var Response $response */
            $response = $this->guzzleClient->request('POST', self::GOOGLE_RECAPTCHA_API_VERIFY_URL, [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $gResponse,
                    'remoteip' => $remoteIp
                ]
            ]);
            $recaptcha = $this->jsonSerializer->unserialize((string)$response->getBody());
            $this->logger->info('Invisible ReCaptcha Validation', [
                'remoteip' => $remoteIp,
                'recaptcha' => $recaptcha,
                'response' => $gResponse,
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
        return !!$recaptcha['success'];
    }
}
