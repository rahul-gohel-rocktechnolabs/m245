var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Mageants_Orderattribute/js/action/set-shipping-information-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/paypal-express-abstract': {
                'Mageants_Orderattribute/js/action/paypal-express-abstract': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'Mageants_Orderattribute/js/view/shipping-information/address-renderer/default-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Mageants_Orderattribute/js/view/shipping-mixin': true
            }
        }
    }
};
