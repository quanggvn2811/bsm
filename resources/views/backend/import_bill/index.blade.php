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
                    <div class="col-md-4 pd-l-0">
                        <div class="short-url-menu">
                            <div class="first">
                                <a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a>
                            </div>
                            <div class="second">
                                <a href="{{ route('admin.import_bills.index', $stock->id) }}">All Bills</a>
                            </div>
                        </div>
                    </div>
                    <div class="btn-create">
                        <a href="{{ route('admin.import_bills.create', $stock->id) }}" class="btn btn-success btn-add-product">Add A Bill</a>
                    </div>
                </div>
                {{--@include('backend.order.includes.search_form')--}}
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4 style="margin-bottom: 0">Bills ({{ $importBills->total() }})</h4>
                    <div class="bsm-pagination" style="float: right">
                        {{ $importBills->appends(Request::all())->links() }}
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="" class="">#</th>
                            <th style="min-width: 92px;" class="">Date</th>
                            <th>Supplier</th>
                            <th class="">Total Bill</th>
                            <th>Notes</th>
                            <th class="" style="min-width: 120px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalBill = 0; ?>
                            @foreach($importBills as $bill)
                                <?php $totalBill += $bill->total; ?>
                                <tr data-import_bill_id="{{ $bill->id }}" class="bill-line active">
                                    <td><a href="{{ route('admin.import_bills.edit', ['stock' => $stock->id, 'importBill' => $bill->id]) }}">{{ $bill->id }}</a></td>
                                    <td>{{ $bill->date }}</td>
                                    <td>{{ $bill->supplier->name ?? '' }}</td>
                                    <td>{{ number_format($bill->total) }}</td>
                                    <td>{{ $bill->notes }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-edit-bill" href="{{ route('admin.import_bills.edit', ['stock' => $stock->id, 'importBill' => $bill->id]) }}"><i class="fa fa-edit"></i></a>
                                        <div class="" style="display: inline-block">
                                            <form style="display: inline-block" action="{{ route('admin.import_bills.destroy', ['stock' => $stock->id, 'importBill' => $bill->id]) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" onclick="return confirm('Delete bill #{{ $bill->id }}, are you sure?')"
                                                        class="btn btn-danger btn-delete-product">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="active">
                                <td colspan="3" style="color: red; border-left: 10px solid red">
                                    Total Bills
                                </td>
                                <td colspan="3">{{ number_format($totalBill) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="bsm-pagination" style="float: right">
                        {{ $importBills->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bill-line td {
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

        @media only screen and (max-width: 1200px) {
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
