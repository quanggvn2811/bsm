@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <h2 class="title1 col-md-2">Shopee Connection</h2>
                <div class="btn-create">
                    <button class="btn btn-success btn-add-stock">Add Stock</button>
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table">
                    <h4>Shop List</h4>
                    <table class="table">
                        <thead>
                        {{--<tr>
                            <th>#</th>
                            <th style="text-align: center; background-color: #222d32; color: #ffffff" colspan="2">BSM Shop</th>
                            <th style="text-align: center; background-color: #ee4d2d; color: #ffffff" colspan="3">Shopee Shop</th>
                        </tr>--}}
                        <tr>
                            <th>#</th>
                            <th>BSM Shop</th>
                            <th>Shopee Connection</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($shops as $shop)
                        <tr class="active">
                            <th scope="row">1</th>
                            <td>{{ $shop->name }}</td>
                            <td><i class="fa fa-check-circle"></i> Not connected</td>
                            <td>
                                <button class="btn btn-primary"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/stocks.js') }}"></script>
    <input type="hidden" value="-1" name="stock_id">
    @include('backend.stock.includes.add_update_stock_dialog')
@endsection
{{--{{ script('js/stocks.js') }}--}}
