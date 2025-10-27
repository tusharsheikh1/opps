{{-- Phone History Modal --}}
<div class="modal fade" id="phoneHistoryModal" tabindex="-1" role="dialog" aria-labelledby="phoneHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="phoneHistoryModalLabel">
                    <i class="fas fa-history"></i> Customer History - <span id="historyPhone"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Loading State --}}
                <div id="historyLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading customer history and courier data...</p>
                </div>

                {{-- Risk Assessment Section --}}
                <div id="riskAssessment" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-gradient-warning">
                            <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Risk Assessment</h6>
                        </div>
                        <div class="card-body">
                            <div id="riskAlert" class="alert risk-indicator" role="alert">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-2">
                                            <span id="riskBadge" class="badge badge-lg"></span>
                                        </h5>
                                        <p class="mb-0" id="riskRecommendation"></p>
                                    </div>
                                    <div>
                                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Courier History Section --}}
                <div id="courierHistorySection" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-gradient-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-truck"></i> Courier Delivery History</h6>
                        </div>
                        <div class="card-body">
                            {{-- Overall Stats --}}
                            <div class="row courier-stats mb-3">
                                <div class="col-md-3 text-center">
                                    <div class="stat-box">
                                        <h4 class="text-primary mb-1" id="courierTotalOrders">0</h4>
                                        <small class="text-muted text-uppercase">Total Orders</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box">
                                        <h4 class="text-success mb-1" id="courierSuccessful">0</h4>
                                        <small class="text-muted text-uppercase">Delivered</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box">
                                        <h4 class="text-danger mb-1" id="courierCancelled">0</h4>
                                        <small class="text-muted text-uppercase">Cancelled</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box">
                                        <h4 class="mb-1" id="courierSuccessRate">0%</h4>
                                        <small class="text-muted text-uppercase">Success Rate</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Detailed Courier Breakdown --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Courier Service</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Delivered</th>
                                            <th class="text-center">Cancelled</th>
                                            <th class="text-center">Success Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody id="courierDetailsTable">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin"></i> Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- No Courier Data Message --}}
                <div id="noCourierData" style="display: none;">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>No Courier Data Available</strong>
                        <p class="mb-0 mt-2">No courier delivery history found for this phone number.</p>
                    </div>
                </div>

                {{-- Customer Summary Section --}}
                <div id="customerSummary" style="display: none;">
                    <div class="card mb-3">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Customer Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Customer Name:</strong></p>
                                    <h5 class="text-primary" id="customerName">N/A</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Customer Since:</strong></p>
                                    <h5 class="text-muted" id="customerSince">N/A</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Total Orders:</strong></p>
                                    <h5 class="text-info" id="totalOrders">0</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Total Spent:</strong></p>
                                    <h5 class="text-success" id="totalSpent">0</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Completed:</strong></p>
                                    <h5 class="badge badge-success badge-lg" id="completedOrders">0</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Pending:</strong></p>
                                    <h5 class="badge badge-warning badge-lg" id="pendingOrders">0</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Cancelled:</strong></p>
                                    <h5 class="badge badge-danger badge-lg" id="cancelledOrders">0</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-2"><strong>Avg Order Value:</strong></p>
                                    <h5 class="text-primary" id="avgOrderValue">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Orders List Section --}}
                <div id="ordersContainer" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-gradient-secondary">
                            <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Order History</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historyOrdersList">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                <p class="mt-2">Loading orders...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- No Orders Message --}}
                <div id="noOrders" style="display: none;">
                    <div class="alert alert-warning text-center py-5" role="alert">
                        <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                        <h5>No Orders Found</h5>
                        <p class="mb-0">No order history found for this phone number.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Phone History Modal Styles */
    .stat-box {
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .badge-lg {
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
    }

    .risk-indicator {
        border-left: 4px solid;
        padding-left: 15px;
    }

    .risk-high {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .risk-medium {
        border-color: #ffc107;
        background-color: #fff3cd;
    }

    .risk-low {
        border-color: #28a745;
        background-color: #d4edda;
    }

    .risk-new {
        border-color: #17a2b8;
        background-color: #d1ecf1;
    }

    .success-rate-high {
        color: #28a745 !important;
        font-weight: bold;
    }

    .success-rate-medium {
        color: #ffc107 !important;
        font-weight: bold;
    }

    .success-rate-low {
        color: #dc3545 !important;
        font-weight: bold;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    }

    .opacity-50 {
        opacity: 0.5;
    }

    #phoneHistoryModal .modal-xl {
        max-width: 1200px;
    }

    #phoneHistoryModal .table thead th {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #phoneHistoryModal .courier-stats {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
    }
</style>