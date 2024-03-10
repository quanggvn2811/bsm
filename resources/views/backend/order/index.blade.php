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
                {{--@include('backend.product.includes.search_form')--}}
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4>Orders List ({{ $orders->total() }})</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="">Date</th>
                            <th >Order Number</th>
                            <th class="">Priority</th>
                            <th>Status</th>
                            <th>Shipping Unit</th>
                            <th class="hide_with_mobile">Notes</th>
                            <th class="">Customer</th>
                            <th class="">Phone</th>
                            <th class="hide_with_mobile">Address</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                        <tr data-order_id="{{ $order->id }}" class="active order-lines">
                            <td class="order_date">{{ $order->order_date }}</td>
                            <td class="order_number" style="font-weight: bold; font-size: 18px"><a href="{{ route('admin.orders.show', ['stock' => $stock->id, 'order' => $order->id]) }}">{{ $order->order_number }}</a></td>
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
                            <td class="address hide_with_mobile">{{ $order->order_address }}</td>
                        </tr>
                        @endforeach
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
        .product-lines td {
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

        .tables .order_priority .normal {
            color: #fff;
            background-color: #5cb85c !important;
            border-color: #4cae4c !important;
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
        }
        .tables .order_priority .high {
            color: #fff;
            background-color: #c9302c !important;
            border-color: #ac2925 !important;
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
        }
        .tables .order_priority .low {
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
            background-color: #999;
            border-color: #999;
            color: #fff;
        }

        .tables .order_status select {
            border-radius: 8px;
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
            border-radius: 8px;
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
    </style>
    <script src="{{ asset('public/js/orders.js')  . '?v=' . config('app.commit_version') }}"></script>
@endsection
