/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define(
    [   
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/template'
    ],
    function (storage, errorProcessor, fullScreenLoader, template) {
        'use strict';

        //var displayAt = window.checkoutConfig.shippingproduct.delivery_date_product.displayAt;
        if(window.checkoutConfig.shipping){
            jQuery("#payment-delivery #date").text(jQuery('[name="delivery_date"]').val());
            jQuery("#payment-delivery #timeslot").text(jQuery('[name="delivery_timeslot"]').val());
            if(jQuery('[name="delivery_comment"]').val() != null){

                jQuery("#payment-delivery #comment").text(jQuery('[name="delivery_comment"]').val());
            }else{
                jQuery("#payment-delivery #comment").hide();
            }

            setInterval(function () {
                var pageURL = jQuery(location).attr("href");
                if(pageURL.indexOf("payment") != -1){
                    jQuery("#payment-delivery").show();

                    jQuery("#payment-delivery #date").text(jQuery.cookie('delivery_date'));
                    jQuery("#payment-delivery #timeslot").text(jQuery.cookie('delivery_timeslot'));
                    if(jQuery.cookie('delivery_comment')){

                        jQuery("#payment-delivery #comment").text(jQuery.cookie('delivery_comment'));
                    }else{
                        jQuery("#payment-delivery #comment").hide();
                    }
                } else {
                    jQuery("#payment-delivery").hide();
                }
            },1000);
        }
       
        return function (serviceUrl, payload, messageContainer) {
            fullScreenLoader.startLoader();

            return storage.post(
                serviceUrl, JSON.stringify(payload)
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                }
            ).always(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
