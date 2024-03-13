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
                        <a href="{{ route('admin.products.index', $stock->id) }}">All Products</a></h2>
                    <div class="btn-create">
                        <a href="{{ route('admin.products.create', $stock->id) }}" class="btn btn-success btn-add-product">Add Product</a>
                    </div>
                </div>
                @include('backend.product.includes.search_form')
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto; position: relative">
                    <h4>Products List ({{ $products->total() }})</h4>
                    <div class="row" style="width: 200px; float: right; display: flex">
                        <div class="col-12  mt-2 text-right d-block d-sm-none">
                            {{ $products->appends(request()->input())->render('vendor.pagination.simple-bootstrap-4') }}
                        </div>
                        {{--<div class="col-7 text-right d-none d-sm-block">
                            {{ $products->appends(request()->input())->onEachSide(4)->links() }}
                        </div>--}}
                    </div>
                    <div class="form-check form-switch" style="display: inline-block; color: red; position: absolute; top: 1.6em; right: 1.5em; font-size: 18px; font-style: italic">
                        <input type="checkbox" id="is-today-checked-mode" name="is-today-checked-mode" value="1">
                        <label for="is-today-checked-mode">Today checked</label>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="hide_with_mobile">SKU</th>
                            <th >Name</th>
                            <th class="hide_with_mobile">Description</th>
                            <th>Avatar</th>
                            <th>Quantity</th>
                            <th class="hide_with_mobile">Category</th>
                            <th class="hide_with_mobile">Checked Date</th>
                            <th class="hide_with_mobile">Action</th>
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
                        <tr data-product_id="{{ $product->id }}" class="active product-lines">
                            <td class="sku hide_with_mobile"> {{ $product->sku }}</td>
                            <td class="name"><a href="{{ route('admin.products.edit', ['stock' => $stock->id, 'product' => $product->id]) }}">{{ $product->name }}</a></td>
                            <td class="description hide_with_mobile">{{ $product->description }}</td>
                            <td class="avatar" style="padding: 3px"><img class="avatar_product" style="max-width: 120px; max-height: 120px" src="{{ $avatarSrc }}"></td>
                            <td class="quantity">
                                <button class="btn btn-danger subQuantity"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-default quantityValue">
                                    {{ $product->quantity }}
                                </button>
                                <button class="btn btn-success plusQuantity"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="category hide_with_mobile">{{ $product->category->name }}</td>
                            <td class="checked-date">
                                <?php
                                    $bgColor = '#999';
                                    if (!empty($product->checked_date) && '01/01/2024' !== $product->checked_date) {
                                        $bgColor = '#399b12';
                                    }
                                ?>
                                <input value="{{ $product->checked_date }}" style="width: 110px; border-radius: 4px; background-color: {{ $bgColor }}; color: #ffffff" class="btn" type="text" name="checked-date-{{ $product->id }}" id="checked-date-{{ $product->id }}">
                                <i class="fa fa-check-circle alert-updated-checked-date-{{ $product->id }}" style="font-size: 20px; color: #00ad45; display: none" aria-hidden="true"></i>
                            </td>
                            <td class="btn-action hide_with_mobile">
                                <a href="{{ route('admin.products.edit', ['stock' => $stock->id, 'product' => $product->id]) }}" class="btn btn-primary btn-edit-product"><i class="fa fa-edit"></i></a>
                                <form style="display: inline-block" action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Delete this product, are you sure?')"
                                            class="btn btn-danger btn-delete-product">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
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
    </style>
    <script src="{{ asset('public/js/products.js')  . '?v=' . config('app.commit_version') }}"></script>
    <input type="hidden" value="{{ $stock->id }}" name="stock_id">
    <input type="hidden" value="{{ json_encode($products) }}" id="_products">
@endsection
