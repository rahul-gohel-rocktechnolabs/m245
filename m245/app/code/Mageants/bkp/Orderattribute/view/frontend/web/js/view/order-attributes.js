/**
 * @category  Mageants Orderattribute
 * @package   Mageants_Orderattribute
 * @copyright Copyright (c) 2020 Mageants
 * @author    Mageants Team <support@Mageants.com>
 */
define(
    [
        'jquery',
        'Mageants_Orderattribute/js/view/order-attributes-guest'
    ],
    function ($, orderAttributesGuest) {

        return orderAttributesGuest.extend({
            defaults: {
                template: 'Mageants_Orderattribute/order-attributes'
            }
        });
    }
);
