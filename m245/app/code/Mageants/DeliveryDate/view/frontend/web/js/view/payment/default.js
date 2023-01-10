/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'uiRegistry',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Ui/js/model/messages',
    'uiLayout',
    'Magento_Checkout/js/action/redirect-on-success'
    // 'jquery/jquery.cookie',
    // 'domReady!'
], function (
    ko,
    $,
    Component,
    placeOrderAction,
    selectPaymentMethodAction,
    quote,
    customer,
    paymentService,
    checkoutData,
    checkoutDataResolver,
    registry,
    additionalValidators,
    Messages,
    layout,
    redirectOnSuccessAction
) {
    'use strict';

    return Component.extend({
        redirectAfterPlaceOrder: true,
        isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

        /**
         * After place order callback
         */
        afterPlaceOrder: function () {
            // Override this function and put after place order logic here
        },

        /**
         * Initialize view.
         *
         * @return {exports}
         */
        initialize: function () {
            var billingAddressCode,
                billingAddressData,
                defaultAddressData;

            this._super().initChildren();
            quote.billingAddress.subscribe(function (address) {
                this.isPlaceOrderActionAllowed(address !== null);
            }, this);
            checkoutDataResolver.resolveBillingAddress();

            billingAddressCode = 'billingAddress' + this.getCode();
            registry.async('checkoutProvider')(function (checkoutProvider) {
                defaultAddressData = checkoutProvider.get(billingAddressCode);

                if (defaultAddressData === undefined) {
                    // Skip if payment does not have a billing address form
                    return;
                }
                billingAddressData = checkoutData.getBillingAddressFromData();

                if (billingAddressData) {
                    checkoutProvider.set(
                        billingAddressCode,
                        $.extend(true, {}, defaultAddressData, billingAddressData)
                    );
                }
                checkoutProvider.on(billingAddressCode, function (providerBillingAddressData) {
                    checkoutData.setBillingAddressFromData(providerBillingAddressData);
                }, billingAddressCode);
            });

            return this;
        },

        /**
         * Initialize child elements
         *
         * @returns {Component} Chainable.
         */
        initChildren: function () {
            this.messageContainer = new Messages();
            this.createMessagesComponent();

            return this;
        },

        /**
         * Create child message renderer component
         *
         * @returns {Component} Chainable.
         */
        createMessagesComponent: function () {

            var messagesComponent = {
                parent: this.name,
                name: this.name + '.messages',
                displayArea: 'messages',
                component: 'Magento_Ui/js/view/messages',
                config: {
                    messageContainer: this.messageContainer
                }
            };

            layout([messagesComponent]);

            return this;
        },

        /**
         * Place order.
         */
        placeOrder: function (data, event) {

            //add for check Delivery block validation Start
             
                if ($("#delivery_date").val() =="") {
                    $("div.date_message").remove();
                    $('#delivery_date').attr('style', "border-radius: 1px; border:#ed8380 1px solid;");
                    $("#delivery_date").parent().after("<div class='date_message' style='color:#e02b27;font-size:1.2rem;margin-top:7px;'>This is a required field.</div>");
                    return false;
                } else {
                    $("div.date_message").remove();
                    $("#delivery_date").removeAttr("style");
                    
                }
                
                if ($("#delivery_timeslot").val() =="")//last remove line && $.cookie('delivery_timeslot') == ''.. 
                {
                    $("div.timeslot_message").remove();
                    $("#delivery_timeslot").change(function() {
                        $("div.timeslot_message").remove();
                        if ($("#delivery_timeslot").val() =="") {
                            $('#delivery_timeslot').attr('style', "margin-top: 10px;border-radius: 1px; border:#ed8380 1px solid;");
                            $("#delivery_timeslot").parent().after("<div class='timeslot_message' style='color:#e02b27;font-size:1.2rem;margin-top:7px;'>This is a required field.</div>");
                            return false;                        
                        }else{
                            $("div.timeslot_message").remove();
                            $("#delivery_timeslot").attr('style', "margin-top: 10px;");
                        }
                    });
                    
                    $('#delivery_timeslot').attr('style', "margin-top: 10px;border-radius: 1px; border:#ed8380 1px solid;");
                    $("#delivery_timeslot").parent().after("<div class='timeslot_message' style='color:#e02b27;font-size:1.2rem;margin-top:7px;'>This is a required field.</div>");
                    return false;

                } else {
                    $("div.timeslot_message").remove();
                    $("#delivery_timeslot").change(function() {
                        $("div.timeslot_message").remove();
                        if ($("#delivery_timeslot").val() =="") {
                            $('#delivery_timeslot').attr('style', "margin-top: 10px;border-radius: 1px; border:#ed8380 1px solid;");
                            $("#delivery_timeslot").parent().after("<div class='timeslot_message' style='color:#e02b27;font-size:1.2rem;margin-top:7px;'>This is a required field.</div>");
                            return false;                        
                        }else{
                            $("div.timeslot_message").remove();
                            $("#delivery_timeslot").attr('style', "margin-top: 10px;");
                        }
                    });

                    $("div.timeslot_message").remove();
                    $("#delivery_timeslot").attr('style', "margin-top: 10px;");
                }
            //add for check Delivery block validation End
            
            var self = this;

            if (event) {
                event.preventDefault();
            }

            if (this.validate() && additionalValidators.validate()) {
                this.isPlaceOrderActionAllowed(false);

                this.getPlaceOrderDeferredObject()
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                        function () {
                            self.afterPlaceOrder();

                            if (self.redirectAfterPlaceOrder) {
                                redirectOnSuccessAction.execute();
                            }
                        }
                    );

                return true;
            }

            return false;
        },

        /**
         * @return {*}
         */
        getPlaceOrderDeferredObject: function () {
            return $.when(
                placeOrderAction(this.getData(), this.messageContainer)
            );
        },

        /**
         * @return {Boolean}
         */
        selectPaymentMethod: function () {
            selectPaymentMethodAction(this.getData());
            checkoutData.setSelectedPaymentMethod(this.item.method);

            return true;
        },

        isChecked: ko.computed(function () {
            return quote.paymentMethod() ? quote.paymentMethod().method : null;
        }),

        isRadioButtonVisible: ko.computed(function () {
            return paymentService.getAvailablePaymentMethods().length !== 1;
        }),

        /**
         * Get payment method data
         */
        getData: function () {
            return {
                'method': this.item.method,
                'po_number': null,
                'additional_data': null
            };
        },

        /**
         * Get payment method type.
         */
        getTitle: function () {
            return this.item.title;
        },

        /**
         * Get payment method code.
         */
        getCode: function () {
            return this.item.method;
        },

        /**
         * @return {Boolean}
         */
        validate: function () {
            return true;
        },

        /**
         * @return {String}
         */
        getBillingAddressFormName: function () {
            return 'billing-address-form-' + this.item.method;
        },

        /**
         * Dispose billing address subscriptions
         */
        disposeSubscriptions: function () {
            // dispose all active subscriptions
            var billingAddressCode = 'billingAddress' + this.getCode();

            registry.async('checkoutProvider')(function (checkoutProvider) {
                checkoutProvider.off(billingAddressCode);
            });
        }
    });
});
