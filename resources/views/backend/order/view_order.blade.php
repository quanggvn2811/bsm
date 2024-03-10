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
                    {{--/*<?php dd($order) ?>*/--}}
                    <h2 class="title1 col-md-4">{{ $stock->name }} /
                        {{ $order->order_number }}</h2>
                    <div class="container col-md-8">
                        <div class="row">
                            <div class="btn-back col-md-2" style="margin: 10px 0">
                                <a href="{{ route('admin.orders.index', $stock->id) }}" class="btn btn-dark btn-add-product">Back<i class="fa fa-backward" aria-hidden="true"></i></a>
                            </div>
                            <div class="update_priority col-md-3" data-order_id="{{ $order->id }}" style="margin: 10px 0">
                                <select name="priority" id="priority" class="form-control btn {{strtolower(\App\Models\Order::ORDER_PRIORITY[$order->priority])}}">
                                    @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                        <option @if($order->priority == $pKey) selected @endif value="{{ $pKey }}">{{ $priority }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-priority-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </div>
                            <div class="update_status col-md-3" data-order_id="{{ $order->id }}" style="margin: 10px 0">
                                <select name="status_id" id="status_id" class="form-control btn {{str_replace(' ', '_', strtolower(\App\Models\Order::ORDER_STATUS[$order->status_id]))}}">
                                    @foreach(\App\Models\Order::ORDER_STATUS as $statusKey => $status)
                                        <option @if($order->status_id == $statusKey) selected @endif value="{{ $statusKey }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                                <i class="fa fa-check-circle alert-updated-status-{{ $order->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row pd-0-10 bs-example widget-shadow" style="overflow-x:auto;">
                    <?php
                        $totalItems = 0;
                        foreach ($order->order_detail as $detail) {
                            $qty = $detail->quantity ?? 1;
                            $totalItems += $qty;
                        }
                    ?>
                    <h4 class="header-wrapper order-detail-header">Orders Detail - Total: <span style="color: red !important;">{{ $totalItems }}</span> ITEMS</h4>
                    <table class="table table-bordered detail-order order-detail-body">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Image</th>
                                <th>SKU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->order_detail as $index => $detail)
                                    <?php
                                    $prodImages = json_decode($detail->product->images);
                                    $avatarSrc = '#';
                                    if (!empty($prodImages[0])) {
                                        $avatar = $prodImages[0];
                                        $avatarSrc = asset('public/' . \App\Models\Product::PUBLIC_PROD_IMAGE_FOLDER . '/' . $avatar);
                                    }
                                    ?>
                                <tr data-product_id="" class="product-item-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('admin.products.edit', ['stock' => $stock->id, 'product' => $detail->product->id]) }}">{{ $detail->product->name }}</a></td>
                                    <td style="width: 50px; text-align: center"><button type="button" class="btn @if(1 === intval($detail->quantity ?? 1)) btn-dark @else btn-danger @endif">
                                        {{ $detail->quantity ?? 1 }}</button></td>
                                    <td style="width: 200px; height: 200px; padding: 0">
                                        <img class="avatar-item" style="max-width: 200px; max-height: 200px" src=" {{ $avatarSrc }}" alt="">
                                    </td>
                                    <td style="width: 50px; text-align: center">{{ $detail->product->sku }}</td>
                                </tr>
                            @endforeach
                        <tr>
                            <td colspan="5" style="color: red; border-left: 10px solid red">
                                {{ $order->notes }}
                            </td>
                        </tr>
                            <tr>
                                <td>
                                    BOX SIZE
                                    <br><small style="color: red">DVT: Centimets</small>
                                </td>
                                <td colspan="4">
                                    {{--<form action="" method="post">--}}
                                        <div class="row">
                                            <?php
                                                $x = $y = $z = 0;

                                                if ($order->box_size) {
                                                    list($x, $y, $z) = explode(';', $order->box_size);
                                                }
                                            ?>
                                            <div class="col-md-3 box-size-field">
                                                <input value="{{ $x }}" name="long" required type="number" class="form-control box-size-input" style="border-radius: 4px" placeholder="Long (Chiều Dài)">
                                            </div>
                                            <div class="col-md-3 box-size-field">
                                                <input value="{{ $y }}" name="wide" required type="number" class="form-control box-size-input" style="border-radius: 4px" placeholder="Wide (Chiều Rộng)">
                                            </div>
                                            <div class="col-md-3 box-size-field">
                                                <input value="{{ $z }}" required name="high" type="number" class="form-control box-size-input" style="border-radius: 4px" placeholder="High (Chiều Cao)">
                                            </div>
                                            <div class="col-md-3 box-size-field">
                                                <button class="btn btn-success btn-save-box-size">Save</button>
                                                <i class="fa fa-check-circle alert-updated-box-size" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                                                <span class="save-box-size-error" style="color: red; display: none"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> X, Y, Z is required!</span>
                                            </div>
                                        </div>
                                    {{--</form>--}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4 class="header-wrapper customer-info-header">Customer Info</h4>
                    <div class="customer-info customer-info-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Name<i style="color: #337ab7; margin-left: 10px; font-size: 20px" class="fa fa-clone content-info-name-icon" data-trigger_to="content-info-name"></i></label>
                                <div class="content-info-name col-md-8">{{ $order->customer->name }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Phone<i style="color: #337ab7; margin-left: 10px; font-size: 20px" class="fa fa-clone content-info-phone-icon" data-trigger_to="content-info-phone"></i></label>
                                <div class="content-info-phone col-md-8">{{ $order->customer->phone }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Address<i style="color: #337ab7; margin-left: 10px; font-size: 20px" class="fa fa-clone content-info-address-icon" data-trigger_to="content-info-address"></i></label>
                                <div class="content-info-address col-md-8">{{ $order->order_address }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4">URL</label>
                                <div class="content-info col-md-8">{{ $order->customer->info_url }}</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4">More Info</label>
                                <div class="content-info col-md-8">{{ $order->more_info }}</div>
                            </div>
                            <div class="form-group col-md-4">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4 class="header-wrapper order-info-header">Order Info And Calculator</h4>
                    <div class="order-info order-info-body" style="display: none">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Order Date</label>
                                <div class="content-info col-md-8">{{ $order->order_date }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Order number</label>
                                <div class="content-info col-md-8">{{ $order->order_number }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Shop Name</label>
                                <div class="content-info col-md-8">{{ $order->shop->name }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Total Order</label>
                                <div class="content-info col-md-8">{{ number_format($order->total) }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Ship By Customer</label>
                                <div class="content-info col-md-8">{{ number_format($order->ship_by_customer) }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Ship By Shop</label>
                                <div class="content-info col-md-8">{{ number_format($order->ship_by_shop) }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Amount Cost</label>
                                <div class="content-info col-md-8">{{ number_format($order->cost) }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">Amount Profit</label>
                                <?php $amountCost = $order->total + $order->ship_by_customer - $order->ship_by_shop - $order->cost; ?>
                                <div class="content-info col-md-8">{{ number_format($amountCost) }}</div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-md-4">% Profit</label>
                                <div class="content-info col-md-8">{{ $order->total > 0 ? number_format($amountCost / $order->total, 2) : 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_order_id" id="_order_id" value="{{ $order->id }}">
    <style>
        .detail-order td {
            vertical-align: middle !important;
        }
        .header-wrapper {
            text-transform: uppercase;
            font-style: italic;
            color: green !important;
            cursor: pointer;
        }
        .update_priority .normal {
            color: #fff;
            background-color: #5cb85c !important;
            border-color: #4cae4c !important;
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
        }
        .update_priority .high {
            color: #fff;
            background-color: #c9302c !important;
            border-color: #ac2925 !important;
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
        }
        .update_priority .low {
            border-radius: 8px;
            padding: 5px 15px;
            width: 110px;
            background-color: #999;
            border-color: #999;
            color: #fff;
        }

        .update_status select {
            border-radius: 8px;
            padding: 5px 15px;
            width: 130px;
            border-color: #999;
            color: #fff;
        }
        .update_status .waiting {
            background-color: rgb(230, 230, 230);
            color: rgb(61, 61, 61);
        }
        .update_status .pending {
            background-color: rgb(255, 207, 201);
            color: rgb(177, 2, 2);
        }
        .update_status .today_handle {
            background-color: #5cb85c;
            border-color: #4cae4c;
        }
        .update_status .processing {
            background-color: rgb(255, 229, 160);
            color: rgb(71, 56, 33);
        }
        .update_status .take_care {
            background-color: #c9302c;
            border-color: #ac2925;
        }
        .update_status .shipped {
            background-color: rgb(191, 225, 246);
            color: rgb(10, 83, 168);
        }
        .update_status .failed {
            background-color: rgb(90, 50, 134);
            color: rgb(229, 207, 242);
        }
        .update_status .completed {
            background-color: rgb(212, 237, 188);
            color: rgb(17, 115, 75);
        }
        @media only screen and (max-width: 991px) {
            .box-size-field {
                margin: 10px 0;
                max-width: 250px;
            }
        }
    </style>
    <script src="{{ asset('public/js/orders.js')  . '?v=' . config('app.commit_version') }}"></script>
@endsection
