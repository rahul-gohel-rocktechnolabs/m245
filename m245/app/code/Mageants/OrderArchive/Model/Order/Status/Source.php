<?php
/**
 * @category Mageants OrderArchive
 * @package Mageants OrderArchive
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OrderArchive\Model\Order\Status;

/**
 * Order archive model
 *
 */
class Source extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * Retrieve order statuses as options for select
     *
     * @see \Magento\Sales\Model\Config\Source\Order\Status:toOptionArray()
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options);
        // Remove '--please select--' option
        return $options;
    }
}
