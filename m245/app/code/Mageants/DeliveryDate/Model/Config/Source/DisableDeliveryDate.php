<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\DeliveryDate\Model\Config\Source;

class DisableDeliveryDate implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Locale\ListsInterface $localeLists)
    {
        $this->localeLists = $localeLists;
    }

    /**
     * Options function
     *
     * @return void
     */
    public function toOptionArray()
    {
        $options = $this->localeLists->getOptionWeekdays();
        // array_unshift(
        //     $options, [
        //     'label' => __('No Day'),
        //     'value' => -1
        //     ]
        // );
        return $options;
    }
}
