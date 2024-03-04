$(document).ready(function() {
    const ProductQuantity = {
        prodId: null,
        quantityValueSelector: '',
        updateQuantity: function (plusValue, quantityValueSelector) {
            if (null === this.prodId) {
                return false;
            }

            $.ajax({
                type:'POST',
                url:'/admin/products/' + this.prodId + '/update_quantity',
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val(),
                    plus_value: plusValue,
                },
                success: function(data) {
                    quantityValueSelector.text(data.product_quantity);
                    $('.bsm-alert').show();
                    setTimeout(function () {
                        $('.bsm-alert').hide();
                    }, 2000);
                    // window.location.reload();
                },
                error: function() {
                }
            });
        }
    }

    $('.quantity .subQuantity').on('click', function () {
        ProductQuantity.prodId = $(this).closest('tr').data('product_id');
        ProductQuantity.updateQuantity(-1, $(this).closest('tr').find('.quantityValue'));
    });

    $('.quantity .plusQuantity').on('click', function () {
        ProductQuantity.prodId = $(this).closest('tr').data('product_id');
        ProductQuantity.updateQuantity(1, $(this).closest('tr').find('.quantityValue'));
    });

    $('.prodType').on('change', function () {
        const TYPE_MULTIPLE = 2;
        TYPE_MULTIPLE == $(this).val() ? $('.sub_product_section').show() : $('.sub_product_section').hide();
    });

    $('.avatar_product').on('click', function (e) {
        const $this = $(e.target);
        let imgWith = e.target.offsetWidth;
        const defaultWidth = 120;
        if (imgWith > defaultWidth) {
            $this.css("max-width", defaultWidth);
            $this.css("max-height", defaultWidth);
        } else {
            $this.css("max-width", 300);
            $this.css("max-height", 300);
        }
    });

    // Add supplier row
    $('.btn-add-supplier-row').on('click', function (e) {
        const template = $('.supplier-row-template');
        const prodSuppliersIndex = template.attr('data-prod_suppliers_index');
        let clone = template.clone().removeClass('supplier-row-template').css('display', 'block');
        clone.find('.prod_suppliers_id').attr('name', 'prod_suppliers[' + prodSuppliersIndex + '][id]');
        clone.find('.prod_suppliers_cost').attr('name', 'prod_suppliers[' + prodSuppliersIndex + '][cost]').addClass('is_active');
        clone.find('.prod_suppliers_sku').attr('name', 'prod_suppliers[' + prodSuppliersIndex + '][sku]');
        template.attr('data-prod_suppliers_index', parseInt(prodSuppliersIndex) + 1);
        $('.suppliers').append(clone);
    });

    $(document).on('click', '.btn-delete-supplier-row', function (e) {
        $(e.target).closest('.supplier-row').removeClass('is_active').remove();
        calculateAVGCost();
    });

    $('.btn-edit-average-cost').on('click', function (e) {
        $('input[name="cost"]').removeAttr('readonly').focus();
    });

    $(document).on('keyup', '.prod_suppliers_cost', calculateAVGCost);

    function calculateAVGCost() {
        let suppliersCostElm = $('.prod_suppliers_cost');
        let totalCost = 0;
        suppliersCostElm.each(function (index, elm) {
            if (!isNaN(parseInt($(elm).val())) && $(elm).hasClass('is_active')) {
                totalCost += parseInt($(elm).val());
            }
        });

        let avgCost = totalCost / (suppliersCostElm.length - 1);

        $('#avg_cost').val(avgCost.toFixed(2));
    }
});



