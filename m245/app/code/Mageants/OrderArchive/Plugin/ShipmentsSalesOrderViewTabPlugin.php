<?php
 /**
  * @category Mageants OrderArchive
  * @package Mageants OrderArchive
  * @copyright Copyright (c) 2019 Mageants
  * @author Mageants Team <support@mageants.com>
  */
declare(strict_types=1);
namespace Mageants\OrderArchive\Plugin;

use Magento\Framework\AuthorizationInterface;
use Magento\Sales\Block\Adminhtml\Order\View\Tab\Shipments;

/**
 * Order Shipments Plugin to set the tab
 */
class ShipmentsSalesOrderViewTabPlugin
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    /**
     * Verify the user authorization to see the Shipments tab
     *
     * @param Shipments $shipments
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanShowTab(Shipments $shipments, bool $result):bool
    {
        return $result && $this->authorization->isAllowed('Mageants_OrderArchive::shipments');
    }
}
