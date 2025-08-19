<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 text-gray-800 mb-0">Create New Rental</h1>
            <p class="text-muted">Create a new forklift rental booking</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('rentals') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Rentals
            </a>
        </div>
    </div>

    <form id="createRentalForm">
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Customer Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user"></i> Customer Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerName" name="customer_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customerCompany" name="customer_company" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="customerEmail" name="customer_email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="customerPhone" name="customer_phone" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contactPerson" name="contact_person">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Customer Address</label>
                                <textarea class="form-control" id="customerAddress" name="customer_address" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forklift Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-truck"></i> Forklift Selection
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Available Forklifts <span class="text-danger">*</span></label>
                                <div id="forkliftSelection" class="row">
                                    <?php if (!empty($forklifts)): ?>
                                        <?php foreach ($forklifts as $forklift): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="forklift-card border rounded p-3 cursor-pointer" data-forklift-id="<?= $forklift['forklift_id'] ?>" data-rate="<?= $forklift['daily_rate'] ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="forklift_id" value="<?= $forklift['forklift_id'] ?>" id="forklift_<?= $forklift['forklift_id'] ?>">
                                                        <label class="form-check-label w-100" for="forklift_<?= $forklift['forklift_id'] ?>">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-1"><?= esc($forklift['unit_code']) ?></h6>
                                                                    <p class="text-muted mb-1"><?= esc($forklift['brand']) ?> <?= esc($forklift['model']) ?></p>
                                                                    <small class="text-muted">Capacity: <?= esc($forklift['capacity']) ?></small>
                                                                </div>
                                                                <div class="text-end">
                                                                    <div class="badge bg-primary">Available</div>
                                                                    <div class="text-muted small">Rp <?= number_format($forklift['daily_rate'], 0, ',', '.') ?>/day</div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> No forklifts available at the moment.
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rental Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar"></i> Rental Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rental Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="rentalType" name="rental_type" required>
                                    <option value="">Select Type</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="number" class="form-control" id="rentalDuration" name="rental_duration" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rate per Period <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="rentalRate" name="rental_rate" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rate Type</label>
                                <select class="form-select" id="rentalRateType" name="rental_rate_type" required>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Services -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus-circle"></i> Additional Services
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Delivery -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="deliveryRequired" name="delivery_required">
                                    <label class="form-check-label" for="deliveryRequired">
                                        Delivery Required
                                    </label>
                                </div>
                                <div id="deliveryOptions" class="mt-2 d-none">
                                    <label class="form-label">Delivery Address</label>
                                    <textarea class="form-control mb-2" id="deliveryAddress" name="delivery_address" rows="2"></textarea>
                                    <label class="form-label">Delivery Cost</label>
                                    <input type="number" class="form-control" id="deliveryCost" name="delivery_cost" value="0">
                                </div>
                            </div>
                            
                            <!-- Pickup -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pickupRequired" name="pickup_required">
                                    <label class="form-check-label" for="pickupRequired">
                                        Pickup Required
                                    </label>
                                </div>
                                <div id="pickupOptions" class="mt-2 d-none">
                                    <label class="form-label">Pickup Address</label>
                                    <textarea class="form-control mb-2" id="pickupAddress" name="pickup_address" rows="2"></textarea>
                                    <label class="form-label">Pickup Cost</label>
                                    <input type="number" class="form-control" id="pickupCost" name="pickup_cost" value="0">
                                </div>
                            </div>
                            
                            <!-- Operator -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="operatorRequired" name="operator_required">
                                    <label class="form-check-label" for="operatorRequired">
                                        Operator Required
                                    </label>
                                </div>
                                <div id="operatorOptions" class="mt-2 d-none">
                                    <label class="form-label">Operator Name</label>
                                    <input type="text" class="form-control mb-2" id="operatorName" name="operator_name">
                                    <label class="form-label">Operator Cost</label>
                                    <input type="number" class="form-control" id="operatorCost" name="operator_cost" value="0">
                                </div>
                            </div>
                            
                            <!-- Service Inclusions -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Service Inclusions</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fuelIncluded" name="fuel_included">
                                    <label class="form-check-label" for="fuelIncluded">
                                        Fuel Included
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenanceIncluded" name="maintenance_included">
                                    <label class="form-check-label" for="maintenanceIncluded">
                                        Maintenance Included
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="insuranceIncluded" name="insurance_included">
                                    <label class="form-check-label" for="insuranceIncluded">
                                        Insurance Included
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-sticky-note"></i> Additional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-control" id="poNumber" name="po_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" name="payment_method">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Terms</label>
                                <select class="form-select" id="paymentTerms" name="payment_terms">
                                    <option value="">Select Payment Terms</option>
                                    <option value="cash_on_delivery">Cash on Delivery</option>
                                    <option value="net_7">Net 7 Days</option>
                                    <option value="net_14">Net 14 Days</option>
                                    <option value="net_30">Net 30 Days</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Special Terms</label>
                                <textarea class="form-control" id="specialTerms" name="special_terms" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calculator"></i> Pricing Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="pricing-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotalAmount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="discountAmount" name="discount_amount" value="0">
                                    <span class="input-group-text">Rp</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (10%):</span>
                                <span id="taxAmount">Rp 0</span>
                                <input type="hidden" id="taxAmountHidden" name="tax_amount" value="0">
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery:</span>
                                <span id="deliveryDisplay">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pickup:</span>
                                <span id="pickupDisplay">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Operator:</span>
                                <span id="operatorDisplay">Rp 0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Amount:</strong>
                                <strong id="totalAmount">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Security Deposit:</span>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="securityDeposit" name="security_deposit" value="0">
                                    <span class="input-group-text">Rp</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Create Rental
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Initialize form
    initializeForm();
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#startDate').attr('min', today);
    
    // Event handlers
    $('#startDate, #endDate, #rentalType').on('change', calculateDuration);
    $('#rentalRate, #rentalDuration, #discountAmount, #deliveryCost, #pickupCost, #operatorCost').on('input', calculateTotal);
    $('#deliveryRequired').on('change', toggleDeliveryOptions);
    $('#pickupRequired').on('change', togglePickupOptions);
    $('#operatorRequired').on('change', toggleOperatorOptions);
    $('#rentalType').on('change', updateRateType);
    
    // Forklift selection
    $('.forklift-card').on('click', function() {
        const radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true);
        updateSelectedForklift();
    });
    
    // Form submission
    $('#createRentalForm').on('submit', handleFormSubmit);
});

function initializeForm() {
    // Set default values
    $('#rentalRateType').val('daily');
    
    // Clear validations
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function calculateDuration() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    const rentalType = $('#rentalType').val();
    
    if (startDate && endDate && rentalType) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        let duration;
        switch (rentalType) {
            case 'daily':
                duration = diffDays;
                break;
            case 'weekly':
                duration = Math.ceil(diffDays / 7);
                break;
            case 'monthly':
                duration = Math.ceil(diffDays / 30);
                break;
            case 'yearly':
                duration = Math.ceil(diffDays / 365);
                break;
            default:
                duration = diffDays;
        }
        
        $('#rentalDuration').val(duration);
        calculateTotal();
    }
}

function calculateTotal() {
    const rate = parseFloat($('#rentalRate').val()) || 0;
    const duration = parseInt($('#rentalDuration').val()) || 0;
    const discount = parseFloat($('#discountAmount').val()) || 0;
    const deliveryCost = parseFloat($('#deliveryCost').val()) || 0;
    const pickupCost = parseFloat($('#pickupCost').val()) || 0;
    const operatorCost = parseFloat($('#operatorCost').val()) || 0;
    
    const subtotal = rate * duration;
    const discountAmount = Math.min(discount, subtotal);
    const taxableAmount = subtotal - discountAmount;
    const taxAmount = taxableAmount * 0.1; // 10% tax
    const totalAmount = taxableAmount + taxAmount + deliveryCost + pickupCost + operatorCost;
    
    $('#subtotalAmount').text('Rp ' + formatNumber(subtotal));
    $('#taxAmount').text('Rp ' + formatNumber(taxAmount));
    $('#taxAmountHidden').val(taxAmount);
    $('#deliveryDisplay').text('Rp ' + formatNumber(deliveryCost));
    $('#pickupDisplay').text('Rp ' + formatNumber(pickupCost));
    $('#operatorDisplay').text('Rp ' + formatNumber(operatorCost));
    $('#totalAmount').text('Rp ' + formatNumber(totalAmount));
}

function toggleDeliveryOptions() {
    if ($('#deliveryRequired').is(':checked')) {
        $('#deliveryOptions').removeClass('d-none');
    } else {
        $('#deliveryOptions').addClass('d-none');
        $('#deliveryAddress').val('');
        $('#deliveryCost').val(0);
        calculateTotal();
    }
}

function togglePickupOptions() {
    if ($('#pickupRequired').is(':checked')) {
        $('#pickupOptions').removeClass('d-none');
    } else {
        $('#pickupOptions').addClass('d-none');
        $('#pickupAddress').val('');
        $('#pickupCost').val(0);
        calculateTotal();
    }
}

function toggleOperatorOptions() {
    if ($('#operatorRequired').is(':checked')) {
        $('#operatorOptions').removeClass('d-none');
    } else {
        $('#operatorOptions').addClass('d-none');
        $('#operatorName').val('');
        $('#operatorCost').val(0);
        calculateTotal();
    }
}

function updateRateType() {
    const rentalType = $('#rentalType').val();
    $('#rentalRateType').val(rentalType);
    calculateTotal();
}

function updateSelectedForklift() {
    const selectedCard = $('.forklift-card input[type="radio"]:checked').closest('.forklift-card');
    const rate = selectedCard.data('rate');
    
    if (rate) {
        $('#rentalRate').val(rate);
        calculateTotal();
    }
    
    // Update visual selection
    $('.forklift-card').removeClass('border-primary');
    selectedCard.addClass('border-primary');
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Prepare data
    const formData = new FormData(document.getElementById('createRentalForm'));
    
    // Show loading state
    const submitBtn = $('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...').prop('disabled', true);
    
    // Submit form
    $.ajax({
        url: '<?= base_url('rentals/store') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            } else {
                showNotification('error', response.message);
                if (response.errors) {
                    displayValidationErrors(response.errors);
                }
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            showNotification('error', 'An error occurred while creating the rental');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function validateForm() {
    let isValid = true;
    
    // Clear previous validations
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    // Required fields
    const requiredFields = [
        'customer_name',
        'customer_company',
        'customer_email',
        'customer_phone',
        'rental_type',
        'start_date',
        'end_date',
        'rental_rate'
    ];
    
    requiredFields.forEach(field => {
        const element = $(`[name="${field}"]`);
        if (!element.val()) {
            element.addClass('is-invalid');
            element.siblings('.invalid-feedback').text('This field is required');
            isValid = false;
        }
    });
    
    // Check if forklift is selected
    if (!$('input[name="forklift_id"]:checked').length) {
        $('#forkliftSelection').addClass('is-invalid');
        $('#forkliftSelection').siblings('.invalid-feedback').text('Please select a forklift');
        isValid = false;
    }
    
    // Validate dates
    const startDate = new Date($('#startDate').val());
    const endDate = new Date($('#endDate').val());
    
    if (startDate >= endDate) {
        $('#endDate').addClass('is-invalid');
        $('#endDate').siblings('.invalid-feedback').text('End date must be after start date');
        isValid = false;
    }
    
    // Validate email
    const email = $('#customerEmail').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        $('#customerEmail').addClass('is-invalid');
        $('#customerEmail').siblings('.invalid-feedback').text('Please enter a valid email address');
        isValid = false;
    }
    
    return isValid;
}

function displayValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const element = $(`[name="${field}"]`);
        element.addClass('is-invalid');
        element.siblings('.invalid-feedback').text(errors[field]);
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<style>
.forklift-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.forklift-card:hover {
    border-color: #0061f2 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.forklift-card.border-primary {
    border-color: #0061f2 !important;
    background-color: rgba(0, 97, 242, 0.05);
}

.pricing-summary {
    font-size: 0.9rem;
}

.pricing-summary .input-group-sm {
    max-width: 120px;
}

.position-sticky {
    position: -webkit-sticky;
    position: sticky;
}
</style>
<?= $this->endSection() ?> 