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
            let prefix = params.fieldPrefix;
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
                                + '<input type="hidden" name="' + prefix + 'sku[' + product.sku + ']" value="' + product.sku + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="' + prefix + 'desc[' + product.sku + ']" value="' + product.desc + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="' + prefix + 'minqty[' + product.sku + ']" value="' + product.min_qty + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="' + prefix + 'maxqty[' + product.sku + ']" value="' + product.max_qty + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td>'
                                + '<input type="text" class="admin__control-text" name="' + prefix + 'price[' + product.sku + ']" value="' + product.price + '" data-form-part="eshow_form"/>'
                                + '</td>';
                            tableRow += '<td class="data-grid-checkbox-cell">'
                                + '<label class="data-grid-checkbox-cell-inner">'
                                + '<input class="admin__control-checkbox" id="remove' + prefix + product.sku + '" type="checkbox" name="' + prefix + 'remove[' + product.sku + ']" value="1" data-form-part="eshow_form"/>'
                                + '<label for="remove' + prefix + product.sku + '"></label>'
                                + '</label>'
                                + '</td>';

                            fillTableElement.find('tbody').append('<tr>' + tableRow + '</tr>');
                        });
                        sourceElement.clear();
                    }
                });
            }
        },
    });
});