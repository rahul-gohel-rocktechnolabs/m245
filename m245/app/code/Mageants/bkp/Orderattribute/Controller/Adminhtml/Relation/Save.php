<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Relation;

class Save extends \Mageants\Orderattribute\Controller\Adminhtml\Relation
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                        $context
     * @param \Magento\Framework\Registry                                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory                 $resultPageFactory
     * @param \Mageants\Orderattribute\Api\RelationRepositoryInterface   $relationRepository
     * @param \Mageants\Orderattribute\Model\RelationFactory             $relationFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface      $dataPersistor
     * @param \Psr\Log\LoggerInterface                                   $logger
     * @param \Magento\Framework\Controller\ResultFactory                $resultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository,
        \Mageants\Orderattribute\Model\RelationFactory $relationFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $relationRepository, $relationFactory);
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Save Action
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $relationId = $this->getRequest()->getParam('relation_id');
        if ($data = $this->getRequest()->getPostValue()) {

            /** @var \Mageants\Orderattribute\Model\Relation $model */
            $model = $this->relationFactory->create();

            try {
                if ($relationId) {
                    $model = $this->relationRepository->get($relationId);
                }

                $model->loadPost($data);

                $this->relationRepository->save($model);

                $this->messageManager->addSuccessMessage(__('The Relation has been saved.'));
                $this->_getSession()->setPageData(false);
                $this->dataPersistor->clear('mageants_order_attributes_relation');

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mgorderattribute/*/edit', ['relation_id' => $relationId]);
                    return $resultRedirect;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($relationId) {
                    $resultRedirect->setPath('mgorderattribute/*/edit', ['relation_id' => $relationId]);
                    return $resultRedirect;
                }
                if ($data) {
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('mageants_order_attributes_relation', $data);
                }
                $resultRedirect->setPath('mgorderattribute/*/new');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('The Relation has not been saved. Please review the error log for the details.')
                );
                $this->logger->critical($e);
                if ($relationId) {
                    $resultRedirect->setPath('mgorderattribute/*/edit', ['relation_id' => $relationId]);
                    return $resultRedirect;
                }
                if ($data) {
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('mageants_order_attributes_relation', $data);
                }
                $resultRedirect->setPath('mgorderattribute/*/new');
                return $resultRedirect;
            }

        }
        $resultRedirect->setPath('mgorderattribute/*/');
        return $resultRedirect;
    }
}
