$(document).ready(function (string) {

    var orders = JSON.parse($('#_orders').val());

    const STATUS_FAILED = 7;

    const RevenueReport = {
        selector: {
            statistical: $('#statistical-content'),
            charts: $('#charts-content'),
        },
        showReport: function (selector, callbackAction) {
            // Prepare data
            callbackAction();
        },
        statisticalMonth: function () {
            const searchParams = new URLSearchParams(window.location.search);
            let month = searchParams.has('month') ? searchParams.get('month') : moment().format('MM/YYYY');
            month = '01/' + month;
            let from = moment(month, 'DD/MM/YYYY').startOf('month').format('DD/MM/YYYY');
            let to = moment(month, 'DD/MM/YYYY').endOf('month').format('DD/MM/YYYY');
            let statisticalMonth = [];
            orders.forEach(function (order) {
                let isset = typeof statisticalMonth[order.order_date] !== 'undefined';
                let failed;
                let numberOfTotal;
                let total;
                let numberOfFailed;
                total = failed = numberOfTotal = numberOfFailed = 0;
                // date, failed, revenue, total order, profit
                if (STATUS_FAILED === order.status_id) {
                    failed = order.total;
                    numberOfFailed = 1;
                } else {
                    total = order.total;
                    numberOfTotal = 1;
                }

                if (isset) {
                    statisticalMonth[order.order_date].revenue += total;
                    statisticalMonth[order.order_date].numberOfTotal += numberOfTotal;
                    statisticalMonth[order.order_date].failed += failed;
                    statisticalMonth[order.order_date].numberOfFailed += numberOfFailed;
                } else {
                    statisticalMonth[order.order_date] = {
                        revenue: total,
                        numberOfTotal: numberOfTotal,
                        failed: failed,
                        numberOfFailed: numberOfFailed,
                        order_date: order.order_date,
                    }
                }

            });

            let html =
                '<table class="table statisticalMonth-table statisticalMonth">\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="min-width: 92px;" class="order-date">Date</th>\n' +
                '<th style="" class="number-of-failed">Number Of Failed</th>\n' +
                '<th style="" class="failed">Failed</th>\n' +
                '<th style="" class="number-of-revenue">Number Of Revenue</th>\n' +
                '<th style="" class="revenue">Revenue</th>\n' +
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
            while (moment(index, 'DD/MM/YYYY') <= moment(to, 'DD/MM/YYYY')) {
                html += '<tr class="active">';
                html += '<td>' + index + '</td>';
                if ('undefined' !== typeof statisticalMonth[index]) {
                    let data = statisticalMonth[index];

                    // Calculate sum
                    sumNumberOfFailed += data.numberOfFailed;
                    sumFailed += data.failed;
                    sumNumberOfTotal += data.numberOfTotal;
                    sumRevenue += data.revenue;

                    html += '<td>' + data.numberOfFailed + '</td>';
                    html += '<td>' + data.failed + '</td>';
                    html += '<td>' + data.numberOfTotal + '</td>';
                    html += '<td>' + getPriceFormat(data.revenue) + '</td>';
                } else {
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
            html += '<td>' + getPriceFormat(sumRevenue) + '</td>';
            html += '</tr>';

            html += '</tbody>\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="min-width: 92px;" class="order-date">Date</th>\n' +
                '<th style="" class="number-of-failed">Number Of Failed</th>\n' +
                '<th style="" class="failed">Failed</th>\n' +
                '<th style="" class="number-of-revenue">Number Of Revenue</th>\n' +
                '<th style="" class="revenue">Revenue</th>\n' +
                '</tr>\n' +
                '</thead>\n' +
                '</table>';
            RevenueReport.selector.statistical.find('.data-title').text('Statistical Month');
            RevenueReport.selector.statistical.find('.data-content').empty().append(html);
        }
    }

    RevenueReport.showReport(
        RevenueReport.selector.statistical,
        RevenueReport.statisticalMonth
    );
});
