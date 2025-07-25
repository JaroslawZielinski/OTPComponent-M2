<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Container\Form;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * @method getFields(): array
 */
class Fields extends Template
{
    /**
     * @var string
     */
    private $templatePath;

    /**
     * @inheritDoc
     */
    public function __construct(
        Context $context,
        array $data = [],
        string $templatePath = '',
    ) {
        $this->templatePath = $templatePath;
        parent::__construct($context, $data);
    }

    protected function _construct(): void
    {
        $this->setTemplate($this->templatePath);
        parent::_construct();
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
