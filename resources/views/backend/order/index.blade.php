@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <div id="page-wrapper">
        @include('includes.messages')
        <div class="main-page">
            <div class="tables">
                <div class="row">
                    <h2 class="title1 col-md-4"><a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a> /
                        <a href="{{ route('admin.orders.index', $stock->id) }}">All Orders</a></h2>
                    <div class="btn-create">
                        <a href="{{ route('admin.orders.create', $stock->id) }}" class="btn btn-warning btn-add-product">Add Order</a>
                    </div>
                </div>
                @include('backend.order.includes.search_form')
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4 style="margin-bottom: 0">Orders List ({{ $orders->total() }})</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="min-width: 92px;" class="">Date</th>
                            <th >Order Number</th>
                            <th class="">Priority</th>
                            <th>Status</th>
                            <th>Shipping Unit</th>
                            <th class="hide_with_mobile">Notes</th>
                            <th class="min-with-150">Customer</th>
                            <th class="">Phone</th>
                            <th class="hide_with_mobile min-with-150">Address</th>
                            @if($isAdmin)
                                <th class="total">Total</th>
                                <th class="amount-profit">Amount Profit</th>
                                <th class="profit-percent">% Profit</th>
                            @endif
                            <th class="">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sumTotal = 0;
                            $sumProfit = 0;
                        ?>
                        @foreach($orders as $order)
                        <tr data-order_id="{{ $order->id }}" class="active order-lines">
                            <td class="order_date">{{ $order->order_date }}</td>
                            <?php
                                $redirectUrl = route('admin.orders.show', ['stock' => $stock->id, 'order' => $order->id]);
                                session()->put('url_back_to_order_list', url()->full());
                            ?>
                            <td class="order_number" style="font-weight: bold; font-size: 18px"><a href="{{ $redirectUrl }}">{{ $order->order_number }}</a></td>
                            <td class="order_priority">
                                <select name="priority" id="priority" class="form-control btn {{strtolower(\App\Models\Order::ORDER_PRIORITY[$order->priority])}}">
                                    @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                        <option @if($order->priority == $pKey) selected @endif value="{{ $pKey }}">{{ $priority }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-priority-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </td>
                            <td class="order_status">
                                <select name="status_id" id="status_id" class="form-control btn {{str_replace(' ', '_', strtolower(\App\Models\Order::ORDER_STATUS[$order->status_id]))}}">
                                    @foreach(\App\Models\Order::ORDER_STATUS as $statusKey => $status)
                                        <option @if($order->status_id == $statusKey) selected @endif value="{{ $statusKey }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-status-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </td>
                            <td class="order_shipping_unit">
                                <select name="shipping_unit" id="shipping_unit" class="form-control shipping_unit btn {{ strtolower(\App\Models\ShippingUnit::whereId($order->shipping_unit)->first()->acronym) }}">
                                    @foreach($shippingUnits as $unit)
                                        <option @if($order->shipping_unit == $unit->id) selected @endif value="{{ $unit->id }}">{{ $unit->acronym }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-shipping-unit-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </td>
                            <td class="order_notes hide_with_mobile">{!! $order->notes !!}</td>
                            <td class="customer">{{ $order->customer->name }}</td>
                            <td class="phone">{{ $order->customer->phone }}</td>
                            <td class="address hide_with_mobile min-with-150">{{ $order->order_address }}</td>
                            @if($isAdmin)
                                    <?php
                                    $amountProfit = $order->total + $order->ship_by_customer - $order->ship_by_shop - $order->cost;
                                    $sumTotal += $order->total;
                                    $sumProfit += $amountProfit;
                                    ?>
                                <td class="total">{{ number_format($order->total) }}</td>
                                <td class="amount-profit">{{ number_format($amountProfit) }}</td>
                                <td class="profit-percent">{{ $order->total > 0 ? number_format($amountProfit / $order->total, 2) : 0 }}%</td>
                            @endif
                            <td class="action"><a class="btn btn-primary btn-edit-order" href="{{ route('admin.orders.edit', ['stock' => $stock->id, 'order' => $order->id]) }}"><i class="fa fa-edit"></i></a></td>
                        </tr>
                        @endforeach
                        @if($isAdmin)
                            <tr class="active order-lines">
                                <td colspan="100%" style="color: red;">
                                    Sum Total: {{ number_format($sumTotal) }} -
                                    Sum Profit: {{ number_format($sumProfit) }} -
                                    Sum % Profit: {{ $sumTotal > 0 ? number_format($sumProfit / $sumTotal, 2) : 0 }}%
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="row" style="width: 200px; float: right; display: flex">
                        <div class="col-12  mt-2 text-right d-block d-sm-none">
                            {{ $orders->appends(request()->input())->render('vendor.pagination.simple-bootstrap-4') }}
                        </div>
                        {{--<div class="col-7 text-right d-none d-sm-block">
                            {{ $orders->appends(request()->input())->onEachSide(4)->links() }}
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .order-lines td {
            vertical-align: middle !important;
        }
        .product-lines .name, .product-lines .description {
            max-width: 250px;
        }

        .form-search-advanced input, .form-search-advanced select {
            border-radius: 4px;
        }
        @media only screen and (max-width: 900px) {
            .hide_with_mobile {
                display: none;
            }
            .quantityValue {
                margin: 5px 0;
            }
            .prod_calculation_wrapper {
                margin-bottom: 15px;
            }
        }

        @media only screen and (max-width: 767px) {
            .date-to-wrapper {
                margin-top: 20px !important;
            }
        }

        @media only screen and (max-width: 1024px) {
            .tables .order_priority .btn {
                padding: 0 !important;
                width: 100px !important;
                height: 28px;
            }
            .tables .order_status .btn {
                padding: 0 !important;
                width: 110px !important;
                height: 28px;
            }
            .tables .order_shipping_unit .btn {
                height: 28px;
                padding: 0 !important;
            }
            .btn-edit-order {
                padding: 5px 14px;
                font-size: 11px;
                line-height: 1.5;
                border-radius: 3px;
            }
            .order-lines td {
                padding: 10px !important;
            }
            .btn-sm-action {
                padding: 5px 14px;
                font-size: 13px;
                line-height: 1.5;
                border-radius: 3px;
            }
        }

        .tables .order_priority .normal {
            color: #fff;
            background-color: #5cb85c !important;
            border-color: #4cae4c !important;
            border-radius: 4px;
            padding: 5px 15px;
            width: 110px;
        }
        .tables .order_priority .high {
            color: #fff;
            background-color: #c9302c !important;
            border-color: #ac2925 !important;
            border-radius: 4px;
            padding: 5px 15px;
            width: 110px;
        }
        .tables .order_priority .low {
            border-radius: 4px;
            padding: 5px 15px;
            width: 110px;
            background-color: #999;
            border-color: #999;
            color: #fff;
        }

        .tables .order_status select {
            border-radius: 4px;
            padding: 5px 15px;
            width: 130px;
            border-color: #999;
            color: #fff;
        }
        .tables .order_status .waiting {
            background-color: rgb(230, 230, 230);
            color: rgb(61, 61, 61);
        }
        .tables .order_status .pending {
            background-color: rgb(255, 207, 201);
            color: rgb(177, 2, 2);
        }
        .tables .order_status .today_handle {
            background-color: #5cb85c;
            border-color: #4cae4c;
        }
        .tables .order_status .processing {
            background-color: rgb(255, 229, 160);
            color: rgb(71, 56, 33);
        }
        .tables .order_status .take_care {
            background-color: #c9302c;
            border-color: #ac2925;
        }
        .tables .order_status .shipped {
            background-color: rgb(191, 225, 246);
            color: rgb(10, 83, 168);
        }
        .tables .order_status .failed {
            background-color: rgb(90, 50, 134);
            color: rgb(229, 207, 242);
        }
        .tables .order_status .completed {
            background-color: rgb(212, 237, 188);
            color: rgb(17, 115, 75);
        }
        #shipping_unit {
            width: 100px;
            border-radius: 4px;
        }
        .ghn {
            color: #f26522;
        }
        .vtp {
            color: #EE0033;
        }
        .best {
            color: rgba(226,7,38,.88);
        }
        .ghtk {
            color: #01904a;
        }
        .min-with-150 {
            min-width: 150px;
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    <script src="{{ asset('public/js/orders.js')  . '?v=' . config('app.commit_version') }}"></script>
@endsection
