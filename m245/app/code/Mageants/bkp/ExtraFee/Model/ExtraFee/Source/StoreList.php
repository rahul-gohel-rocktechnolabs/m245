<?php
/**
 * @category Mageants ExtraFee
 * @package Mageants_ExtraFee
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ExtraFee\Model\ExtraFee\Source;

use \Magento\Store\Model\System\Store;

class StoreList implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_systemStore;

    /**
     * Constructor
     *
     * @param Store $systemStore
     */
    public function __construct(
        Store $systemStore
    ) {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return array of carriers. If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $storeId=$this->_systemStore->getStoreValuesForForm(false, true);
        return $storeId;
    }
}
