<?php

/**
 * @category Mageants DeliveryDate
 * @package Mageants_DeliveryDate
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\DeliveryDate\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    /**
     * @var quoteRepository
     */
    protected $quoteRepository;

    /**
     * Constructor function
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Before Save Address Save Data function
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param mixed $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
      
        $extAttributes = $addressInformation->getExtensionAttributes();
        $deliveryDate = $extAttributes->getDeliveryDate();

        $deliveryComment = $extAttributes->getDeliveryComment();
        $deliveryTimeslot = $extAttributes->getDeliveryTimeslot();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setDeliveryDate($deliveryDate);
        $quote->setDeliveryComment($deliveryComment);
        $quote->setDeliveryTimeslot($deliveryTimeslot);
    }
}
