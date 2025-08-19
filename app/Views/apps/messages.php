<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>Messages - OPTIMA<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope mr-2"></i>Messages
        </h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
            <i class="fas fa-plus"></i> New Message
        </button>
    </div>

    <div class="row">
        <!-- Message List -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-inbox"></i> Inbox
                        </h6>
                        <span class="badge bg-primary">5</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="messageList">
                        <!-- Message Item -->
                        <div class="list-group-item list-group-item-action message-item active" data-message-id="1">
                            <div class="d-flex justify-content-between">
                                <div class="message-sender fw-bold">Ahmad Hidayat</div>
                                <div class="message-time text-muted small">10:30</div>
                            </div>
                            <div class="message-subject text-truncate">Laporan Maintenance Unit FL001</div>
                            <div class="message-preview text-muted small">Unit FL001 telah selesai maintenance dan siap...</div>
                        </div>

                        <div class="list-group-item list-group-item-action message-item" data-message-id="2">
                            <div class="d-flex justify-content-between">
                                <div class="message-sender fw-bold">Siti Nurhaliza</div>
                                <div class="message-time text-muted small">09:15</div>
                            </div>
                            <div class="message-subject text-truncate">Permintaan Sewa Forklift</div>
                            <div class="message-preview text-muted small">PT Gudang Sentral meminta sewa 3 unit forklift...</div>
                        </div>

                        <div class="list-group-item list-group-item-action message-item" data-message-id="3">
                            <div class="d-flex justify-content-between">
                                <div class="message-sender fw-bold">Budi Santoso</div>
                                <div class="message-time text-muted small">Yesterday</div>
                            </div>
                            <div class="message-subject text-truncate">Update Stok Sparepart</div>
                            <div class="message-preview text-muted small">Stok beberapa sparepart sudah menipis dan perlu...</div>
                        </div>

                        <div class="list-group-item list-group-item-action message-item" data-message-id="4">
                            <div class="d-flex justify-content-between">
                                <div class="message-sender fw-bold">Dewi Sartika</div>
                                <div class="message-time text-muted small">Yesterday</div>
                            </div>
                            <div class="message-subject text-truncate">Konfirmasi Jadwal PMP</div>
                            <div class="message-preview text-muted small">Mohon konfirmasi jadwal preventive maintenance...</div>
                        </div>

                        <div class="list-group-item list-group-item-action message-item" data-message-id="5">
                            <div class="d-flex justify-content-between">
                                <div class="message-sender fw-bold">System Notification</div>
                                <div class="message-time text-muted small">2 days ago</div>
                            </div>
                            <div class="message-subject text-truncate">Backup Database Berhasil</div>
                            <div class="message-preview text-muted small">Backup otomatis database telah berhasil dilakukan...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-envelope-open"></i> Message Details
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="replyMessage()">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="forwardMessage()">
                                <i class="fas fa-share"></i> Forward
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteMessage()">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="messageContent">
                    <div class="message-header mb-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">Laporan Maintenance Unit FL001</h5>
                                <div class="text-muted">
                                    <strong>From:</strong> Ahmad Hidayat &lt;ahmad.hidayat@saranamitra.co.id&gt;
                                </div>
                                <div class="text-muted">
                                    <strong>To:</strong> You &lt;admin@optima.com&gt;
                                </div>
                                <div class="text-muted">
                                    <strong>Date:</strong> Today, 10:30 AM
                                </div>
                            </div>
                            <div class="message-priority">
                                <span class="badge bg-warning">High Priority</span>
                            </div>
                        </div>
                    </div>

                    <div class="message-body">
                        <p>Selamat pagi,</p>
                        
                        <p>Saya ingin melaporkan bahwa maintenance rutin untuk Unit FL001 (Toyota 8FBE20) telah selesai dilakukan pada tanggal hari ini.</p>
                        
                        <p><strong>Detail pekerjaan yang telah dilakukan:</strong></p>
                        <ul>
                            <li>Penggantian oli mesin dan hydraulic</li>
                            <li>Pemeriksaan dan pembersihan filter udara</li>
                            <li>Kalibrasi sistem pengereman</li>
                            <li>Pengecekan kondisi ban dan rantai</li>
                            <li>Testing keseluruhan fungsi operasional</li>
                        </ul>
                        
                        <p><strong>Status Unit:</strong> Unit telah lulus quality check dan siap untuk dioperasikan kembali.</p>
                        
                        <p><strong>Rekomendasi:</strong> Maintenance berikutnya dijadwalkan dalam 3 bulan atau 500 jam operasi.</p>
                        
                        <p>Unit sudah dapat dikembalikan ke area operasional. Mohon update status di sistem menjadi "Available".</p>
                        
                        <p>Terima kasih atas perhatiannya.</p>
                        
                        <p>Best regards,<br>
                        Ahmad Hidayat<br>
                        Senior Technician<br>
                        Service Division</p>
                    </div>

                    <div class="message-attachments mt-4">
                        <h6 class="text-muted mb-2">Attachments (2)</h6>
                        <div class="d-flex gap-2">
                            <div class="attachment-item">
                                <a href="#" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-file-pdf text-danger"></i> Maintenance_Report_FL001.pdf
                                </a>
                            </div>
                            <div class="attachment-item">
                                <a href="#" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-file-image text-primary"></i> Before_After_Photos.zip
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Message Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Compose New Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="composeForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="recipient" class="form-label">To</label>
                                <select class="form-select" id="recipient" required>
                                    <option value="">Select recipient...</option>
                                    <option value="ahmad.hidayat">Ahmad Hidayat (Technician)</option>
                                    <option value="siti.nurhaliza">Siti Nurhaliza (Marketing)</option>
                                    <option value="budi.santoso">Budi Santoso (Warehouse)</option>
                                    <option value="dewi.sartika">Dewi Sartika (Service)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority">
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="messageBody" class="form-label">Message</label>
                        <textarea class="form-control" id="messageBody" rows="8" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments</label>
                        <input type="file" class="form-control" id="attachments" multiple>
                        <div class="form-text">You can select multiple files</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Message item click handler
    $('.message-item').click(function() {
        $('.message-item').removeClass('active');
        $(this).addClass('active');
        
        const messageId = $(this).data('message-id');
        loadMessageContent(messageId);
    });
});

function loadMessageContent(messageId) {
    // Simulate loading message content
    const messages = {
        1: {
            subject: 'Laporan Maintenance Unit FL001',
            from: 'Ahmad Hidayat <ahmad.hidayat@saranamitra.co.id>',
            to: 'You <admin@optima.com>',
            date: 'Today, 10:30 AM',
            priority: 'High Priority',
            content: `
                <p>Selamat pagi,</p>
                <p>Saya ingin melaporkan bahwa maintenance rutin untuk Unit FL001 telah selesai...</p>
            `
        },
        2: {
            subject: 'Permintaan Sewa Forklift',
            from: 'Siti Nurhaliza <siti.nurhaliza@saranamitra.co.id>',
            to: 'You <admin@optima.com>',
            date: 'Today, 09:15 AM',
            priority: 'Normal',
            content: `
                <p>Halo,</p>
                <p>PT Gudang Sentral menghubungi kami untuk permintaan sewa 3 unit forklift...</p>
            `
        }
        // Add more messages as needed
    };
    
    if (messages[messageId]) {
        const msg = messages[messageId];
        $('#messageContent').html(`
            <div class="message-header mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${msg.subject}</h5>
                        <div class="text-muted"><strong>From:</strong> ${msg.from}</div>
                        <div class="text-muted"><strong>To:</strong> ${msg.to}</div>
                        <div class="text-muted"><strong>Date:</strong> ${msg.date}</div>
                    </div>
                    <div class="message-priority">
                        <span class="badge ${msg.priority === 'High Priority' ? 'bg-warning' : 'bg-secondary'}">${msg.priority}</span>
                    </div>
                </div>
            </div>
            <div class="message-body">${msg.content}</div>
        `);
    }
}

function sendMessage() {
    const form = document.getElementById('composeForm');
    if (form.checkValidity()) {
        OptimaPro.showLoading('Sending message...');
        
        setTimeout(() => {
            OptimaPro.hideLoading();
            $('#composeModal').modal('hide');
            OptimaPro.showNotification('Message sent successfully!', 'success');
            form.reset();
        }, 1000);
    } else {
        form.reportValidity();
    }
}

function replyMessage() {
    $('#composeModal').modal('show');
    $('#subject').val('Re: Laporan Maintenance Unit FL001');
    $('#recipient').val('ahmad.hidayat');
}

function forwardMessage() {
    $('#composeModal').modal('show');
    $('#subject').val('Fwd: Laporan Maintenance Unit FL001');
}

function deleteMessage() {
    if (confirm('Are you sure you want to delete this message?')) {
        OptimaPro.showNotification('Message deleted successfully!', 'success');
        // Remove message from list or reload
    }
}
</script>
<?= $this->endSection() ?> 