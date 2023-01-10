<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class Attribute extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $entityTypeId;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->initEntityTypeId();
    }

    /**
     * Create eav attribute model
     *
     * @return \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute
     */
    public function createEavAttributeModel()
    {
        return $this->_objectManager
            ->create(\Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute::class)
            ->setEntityTypeId($this->entityTypeId);
    }

    /**
     * Init entity type id
     */
    public function initEntityTypeId()
    {
        $this->entityTypeId = $this->_objectManager
            ->create(\Magento\Eav\Model\Entity::class)
            ->setType(\Magento\Sales\Model\Order::ENTITY)->getTypeId();
    }

    /**
     * Create action page
     *
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Order'), __('Order'))
            ->addBreadcrumb(__('Order Attributes'), __('Order Attributes'))
            ->setActiveMenu('Mageants_Orderattribute::attributes_list');
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }

        $resultPage->getConfig()->getTitle()->prepend(__('Order Attributes'));
        return $resultPage;
    }

    /**
     * Is order attribute
     *
     * @param \Mageants\Orderattribute\Model\ResourceModel\Eav\Attribute  $orderAttribute
     * @return bool
     */
    protected function isOrderAttribute($orderAttribute)
    {
        return ($orderAttribute->getEntityTypeId() == $this->entityTypeId);
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_Orderattribute::order_attributes');
    }

    /**
     * Create attribute value model
     *
     * @return \Mageants\Orderattribute\Model\Order\Attribute
     */
    protected function createAttributeValueModel()
    {
        return $this->_objectManager->create(\Mageants\Orderattribute\Model\Order\Attribute::class);
    }
}
