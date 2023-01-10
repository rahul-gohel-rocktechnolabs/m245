define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'orderapproval',
                component: 'Mageants_OrderApprovalRules/js/view/payment/method-renderer/orderapproval-method'
            }
        );
        return Component.extend({});
    }
);
