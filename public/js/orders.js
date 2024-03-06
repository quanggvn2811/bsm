jQuery.noConflict(true);

$(document).ready(function() {

    $('.toggle-user-plus-info').click(function () {
        $('.user-plus-info').toggle();
    });

    $('.header-customer-info').click(function () {
        $('.body-customer-info').toggle();
    });

    $('.header-order-info').click(function () {
        $('.body-order-info').toggle();
    });

    $('.header-order-detail').click(function () {
        $('.body-order-detail').toggle();
    });

    $('#order_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1920,
        maxYear: parseInt(moment().format('YYYY'),10),
        locale: {
            format: 'DD/MM/YYYY'
        },
        autoApply: true,
    })
        .attr('readonly', 'readonly');
    $('input[name="order_date"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    $('.image-trigger-upload-evidence').on('click', function (e) {
        $('.custom-file-input').click();
    });

    $('.select-product-list').selectize({
        //sortField: 'text'
    });

    $('.btn-add-product-detail-row').on('click', function (e) {
        const selectedProductId = $('.select-product-list').val();
        if (!selectedProductId) {
            return;
        }

        const productById = JSON.parse($('#product_by_id_string').val());

        let selectedProduct = productById[selectedProductId];

        let avatarUrl = JSON.parse(selectedProduct['images'])[0] ?? '#';

        avatarUrl = productImagePublicFolder + '/' + avatarUrl;

        let index = $('.plus-product-item-row').length + 1;

        let subProductIds = selectedProduct['sub_product_id'];

        let subProductList = '';

        if (subProductIds) {
            subProductIds = JSON.parse(subProductIds);
            subProductIds.forEach(function (subProductId) {
                let subProductName = productById[subProductId]['name'];
                if (subProductList) {
                    subProductList += '<br>> ' + subProductName;
                } else {
                    subProductList += '> ' + subProductName;
                }
            })
        }

        let plusRowHtml = '<tr class="plus-product-item-row">\n' +
            '<th scope="row">' + index + '</th>\n' +
            '<td>' + selectedProduct["sku"] + '</td>\n' +
            '<td>' + selectedProduct["name"] + '</td>\n' +
            '<td><input type="number" class="form-control quantity-plus" value="1"></td>\n' +
            '<td><input type="number" class="form-control cost-plus" value="' + selectedProduct["cost"] + '"></td>\n' +
            '<td><input type="number" class="form-control cost-plus" value="' + selectedProduct["price"] + '"></td>\n' +
            '<td><img class="avatar-plus" style="max-width: 100px; max-height: 100px" src="' + avatarUrl + '" alt=""></td>\n' +
            '<td>' + subProductList + '</td>\n' +
            '<td><button type="button" class="btn btn-danger btn-delete-plus-product-row"><i class="fa fa-trash"></i></button></td>\n' +
            '</tr>';

        $('.body-order-detail tbody').append(plusRowHtml);

    });

    $(document).on('click', '.btn-delete-plus-product-row', function (e) {
        $(e.target).closest('tr').remove();
    })
});
