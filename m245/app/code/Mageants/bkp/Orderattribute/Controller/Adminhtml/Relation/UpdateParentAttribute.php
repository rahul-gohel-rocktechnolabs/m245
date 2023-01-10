<?php
/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

namespace Mageants\Orderattribute\Controller\Adminhtml\Relation;

class UpdateParentAttribute extends \Mageants\Orderattribute\Controller\Adminhtml\Relation
{
    /**
     * @var \Mageants\Orderattribute\Model\Relation\AttributeOptionsProvider
     */
    private $optionsProvider;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Mageants\Orderattribute\Model\Relation\DependentAttributeProvider
     */
    private $attributeProvider;

    /**
     * UpdateParentAttribute constructor.
     *
     * @param \Magento\Backend\App\Action\Context                                  $context
     * @param \Magento\Framework\Registry                                          $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory                           $resultPageFactory
     * @param \Mageants\Orderattribute\Api\RelationRepositoryInterface             $relationRepository
     * @param \Mageants\Orderattribute\Model\RelationFactory                       $relationFactory
     * @param \Magento\Framework\Json\EncoderInterface                             $jsonEncoder
     * @param \Mageants\Orderattribute\Model\Relation\AttributeOptionsProvider     $optionsProvider
     * @param \Mageants\Orderattribute\Model\Relation\DependentAttributeProvider   $attributeProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageants\Orderattribute\Api\RelationRepositoryInterface $relationRepository,
        \Mageants\Orderattribute\Model\RelationFactory $relationFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Mageants\Orderattribute\Model\Relation\AttributeOptionsProvider $optionsProvider,
        \Mageants\Orderattribute\Model\Relation\DependentAttributeProvider $attributeProvider
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $relationRepository, $relationFactory);
        $this->jsonEncoder = $jsonEncoder;
        $this->optionsProvider = $optionsProvider;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * For Ajax
     *
     * @return \Magento\Framework\App\Response\Http with JSON
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $response = [
            'error' => __('The attribute_id is not defined. Please try to reload the page. '),
        ];
        if ($attributeId) {
            try {
                $attributeOptions = $this->optionsProvider->setParentAttributeId($attributeId)->toOptionArray();
                $dependentAttributes = $this->attributeProvider->setParentAttributeId($attributeId)->toOptionArray();
                $response = [
                    'attribute_options' => $attributeOptions,
                    'dependent_attributes' => $dependentAttributes,
                    'error' => 0,
                ];
            } catch (\Exception $exception) {
                $response = [
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return $this->getResponse()->representJson(
            $this->jsonEncoder->encode($response)
        );
    }
}
