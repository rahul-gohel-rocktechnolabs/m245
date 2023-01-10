define([
    'jquery',
    'ko',
    'uiComponent',
    //'Magento_Checkout/js/view/shipping-information',
    'jquery/jquery.cookie',
    'Magento_Ui/js/model/messageList',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component,Cookie,messageList,modal,quote,customerData) {
    'use strict';
        if(window.checkoutConfig.shippingproduct){
            var Url = window.checkoutConfig.shippingproduct.delivery_date_product.baseUrl;
            var deliverydate = window.checkoutConfig.shippingproduct.delivery_date_product.date_product;
            var deliverytime = window.checkoutConfig.shippingproduct.delivery_date_product.time_product;
            var deliverycomment = window.checkoutConfig.shippingproduct.delivery_date_product.comment_product;
            var productData = ko.observableArray();
            return Component.extend({         
                isConfigurationProduct: ko.observable(true),
                isNotConfigurationProduct: ko.observable(false),
                productData:productData,
                defaults: {
                    template: 'Mageants_DeliveryDate/delivery-sidebar-view'
                },
                initialize: function(){
                     this._super(); 
                     this.populateMyItems();             
                     return this;
                 }, 
                populateMyItems: function(){
                     var self =  this;
                     $.ajax({
                        url: Url,
                        data:{
                            deliverydate: deliverydate,
                            deliverytime: deliverytime,
                            deliverycomment: deliverycomment
                        },
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            localStorage.setItem("cartData", data);
                            self.productData(JSON.parse(data));
                        },
                        error: function () {
                            console.log('Error happens. Try again.');
                        }
                     });
                     this.productData(JSON.parse(localStorage.getItem("cartData")));   
                     localStorage.removeItem('cartData');                        
                }
            });
        } else {
            return Component.extend({
                isConfigurationProduct : ko.observable(false),
                isNotConfigurationProduct : ko.observable(true),
                defaults: {
                    template: 'Mageants_DeliveryDate/delivery-sidebar-view'
                }
            });
        }
});