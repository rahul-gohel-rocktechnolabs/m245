<?php
namespace Mageants\OrderApprovalRules\Plugin\Block\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

class Toolbar
{
    /**
     * @var \Magento\Framework\App\RequestInterface $request,
     */
    protected $request;

    /**
     * Plugin constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\Order $order
    ) {
        $this->request = $request;
        $this->_order = $order;
    }

    /**
     * Before push button remove the order shipment invoice
     *
     * @param ToolbarContext $toolbar
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @return void
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        $order_id = $this->request->getParam('order_id');
        $orderCollection = $this->_order->load($order_id);
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }
        if ($orderCollection->getStatus() == 'orderapprovalpending') {
            $buttonList->remove('order_ship');
            $buttonList->remove('order_invoice');
            $buttonList->remove('order_cancel');
        }
        return [$context, $buttonList];
    }
}
