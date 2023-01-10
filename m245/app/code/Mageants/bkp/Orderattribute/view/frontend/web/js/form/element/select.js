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
    'Magento_Ui/js/form/element/select',
    'Mageants_Orderattribute/js/action/observe-shipping-method',
    'Mageants_Orderattribute/js/form/relationAbstract'
], function (ko, _, utils, Select, observeShippingMethod, relationAbstract) {
    'use strict';

    // relationAbstract - attribute dependencies
    return Select.extend(relationAbstract).extend({
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
