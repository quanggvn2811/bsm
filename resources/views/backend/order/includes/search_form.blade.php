<div class="form-search-advanced col">
    <form method="GET">
        <div class="row col">
            <h4 class="header-wrapper search-order-header">Search Orders</h4>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-2" for="title">Name</label>
                    <div class="col-md-10">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="customer_name"
                               value="{{$_GET['customer_name'] ?? ''}}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="title">Phone</label>
                    <div class="col-md-10">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="customer_phone"
                               value="{{$_GET['customer_phone'] ?? ''}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2" for="title">Order Date</label>
                    <div class="col-md-5">
                        <input required type="text" @if(isset($_GET['order_date_from'])) value="{{ $_GET['order_date_from'] }}" @endif name="order_date_from" class="form-control order_date_from col-md-6" id="order_date_from">
                    </div>
                    <div class="col-md-5">
                        <input required type="text" @if(isset($_GET['order_date_to'])) value="{{ $_GET['order_date_to'] }}" @endif name="order_date_to" class="form-control order_date_to col-md-6" id="order_date_to">
                    </div>
                </div>
            </div>
            <div class="row col-md-6 search-box-item">
                <div class="form-group row">
                    <label class="col-md-2" for="title">Order Number</label>
                    <div class="col-md-10">
                        <input class="form-control"  autofocus type="text" id="validationCustom01" name="order_number"
                               value="{{$_GET['order_number'] ?? ''}}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="title">Shop Name</label>
                    <div class="col-md-10">
                        <select name="shop_id" id="shop_id" required class="form-control">
                            @foreach($shops as $shop)
                                    <?php
                                    $selected = '';
                                    if (isset($_GET['shop_id'])) {
                                        if ($_GET['shop_id'] == $shop->id) {
                                            $selected = 'selected';
                                        }
                                    } else if('MDS' === $shop->prefix) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                <option {{ $selected }} value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                                {{--<option value="">All Shops</option>--}}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="col-md-2" for="title">Priority</label>
                        <div class="col-md-10">
                            <select name="priority" id="priority" class="form-control">
                                <option value="0">All Priorities</option>
                                @foreach(\App\Models\Order::ORDER_PRIORITY as $pKey => $priority)
                                        <?php
                                        $selectedPriority = '';
                                        if (isset($_GET['priority'])) {
                                            if ($_GET['priority'] == $pKey) {
                                                $selectedPriority = 'selected';
                                            }
                                        }
                                        ?>
                                    <option {{ $selectedPriority }} value="{{ $pKey }}">{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="col-md-2" for="title">Status</label>
                        <div class="col-md-10">
                            <select name="status_id" id="" class="form-control">
                                <option value="0">All Status</option>
                                @foreach(\App\Models\Order::ORDER_STATUS as $sKey => $status)
                                        <?php
                                        $selectedStatus = '';
                                        if (isset($_GET['status_id'])) {
                                            if ($_GET['status_id'] == $sKey) {
                                                $selectedStatus = 'selected';
                                            }
                                        }
                                        ?>
                                    <option {{ $selectedStatus }} value="{{ $sKey }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row col search-box-item justify-content-center">
            <button class="btn btn-lg btn-primary pl-3 pr-3"  type="submit">Search</button>
            <a class="btn btn-lg btn-dark pl-3 pr-3" href="{{ route('admin.orders.index', ['stock' => $stock->id]) }}" style="margin-left: 10px">Reset</a>
        </div>
    </form>
</div>
<style>
    .justify-content-center {
        display: flex;
        justify-content: center;
    }
    .header-wrapper {
        text-transform: uppercase;
        font-style: italic;
        color: green !important;
        cursor: pointer;
    }
    .search-box-item {
        /*display: none;*/
    }
</style>
