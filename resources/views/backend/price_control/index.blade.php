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
                                <a href="">Price Control</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{--@include('backend.order.includes.search_form')--}}
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <h4 style="margin-bottom: 0">Price Product ({{ $products->total() }})</h4>
                    <div class="bsm-pagination" style="float: right">
                        {{ $products->appends(Request::all())->links() }}
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="" class="product-id">#</th>
                            <th style="" class="product-sku">SKU</th>
                            <th style="" class="product-name">Product Name</th>
                            <th style="" class="product-avatar">Avatar</th>
                            <th style="" class="product-cost">Cost</th>
                            @foreach($shops as $shop)
                                <th>{{ $shop->name }}</th>
                            @endforeach
                            <th style="" class="action">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                    <?php
                                    $prodImages = json_decode($product->images);
                                    $avatarSrc = '#';
                                    if (!empty($prodImages[0])) {
                                        $avatar = $prodImages[0];
                                        $avatarSrc = asset('public/' . \App\Models\Product::PUBLIC_PROD_IMAGE_FOLDER . '/' . $avatar);
                                    }
                                    ?>
                                <tr class="active product-lines" data-product-id="{{ $product->id }}">
                                    <td style="" class="product-id">{{ $product->id }}</td>
                                    <td style="" class="product-sku">{{ $product->sku }}</td>
                                    <td style="" class=""><span class="span-tooltip" data-toggle="tooltip" data-original-title="{{ $product->name }}">{{ $product->name }}</span></td>
                                    <td class="avatar" style="padding: 3px"><img class="avatar_product" style="max-width: 80px; max-height: 80px" src="{{ $avatarSrc }}"></td>
                                    <td style="" class="product-cost">{{ number_format($product->cost) }}</td>
                                    @foreach($shops as $shop)
                                        <td>
                                            <input data-product_id="{{ $product->id }}" data-shop_id="{{ $shop->id }}" {{--data-toggle="tooltip" data-original-title="{{ $product->name }}"--}} style="width: 120px; border-radius: 4px" type="number" class="form-control shop-price" value="{{ \App\Models\PriceControl::whereProductId($product->id)->whereShopId($shop->id)->first()->price ?? 0 }}">
                                            <i class="fa fa-check-circle alert-updated-price-control-{{$product->id . '_' . $shop->id}}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                                        </td>
                                    @endforeach
                                    <td class="action">
                                        <a class="btn btn-primary btn-edit-price-control" href="#"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bsm-pagination" style="float: right">
                        {{ $products->appends(Request::all())->links() }}
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
        .product-name {
            /*min-width: 150px;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;*/
        }
        .span-tooltip {
            max-width: 300px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    <script src="{{ asset('public/js/main.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script>
        $(document).ready(function () {
            $('.shop-price').on('change', function (e) {
                let price = $(this).val();
                if (parseInt(price) > 0) {
                    let padEnd = String(parseInt(price)).padEnd(String(parseInt(price)).length + 3, '0');
                    // new Intl.NumberFormat().format(parseInt(padEnd))
                    $(e.target).val(padEnd);
                    price = parseInt(padEnd);
                }

                let shopId = $(this).data('shop_id');
                let productId = $(this).data('product_id');

                $.ajax({
                    type:'POST',
                    url:'/admin/price_controls/save_shop_price_control',
                    dataType: 'json',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        price: price,
                        shop_id: shopId,
                        product_id: productId,
                    },
                    success: function(data) {
                        let alert = $('.alert-updated-price-control-' + productId + '_' + shopId);
                        alert.show();
                        setTimeout(function () {
                            alert.hide();
                        }, 2000);
                    },
                });
            });
        });
    </script>
@endsection
