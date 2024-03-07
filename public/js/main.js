$(document).ready(function() {
    // Auto concat 000 and the end of number
    $(document).on('change', '#total, #ship_by_customer, #ship_by_shop, #cost, .cost-plus, .price-plus, .is-price-type', function (e) {
        let value = e.target.value;
        if (parseInt(value) > 0) {
            let padEnd = String(parseInt(value)).padEnd(String(parseInt(value)).length + 3, '0');
            // new Intl.NumberFormat().format(parseInt(padEnd))
            $(e.target).val(padEnd);
        }
    })
});
