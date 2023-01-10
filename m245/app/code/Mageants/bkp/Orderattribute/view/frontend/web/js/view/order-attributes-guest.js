/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'uiRegistry',
        'Mageants_Orderattribute/js/action/save-order-attributes-to-quote'
    ],
    function (
        $,
        ko,
        Component,
        _,
        registry,
        saveOrderAttributesToQuote
    ) {

        return Component.extend({
            
            isVisible: ko.observable(false),
            /**
             * tmp save attributes which should be shown by relation,
             * for avoid hide by other relation option
             */
            dependsToShow: [],

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();

                registry.async('checkoutProvider')(function (checkoutProvider) {

                    checkoutProvider.on('shippingAddress.custom_attributes', function (orderAttributes) {
                        saveOrderAttributesToQuote(orderAttributes);
                    });
                    checkoutProvider.on('shippingAddress.custom_attributes_beforemethods', function (orderAttributes) {
                        saveOrderAttributesToQuote(orderAttributes);
                    });

                    jQuery('body').on(
                        {'click': function(){
                            var source = registry.get('checkoutProvider');
                            saveOrderAttributesToQuote(source.get('shippingAddress.custom_attributes_beforemethods'));
                        }},
                        "#billing-address-same-as-shipping-checkmo, .action.action-update"
                    );
                    jQuery('body').on(
                        {'click': function(){
                            saveOrderAttributesToQuote(null);
                        }},
                        "button[data-role='opc-continue']"
                    );

                });

                return this;
            },

            initObservable: function () {
                this._super()
                    .observe({
                        isBoolean: false
                    });
                this.elems.subscribe(function(childElements) {
                    childElements.map(function(element) {
                        // var element = registry.get(elementName);
                        if(element && _.isFunction(element.checkDependencies)) {
                            element.checkDependencies();
                        }
                    }.bind(this));
                    this.dependsToShow = [];
                }.bind(this));
                return this;
            }
        });
    }
);
