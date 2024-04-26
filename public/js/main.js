$(document).ready(function() {
    // Auto concat 000 and the end of number
    $(document).on('change', '#total, #ship_by_customer, #ship_by_shop, #cost, .is-price-type', function (e) {
        let value = e.target.value;
        if (parseInt(value) > 0) {
            let padEnd = String(parseInt(value)).padEnd(String(parseInt(value)).length + 3, '0');
            // new Intl.NumberFormat().format(parseInt(padEnd))
            $(e.target).val(padEnd);
        }
    });

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });

    window.getPriceFormat = function (value) {
        return parseFloat(value, 10).toFixed(2)
            .replace(/(\d)(?=(\d{3})+\.)/g, "$1,")
            .toString()
            .replace('.00', '')
            ;
    }

    $('.title-header-toggle').on('click', function (e) {
        let triggerTo = $(this).data('trigger_to');
        console.log(triggerTo)
        $('.' + triggerTo).toggle();
    });
});
