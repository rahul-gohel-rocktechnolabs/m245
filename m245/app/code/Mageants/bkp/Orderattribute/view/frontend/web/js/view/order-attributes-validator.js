
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Mageants_Orderattribute/js/model/order-attributes-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';

        additionalValidators.registerValidator(agreementValidator);

        return Component.extend({});
    }
);

