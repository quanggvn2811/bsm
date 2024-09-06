<!-- Modal -->
<div class="modal fade" id="update-order-from-pancake-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Select Shop</label>
                    <select required name="select_update_shop" class="form-control select_update_shop col-md-6" id="select_update_shop">
                        @foreach($pancakeShopId as $prefix => $pCakeShopId)
                            <?php
                                $shopName = $shops[0]['name'];
                                foreach ($shops as $shop) {
                                    if ($prefix === $shop['prefix']) {
                                        $shopName = $shop['name'];
                                        break;
                                    }
                                }
                            ?>
                            <option value="{{ $pCakeShopId }}">{{ $shopName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Date From:</label>
                    <input required type="text" name="pancake_date_from" class="form-control pancake_date_from col-md-6" id="pancake_date_from">
                </div>
                <div class="form-group">
                    <label for="description">Date To:</label>
                    <input required type="text" name="pancake_date_to" class="form-control pancake_date_to col-md-6" id="pancake_date_to">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-do-update-order-from-pancake">Update</button>
            </div>
        </div>
    </div>
</div>
