<div class="col-sm-12 col-12 text-right"
     style="display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end;
            grid-column-gap: 8px;">
    @if ($order->status != 5)
        @if ($order->status != 2)
            @if ($order->status != 3)
                <a title="@if ($order->pay_staus == 1) Unpaid @else Paid @endif"
                   href="{{ route('admin.order.pay', ['id' => $order->id]) }}"
                   class="btn @if ($order->pay_staus == 1) btn-danger @else btn-success @endif btn-sm">
                    <i class="fas fa-money-bill"></i>
                    @if ($order->pay_staus == 1)
                        Unpaid
                    @else
                        Paid
                    @endif
                </a>
            @endif
        @endif

        @if (setting('STEEDFAST_STATUS') == 1 && $order->status != 9)
            <form action="{{ route('admin.setting.courier.sendsteedfast') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice" value="{{ $order->invoice }}">
                <input type="hidden" name="recipient_name" value="{{ $order->first_name }}">
                <input type="hidden" name="recipient_phone" value="{{ $order->phone }}">
                <input type="hidden" name="recipient_address"
                       value="{{ $order->address . ', ' . $order->town . ', ' . $order->district . ', ' . $order->post_code }}">
                @if ($order->pay_staus == 1)
                    <input type="hidden" name="cod_amount" value="0.00">
                @else
                    <input type="hidden" name="cod_amount" value="{{ $order->total }}">
                @endif
                <input type="hidden" name="note" value="N/A">
                <input class="btn btn-info btn-sm" type="submit" value="Send Courier">
            </form>
        @else
            <i class="btn btn-info btn-sm">Courierd Already</i>
        @endif

        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editOrderModal">
            <i class="fas fa-edit"></i>
            Edit Order
        </button>

        @if(!empty($order->phone))
            <button type="button" class="btn btn-purple btn-sm" data-toggle="modal" data-target="#smsModal">
                <i class="fas fa-sms"></i>
                Send SMS
            </button>
        @endif

        <a title="Processing" href="{{ routeHelper('order/status/processing/' . $order->id) }}"
           onclick="return confirm('Are you sure you want to change the status of this order?')"
           class="btn btn-primary btn-sm">
            <i class="fas fa-running"></i>
            Processing
        </a>

        @if ($order->status == 6)
            <a title="Accept return request"
               href="{{ routeHelper('order/status/return_req_accept/' . $order->id) }}"
               onclick="return confirm('Return process will start. Are you sure?')" class="btn btn-success btn-sm">
                Return Accept
            </a>
        @elseif ($order->status == 7)
            <a title="Complete the return process, you got the product from customer as a return completely."
               href="{{ routeHelper('order/status/return_complete/' . $order->id) }}"
               onclick="return confirm('Complete the return, you got the product from customer?')"
               class="btn btn-success btn-sm">
                Return Complete
            </a>
        @elseif ($order->status != 2 && $order->status != 3 && $order->status != 6 && $order->status != 7 && $order->status != 8)
            <a title="Shipping" href="{{ routeHelper('order/status/shipping/' . $order->id) }}"
               id="btnShipping" onclick="return confirm('Are you sure you want to mark this order as shipping?')"
               class="btn btn-info btn-sm">
                <i class="fas fa-plane"></i> Shipping
            </a>

            <a title="Delivered" href="{{ routeHelper('order/status/delivered/' . $order->id) }}"
               onclick="return confirm('Are you sure you want to mark this order as delivered?')"
               class="btn btn-success btn-sm">
                <i class="fas fa-thumbs-up"></i>
                Delivered
            </a>
        @endif
        @if ($order->status != 3 && $order->status != 2)
            <a title="Cancel" href="{{ routeHelper('order/status/cancel/' . $order->id) }}"
               onclick="return confirm('Are you sure you want to cancel this order?')"
               class="btn btn-warning btn-sm">
                <i class="fas fa-window-close"></i>
                Cancel
            </a>
        @endif
    @endif
    @if ($order->status == 3)
        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                data-target="#refund">
            Refund
        </button>
    @endif
    @if ($order->status == 2)
        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                data-target="#refund2">
            Refund
        </button>
    @endif
    <a href="{{ route('admin.order.delete', ['did' => $order->id]) }}"
       onclick="return confirm('Are you sure you want to delete this order permanently?')"
       class="btn btn-danger btn-sm"><i class="nav-icon fas fa-trash-alt"></i> Delete</a>
    <a href="{{ routeHelper('order/print/' . $order->id) }}" rel="noopener" target="_blank"
       class="btn btn-default"><i class="fas fa-print"></i> Print</a>
</div>