<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2 text-gradient fw-bold">Help & Support</h1>
        <p class="text-muted mb-0">Find answers to common questions and get help</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-pro" data-bs-toggle="modal" data-bs-target="#contactModal">
            <i class="fas fa-envelope me-2"></i>Contact Support
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5>User Guide</h5>
                <p class="text-muted">Complete documentation and tutorials</p>
                <button class="btn btn-outline-primary btn-sm">View Guide</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-play-circle fa-3x text-success mb-3"></i>
                <h5>Video Tutorials</h5>
                <p class="text-muted">Step-by-step video instructions</p>
                <button class="btn btn-outline-success btn-sm">Watch Videos</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-comments fa-3x text-info mb-3"></i>
                <h5>Live Chat</h5>
                <p class="text-muted">Chat with our support team</p>
                <button class="btn btn-outline-info btn-sm">Start Chat</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-phone fa-3x text-warning mb-3"></i>
                <h5>Phone Support</h5>
                <p class="text-muted">Call our support hotline</p>
                <button class="btn btn-outline-warning btn-sm">Call Now</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="card-title">Frequently Asked Questions</h5>
        <p class="card-subtitle">Common questions and answers</p>
    </div>
    <div class="card-body">
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        How do I create a new work order?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To create a new work order, navigate to the Work Orders page and click the "New Work Order" button. Fill in the required information including unit, description, priority, and assigned technician.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How do I track maintenance schedules?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Use the PMPS (Preventive Maintenance Planning & Scheduling) module to set up recurring maintenance tasks, track schedules, and receive notifications when maintenance is due.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        How do I manage spare parts inventory?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        The Sparepart Management module allows you to track inventory levels, set minimum stock alerts, record part usage, and manage supplier information.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        How do I generate reports?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Each module includes built-in reporting features. You can filter data by date range, status, or other criteria, and export reports in various formats.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title">System Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Application Version:</strong></td>
                        <td>1.0.0</td>
                    </tr>
                    <tr>
                        <td><strong>Framework:</strong></td>
                        <td>CodeIgniter 4.6.1</td>
                    </tr>
                    <tr>
                        <td><strong>PHP Version:</strong></td>
                        <td><?= PHP_VERSION ?></td>
                    </tr>
                    <tr>
                        <td><strong>Database:</strong></td>
                        <td>MySQL</td>
                    </tr>
                    <tr>
                        <td><strong>Last Update:</strong></td>
                        <td><?= date('Y-m-d H:i:s') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-envelope text-primary me-3"></i>
                    <div>
                        <strong>Email Support</strong><br>
                        <span class="text-muted">support@optima.com</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-phone text-success me-3"></i>
                    <div>
                        <strong>Phone Support</strong><br>
                        <span class="text-muted">+62 21 1234 5678</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-clock text-info me-3"></i>
                    <div>
                        <strong>Support Hours</strong><br>
                        <span class="text-muted">Mon-Fri, 8:00 AM - 6:00 PM WIB</span>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-globe text-warning me-3"></i>
                    <div>
                        <strong>Website</strong><br>
                        <span class="text-muted">www.optima.com</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select class="form-select" id="subject" required>
                            <option value="">Select a topic...</option>
                            <option value="technical">Technical Issue</option>
                            <option value="feature">Feature Request</option>
                            <option value="training">Training Request</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="5" required 
                                  placeholder="Please describe your issue or question in detail..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitContact()">Send Message</button>
            </div>
        </div>
    </div>
</div>

<script>
function submitContact() {
    const form = document.getElementById('contactForm');
    if (form.checkValidity()) {
        showNotification('Support request sent successfully! We will respond within 24 hours.', 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
        modal.hide();
        form.reset();
    } else {
        form.reportValidity();
    }
}
</script>

<?= $this->endSection() ?>
