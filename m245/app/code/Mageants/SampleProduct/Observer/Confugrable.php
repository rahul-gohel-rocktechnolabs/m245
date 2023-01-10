<?php
/**
 * @category   Mageants SampleProduct
 * @package    Mageants_SampleProduct
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\SampleProduct\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session;

class Confugrable implements ObserverInterface
{
    const MAX_QTY = 'sample_section/sample_general/max_qty';
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Framework\App\Request\Http $request
     */
    
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession
    ) {
        $this->_productloader = $_productloader;
        $this->messageManager = $messageManager;
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
      /*echo "called";
      exit;*/
        $item = $observer->getEvent()->getData('quote_item');
      
        $quote = $item->getQuote();

        if ($this->_request->getParam('sample-price')) {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            $params = $this->getRequest()->getParams();
            try {
                if (isset($params['qty'])) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');
                if ($product->getTypeId()=="configurable") {
                    $_children = $product->getTypeInstance()->getUsedProducts($product);
                    $additionalOptions[] = [
                        'label' => __("Sample Order"),
                        'value' => ' - '.__("Yes"),
                    ];
                    foreach ($_children as $child) {
                      //  print_r(get_class_methods($child));exit;
                        if ($child->getOfferSample()) {
                            unset($params['super_attribute']);
                            //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $formKey = $this->_objectManager->create('\Magento\Framework\Data\Form\FormKey');
                            $params['form_key'] = $formKey->getFormKey();
                            $params['product']=$child->getId();
                            $params['qty']=1;
                            $params['price']=1;
                            //$child->addCustomOption('additional_options', serialize($additionalOptions));
                                
                             $this->cart->addProduct($child, $params);
                            $this->cart->save();
                        }
                    }
                    if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                        if (!$this->cart->getQuote()->getHasError()) {
                            $message = __(
                                'You added %1 to your shopping cart.',
                                $product->getName()
                            );
                            $this->messageManager->addSuccessMessage($message);
                        }
                        return $this->goBack(null, $product);
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $this->messageManager->addNotice(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                    );
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->messageManager->addError(
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                        );
                    }
                }

                $url = $this->_checkoutSession->getRedirectUrl(true);

                if (!$url) {
                    $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                    $url = $this->_redirect->getRedirectUrl($cartUrl);
                }

                return $this->goBack($url);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->goBack();
            }
        }
    }
}
