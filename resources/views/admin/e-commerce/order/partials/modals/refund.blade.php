{{-- Refund Modal 1 --}}
<div class="modal fade" id="refund" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="post" action="{{ route('admin.refund') }}">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" name="order" value="{{ $order->id }}">
                        <input class="form-control" type="text" placeholder="amount" name="amount">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="method">
                            <option value="wallate">Wallate</option>
                            <option value="Bank">Bank</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Refund</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Refund Modal 2 --}}
<div class="modal fade" id="refund2" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="post" action="{{ route('admin.refund_2') }}">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" name="order" value="{{ $order->id }}">
                        <input class="form-control" type="text" placeholder="amount" name="amount">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="method">
                            <option value="wallate">Wallate</option>
                            <option value="Bank">Bank</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Refund</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>