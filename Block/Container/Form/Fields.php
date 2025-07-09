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
    private $template;

    /**
     * @inheritDoc
     */
    public function __construct(
        string $template,
        Context $context,
        array $data = []
    ) {
        $this->template = $template;
        parent::__construct($context, $data);
    }

    protected function _construct(): void
    {
        $this->setTemplate($this->template);
        parent::_construct();
    }
}
