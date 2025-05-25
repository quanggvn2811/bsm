$(document).ready(function (string) {

    var orders = JSON.parse($('#_orders').val());

    const STATUS_FAILED = 7;

    const RevenueReport = {
        selector: {
            statistical: $('#statistical-content'),
            summary: $('#summary-content'),
            charts: $('#charts-content'),
        },
        showReport: function (selector, callbackAction) {
            // Prepare data
            callbackAction();
        },
        prepareOrderField: function () {
            let returnData = [];
            orders.forEach(function (order) {
                let isset = typeof returnData[order.order_date] !== 'undefined';
                let failed;
                let numberOfTotal;
                let total;
                let profit;
                let numberOfFailed;
                total = failed = numberOfTotal = numberOfFailed = profit = 0;
                // date, failed, revenue, total order, profit
                if (STATUS_FAILED === order.status_id) {
                    failed = order.total;
                    numberOfFailed = 1;
                } else {
                    total = order.total;
                    numberOfTotal = 1;
                    profit = order.total + order.ship_by_customer - order.ship_by_shop - order.cost;
                }

                if (isset) {
                    returnData[order.order_date].revenue += total;
                    returnData[order.order_date].numberOfTotal += numberOfTotal;
                    returnData[order.order_date].failed += failed;
                    returnData[order.order_date].profit += profit;
                    returnData[order.order_date].numberOfFailed += numberOfFailed;
                } else {
                    returnData[order.order_date] = {
                        revenue: total,
                        numberOfTotal: numberOfTotal,
                        failed: failed,
                        numberOfFailed: numberOfFailed,
                        profit: profit,
                        order_date: order.order_date,
                    }
                }

            });

            return returnData;
        },
        getSearchFromTo: function () {
            const searchParams = new URLSearchParams(window.location.search);
            let from = searchParams.get('bill_date_from');
            let to = searchParams.get('bill_date_to');
            if (!from && !to) {
                let month = searchParams.has('month') ? searchParams.get('month') : moment().format('MM/YYYY');
                month = '01/' + month;
                from = moment(month, 'DD/MM/YYYY').startOf('month').format('DD/MM/YYYY');
                to = moment(month, 'DD/MM/YYYY').endOf('month').format('DD/MM/YYYY');
            }

            return [from, to]
        },
        statisticalMonth: function () {
            let [from ,to] = RevenueReport.getSearchFromTo();
            let statisticalMonth = RevenueReport.prepareOrderField();
            let html =
                '<table class="table statisticalMonth-table statisticalMonth">\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="min-width: 92px;" class="order-date">Date</th>\n' +
                '<th style="" class="number-of-failed">Number Of Failed</th>\n' +
                '<th style="" class="failed">Failed</th>\n' +
                '<th style="" class="number-of-revenue">Number Of Revenue</th>\n' +
                '<th style="" class="revenue">Revenue</th>\n' +
                '<th style="" class="profit">Profit</th>\n' +
                '<th style="" class="profit-percent">% Profit</th>\n' +
                '</tr>\n' +
                '</thead>\n' +
                '<tbody>\n';

            /*for (let date in statisticalMonth) {
                let data = statisticalMonth[date];
                html += '<tr>';
                html += '<td>' + date + '</td>';
                html += '<td>' + data.numberOfFailed + '</td>';
                html += '<td>' + data.failed + '</td>';
                html += '<td>' + data.numberOfTotal + '</td>';
                html += '<td>' + data.revenue + '</td>';
                html += '</tr>';
            }*/
            // Loop with sort
            let index = from;
            let sumNumberOfFailed = 0;
            let sumFailed = 0;
            let sumNumberOfTotal = 0;
            let sumRevenue = 0;
            let sumProfit = 0;
            while (moment(index, 'DD/MM/YYYY') <= moment(to, 'DD/MM/YYYY')) {
                html += '<tr class="active">';
                let orderUrl = $('#_order_list_url').val();
                orderUrl += '?order_date_from=' + index;
                orderUrl += '&order_date_to=' + index;
                orderUrl += '&status_id[]=0';
                html += '<td><a href="' + orderUrl + '">' + index + '</a></td>';
                if ('undefined' !== typeof statisticalMonth[index]) {
                    let data = statisticalMonth[index];

                    // Calculate sum
                    sumNumberOfFailed += data.numberOfFailed;
                    sumFailed += data.failed;
                    sumNumberOfTotal += data.numberOfTotal;
                    sumRevenue += data.revenue;
                    sumProfit += data.profit;

                    html += '<td>' + data.numberOfFailed + '</td>';
                    html += '<td>' + data.failed + '</td>';
                    html += '<td>' + data.numberOfTotal + '</td>';
                    html += '<td>' + getPriceFormat(data.revenue) + '</td>';
                    html += '<td>' + getPriceFormat(data.profit) + '</td>';
                    html += '<td>' + (100 * (data.profit / data.revenue)).toFixed(2) + ' %</td>';
                } else {
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                }

                html += '</tr>';

                index = moment(index, 'DD/MM/YYYY').add(1, 'day').format('DD/MM/YYYY');
            }

            html += '<tr class="active" style="color: red">';
            html += '<td>Sum</td>';
            html += '<td>' + sumNumberOfFailed + '</td>';
            html += '<td>' + sumFailed + '</td>';
            html += '<td>' + sumNumberOfTotal + '</td>';
            // Todo:: check this section:  sumProfit = sumRevenue - sumProfitOfFail
            html += '<td>' + getPriceFormat(sumRevenue) + '</td>';
            html += '<td>' + getPriceFormat(sumProfit) + '</td>';
            html += '<td>' + (100 * (sumProfit / sumRevenue)).toFixed(2) + ' %</td>';
            html += '</tr>';

            html += '</tbody>\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="min-width: 92px;" class="order-date">Date</th>\n' +
                '<th style="" class="number-of-failed">Number Of Failed</th>\n' +
                '<th style="" class="failed">Failed</th>\n' +
                '<th style="" class="number-of-revenue">Number Of Revenue</th>\n' +
                '<th style="" class="revenue">Revenue</th>\n' +
                '<th style="" class="revenue">Profit</th>\n' +
                '<th style="" class="revenue">% Profit</th>\n' +
                '</tr>\n' +
                '</thead>\n' +
                '</table>';
            RevenueReport.selector.statistical.find('.data-title').text('Statistical Report');
            RevenueReport.selector.statistical.find('.data-content').empty().append(html);
        },
        summaryMonth: function () {

        }
    }

    RevenueReport.showReport(
        RevenueReport.selector.statistical,
        RevenueReport.statisticalMonth
    );

    // Config date from/ date to
    $('input[name="bill_date_from"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    const searchParams = new URLSearchParams(window.location.search);
    if (!searchParams.has('bill_date_from')) {
        $('#bill_date_from').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'),10),
            startDate: moment().startOf('month').format('DD/MM/YYYY'),
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
        })
            .attr('readonly', 'readonly');
    } else {
        $('#bill_date_from').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
        })
            .attr('readonly', 'readonly');
    }

    if (!searchParams.has('bill_date_to')) {
        $('#bill_date_to').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'),10),
            startDate: moment().endOf('month').format('DD/MM/YYYY'),
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
        })
            .attr('readonly', 'readonly');
    } else {
        $('#bill_date_to').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 2000,
            maxYear: parseInt(moment().format('YYYY'),10),
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
        })
            .attr('readonly', 'readonly');
    }
    $('input[name="bill_date_to"]').on('apply.daterangepicker', function(ev, picker) {
        const datePicker = picker.endDate.format('DD/MM/YYYY');
        $(this).val(datePicker);
    });

    $('.search-date').on('click', function (e) {
        let from = $('.bill_date_from').val();
        let to = $('.bill_date_to').val();

        // Today search
        if ($(this).hasClass('today')) {
            from = to = moment().format('DD/MM/YYYY');
        }

        if ($(this).hasClass('yesterday')) {
            from = to = moment().subtract(1, 'day').format('DD/MM/YYYY');
        }

        if ($(this).hasClass('this-month')) {
            from = moment().startOf('month').format('DD/MM/YYYY');
            to = moment().endOf('month').format('DD/MM/YYYY');
        }

        $('.bill_date_from').val(from);
        $('.bill_date_to').val(to);
        $('.btn-submit-search').click();
    });

    // Toggle search orders
    var TOGGLE_SEARCH_REVENUE_KEY = 'IS_SHOW_SEARCH_REVENUE';

    let toggleSearchRevenueStatus = window.localStorage.getItem(TOGGLE_SEARCH_REVENUE_KEY) ?? 0;

    toggleSearchRevenueStatus == 1 ? $('.search-box-item').show() : $('.search-box-item').hide();

    function updateToggleSearchRevenueKey(key) {
        window.localStorage.setItem(TOGGLE_SEARCH_REVENUE_KEY, key);
    }

    $('.search-bill-header').on('click', function (e) {
        $('.search-box-item').toggle();
        $('.search-box-item').is(":visible") ? updateToggleSearchRevenueKey(1) : updateToggleSearchRevenueKey(0);
    });
});
