<?php
/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessOrder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;
 
    /**
     * Constructor function
     *
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }
    /**
     * Multishipping Remove order function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $x = $this->cookieManager->getCookie('loop');
        for ($i=1; $i<=$x; $i++) {

            if ($this->cookieManager->getCookie('delivery_date'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_date'.$i,
                     $metadata
                 );
            }

            if ($this->cookieManager->getCookie('delivery_timeslot'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_timeslot'.$i,
                     $metadata
                 );
            }
            if ($this->cookieManager->getCookie('delivery_comment'.$i)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath('/');
     
                 $this->cookieManager->deleteCookie(
                     'delivery_comment'.$i,
                     $metadata
                 );
            }
        }
        return true;
    }
}
