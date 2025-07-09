<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Block\Adminhtml\Widget\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Widget\Model\Widget\Instance;
use JaroslawZielinski\OTPComponent\Block\Adminhtml\Container\Form\Otp as OtpBlock;
use JaroslawZielinski\OTPComponent\ViewModel\Otp as OtpViewModel;

/**
 * @see https://scandiweb.com/blog/magento-series-implementing-a-widget/
 */
class OtpObject extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * @var OtpViewModel
     */
    protected $otpViewModel;

    /**
     * @inheritDoc
     */
    public function __construct(
        Registry $registry,
        JsonSerializer $jsonSerializer,
        OtpViewModel $otpViewModel,
        Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonSerializer = $jsonSerializer;
        $this->otpViewModel = $otpViewModel;
        parent::__construct($context, $data);
    }

    /**
     */
    private function getOtpBlock(string $htmlId): OtpBlock
    {
        // Pages/Blocks
        $widgetJson = $this->_request->getParam('widget', '{}');
        $params = $this->jsonSerializer->unserialize($widgetJson);
        // Widgets
        if (empty($params)) {
            /** @var Instance $widgetInstance */
            $widgetInstance = $this->registry->registry('current_widget_instance') ?? null;
            if (!empty($widgetInstance)) {
                $params = $widgetInstance->getWidgetParameters();
                $params['values']['otp'] = $params['otp'] ?? null;
            }
        }
        $config = $this->_getData('config');
        $config['name'] = 'parameters[otp]';
        $otp = $params['values']['otp'] ?? null;
        if (!empty($otp)) {
            $config['initialValue'] = $otp;
        } else {
            $config['initialValue'] = '';
        }

        /** @var OtpBlock $block */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $block = $this->_layout
            ->createBlock(OtpBlock::class)
            ->setViewModel($this->otpViewModel)
            ->setConfig($config)
            ->setHtmlId($htmlId);
        return $block;
    }

    /**
     */
    public function getOtpBlockHtml(string $htmlId): string
    {
        $otpBlock = $this->getOtpBlock($htmlId);
        return !empty($otpBlock) ? $otpBlock->toHtml() : 'empty';
    }

    /**
     * {@inheritDoc}
     * @see https://magento.stackexchange.com/questions/258660/magento-2-how-to-bind-knockout-event-on-ajax-html-response#answer-258687
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $htmlId = 'otp-container';
        $html = $this->getOtpBlockHtml($htmlId);
        $additionalStyles = <<<styles
<style type="text/css">
/*<![CDATA[*/
label[data-ui-id="wysiwyg-widget-options-fieldset-element-label-parameters-otp-label"] + .admin__field-control.control .control-value,
label[data-ui-id="widget-instance-edit-tab-properties-fieldset-element-label-parameters-otp-label"] + .admin__field-control.control .control-value { display: none; }
/*]]>*/
</style>
<script>
    require([
        'otpFormsUtils',
    ], function(formsUtils) {
        formsUtils.ajaxMagentoReinit('.{$htmlId}');
    });
</script>
styles;
        $element->setData('after_element_html', $html . $additionalStyles);
        return $element;
    }
}
