<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Ui\Component\Listing\Column\Form;

class Date extends \Magento\Ui\Component\Listing\Columns\Date
{
    /**
     * @inheritDoc
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();
        if (isset($config['filter'])) {
            $filterDate = $config['filterDate'] ?? $this->timezone->getDateFormatWithLongYear();
            $config['options']['dateFormat'] = $filterDate;
            $config['filter'] = [
                'filterType' => 'dateRange',
                'templates' => [
                    'date' => [
                        'options' => [
                            'dateFormat' => $filterDate
                        ]
                    ]
                ]
            ];
        }
        $this->setData('config', $config);
    }
}
