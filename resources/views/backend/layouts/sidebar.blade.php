<?php $stock = \App\Models\Stock::whereName('MAKE STOCK')->first(); ?>
<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">
    <!--left-fixed -navigation-->
    <aside class="sidebar-left">
        <nav class="navbar navbar-inverse">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <h1><a class="navbar-brand" href="{{ url('admin/') }}"><span class="fa fa-area-chart"></span> BSM<span class="dashboard_text">Basic Stock Manager</span></a></h1>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="sidebar-menu">
                    {{--<li class="header">MAIN NAVIGATION</li>--}}
                    <li class="treeview">
                        <a href="{{ url('admin/') }}">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="treeview @if(\Illuminate\Support\Facades\Route::is('admin/stocks/*')) active @endif">
                        <a href="{{ url('admin/stocks') }}">
                            <i class="fa fa-database"></i> <span>Stocks</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="{{ route('admin.categories.index', $stock->id) }}">
                            <i class="fa fa-list"></i> <span>Categories</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-product-hunt"></i>
                            <span>Products</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('admin.products.index', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i>All Products</a></li>
                            <li><a href="{{ route('admin.products.create', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i>Add Product</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-shopping-cart"></i>
                            <span>Orders</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('admin.orders.index', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i>All Orders</a></li>
                            <li><a href="{{ route('admin.orders.create', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i> Add Order</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-file"></i>
                            <span>Bills</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('admin.import_bills.index', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i>All Bills</a></li>
                            <li><a href="{{ route('admin.import_bills.create', ['stock' => $stock->id]) }}"><i class="fa fa-angle-right"></i>Add A Bill</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="{{ route('admin.price_controls.index', $stock->id) }}">
                            <i class="fa fa-money"></i> <span>Price Control</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
    </aside>
</div>
