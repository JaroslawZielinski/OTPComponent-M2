<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Plugin\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use JaroslawZielinski\OTPComponent\Block\Container\Form\Fields;
use JaroslawZielinski\OTPComponent\Model\FormFactory;

class FormFactoryPlugin
{
    private const ALLOWED_ROUTES = [
        'jaroslawzielinski_otpcomponent_form_otp_preprocess',
        'jaroslawzielinski_otpcomponent_form_otp_process'
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Fields
     */
    private $hiddenExtraFields;

    public function __construct(
        RequestInterface $request,
        Fields $hiddenExtraFields
    ) {
        $this->request = $request;
        $this->hiddenExtraFields = $hiddenExtraFields;
    }

    /**
     */
    public function beforeCreate(
        FormFactory $subject,
        LayoutInterface $layout,
        array $params = [],
        array $fields = [],
        bool $withContainer = true,
        bool $submitScript = false
    ): array {
        if ($this->isRouteAllowed()) {
            $fields = array_merge($fields, $this->hiddenExtraFields->getFields());
        }
        return [$layout, $params, $fields, $withContainer, $submitScript];
    }

    /**
     */
    private function isRouteAllowed(): bool
    {
        $route = sprintf(
            '%s_%s_%s',
            $this->request->getRouteName(),
            $this->request->getControllerName(),
            $this->request->getActionName()
        );
        return in_array($route, $this->getAllowedRoutes());
    }

    /**
     * @return string[]
     */
    protected function getAllowedRoutes(): array
    {
        return self::ALLOWED_ROUTES;
    }
}
