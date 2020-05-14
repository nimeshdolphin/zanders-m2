define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'uiRegistry',
    'underscore'
], function ($, dynamicRows, registry, _) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(purchaseItems){
            alert(purchaseItems);
        },
    });
});