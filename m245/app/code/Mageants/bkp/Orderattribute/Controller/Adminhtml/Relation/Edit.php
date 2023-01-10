<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Relation;

use Mageants\Orderattribute\Controller\RegistryConstants;

class Edit extends \Mageants\Orderattribute\Controller\Adminhtml\Relation
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
        $relationId = $this->getRequest()->getParam('relation_id');
        if ($relationId) {
            try {
                $model = $this->relationRepository->get($relationId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Relation does not exist.'));
                $resultRedirect->setPath('mgorderattribute/relation/index');
                return $resultRedirect;
            }
        } else {
            /** @var \Mageants\Orderattribute\Model\Relation $model */
            $model = $this->relationFactory->create();
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->coreRegistry->register(RegistryConstants::CURRENT_RELATION_ID, $model);
        $this->_initAction();

        $resultPage = $this->resultPageFactory->create();
        // set title and breadcrumbs
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Order Attribute Relation'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getName() ? __("Edit Relation \"%1s\"", $model->getName()) : __('New Order Attribute Relation')
        );

        return $resultPage;
    }
}
