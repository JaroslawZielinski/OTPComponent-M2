<?php

namespace JaroslawZielinski\OTPComponent\ViewModel;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use JaroslawZielinski\OTPComponent\Helper\Data;

class Otp implements ArgumentInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     */
    public function __construct(
        JsonSerializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    public function arrayToJson(array $inputArray): string
    {
        return Data::escapeQuotes($this->jsonSerializer->serialize($inputArray));
    }
}
