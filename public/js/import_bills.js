jQuery.noConflict(true);

$(document).ready(function() {
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

        let plusRowHtml = '<tr data-product_id="' + selectedProductId + '" class="plus-product-item-row">\n' +
            '<th scope="row">' + index + '</th>\n' +
            '<td>' + selectedProduct["sku"] + '</td>\n' +
            '<td>' + selectedProduct["name"] + '</td>\n' +
            '<td><input type="number" class="form-control quantity-plus" value="1"></td>\n' +
            '<td class="td-cost-plus"><input type="number" class="form-control cost-plus" value="' + selectedProduct["cost"] + '"></td>\n' +
            '<td><img class="avatar-plus" style="max-width: 100px; max-height: 100px" src="' + avatarUrl + '" alt=""></td>\n' +
            '<td><button type="button" class="btn btn-danger btn-delete-plus-product-row"><i class="fa fa-trash"></i></button></td>\n' +
            '</tr>';

        $('.body-import-bill-products tbody').append(plusRowHtml);

        updateOrderProducts();
    });

    // For edit order
    setTimeout(function () {
        updateOrderProducts();
    }, 2000);

    $(document).on('click', '.btn-delete-plus-product-row', function (e) {
        $(e.target).closest('tr').remove();
        updateOrderProducts();
    });

    $(document).on('change', '.quantity-plus, .cost-plus, .price-plus', function (e) {
        updateOrderProducts();
    });

    function updateOrderProducts() {
        let orderProducts = [];
        const plusRows = $('.plus-product-item-row');
        let amountCost = 0;
        let total = 0;
        if (plusRows.length) {
            plusRows.each(function (index, plusRow) {
                let prodId = $(plusRow).data('product_id');
                let prodQty = $(plusRow).find('.quantity-plus').val();
                let prodCost = $(plusRow).find('.cost-plus').val();
                orderProducts.push([prodId, prodQty, prodCost].join(','));

                amountCost += prodQty * prodCost;
            })
        }

        $('.import_bill_products').val(orderProducts.join('_'));

        $('.total_bill').val(amountCost);

    }

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
});
