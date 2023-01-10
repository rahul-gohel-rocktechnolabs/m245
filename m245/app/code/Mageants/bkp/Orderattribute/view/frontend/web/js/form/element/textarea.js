/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */

define([
    'ko',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/textarea',
    'Mageants_Orderattribute/js/action/observe-shipping-method'
], function (ko, _, utils, TextArea, observeShippingMethod) {
    'use strict';

    return TextArea.extend({
        hidedByDepend: false,
        hidedByRate: false,
        /**
         * Calls 'initObservable' of parent, initializes 'options' and 'initialOptions'
         *     properties, calls 'setOptions' passing options to it
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            var observer = new observeShippingMethod(this);
            observer.observeShippingMethods();
            this._super();
            return this;
        }

    });
});
