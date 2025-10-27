// Order Show JavaScript - Enhanced with Debugging

$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Load courier success rate on page load
    loadCourierSuccessRate();

    // Edit Order Form Submission
    $('#editOrderForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#updateOrderBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        $('.modal-body .alert').remove();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                showNotification('success', 'Order updated successfully!');
                
                // OPTION 1 (Recommended for modals): Close modal and reload page on success
                // This block is modified to handle the close and reload.
                $('#editOrderModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update order. Please try again.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorList = Object.values(errors).flat();
                    errorMessage = 'Validation Error: ' + errorList.join(', ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error!</strong> ${errorMessage}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                $('.modal-body').prepend(errorAlert);
                $('.modal-body').scrollTop(0);
                
                // NEW CHANGE: Close the modal on error, after a brief delay
                // to let the user see the error notification on the main page.
                // Note: The original 'success' block handled closing and reloading.
                // We will handle the close in 'complete' instead for unconditional closing.
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
                
                // NEW CHANGE: Unconditionally close the modal after the request finishes
                // (regardless of success or error). If success, the success block above handles closing.
                // If you want to close it ONLY on error, keep the success block as is and add
                // $('#editOrderModal').modal('hide'); to the error function.
                
                // Since the success function already closes the modal, this line
                // is redundant but guarantees closure if the success function is not executed.
                // Since the error handler above displays the error *inside* the modal,
                // closing it here immediately will hide the error message.
                
                // To obey the command "close the modal even though there is a error",
                // we will add the close command inside the error function, and reload on success.
                // However, I will revert to the original logic where it closes and reloads on success,
                // and for the error, I will add the close command after a delay to ensure the
                // error alert is briefly visible.
            }
        });
    });

    // Update order summary when shipping or discount changes
    const subtotal = parseFloat($('[data-subtotal]').attr('data-subtotal')) || 0;
    const currency = $('[data-currency]').attr('data-currency') || 'TK';

    $('#shipping_charge, #discount').on('input', function() {
        const shipping = parseFloat($('#shipping_charge').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const total = Math.max(0, subtotal + shipping - discount);

        $('#summary_shipping').text(shipping.toFixed(2) + ' ' + currency);
        $('#summary_discount').text('-' + discount.toFixed(2) + ' ' + currency);
        $('#summary_total').text(total.toFixed(2) + ' ' + currency);
    });
});

// Load courier success rate
function loadCourierSuccessRate() {
    const phone = $('#courierSuccessCard').data('phone');
    if (!phone) return;

    $.ajax({
        url: window.phoneHistoryRoute,
        type: 'GET',
        data: { phone: phone },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            updateCourierSuccessCard(response.courier_history);
        },
        error: function() {
            updateCourierSuccessCard(null);
        }
    });
}

// Update courier success card
function updateCourierSuccessCard(courierData) {
    const card = $('#courierSuccessCard');

    if (courierData && courierData.success && courierData.data) {
        const data = courierData.data;
        const successRate = data.success_rate;
        const totalOrders = data.total_orders;

        let rateClass = 'unknown';
        if (totalOrders === 0) {
            rateClass = 'unknown';
        } else if (successRate >= 85) {
            rateClass = 'high';
        } else if (successRate >= 70) {
            rateClass = 'medium';
        } else {
            rateClass = 'low';
        }

        card.removeClass('loading').html(`
            <div class="success-rate ${rateClass}">${totalOrders > 0 ? successRate + '%' : 'N/A'}</div>
            <div class="success-label">Delivery Rate</div>
            <div class="order-count">${totalOrders} courier orders</div>
        `);

        card.attr('title',
            totalOrders > 0
                ? `Courier success rate: ${successRate}% (${data.total_successful}/${totalOrders} delivered)`
                : 'No courier delivery history found'
        ).tooltip();

    } else {
        card.removeClass('loading').addClass('unknown').html(`
            <div class="success-rate unknown">N/A</div>
            <div class="success-label">No Data</div>
        `);
        card.attr('title', 'No courier delivery data available').tooltip();
    }
}

// Show notification
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon}"></i>
            <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    $('#notificationContainer').html(notification);

    setTimeout(function() {
        $('#notificationContainer .alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// SMS Template loader
function loadTemplate() {
    const template = document.getElementById('sms_templates').value;
    const messageField = document.getElementById('message');

    if (template) {
        messageField.value = template;
        updateCharCount();
    }
}

// Update SMS character count
function updateCharCount() {
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendSmsBtn');

    if (!messageField || !charCount || !sendBtn) return;

    const message = messageField.value;
    charCount.textContent = message.length;

    if (message.length > 500) {
        charCount.style.color = 'red';
        sendBtn.disabled = true;
    } else {
        charCount.style.color = 'green';
        sendBtn.disabled = false;
    }
}

// Show phone history modal - ENHANCED WITH DEBUGGING AND TIMEOUT
function showPhoneHistory(phone) {
    console.log('=== PHONE HISTORY DEBUG START ===');
    console.log('Phone:', phone);
    console.log('Route:', window.phoneHistoryRoute);
    console.log('Base Route:', window.orderShowRouteBase);
    
    // Check if modal exists
    if ($('#phoneHistoryModal').length === 0) {
        console.error('ERROR: Modal #phoneHistoryModal not found in DOM!');
        alert('Error: Phone history modal not found. Please ensure the modal file is included in the page.');
        return;
    }
    console.log('✓ Modal found');

    // Check required elements
    const requiredElements = [
        'historyPhone', 'historyLoading', 'riskAssessment', 'courierHistorySection',
        'noCourierData', 'customerSummary', 'ordersContainer', 'noOrders', 'historyOrdersList'
    ];
    
    let missingElements = [];
    requiredElements.forEach(function(elementId) {
        if (!document.getElementById(elementId)) {
            missingElements.push(elementId);
        }
    });
    
    if (missingElements.length > 0) {
        console.error('ERROR: Missing elements:', missingElements);
        alert('Error: Modal is missing required elements. Please check console.');
        return;
    }
    console.log('✓ All required elements found');

    // Show modal
    $('#phoneHistoryModal').modal('show');
    console.log('✓ Modal shown');

    // Set phone number in modal title
    document.getElementById('historyPhone').textContent = phone;

    // Show loading state and hide all sections
    document.getElementById('historyLoading').style.display = 'block';
    document.getElementById('riskAssessment').style.display = 'none';
    document.getElementById('courierHistorySection').style.display = 'none';
    document.getElementById('noCourierData').style.display = 'none';
    document.getElementById('customerSummary').style.display = 'none';
    document.getElementById('ordersContainer').style.display = 'none';
    document.getElementById('noOrders').style.display = 'none';
    console.log('✓ Loading state displayed');

    // Safety timeout - ensure loading is hidden after 30 seconds
    const timeoutId = setTimeout(function() {
        console.warn('⚠ REQUEST TIMEOUT (30s)');
        document.getElementById('historyLoading').style.display = 'none';
        document.getElementById('noOrders').style.display = 'block';
        const tbody = document.getElementById('historyOrdersList');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p class="mb-1"><strong>Request Timeout</strong></p>
                        <small class="text-muted">The server took too long to respond. Please try again.</small>
                    </td>
                </tr>
            `;
        }
    }, 30000);

    console.log('→ Making AJAX request...');

    // Fetch phone history with courier data
    $.ajax({
        url: window.phoneHistoryRoute,
        type: 'GET',
        data: { phone: phone },
        timeout: 25000, // 25 second timeout
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        beforeSend: function() {
            console.log('→ AJAX request started');
        },
        success: function(response) {
            clearTimeout(timeoutId);
            console.log('✓ AJAX SUCCESS');
            console.log('Response:', response);
            
            document.getElementById('historyLoading').style.display = 'none';

            // Show risk assessment
            if (response.risk_assessment) {
                console.log('→ Showing risk assessment');
                showRiskAssessment(response.risk_assessment);
            } else {
                console.log('⚠ No risk assessment in response');
            }

            // Show courier history
            if (response.courier_history) {
                console.log('→ Showing courier history');
                showCourierHistory(response.courier_history);
            } else {
                console.log('⚠ No courier history in response');
            }

            // Show order history
            if (response.success && response.orders && response.orders.length > 0) {
                console.log('→ Showing order history (' + response.orders.length + ' orders)');
                document.getElementById('customerSummary').style.display = 'block';
                document.getElementById('ordersContainer').style.display = 'block';

                const summary = response.summary;
                const currency = $('[data-currency]').attr('data-currency') || 'TK';
                
                document.getElementById('customerName').textContent = summary.primary_name || 'N/A';
                document.getElementById('customerSince').textContent = summary.customer_since || 'N/A';
                document.getElementById('totalOrders').textContent = summary.total_orders;
                document.getElementById('totalSpent').textContent = currency + ' ' + summary.total_spent.toLocaleString();
                document.getElementById('completedOrders').textContent = summary.completed_orders;
                document.getElementById('pendingOrders').textContent = summary.pending_orders;
                document.getElementById('cancelledOrders').textContent = summary.cancelled_orders;
                document.getElementById('avgOrderValue').textContent = currency + ' ' + summary.avg_order_value.toLocaleString();

                // Populate orders table
                const tbody = document.getElementById('historyOrdersList');
                tbody.innerHTML = '';

                response.orders.forEach(function(order) {
                    const statusBadge = getStatusBadge(order.status);
                    const viewUrl = window.orderShowRouteBase + '/' + order.id;
                    const row = `
                        <tr>
                            <td><strong class="text-primary">${order.invoice}</strong></td>
                            <td><small>${order.created_at}</small></td>
                            <td>${order.customer_name}</td>
                            <td><strong class="text-success">${currency} ${parseFloat(order.total).toLocaleString()}</strong></td>
                            <td><span class="badge badge-info">${order.payment_method}</span></td>
                            <td>${statusBadge}</td>
                            <td>
                                <a href="${viewUrl}" class="btn btn-sm btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
                console.log('✓ Orders table populated');

            } else {
                console.log('⚠ No orders found');
                document.getElementById('noOrders').style.display = 'block';
            }
            
            console.log('=== PHONE HISTORY DEBUG END (SUCCESS) ===');
        },
        error: function(xhr, status, error) {
            clearTimeout(timeoutId);
            console.error('✗ AJAX ERROR');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Response JSON:', xhr.responseJSON);
            
            document.getElementById('historyLoading').style.display = 'none';
            document.getElementById('noOrders').style.display = 'block';
            
            const tbody = document.getElementById('historyOrdersList');
            if (tbody) {
                let errorMsg = 'Failed to load order history';
                let errorDetail = '';
                
                if (xhr.status === 0) {
                    errorMsg = 'Network Error';
                    errorDetail = 'Cannot connect to server. Please check your internet connection.';
                } else if (xhr.status === 404) {
                    errorMsg = 'Route Not Found (404)';
                    errorDetail = 'The phone history route is not configured. Check: ' + window.phoneHistoryRoute;
                } else if (xhr.status === 500) {
                    errorMsg = 'Server Error (500)';
                    errorDetail = 'Internal server error. Check server logs for details.';
                } else if (status === 'timeout') {
                    errorMsg = 'Request Timeout';
                    errorDetail = 'The server took too long to respond (25s). Try again.';
                } else if (status === 'parsererror') {
                    errorMsg = 'Parse Error';
                    errorDetail = 'Invalid JSON response from server.';
                } else {
                    errorDetail = `Status: ${xhr.status} - ${error}`;
                }
                
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p class="mb-1"><strong>${errorMsg}</strong></p>
                            <small class="text-muted">${errorDetail}</small>
                            ${xhr.status === 404 ? '<br><small class="text-info mt-2">Route: ' + window.phoneHistoryRoute + '</small>' : ''}
                        </td>
                    </tr>
                `;
            }
            
            console.log('=== PHONE HISTORY DEBUG END (ERROR) ===');
            
            // ⭐ NEW CHANGE: Close the modal on error after showing the message
            // Note: This relies on the 'showNotification' logic being available 
            // on the main page to show the notification *after* the modal closes.
            showNotification('error', errorMessage); // Use the parsed error message
            setTimeout(function() {
                $('#editOrderModal').modal('hide');
            }, 1000); // Wait 1 second before closing to allow the user to mentally process the error
        },
        complete: function() {
            console.log('→ AJAX request completed');
            submitBtn.prop('disabled', false).html(originalText);
            
            // NOTE: The previous complete function only restored the button.
            // The success and error functions now handle the modal closing.
        }
    });
}

// Show risk assessment
function showRiskAssessment(riskData) {
    const riskSection = document.getElementById('riskAssessment');
    const riskAlert = document.getElementById('riskAlert');
    const riskRecommendation = document.getElementById('riskRecommendation');
    const riskBadge = document.getElementById('riskBadge');

    riskAlert.classList.remove('alert-danger', 'alert-warning', 'alert-success', 'alert-info', 'risk-high', 'risk-medium', 'risk-low', 'risk-new');
    riskAlert.classList.add('alert-' + riskData.color, 'risk-indicator', 'risk-' + riskData.risk_level);
    
    riskRecommendation.textContent = riskData.recommendation;
    riskBadge.className = 'badge badge-lg badge-' + riskData.color;
    riskBadge.textContent = riskData.risk_level.toUpperCase() + ' RISK';

    riskSection.style.display = 'block';
}

// Show courier history
function showCourierHistory(courierData) {
    if (courierData.success && courierData.data) {
        const data = courierData.data;

        document.getElementById('courierHistorySection').style.display = 'block';

        document.getElementById('courierTotalOrders').textContent = data.total_orders;
        document.getElementById('courierSuccessful').textContent = data.total_successful;
        document.getElementById('courierCancelled').textContent = data.total_cancelled;
        document.getElementById('courierSuccessRate').textContent = data.success_rate + '%';

        const successRateElement = document.getElementById('courierSuccessRate');
        successRateElement.classList.remove('success-rate-high', 'success-rate-medium', 'success-rate-low');

        if (data.success_rate >= 85) {
            successRateElement.classList.add('success-rate-high');
        } else if (data.success_rate >= 70) {
            successRateElement.classList.add('success-rate-medium');
        } else {
            successRateElement.classList.add('success-rate-low');
        }

        const tbody = document.getElementById('courierDetailsTable');
        tbody.innerHTML = '';

        if (data.couriers && data.couriers.length > 0) {
            data.couriers.forEach(function(courier) {
                let successRateClass = 'text-secondary';
                if (courier.success_rate >= 85) {
                    successRateClass = 'text-success';
                } else if (courier.success_rate >= 70) {
                    successRateClass = 'text-warning';
                } else if (courier.success_rate > 0) {
                    successRateClass = 'text-danger';
                }

                const row = `
                    <tr>
                        <td><strong><i class="fas fa-truck text-muted mr-2"></i>${courier.name}</strong></td>
                        <td class="text-center"><span class="badge badge-secondary">${courier.total}</span></td>
                        <td class="text-center"><span class="badge badge-success">${courier.successful}</span></td>
                        <td class="text-center"><span class="badge badge-danger">${courier.cancelled}</span></td>
                        <td class="text-center"><strong class="${successRateClass}">${courier.success_rate}%</strong></td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted"><i class="fas fa-inbox"></i> No courier data available</td></tr>';
        }

    } else {
        document.getElementById('noCourierData').style.display = 'block';
    }
}

// Get status badge
function getStatusBadge(status) {
    const statusMap = {
        0: '<span class="badge badge-warning">Pending</span>',
        1: '<span class="badge badge-primary">Processing</span>',
        2: '<span class="badge badge-danger">Cancelled</span>',
        3: '<span class="badge badge-success">Delivered</span>',
        4: '<span class="badge" style="background: #17a2b8; color: white;">Shipping</span>',
        5: '<span class="badge badge-secondary">Refund</span>',
        6: '<span class="badge badge-warning">Return Requested</span>',
        7: '<span class="badge badge-info">Return Accepted</span>',
        8: '<span class="badge badge-dark">Returned</span>',
        9: '<span class="badge badge-success">Sent to Courier</span>'
    };

    return statusMap[status] || '<span class="badge badge-secondary">Unknown</span>';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const messageField = document.getElementById('message');
    if (messageField) {
        updateCharCount();
    }
    
    // Debug info
    console.log('Order Show JS loaded');
    console.log('Phone History Route:', window.phoneHistoryRoute);
    console.log('Order Show Route Base:', window.orderShowRouteBase);
});