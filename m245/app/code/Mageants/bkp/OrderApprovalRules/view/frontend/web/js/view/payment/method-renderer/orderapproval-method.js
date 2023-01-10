define(
    [	
		'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';
            return Component.extend({
            initialize: function () {
                this._super();
            	this.MessageForBuyers = window.checkoutConfig.message_for_buyers;
            },
            defaults: {
                template: 'Mageants_OrderApprovalRules/payment/orderapproval'
            },
			getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            }	
        });
    }
);