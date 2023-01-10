<?php

namespace Mageants\DeliveryDate\Ui;

use Magento\Framework\Escaper;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

class DeliveryDate extends Column
{
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param SerializerInterface $serializer
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        SerializerInterface $serializer,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->serializer = $serializer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item[$this->getData('name')] == null) {
                    continue;
                }
                if (strpos($item[$this->getData('name')], '0000-00-00') !== false) {
                    $item[$this->getData('name')] = '';
                } else {
                    if (strpos($item[$this->getData('name')], 'delivery_date') !== false) {
                        $deliverydate = $this->serializer->unserialize($item[$this->getData('name')]);
                        $name = '';
                        foreach ($deliverydate as $key => $value) {
                            $name .= $deliverydate[$key]['delivery_date'] . ",";
                        }
                        $item[$this->getData('name')] = preg_replace("/,$/", '', $name);
                    } else {
                        $item[$this->getData('name')] = $item[$this->getData('name')];
                    }
                }
            }
        }

        return $dataSource;
    }
}
