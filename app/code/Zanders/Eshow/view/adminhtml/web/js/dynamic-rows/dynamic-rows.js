define([
    'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'uiRegistry',
    'underscore'
], function ($, dynamicRows, uiRegistry, _) {
    'use strict';

    return dynamicRows.extend({
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        addPurchaseItems: function (params) {
            let thisRef = this;

            let record = {
                sku: "H6",
                description: "DESCRIPTION",
                min_qty: "5",
                max_qty: "8",
                custom_price: "45"
            };

            console.log(thisRef.recordData().length);

            this.addChild({});

            console.log(thisRef.recordData().length);


            /*console.log(thisRef.recordData().length);
            this.addChild({}, thisRef.recordData().length);
            this._updateCollection();
            this.reinitRecordData();
            this.reload();


            console.log(thisRef.recordData().length);
            this.addChild({}, thisRef.recordData().length);*/

            // this.addChild(record, 0);

            /*var recordInstance,
                lastRecord,
                recordsData,
                lastRecordIndex;

            recordsData = this.recordData();
            recordInstance = _.find(this.elems(), function (elem) {
                return elem.index === index;
            });


            this._updateCollection();
            this.removeMaxPosition();
            // recordsData[recordInstance.index][this.deleteProperty] = this.deleteValue;
            this.recordData(recordsData);
            this.reinitRecordData();
            this.reload();
            this._reducePages();
            this._sort();*/


            /*
            let ele_purchaseItems = uiRegistry.get(params.purchaseItems);
            if (ele_purchaseItems.value()) {
                $.ajax({
                    url: window.getProductInfoUrl,
                    showLoader: true,
                    data: {form_key: window.FORM_KEY, 'data': ele_purchaseItems.value()},
                    type: "POST",
                    dataType: 'json',
                    success: function (result) {
                        // console.log(result);
                        let cnt = 0;
                        _(result).each(function (product) {
                            console.log(product);

                            console.log(thisRef.recordData().length);

                            thisRef.addChild({
                                sku: product.sku,
                                description: product.desc,
                                min_qty: product.min_qty,
                                max_qty: product.max_qty,
                                custom_price: product.price,
                                record_id: cnt
                            }, thisRef.recordData().length);

                            thisRef.recordData(recordsData);
                            thisRef.reinitRecordData();
                            thisRef.reload();

                            cnt++;

                        });
                    }
                });
            }*/
        },
    });
});