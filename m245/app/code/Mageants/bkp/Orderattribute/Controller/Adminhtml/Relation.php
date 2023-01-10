<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml;

abstract class Relation extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Mageants_Orderattribute::attributes_relation';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */protected $resultPageFactory;

    /**
     * @var \Mageants\Orderattribute\Api\RelationRepositoryInterface
     */
    protected $relationRepository;

    /**
     * @var \Mageants\Orderattribute\Model\RelationFactory
     */
    protected $relationFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Relation constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository
     * @param \Mageants\Orderattribute\Model\RelationFactory $relationFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository,
        \Mageants\Orderattribute\Model\RelationFactory $relationFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->relationRepository = $relationRepository;
        $this->relationFactory = $relationFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_Orderattribute::attributes_relation')
            ->addBreadcrumb(__('Sales'), __('Sales'))
            ->addBreadcrumb(__('Attribute Relation'), __('Attribute Relation'));
        return $resultPage;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Attribute Relation'));
        return $resultPage;
    }
}
