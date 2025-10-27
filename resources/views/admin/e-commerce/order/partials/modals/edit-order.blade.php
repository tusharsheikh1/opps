<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOrderModalLabel">
                    <i class="fas fa-edit text-warning"></i> Edit Order: {{ $order->invoice }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.order.update', $order->id) }}" method="POST" id="editOrderForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Hidden fields added for model attributes that are in $fillable but not visible in the form --}}
                    {{-- This ensures they are submitted (even if empty) to be updated to NULL or '' --}}
                    <input type="hidden" name="company_name" value="{{ old('company_name', $order->company_name) }}">
                    <input type="hidden" name="country" value="{{ old('country', $order->country) }}">
                    <input type="hidden" name="thana" value="{{ old('thana', $order->thana) }}">

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-user"></i> Customer Information</h6>
                            <hr>

                            <div class="form-group">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="{{ old('first_name', $order->first_name) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="{{ old('last_name', $order->last_name) }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email', $order->email) }}">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                       value="{{ old('phone', $order->phone) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-map-marker-alt"></i> Address Information</h6>
                            <hr>

                            <div class="form-group">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $order->address) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="town">Town/City</label>
                                        <input type="text" class="form-control" id="town" name="town"
                                               value="{{ old('town', $order->town) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <input type="text" class="form-control" id="district" name="district"
                                               value="{{ old('district', $order->district) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="post_code">Post Code</label>
                                <input type="text" class="form-control" id="post_code" name="post_code"
                                       value="{{ old('post_code', $order->post_code) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-info"><i class="fas fa-receipt"></i> Order Details</h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shipping_charge">Shipping Charge</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="shipping_charge"
                                                   name="shipping_charge" step="0.01" min="0"
                                                   value="{{ old('shipping_charge', $order->shipping_charge) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount">Discount</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="discount"
                                                   name="discount" step="0.01" min="0"
                                                   value="{{ old('discount', $order->discount) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="coupon_code">Coupon Code</label>
                                <input type="text" class="form-control" id="coupon_code" name="coupon_code"
                                       value="{{ old('coupon_code', $order->coupon_code) }}">
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Order Summary</h6>
                                    <table class="table table-sm"
                                           data-subtotal="{{ $order->subtotal }}"
                                           data-currency="{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-right">{{ $order->subtotal }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping:</td>
                                            <td class="text-right" id="summary_shipping">{{ $order->shipping_charge }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="text-right text-danger" id="summary_discount">-{{ $order->discount }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td>Total:</td>
                                            <td class="text-right text-success" id="summary_total">{{ $order->total }} {{ setting('CURRENCY_CODE_MIN') ?? 'TK' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h6 class="text-secondary"><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                            <hr>

                            <div class="form-group">
                                <label for="admin_notes">Internal Notes</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes"
                                          rows="8" placeholder="Add internal notes about this order...">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                                <small class="form-text text-muted">
                                    These notes are only visible to admin users and won't be shared with customers.
                                </small>
                            </div>

                            <div class="alert alert-light">
                                <small>
                                    <strong>Last Updated:</strong><br>
                                    {{ $order->updated_at->format('d M Y, h:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="updateOrderBtn">
                        <i class="fas fa-save"></i> Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>