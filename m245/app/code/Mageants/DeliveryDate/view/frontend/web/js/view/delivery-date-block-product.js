define(
    [   'jquery',
        'ko',
        'uiComponent',
    ],
    function ($, ko, Component) {
        'use strict';
        
        return Component.extend({
            DeliveryDateProduct : ko.observable(window.checkoutConfig.shippingproduct.delivery_date_product.date_product),
            DeliveryTimeProduct : ko.observable(window.checkoutConfig.shippingproduct.delivery_date_product.time_product),
            DeliveryCommentProduct : ko.observable(window.checkoutConfig.shippingproduct.delivery_date_product.comment_product)
        });
    }
);