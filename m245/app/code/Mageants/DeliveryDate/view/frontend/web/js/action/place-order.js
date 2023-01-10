/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/place-order'
    // 'jquery/jquery.cookie',
    // 'domReady!'
], function ($,quote, urlBuilder, customer, placeOrderService) {
    'use strict';

    return function (paymentData, messageContainer) {
        var serviceUrl, payload;

        payload = {
            cartId: quote.getQuoteId(),
            billingAddress: quote.billingAddress(),
            paymentMethod: paymentData
        };
    
        var timeslot = $('[name="delivery_timeslot"]').val() ? $('[name="delivery_timeslot"]').val() : $.cookie('delivery_timeslot'); 
        
        $.cookie('delivery_date', $('[name="delivery_date"]').val(), { path: '/' });
        $.cookie('delivery_comment', $('[name="delivery_comment"]').val(), { path: '/' });
        $.cookie('delivery_timeslot', timeslot, { path: '/' });

        if (customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
        } else {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                quoteId: quote.getQuoteId()
            });
            payload.email = quote.guestEmail;
        }

        return placeOrderService(serviceUrl, payload, messageContainer);
    };
});
