@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <h2 class="title1 col-md-4" style="width: 100%; margin-top: .8em"><a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a> / Add Product</h2>
                <div class="form-grids row widget-shadow" data-example-id="basic-forms">
                    <div class="form-body">
                        <form enctype="multipart/form-data" class="add-edit-product-form" method="post" action="#">
                            @csrf
                            <div class="customer-info-div">
                                <h4 class="header-wrapper header-customer-info">Customer Info</h4>
                                <div class="customer-info-wrapper body-customer-info">
                                    <div class="form-group row pd-0-10 body-customer-info">
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">Name</label> <button type="button" style="position: absolute; top: -10px; margin-left: 20px" class="btn btn-sm btn-primary toggle-user-plus-info"><i class="fa fa-user-plus"></i></button>
                                            <input required type="text" name="customer_name" class="form-control" id="customer_name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="customer_phone">Phone</label>
                                            <input type="number" required class="form-control" id="customer_phone" name="customer_phone">
                                        </div>
                                    </div>
                                    <div class="form-group user-plus-info row pd-0-10" style="display: none">
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">Address</label>
                                            <input required type="text" name="customer_address" class="form-control" id="customer_url">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="customer_name">URL</label>
                                            <input required type="text" name="customer_url" class="form-control" id="customer_url">
                                        </div>
                                    </div>
                                    <div class="form-group user-plus-info row pd-0-10" style="display: none">
                                        <div class="form-group col-md-6">
                                            <label for="prodDescription">More Info</label>
                                            <input type="text" class="form-control" id="customer_more_info" name="customer_more_info">
                                        </div>
                                        <div class="col-md-6">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-info-div">
                                <h4 class="header-wrapper header-order-info">Order Info</h4>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="customer_name">Date</label>
                                            <input required type="text" name="order_date" class="form-control order_date" id="order_date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Status</label>
                                            <select name="status_id" id="status_id" required class="form-control">
                                                @foreach(\App\Models\Order::ORDER_STATUS as $sKey => $status)
                                                    <option value="{{ $sKey }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Priority</label>
                                            <select name="priority" id="priority" required class="form-control">
                                                @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                                    <option @if(\App\Models\Order::PRIORITY_NORMAL == $pKey) selected @endif value="{{ $pKey }}">{{ $priority }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="customer_name">Shop Name</label>
                                            <select name="priority" id="priority" required class="form-control">
                                                @foreach($shops as $shop)
                                                    <option @if('MDS' == $shop->prefix) selected @endif value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Total Order</label>
                                            <input type="number"  value="0" class="form-control" id="total" name="total">
                                            <small style="color: red">* Total Without Ship Fee</small>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="customer_phone">Ship By Customer</label>
                                            <input type="number"  value="0" class="form-control" id="ship_by_customer" name="ship_by_customer">
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-4">
                                            <label for="ship_by_shop">Ship By Shop</label>
                                            <input type="number"  value="0" class="form-control" id="ship_by_shop" name="ship_by_shop">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="cost">Cost</label>
                                            <input type="number"  value="0" class="form-control" id="cost" name="cost">
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info-wrapper body-order-info">
                                    <div class="form-group row pd-0-10">
                                        <div class="form-group col-md-6">
                                            <label for="evidence">Evidence</label>
                                            <input class="custom-file-input" type="file" name="evidence[]" multiple="" id="evidence">
                                            <div style="margin-top: 10px; display: flex; justify-content: center; border-style: dotted; border-color: #4F52BA; max-width: 300px; max-height: 300px">
                                                <img class="image-trigger-upload-evidence" style="max-width: 250px; max-height: 250px" src="{{ asset('public/template/images/file-upload.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="notes">Notes</label>
                                            <textarea style="border-radius: 4px" class="form-control" id="notes" cols="10" rows="5" name="notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-detail-div">
                                <h4 class="header-wrapper header-order-detail" style="padding-left: 0">Order Detail</h4>
                                <div class="search-products row" style="padding: 10px 30px">
                                    {{--/*<?php $text = $product->sku ? '[' . $product->sku . '] ' . $product->name : $product->name ?>*/--}}
                                    <select name="product-list" class="select-product-list col-md-9" id="select-state" placeholder="Search product sku or name">
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    {{--<button type="button" class="btn btn-success btn-product-detail-row col-md-3"><i style="margin-right: 10px" class="fa fa-plus"></i>Add Product</button>--}}
                                </div>
                                <div class="order-detail-wrapper body-order-detail">
                                    <div class="form-group row pd-0-10">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>SKU</th>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Image</th>
                                                <th>Sub Products</th>
                                                <th>Action</th>
                                            </tr> </thead>
                                            <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Table cell</td>
                                                <td>Table cell</td>
                                                <td>Table cell</td>
                                                <td>Table cell</td>
                                                <td>Table cell</td>
                                                <td>Table cell</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <button style="margin: 20px" type="submit" class="btn btn-success">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .add-edit-product-form input, .add-edit-product-form select {
            border-radius: 4px;
        }
        .justify-content-center {
            display: flex;
            justify-content: center;
        }
        .first-supplier-row .btn-delete-supplier-row {
            opacity: 0.5;
            pointer-events: none;
        }
        .pd-0-10 {
            padding: 0 10px;
        }
        .header-wrapper {
            text-transform: uppercase;
            font-style: italic;
            color: green !important;
            cursor: pointer;
        }
        .custom-file-input {
            /*display: none;
            opacity: 0;
            height: 0;
            width: 0;*/
        }
        /*.custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            margin: 0;
            opacity: 0;
        }*/

        @media only screen and (max-width: 991px) {
            .search-products {
                padding: 0 !important;
            }
            .btn-product-detail-row {
                margin-top: 20px;
                margin-right: 15px;
                float: right;
            }
        }
    </style>
    <script src="{{ asset('public/js/orders.js') }}"></script>
@endsection
