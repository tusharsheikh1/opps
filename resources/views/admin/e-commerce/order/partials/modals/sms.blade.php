<div class="modal fade" id="smsModal" tabindex="-1" role="dialog"
    aria-labelledby="smsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsModalTitle">
                    <i class="fas fa-sms text-purple"></i>
                    Send Custom SMS to {{ $order->first_name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('admin.order.send.sms') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="form-group">
                        <label for="customer_info"><i class="fas fa-info-circle text-info"></i> Customer Info:</label>
                        <div class="alert alert-info">
                            <strong>Name:</strong> {{ $order->first_name }}<br>
                            <strong>Phone:</strong> {{ $order->phone }}<br>
                            <strong>Invoice:</strong> {{ $order->invoice }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sms_templates"><i class="fas fa-list text-success"></i> Quick Templates:</label>
                        <select class="form-control" id="sms_templates" onchange="loadTemplate()">
                            <option value="">Select a template...</option>
                            <option value="Your order {invoice} is being prepared. We will notify you once it's ready for delivery. Thank you!">Order Preparation</option>
                            <option value="Hi {customer_name}, your order {invoice} is out for delivery and will reach you soon. Please keep your phone available.">Out for Delivery</option>
                            <option value="Your order {invoice} has been delivered successfully. Thank you for shopping with us!">Delivery Confirmation</option>
                            <option value="We apologize for the delay in your order {invoice}. We are working to process it as soon as possible.">Delay Notice</option>
                            <option value="Your payment of {total} TK for order {invoice} has been confirmed. Thank you!">Payment Confirmation</option>
                            <option value="Hi {customer_name}, please confirm your delivery address for order {invoice}. Contact us if you need to make changes.">Address Confirmation</option>
                            <option value="Your order {invoice} is ready for pickup. Please visit our store with a valid ID. Thank you!">Pickup Ready</option>
                            <option value="Thank you {customer_name} for your order {invoice}! Your order total is {total} TK. We appreciate your business.">Thank You Message</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-edit text-primary"></i> SMS Message: <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" id="message" rows="4"
                                  placeholder="Type your custom message here..."
                                  maxlength="500" required
                                  oninput="updateCharCount()"></textarea>
                        <small class="form-text text-muted">
                            <span id="charCount">0</span>/500 characters
                            <br>
                            <strong>Available variables:</strong> {invoice}, {customer_name}, {total}
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Preview:</strong> SMS will be sent to <strong>{{ $order->phone }}</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-purple" id="sendSmsBtn">
                        <i class="fas fa-paper-plane"></i> Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>