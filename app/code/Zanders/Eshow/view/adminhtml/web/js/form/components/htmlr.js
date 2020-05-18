define([
    'jquery',
    'Magento_Ui/js/form/components/html',
    'uiRegistry',
    'underscore'
], function ($, html, uiRegistry, _) {
    'use strict';

    return html.extend({

        getProductInfo: function (params) {
            let thisRef = this;

            console.log(params);
            let sourceElement = uiRegistry.get(params.sourceElement);
            let fillTableElement = $(params.fillTableElement);
            if (sourceElement.value()) {
                $.ajax({
                    url: window.getProductInfoUrl,
                    showLoader: true,
                    data: {form_key: window.FORM_KEY, 'data': sourceElement.value()},
                    type: "POST",
                    dataType: 'json',
                    success: function (result) {
                        _(result).each(function (product) {
                            console.log(product);
                            let tableRow = '';
                            tableRow += '<td>'
                                + product.sku
                                + '<input type="hidden" name="pursku[' + product.sku + ']" value="' + product.sku + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="purdesc[' + product.sku + ']" value="' + product.desc + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="purminqty[' + product.sku + ']" value="' + product.min_qty + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="purmaxqty[' + product.sku + ']" value="' + product.max_qty + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="purprice[' + product.sku + ']" value="' + product.price + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td class="data-grid-checkbox-cell">'
                                + '<label class="data-grid-checkbox-cell-inner">'
                                + '<input class="admin__control-checkbox" id="removePI' + product.sku + '" type="checkbox" name="purremove[' + product.sku + ']" value="1" data-form-part="eshow_form"/>'
                                + '<label for="removePI' + product.sku + '"></label>'
                                + '</label>'
                                + '</td>';

                            fillTableElement.find('tbody').find('tr:last').after('<tr>' + tableRow + '</tr>');
                        });
                        sourceElement.clear();
                    }
                });
            }
        },
    });
});