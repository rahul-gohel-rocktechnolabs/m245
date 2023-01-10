<?php
/**
 * @category   Mageants ImageGallery
 * @package    Mageants_ImageGallery
 * @copyright  Copyright (c) 2019 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\ImageGallery\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magefan\Shopbylook\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * @var \Magefan\Shopbylook\Model\TagFactory
     */

    protected $_appState;

    /**
     * @var \Mageants\ImageGallery\Model\Category
     */
    protected $categoryModel;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Mageants\ImageGallery\Model\Category $categoryModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Mageants\ImageGallery\Model\Category $categoryModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->categoryModel = $categoryModel;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Shopbylook Pages and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */

    public function match(\Magento\Framework\App\RequestInterface $request)
    {

        $urlKey = preg_replace('/\\.[^.\\s]{3,4}$/', '', $request->getRequestString());
        $urlKey = explode("/", $urlKey);

        if ($urlKey[1] != "imagegallery") {
            return false;
        }

        $routes = $this->categoryModel->getCollection()->addFieldToSelect('category_id')
        ->addFieldToFilter('url_key', $urlKey[2]);

        $catId = null;
        foreach ($routes as $route) {
            $catId = $route->getCategoryId();
        }
        if (!$catId) {
            return false;
        }

        $request->setModuleName('imagegallery')
            ->setControllerName('index')
            ->setActionName('view')
            ->setParam('category_id', $catId);
        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class,
            ['request' => $request]
        );
    }
}
