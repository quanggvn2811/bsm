@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="main-page">
            <div class="tables">
                <h2 class="title1 col-md-2">Stocks</h2>
                <div class="btn-create">
                    <button class="btn btn-success btn-add-stock" {{--data-toggle="modal" data-target="#update-stock-dialog"--}}>Add Stock</button>
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table">
                    <h4>Stocks List</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Unique Prefix</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stocks as $index => $stock)
                        <tr class="active">
                            <th scope="row"> {{ $index }}</th>
                            <td><a href="{{ route('admin.categories.index', ['stock' => $stock->id]) }}">{{ $stock->name }}</a></td>
                            <td>{{ $stock->description }}</td>
                            <td>{{ $stock->status ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $stock->unique_prefix }}</td>
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
