<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Observer\Adminhtml;


use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use JaroslawZielinski\OTPComponent\Helper\Data;
use JaroslawZielinski\OTPComponent\Model\Config;

class Validation implements ObserverInterface
{
    public const JS_DATE_FORMAT = 'dd-mm-yy';

    public const PHP_DATE_FORMAT = 'd-m-Y';
    /**
     * @var Config
     */
    private $config;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var string[]
     */
    private $errors;

    public function __construct(
        Config $config,
        JsonSerializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->errors = [];
        $this->config = $config;
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
        $request = $event->getRequest();
        $params = $request->getParams();
        if (!$this->isBackendValid($params)) {
            $errorsString = $this->jsonSerializer->serialize($this->errors);
            throw new LocalizedException(__($errorsString));
        }
    }

    /**
     * @throws \DateMalformedStringException|\Exception
     */
    private function isBackendValid(array $params): bool
    {
        $this->errors = [];
        if ($this->config->isNameEnabled() && $this->config->isNameRequired()) {
            $name = $params['name'] ?? null;
            if (!\Laminas\Validator\StaticValidator::execute(trim((string)$name), 'NotEmpty')) {
                $this->errors[] = __('Name is required.');
            }
        }
        if ($this->config->isSurnameEnabled() && $this->config->isSurnameRequired()) {
            $surname = $params['surname'] ?? null;
            if (!\Laminas\Validator\StaticValidator::execute(trim((string)$surname), 'NotEmpty')) {
                $this->errors[] = __('Surname is required.');
            }
        }
        $email = $params['email'] ?? null;
        if (!\Laminas\Validator\StaticValidator::execute(trim((string)$email), 'EmailAddress')) {
            $this->errors[] = __("Email '%1' of a voter is invalid.", $email);
        }
        $dateOfBirthday = $params['dob'] ?? null;
        if (!\Laminas\Validator\StaticValidator::execute(trim((string)$dateOfBirthday), 'NotEmpty')) {
            $this->errors[] = __('Date of Your birthday is mandatory.');
        }
        if (!\Laminas\Validator\StaticValidator::execute(trim((string)$dateOfBirthday), 'Date', [
            'format' => self::PHP_DATE_FORMAT])) {
            $this->errors[] = __('Date of Your birthday is invalid.');
        }
        $today = date(self::PHP_DATE_FORMAT);
        if (Data::getDateDiff(new \DateTime($today), new \DateTime($dateOfBirthday)) < 0) {
            $this->errors[] = __('Date of Your birthday must not be in the future.');
        }
        $otpNumber = $params['otp6_value'] ?? null;
        if (!empty($otpNumber) && 1 !== preg_match('/^\d{6}$/', $otpNumber)) {
            $this->errors[] = __('OTP Number must contain 6 digits only.');
        }
        return empty($this->errors);
    }
}
