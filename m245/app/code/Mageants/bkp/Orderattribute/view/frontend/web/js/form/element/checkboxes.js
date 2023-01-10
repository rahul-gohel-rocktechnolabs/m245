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
    'Magento_Ui/js/form/element/abstract',
    'Mageants_Orderattribute/js/action/observe-shipping-method',
    'Mageants_Orderattribute/js/form/relationAbstract'
], function (ko, _, utils, Abstract, observeShippingMethod, relationAbstract) {
    'use strict';

    // relationAbstract - attribute dependencies
    return Abstract.extend(relationAbstract).extend({
        hidedByDepend: false,
        hidedByRate: false,
        /**
         * Calls 'initObservable' of parent, initializes 'options' and 'initialOptions'
         *     properties, calls 'setOptions' passing options to it
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            var defaultValue = this.value;
            var observer = new observeShippingMethod(this);
            observer.observeShippingMethods();
            this._super();
            var value = this.value;
            this.value = ko.observableArray([]).extend(value);
            this.value(this.normalizeData(defaultValue));
            return this;
        },

        /**
         * Splits incoming string value.
         *
         * @returns {Array}
         */
        normalizeData: function (value) {
            if (utils.isEmpty(value)) {
                value = [];
            }

            return _.isString(value) ? value.split(',') : value;
        },

        /**
         * Defines if value has changed
         *
         * @returns {Boolean}
         */
        hasChanged: function () {
            var value = this.value(),
                initial = this.initialValue;

            return !utils.equalArrays(value, initial);
        },

        /**
         * override of relationAbstract
         * @param relation
         * @returns {boolean|*}
         */
        isCanShow: function(relation) {
            return this.value().indexOf(relation.option_value) >= 0 && this.visible();
        }
    });
});
