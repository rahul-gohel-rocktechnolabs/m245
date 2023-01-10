<?php
/**
 * @category  Mageants ProductQA
 * @package   Mageants_ProductQA
 * @copyright Copyright (c) 2017 Mageants
 * @author    Mageants Team <info@mageants.com>
 */
namespace Mageants\ProductQA\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Question extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlInterface
     */
    private $_urlBuilder;
    /**
     * @var URL_PATH_PRODUCT_EDIT
     */
    public const  URL_PATH_QUESTION_EDIT = "mageants_productqa/productquestion/edit/";
    /**
     * @var UrlInterface
     */
    private $_productCollectionFactory;

    /**
     * @param ContextInterface         $context                  [description]
     * @param UiComponentFactory       $uiComponentFactory       [description]
     * @param UrlInterface             $urlBuilder               [description]
     * @param ProductCollectionFactory $productCollectionFactory [description]
     * @param array                    $components               [description]
     * @param array                    $data                     [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ProductCollectionFactory $productCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        
        $this->_urlBuilder = $urlBuilder;
        
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $item) {
                $prod_url = $this->_urlBuilder->getUrl(static::URL_PATH_QUESTION_EDIT, ['id' => $item['question_id'] ]);
                    
                $item[$this->getData('name')] = "<a href='#' 
                onclick='setLocation(\"{$prod_url}\")'>".$item['question_id']."</a>";
            }
        }
        return $dataSource;
    }
}
