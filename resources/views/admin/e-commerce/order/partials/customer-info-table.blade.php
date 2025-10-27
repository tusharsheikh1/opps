<table class="table table-bordered table-hover">
    <tbody>
        @if (!empty($order->meet_time))
            <tr>
                <th>Meet Time</th>
                <td>{{ $order->meet_time }}</td>

            </tr>
        @endif
        <tr>
            <th>Customer Name</th>
            <td>{{ $order->first_name }}</td>
            <th>Order ID</th>
            <td>{{ $order->order_id }}</td>
        </tr>
        <tr>
            <th>Invoice</th>
            <td>{{ $order->invoice }}</td>
            <th>Company Name</th>
            <td>{{ $order->company_name }}</td>
        </tr>
        <tr>
            <th>Country</th>
            <td>{{ $order->country }}</td>
            <th>Address</th>
            <td>{{ $order->address }}</td>
        </tr>
        <tr>
            <th>Town</th>
            <td>{{ $order->town }}</td>
            <th>District</th>
            <td>{{ $order->district }}</td>
        </tr>
        <tr>
            <th>Post Code</th>
            <td>{{ $order->post_code }}</td>
            <th>Phone</th>
            <td>
                @if($order->phone)
                    <div class="phone-container">
                        <div class="phone-info">
                            <a href="tel:{{ $order->phone }}" class="text-decoration-none">
                                <i class="fas fa-phone text-success"></i> {{ $order->phone }}
                            </a>

                            @php
                                $cleanPhone = preg_replace('/[^0-9+]/', '', $order->phone);
                                $phoneOrderCount = \App\Models\Order::where(function($query) use ($order, $cleanPhone) {
                                    $query->where('phone', $order->phone)->orWhere('phone', $cleanPhone);
                                    if (strlen($cleanPhone) >= 10) {
                                        if (!str_starts_with($cleanPhone, '+88')) {
                                            $query->orWhere('phone', '+88' . ltrim($cleanPhone, '0'));
                                        }
                                        if (str_starts_with($cleanPhone, '+88')) {
                                            $query->orWhere('phone', '0' . substr($cleanPhone, 3))
                                                  ->orWhere('phone', substr($cleanPhone, 3));
                                        }
                                    }
                                })->count();
                            @endphp
                            @if($phoneOrderCount > 1)
                                <span class="badge badge-primary cursor-pointer"
                                      title="Click to view {{ $phoneOrderCount }} orders from this customer"
                                      onclick="showPhoneHistory('{{ $order->phone }}')"
                                      data-toggle="tooltip"
                                      style="cursor: pointer;">
                                    {{ $phoneOrderCount }} orders
                                </span>
                            @endif

                            <button type="button" class="btn btn-xs btn-outline-info"
                                    onclick="showPhoneHistory('{{ $order->phone }}')"
                                    title="View courier delivery history">
                                <i class="fas fa-shipping-fast"></i> History
                            </button>
                        </div>

                        <div class="courier-success-card loading" id="courierSuccessCard" data-phone="{{ $order->phone }}">
                            <div class="loading-spinner"></div>
                            <div class="success-label">Loading...</div>
                        </div>
                    </div>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $order->email }}</td>
            <th>Shipping Method</th>
            <td>{{ $order->shipping_method }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td colspan="3">{{ $order->payment_method }}</td>
        </tr>
        @if ($order->payment_method == 'Bkash' || $order->payment_method == 'Nagad' || $order->payment_method == 'Rocket')
            <tr>
                <th>Mobile Number</th>
                <td>{{ $order->mobile_number }}</td>
                <th>Transaction ID</th>
                <td>{{ $order->transaction_id }}</td>
            </tr>
        @elseif ($order->payment_method == 'Bank')
            <tr>
                <th>Bank Name</th>
                <td>{{ $order->bank_name }}</td>
                <th>Account Number</th>
                <td>{{ $order->account_number }}</td>
            </tr>
            <tr>
                <th>Holder Name</th>
                <td>{{ $order->holder_name }}</td>
                <th>Branch Name</th>
                <td>{{ $order->branch_name }}</td>
            </tr>
            <tr>
                <th>Routing Number</th>
                <td colspan="3">{{ $order->routing_number }}</td>
            </tr>
        @endif
        <tr>
            <th>Coupon Code</th>
            <td>{{ $order->coupon_code }}</td>
            <th>Subtotal</th>
            <td>{{ $order->subtotal }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
            </td>
        </tr>
        <tr>
            <th>Shipping Charge</th>
            <td>{{ $order->shipping_charge }}
                <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong></td>
            <th>Discount</th>
            <td>{{ $order->discount }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
            </td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td>{{ $order->pay_staus == 1 ? 'Paid' : 'Unpaid' }} </td>
            <th>Payment Date</th>
            <td>{{ $order->pay_date }} </td>
        </tr>
        <tr>
            <th>Partial Payment</th>
            <td>
                @php
                    $part = App\Models\PartialPayment::where('order_id', $order->id)
                        ->where('status', 1)
                        ->sum('amount');
                    $ds = $order->total;
                @endphp
                {{ $part }}<strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
            </td>
            <th>Due</th>
            <td> {{ $order->total - $part }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong>
            </td>
        </tr>
        <tr>
            <th>Total</th>
            <td>{{ $ds }} <strong>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</strong></td>
            <th>Status</th>
            <td>

                @if ($order->status == 0)
                    <span class="badge badge-warning">Pending</span>
                @elseif ($order->status == 1)
                    <span class="badge badge-primary">Processing</span>
                @elseif ($order->status == 2)
                    <span class="badge badge-danger">Canceled</span>
                @elseif ($order->status == 5)
                    <span class="badge badge-danger">Refund</span>
                @elseif ($order->status == 4)
                    <span class="badge" style="background: #7db1b1;">Shipping</span>
                @elseif ($order->status == 6)
                    <span class="badge" style="background: #7db1b1;">Return Request By User</span>
                @elseif ($order->status == 7)
                    <span class="badge" style="background: #7db1b1;">Return process accept by
                        Owner</span>
                @elseif ($order->status == 8)
                    <span class="badge" style="background: #7db1b1;">Returned</span>
                @elseif ($order->status == 9)
                    <span class="badge" style="background: #7db1b1;">Sended to Courier</span>
                @elseif ($order->status == 3)
                    <span class="badge badge-success">Delivered</span>
                @endif
            </td>
        </tr>
        @if ($order->status == 5)
            <tr>
                <th>Refund Method</th>
                <td>{{ $order->refund_method }}</td>
            </tr>
        @endif
        @if (!empty($order->admin_notes))
            <tr>
                <th>Admin Notes</th>
                <td colspan="3">
                    <div class="alert alert-info">
                        <i class="fas fa-sticky-note"></i>
                        {{ $order->admin_notes }}
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>