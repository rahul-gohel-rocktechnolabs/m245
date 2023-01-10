<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Relation;

class NewAction extends \Mageants\Orderattribute\Controller\Adminhtml\Relation
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

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
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * Relation constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository
     * @param \Mageants\Orderattribute\Model\RelationFactory $relationFactory
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository,
        \Mageants\Orderattribute\Model\RelationFactory $relationFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $relationRepository, $relationFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->relationRepository = $relationRepository;
        $this->relationFactory = $relationFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/edit');
        return $resultRedirect;
    }
}
