<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap4.min.css">
<style>
    /* CSS umum sudah ada di optima-pro.css */
    /* Custom styling khusus untuk area & employee management */
    
    .mini-stats-wid { 
        border: none; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        border-radius: 15px; 
    }
    .mini-stats-wid .avatar-sm { height: 3rem; width: 3rem; }
    .mini-stats-wid .mini-stat-icon { line-height: 3rem; text-align:center; }
    
    .form-errors { 
        background-color: #f8d7da; 
        border: 1px solid #f5c6cb; 
        border-radius: 5px; 
        padding: 10px; 
        display: none;
    }
    .form-errors:not(:empty) { 
        display: block;
    }
    
    .btn-view { 
        background-color: #4e73df; 
        border-color: #4e73df; 
        color: white; 
        font-size: 0.75rem; 
        padding: 0.25rem 0.5rem; 
    }
    .btn-view:hover { 
        background-color: #2e59d9; 
        border-color: #2653d4; 
        color: white; 
    }
    
    .employee-role { 
        font-weight: 500; 
        color: #2c3e50 !important; /* Dark color for readability */
    }
    .employee-code { 
        font-family: 'Courier New', monospace; 
        font-size: 0.85rem; 
        color: #495057 !important; /* Darker grey for better contrast */
    }
    .dataTables_wrapper .dataTables_filter input { border-color: #d1d3e2; }
    .text-dark { color: #2c3e50 !important; } /* Ensure dark text is visible */
    .font-weight-medium { font-weight: 500; }
    
    /* Fix white text issue in DataTables */
    #areasTable tbody td,
    #employeesTable tbody td,
    #assignmentsTable tbody td {
        color: #2c3e50 !important; /* Dark grey text for all table cells */
    }
    
    /* Badge colors - ensure they're visible */
    .badge-info { 
        background-color: #36b9cc !important; 
        color: white !important; 
    }
    .badge-primary { 
        background-color: #4e73df !important; 
        color: white !important; 
    }
    .badge-success { 
        background-color: #1cc88a !important; 
        color: white !important; 
    }
    .badge-warning { 
        background-color: #f6c23e !important; 
        color: #2c3e50 !important; 
        font-weight: 600;
    }
    .badge-danger { 
        background-color: #e74a3b !important; 
        color: white !important; 
    }
    .badge-secondary { 
        background-color: #6c757d !important; 
        color: white !important; 
    }
    .badge-light { 
        background-color: #e3e6f0 !important; 
        color: #2c3e50 !important; 
        border: 1px solid #d1d3e2;
        font-weight: 600;
    }
    .badge-dark { 
        background-color: #2c3e50 !important; 
        color: white !important; 
    }
    
    /* Make sure text-muted is still readable */
    .text-muted {
        color: #6c757d !important;
    }
    
    /* Table styling improvements */
    .table thead th {
        background-color: #f8f9fc;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #e3e6f0;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #e3e6f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    .nav-tabs .nav-item { margin-bottom: 0; }
    .nav-tabs .nav-link { 
        border: 1px solid transparent;
        border-top-left-radius: 0.35rem;
        border-top-right-radius: 0.35rem;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: white !important;
        background-color: #4e73df !important;
        border-color: #4e73df !important;
    }
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
        color: #4e73df;
    }
    .table-hover-row {
        background-color: #f1f3f4 !important;
        cursor: pointer;
    }
    .table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    .table tbody tr:hover {
        cursor: pointer;
    }
    
    
    .btn-outline-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        transform: translateY(-2px);
        color: white;
    }
    
    .btn-outline-success:active {
        transform: translateY(0);
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
    }
    
    /* use centralized button styles from optima-pro.css */
    
    /* Fix modal close button styling */
    .modal-header .close {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #6c757d;
        text-shadow: none;
        opacity: 0.8;
        padding: 0;
        margin: 0;
        width: auto;
        height: auto;
        cursor: pointer;
    }
    .modal-header .close:hover {
        color: #495057;
        opacity: 1;
        background: none;
        border: none;
        cursor: pointer;
    }
    .modal-header .close:focus {
        outline: none;
        box-shadow: none;
    }
    .modal-header .close:active {
        transform: scale(0.95);
    }
    
    /* Ensure modal close button is clickable */
    .modal-header .close {
        z-index: 1050;
        position: relative;
    }
    
    /* Fix modal backdrop issues */
    .modal-backdrop {
        z-index: 1040;
    }
    .modal {
        z-index: 1050;
    }
    
    /* Modal action buttons */
    .modal-footer .btn {
        margin-left: 0.5rem;
    }
    .modal-footer .btn:first-child {
        margin-left: 0;
    }
    
    /* DataTable Buttons styling */
    .dt-buttons {
        margin-bottom: 0.5rem;
    }
    .dt-buttons .btn {
        margin-left: 0.25rem;
        margin-right: 0;
    }
    .dt-buttons .btn:first-child {
        margin-left: 0;
    }
    
    /* Tab content header styling */
    .tab-pane .card-header {
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid #e3e6f0;
    }
    .tab-pane .card-header h6 {
        font-weight: 600;
        color: #5a5c69;
    }
    
    /* Real-time update effects */
    .table-success {
        background-color: #d4edda !important;
        transition: background-color 2s ease-out;
    }
    .fade-out-row {
        opacity: 0.3;
        transition: opacity 0.3s ease-out;
    }
    
    /* Fix spacing for assignments buttons */
    .gap-2 > * + * {
        margin-left: 0.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

  <!-- Statistics Cards -->
  <div class="row g-4 mb-4">
      <div class="col-md-3">
          <div class="card card-stats bg-primary text-white h-100">
              <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <h2 class="fw-bold mb-1"><?= $totalAreas ?></h2>
                          <h6 class="card-title text-uppercase small mb-0">Total Areas</h6>
                      </div>
                      <div class="avatar-sm rounded-circle bg-white bg-opacity-25">
                          <i class="fas fa-globe-europe text-white" style="font-size: 1.5rem; line-height: 3rem;"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-stats bg-success text-white h-100">
              <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <h2 class="fw-bold mb-1"><?= $totalEmployees ?></h2>
                          <h6 class="card-title text-uppercase small mb-0">Total Employees</h6>
                      </div>
                      <div class="avatar-sm rounded-circle bg-white bg-opacity-25">
                          <i class="fas fa-users text-white" style="font-size: 1.5rem; line-height: 3rem;"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-stats bg-warning text-white h-100">
              <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <h2 class="fw-bold mb-1"><?= $totalAssignments ?></h2>
                          <h6 class="card-title text-uppercase small mb-0">Active Assignments</h6>
                      </div>
                      <div class="avatar-sm rounded-circle bg-white bg-opacity-25">
                          <i class="fas fa-link text-white" style="font-size: 1.5rem; line-height: 3rem;"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="card card-stats bg-info text-white h-100">
              <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <h2 class="fw-bold mb-1" id="roleDistribution">-</h2>
                          <h6 class="card-title text-uppercase small mb-0">Role Distribution</h6>
                      </div>
                      <div class="avatar-sm rounded-circle bg-white bg-opacity-25">
                          <i class="fas fa-chart-pie text-white" style="font-size: 1.5rem; line-height: 3rem;"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Page Header -->
  <div class="row">
      <div class="col-12">
          <!-- Page header removed as requested -->
      </div>
  </div>

  <!-- Main Content Tabs -->
  <div class="card table-card shadow mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          <ul class="nav nav-tabs flex-grow-1" role="tablist">
              <li class="nav-item">
                  <a class="nav-link active" id="areas-tab" data-bs-toggle="tab" href="#areasTab" role="tab" aria-controls="areasTab" aria-selected="true">
                      <i class="fas fa-map-marked-alt mr-1"></i> Service Areas
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="employees-tab" data-bs-toggle="tab" href="#employeesTab" role="tab" aria-controls="employeesTab" aria-selected="false">
                      <i class="fas fa-users mr-1"></i> Employees
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="assignments-tab" data-bs-toggle="tab" href="#assignmentsTab" role="tab" aria-controls="assignmentsTab" aria-selected="false">
                      <i class="fas fa-link mr-1"></i> Assignments
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#analyticsTab" role="tab" aria-controls="analyticsTab" aria-selected="false">
                      <i class="fas fa-chart-bar mr-1"></i> Analytics
                  </a>
              </li>
          </ul>
      </div>
      <div class="card-body">
          <div class="tab-content" id="managementTabsContent">
                      <!-- Areas Tab -->
                      <div class="tab-pane fade show active" id="areasTab" role="tabpanel">
                          <div class="card-header bg-light d-flex justify-content-between align-items-center mb-3">
                              <h6 class="mb-0"><i class="fas fa-map-marked-alt text-primary"></i> Service Areas</h6>
                              <div>
                                  <button type="button" class="btn btn-info btn-sm mr-2" onclick="refreshAreas()" title="Refresh Areas Data">
                                      <i class="fas fa-sync-alt"></i> Refresh
                                  </button>
                                  <a href="<?= base_url('service/export_area') ?>" class="btn btn-outline-success btn-sm">
                                      <i class="fas fa-file-excel"></i> Export Area
                                  </a>
                                  <button type="button" class="btn btn-primary btn-sm" onclick="showAddAreaModal()">
                                      <i class="fas fa-plus"></i> Add New Area
                                  </button>
                              </div>
                          </div>
                          <div class="table-responsive">
                              <table id="areasTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Area Code</th>
                                          <th>Area Name</th>
                                          <th>Description</th>
                                          <th>Customers</th>
                                          <th>Employees</th>
                                          <th>Assignments</th>
                                      </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>
                      </div>

                      <!-- Employees Tab -->
                      <div class="tab-pane fade" id="employeesTab" role="tabpanel">
                          <div class="card-header bg-light d-flex justify-content-between align-items-center mb-3">
                              <h6 class="mb-0"><i class="fas fa-users text-success"></i> Employees</h6>
                              <div>
                                  <button type="button" class="btn btn-info btn-sm mr-2" onclick="refreshEmployees()" title="Refresh Employees Data">
                                      <i class="fas fa-sync-alt"></i> Refresh
                                  </button>
                                  <a href="<?= base_url('service/export_employee') ?>" class="btn btn-outline-success btn-sm">
                                      <i class="fas fa-file-excel"></i> Export Employee
                                  </a>
                                  <button type="button" class="btn btn-primary btn-sm" onclick="showAddEmployeeModal()">
                                      <i class="fas fa-plus"></i> Add New Employee
                                  </button>
                              </div>
                          </div>
                          <div class="table-responsive">
                              <table id="employeesTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Staff Code</th>
                                          <th>Name</th>
                                          <th>Role</th>
                                          <th>Department</th>
                                          <th>Phone</th>
                                          <th>Email</th>
                                      </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>
                      </div>

                      <!-- Assignments Tab -->
                      <div class="tab-pane fade" id="assignmentsTab" role="tabpanel">
                          <div class="row mb-4">
                              <div class="col-md-8">
                                  <h5 class="card-title mb-0">Area Assignments Management</h5>
                                  <p class="text-muted small mb-0">Manage employee assignments to service areas</p>
                              </div>
                          </div>
                          <div class="alert alert-info">
                              <strong><i class="fas fa-info-circle"></i> Cara Kerja Tab Assignments:</strong>
                              <ul class="mb-0 mt-2">
                                  <li><strong>1. Pilih Area:</strong> Pilih area dari dropdown di panel kiri</li>
                                  <li><strong>2. Lihat Assignments:</strong> Tabel di kanan akan menampilkan semua employee yang ditugaskan ke area tersebut</li>
                                  <li><strong>3. Filter Role:</strong> Gunakan dropdown "All Roles" untuk filter berdasarkan role (Supervisor, Foreman, Mechanic, Helper)</li>
                                  <li><strong>4. Tambah Assignment:</strong> Klik "New Assignment" untuk menugaskan employee baru ke area</li>
                                  <li><strong>5. Edit/Delete:</strong> Klik tombol edit/delete pada setiap assignment untuk mengubah atau menghapus</li>
                              </ul>
                          </div>
                          <div class="row">
                              <div class="col-md-4">
                                  <div class="card shadow-sm">
                                      <div class="card-header bg-light">
                                          <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> Select Area</h6>
                                      </div>
                                      <div class="card-body">
                                          <select id="assignAreaSelect" class="form-control mb-3" onchange="loadAreaAssignments()">
                                              <option value="">-- Select Area --</option>
                                              <?php if(isset($areas) && is_array($areas)): ?>
                                                  <?php foreach ($areas as $area): ?>
                                                      <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
                                                  <?php endforeach; ?>
                                              <?php endif; ?>
                                          </select>
                                          <div id="areaAssignmentSummary" class="mb-3"></div>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-8">
                                  <div class="card shadow-sm">
                                      <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                          <h6 class="mb-0"><i class="fas fa-users text-info"></i> Area Assignments</h6>
                                          <div class="d-flex align-items-center gap-2">
                                              <select id="filterRoleAssignments" class="form-control form-control-sm" onchange="filterAssignments()" style="width:auto; min-width: 120px;">
                                                  <option value="">All Roles</option>
                                                  <option value="SUPERVISOR">Supervisor</option>
                                                  <option value="FOREMAN">Foreman</option>
                                                  <option value="ADMIN">Admin</option>
                                                  <option value="MECHANIC">Mechanic</option>
                                                  <option value="HELPER">Helper</option>
                                              </select>
                                              <button type="button" class="btn btn-primary btn-sm" onclick="showAddAssignmentModal()">
                                                  <i class="fas fa-link"></i> New Assignment
                                              </button>
                                              <button type="button" class="btn btn-secondary btn-sm" onclick="forceRefreshAssignments()" title="Refresh assignments data">
                                                  <i class="fas fa-sync-alt"></i> Refresh
                                              </button>
                                          </div>
                                      </div>
                                      <div class="card-body">
                                          <div id="areaAssignmentsTable">
                                              <div class="text-center text-muted py-4">
                                                  <i class="fas fa-arrow-left text-muted"></i>
                                                  <p class="mb-0">Select an area from the left panel to view assignments</p>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Analytics Tab -->
                      <div class="tab-pane fade" id="analyticsTab" role="tabpanel">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="card">
                                      <div class="card-header d-flex justify-content-between">
                                          <h6>Employees by Role</h6>
                                          <select id="roleChartFilter" class="form-control form-control-sm" style="width:auto;">
                                              <option value="ALL">All</option>
                                              <option value="SUPERVISOR">Supervisor</option>
                                              <option value="FOREMAN">Foreman</option>
                                              <option value="ADMIN">Admin</option>
                                              <option value="MECHANIC">Mechanic</option>
                                              <option value="HELPER">Helper</option>
                                          </select>
                                      </div>
                                      <div class="card-body">
                                          <canvas id="employeesByRoleChart" height="200"></canvas>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="card">
                                      <div class="card-header">
                                          <h6>Assignments by Area</h6>
                                      </div>
                                      <div class="card-body">
                                          <canvas id="assignmentsByAreaChart" height="200"></canvas>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="card">
                                      <div class="card-header">
                                          <h6>Role Coverage Matrix</h6>
                                      </div>
                                      <div class="card-body">
                                          <div id="roleCoverageMatrix" class="table-responsive"></div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>


<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Area</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addAreaForm">
        <div class="modal-body">
          <div class="form-errors text-danger small mb-3" style="display: none;"></div>
          <div class="form-group">
            <label>Area Code <span class="text-danger">*</span></label>
            <input type="text" name="area_code" class="form-control" required maxlength="10" placeholder="Enter area code">
          </div>
          <div class="form-group">
            <label>Area Name <span class="text-danger">*</span></label>
            <input type="text" name="area_name" class="form-control" required maxlength="255" placeholder="Enter area name">
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="area_description" class="form-control" rows="3" placeholder="Enter area description (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Area</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Employee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addEmployeeForm">
        <div class="modal-body">
          <div class="form-errors alert alert-danger" style="display: none;"></div>
          <div class="form-group">
            <label>Staff Code <span class="text-danger">*</span></label>
            <input type="text" name="staff_code" class="form-control" required maxlength="20">
          </div>
          <div class="form-group">
            <label>Staff Name <span class="text-danger">*</span></label>
            <input type="text" name="staff_name" class="form-control" required maxlength="255">
          </div>
          <div class="form-group">
            <label>Role <span class="text-danger">*</span></label>
            <select name="staff_role" class="form-control" required>
              <option value="">-- Select Role --</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="ADMIN">Admin</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="HELPER">Helper</option>
            </select>
          </div>
          <div class="form-group">
            <label>Department</label>
            <select name="departemen_id" class="form-control">
              <option value="">-- Select Department --</option>
              <option value="1">DIESEL</option>
              <option value="2">ELECTRIC</option>
              <option value="3">GASOLINE</option>
            </select>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" maxlength="20">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" maxlength="100">
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Employee</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Area Assignment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addAssignmentForm">
        <div class="modal-body">
          <div class="form-group">
            <label>Area <span class="text-danger">*</span></label>
            <select name="area_id" id="assignment_area_id" class="form-control" required onchange="loadAvailableEmployeesForAssignment()">
              <option value="">-- Select Area --</option>
              <?php foreach ($areas as $area): ?>
                <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Role Filter</label>
            <select id="assignment_role_filter" class="form-control" onchange="loadAvailableEmployeesForAssignment()">
              <option value="">All Roles</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="ADMIN">Admin</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="HELPER">Helper</option>
            </select>
          </div>
          <div class="form-group">
            <label>Employee <span class="text-danger">*</span></label>
            <select name="staff_id" id="assignment_staff_id" class="form-control" required>
              <option value="">-- Select Employee --</option>
            </select>
          </div>
          <div class="form-group">
            <label>Assignment Type <span class="text-danger">*</span></label>
            <select name="assignment_type" class="form-control" required>
              <option value="PRIMARY">PRIMARY</option>
              <option value="BACKUP">BACKUP</option>
              <option value="TEMPORARY">TEMPORARY</option>
            </select>
          </div>
          <div class="form-group">
            <label>Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>
          <div class="form-group">
            <label>End Date (optional)</label>
            <input type="date" name="end_date" class="form-control">
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Placeholder Modals (Edit/View) -->
<div class="modal fade" id="viewAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Area Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="areaDetailsContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Employee Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="employeeDetailsContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Area Modal -->
<div class="modal fade" id="editAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Area</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editAreaForm">
        <input type="hidden" name="id" id="edit_area_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Area Code <span class="text-danger">*</span></label>
            <input type="text" name="area_code" id="edit_area_code" class="form-control" required maxlength="10">
          </div>
          <div class="form-group">
            <label>Area Name <span class="text-danger">*</span></label>
            <input type="text" name="area_name" id="edit_area_name" class="form-control" required maxlength="255">
          </div>
            <div class="form-group">
            <label>Description</label>
            <textarea name="area_description" id="edit_area_description" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Area</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Employee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editEmployeeForm">
        <input type="hidden" name="id" id="edit_staff_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Staff Code <span class="text-danger">*</span></label>
            <input type="text" name="staff_code" id="edit_staff_code" class="form-control" required maxlength="20">
          </div>
          <div class="form-group">
            <label>Staff Name <span class="text-danger">*</span></label>
            <input type="text" name="staff_name" id="edit_staff_name" class="form-control" required maxlength="255">
          </div>
          <div class="form-group">
            <label>Role <span class="text-danger">*</span></label>
            <select name="staff_role" id="edit_staff_role" class="form-control" required>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="ADMIN">Admin</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="HELPER">Helper</option>
            </select>
          </div>
          <div class="form-group">
            <label>Department</label>
            <select name="departemen_id" id="edit_staff_departemen_id" class="form-control">
              <option value="">-- Select Department --</option>
              <option value="1">DIESEL</option>
              <option value="2">ELECTRIC</option>
            </select>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" id="edit_staff_phone" class="form-control" maxlength="20">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="edit_staff_email" class="form-control" maxlength="100">
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" id="edit_staff_address" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="edit_staff_description" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Employee</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Assignment Modal -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Assignment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editAssignmentForm">
        <input type="hidden" name="id" id="edit_assignment_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Area</label>
            <input type="text" id="edit_assignment_area" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Employee</label>
            <input type="text" id="edit_assignment_staff" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Role</label>
            <input type="text" id="edit_assignment_role" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Assignment Type <span class="text-danger">*</span></label>
            <select name="assignment_type" id="edit_assignment_type" class="form-control" required>
              <option value="PRIMARY">PRIMARY</option>
              <option value="BACKUP">BACKUP</option>
              <option value="TEMPORARY">TEMPORARY</option>
            </select>
          </div>
          <div class="form-group">
            <label>Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" id="edit_assignment_start" class="form-control" required>
          </div>
          <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" id="edit_assignment_end" class="form-control">
          </div>
          <div class="form-group">
            <label>Status Active</label>
            <select name="is_active" id="edit_assignment_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" id="edit_assignment_notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Employee Detail Modal -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Employee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td class="font-weight-bold text-muted" style="width: 40%;">Staff Code:</td>
                <td id="detail_staff_code" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Full Name:</td>
                <td id="detail_staff_name" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Role:</td>
                <td id="detail_staff_role" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Department:</td>
                <td id="detail_departemen" class="text-dark">-</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td class="font-weight-bold text-muted" style="width: 40%;">Phone:</td>
                <td id="detail_phone" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Email:</td>
                <td id="detail_email" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Address:</td>
                <td id="detail_address" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Hire Date:</td>
                <td id="detail_hire_date" class="text-dark">-</td>
              </tr>
            </table>
          </div>
        </div>
        
        <hr>
        
        <div class="row">
          <div class="col-12">
            <h6 class="font-weight-bold text-muted mb-3">Area Assignments</h6>
            <div id="detail_assignments">
              <div class="text-muted">Loading assignments...</div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-warning" onclick="editEmployeeFromDetail()" id="editEmployeeFromDetailBtn">
          <i class="fas fa-edit"></i> Edit Employee
        </button>
        <button type="button" class="btn btn-danger" onclick="deleteEmployeeFromDetail()" id="deleteEmployeeFromDetailBtn">
          <i class="fas fa-trash"></i> Delete Employee
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Area Detail Modal -->
<div class="modal fade" id="areaDetailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Area</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td class="font-weight-bold text-muted" style="width: 40%;">Area Code:</td>
                <td id="area_detail_code" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Area Name:</td>
                <td id="area_detail_name" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Description:</td>
                <td id="area_detail_description" class="text-dark">-</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-borderless table-sm">
              <tr>
                <td class="font-weight-bold text-muted" style="width: 40%;">Customers:</td>
                <td id="area_detail_customers" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Employees:</td>
                <td id="area_detail_employees" class="text-dark">-</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted">Created:</td>
                <td id="area_detail_created" class="text-dark">-</td>
              </tr>
            </table>
          </div>
        </div>
        
        <hr>
        
        <div class="row">
          <div class="col-12">
            <h6 class="font-weight-bold text-muted mb-3">Employee Assignments</h6>
            <div id="area_detail_assignments">
              <div class="text-center text-muted py-3">
                <i class="fas fa-spinner fa-spin"></i> Loading assignments...
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-warning" onclick="editAreaFromDetail()" id="editAreaFromDetailBtn">
          <i class="fas fa-edit"></i> Edit Area
        </button>
        <button type="button" class="btn btn-danger" onclick="deleteAreaFromDetail()" id="deleteAreaFromDetailBtn">
          <i class="fas fa-trash"></i> Delete Area
        </button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let areasTable, employeesTable;
let employeesByRoleChart, assignmentsByAreaChart;

$(document).ready(function() {
  initializeAreaTable();
  initializeEmployeeTable();
  initializeCharts();
  bindForms();
  buildRoleCoverageMatrix();
  
  // Restore tab if page was reloaded for refresh
  restoreActiveTab();
  
  // Track current active tab
  window.currentActiveTab = 'areasTab'; // Default active tab
  
  // Explicitly activate the first tab to prevent stacking issues
  setTimeout(function() {
    $('#areas-tab').tab('show');
    console.log('✅ Explicitly activated Areas tab to prevent stacking');
  }, 100);
  
  // Tab change tracking
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const targetTab = $(e.target).attr('href').substring(1); // Remove # from href
    window.currentActiveTab = targetTab;
    console.log('Active tab changed to:', targetTab);
  });
  
  // Fix modal close button functionality - comprehensive fix
  $(document).on('click', '.modal .close, .modal [data-dismiss="modal"]', function(e) {
    console.log('Close button clicked:', this);
    e.preventDefault();
    e.stopPropagation();
    const modal = $(this).closest('.modal');
    console.log('Modal found:', modal);
    modal.modal('hide');
  });
  
  // Fix modal backdrop click
  $(document).on('click', '.modal', function(e) {
    if (e.target === this) {
      $(this).modal('hide');
    }
  });
  
  // Fix ESC key to close modal
  $(document).on('keydown', function(e) {
    if (e.keyCode === 27) { // ESC key
      $('.modal.show').modal('hide');
    }
  });
  
  // Specific fix for all modal close buttons with more specific selectors
  $(document).on('click', 'button[data-dismiss="modal"]', function(e) {
    console.log('Data-dismiss button clicked:', this);
    e.preventDefault();
    e.stopPropagation();
    const modal = $(this).closest('.modal');
    if (modal.length) {
      modal.modal('hide');
    }
  });
  
  // Specific fix for Cancel buttons
  $(document).on('click', '.btn-secondary', function(e) {
    console.log('Cancel button clicked:', this);
    const modal = $(this).closest('.modal');
    if (modal.length && $(this).text().toLowerCase().includes('cancel') || $(this).text().toLowerCase().includes('close') || $(this).text().toLowerCase().includes('tutup')) {
      e.preventDefault();
      e.stopPropagation();
      modal.modal('hide');
    }
  });
  
  // Ensure modal events are properly bound
  $('.modal').on('hidden.bs.modal', function() {
    $(this).find('form')[0]?.reset();
    $(this).find('.form-errors').html('').hide();
  });
});

/* ===================== TABLE INITIALIZATIONS ===================== */
function initializeAreaTable() {
  try {
    areasTable = $('#areasTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '<?= base_url('service/area-management/getAreas') ?>',
      type: 'POST',
      data: function(d) {
        // Add any additional data if needed
        return d;
      },
      dataSrc: function(json) {
        console.log('DataTable Response:', json);
        if (json && json.data) {
          return json.data;
        } else {
          console.error('Invalid response format:', json);
          return [];
        }
      },
      error: function(xhr, error, code) {
        console.error('DataTable AJAX Error:', error, code);
        console.error('Response:', xhr.responseText);
        // Return empty array to prevent DataTable error
        return [];
      }
    },
    columns: [
      { data: 'area_code', render: d => `<span class="employee-code">${d}</span>` },
      { data: 'area_name', render: d => `<span class="text-dark font-weight-medium">${d}</span>` },
      { data: 'description', render: d => d ? (d.length > 50 ? `<span class="text-dark">${d.substring(0,50)}</span><span class="text-muted">…</span>` : `<span class="text-dark">${d}</span>`) : '<span class="text-muted">-</span>' },
      { data: 'customers_count', render: d => `<span class="badge badge-info">${d || 0}</span>` },
      { data: 'employees_count', render: d => `<span class="badge badge-primary">${d || 0}</span>` },
      { data: 'assignment_summary', orderable:false, render: renderAssignmentSummary }
    ],
    order: [[1,'asc']],
    pageLength: 25,
    language: {
      emptyTable: "No areas found",
      info: "Showing _START_ to _END_ of _TOTAL_ areas",
      infoEmpty: "Showing 0 to 0 of 0 areas"
    },
    drawCallback: function(settings) {
      console.log('DataTable draw completed');
      
      // Add click event to table rows
      $('#areasTable tbody').off('click', 'tr').on('click', 'tr', function() {
        const data = areasTable.row(this).data();
        if (data) {
          viewAreaDetail(data.area_code, data);
        }
      });
      // Add hover effect
      $('#areasTable tbody tr').hover(
        function() { $(this).addClass('table-hover-row'); },
        function() { $(this).removeClass('table-hover-row'); }
      );
    }
  });
  } catch (error) {
    console.error('Error initializing Areas DataTable:', error);
    // Fallback: show error message
    $('#areasTable').html('<div class="alert alert-danger">Error loading areas data. Please refresh the page.</div>');
  }
}

function initializeEmployeeTable() {
  employeesTable = $('#employeesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '<?= base_url('service/area-management/getEmployees') ?>', type: 'POST' },
    columns: [
      { data: 'staff_code', render: d => `<span class="employee-code">${d}</span>` },
      { data: 'staff_name', render: d => `<span class="text-dark font-weight-medium">${d}</span>` },
      { data: 'staff_role', render: r => `<span class="badge badge-pill badge-${roleBadgeColor(r)} employee-role">${r}</span>` },
      { data: 'departemen', render: d => d ? `<span class="text-dark">${d}</span>` : '<span class="text-muted">-</span>' },
      { data: 'phone', render: d => d ? `<span class="text-dark">${d}</span>` : '<span class="text-muted">-</span>' },
      { data: 'email', render: d => d ? `<span class="text-dark">${d}</span>` : '<span class="text-muted">-</span>' }
    ],
    order: [[1,'asc']],
    pageLength: 25,
    language: {
      emptyTable: "No employees found",
      info: "Showing _START_ to _END_ of _TOTAL_ employees",
      infoEmpty: "Showing 0 to 0 of 0 employees"
    },
    drawCallback: function(settings) {
      // Add click event to table rows
      $('#employeesTable tbody').off('click', 'tr').on('click', 'tr', function() {
        const data = employeesTable.row(this).data();
        if (data && data.id) {
          viewEmployeeDetail(data.id);
        }
      });
      // Add hover effect
      $('#employeesTable tbody tr').hover(
        function() { $(this).addClass('table-hover-row'); },
        function() { $(this).removeClass('table-hover-row'); }
      );
    }
  });
}

/* ===================== CHARTS ===================== */
function initializeCharts() {
  const roleCtx = document.getElementById('employeesByRoleChart');
  const assignmentsCtx = document.getElementById('assignmentsByAreaChart');

  const employeesByRoleData = <?= isset($employeesByRole) ? json_encode($employeesByRole) : '[]' ?>;
  const assignmentsByAreaData = <?= isset($assignmentsByArea) ? json_encode($assignmentsByArea) : '[]' ?>;

  if (roleCtx && employeesByRoleData && employeesByRoleData.length > 0) {
    employeesByRoleChart = new Chart(roleCtx, {
      type: 'bar',
      data: {
        labels: employeesByRoleData.map(r => r.role || 'Unknown'),
        datasets: [{
          label: 'Employees',
          data: employeesByRoleData.map(r => parseInt(r.employee_count) || 0),
          backgroundColor: '#4e73df'
        }]
      },
      options: { responsive:true, maintainAspectRatio:false }
    });
  }

  if (assignmentsCtx && assignmentsByAreaData && assignmentsByAreaData.length > 0) {
    assignmentsByAreaChart = new Chart(assignmentsCtx, {
      type: 'bar', // Changed from horizontalBar which is deprecated
      data: {
        labels: assignmentsByAreaData.map(a => a.area_name || 'Unknown'),
        datasets: [{
          label: 'Assignments',
          data: assignmentsByAreaData.map(a => parseInt(a.assignment_count) || 0),
          backgroundColor: '#1cc88a'
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        indexAxis: 'y' // This makes the bar chart horizontal
      }
    });
  }
}

/* ===================== ROLE COVERAGE MATRIX ===================== */
function buildRoleCoverageMatrix() {
  $.ajax({
    url: '<?= base_url('service/area-management/getAreas') ?>',
    type: 'POST',
    data: { draw:1, start:0, length:1000 },
    success: function(resp) {
      if (!resp.data) return;
      const roles = ['SUPERVISOR','FOREMAN','MECHANIC','HELPER'];
      let html = '<table class="table table-sm table-bordered"><thead><tr><th>Area</th>' + roles.map(r => `<th>${r}</th>`).join('') + '</tr></thead><tbody>';
      resp.data.forEach(area => {
        html += `<tr><td>${area.area_code}</td>`;
        
        // Supervisor (kita tidak punya ini dalam struktur baru)
        html += '<td>-</td>';
        
        // Foreman 
        if (area.assignment_summary && area.assignment_summary.foreman) {
          html += `<td><span class='badge badge-success'>1</span></td>`;
        } else {
          html += '<td>-</td>';
        }
        
        // Mechanic
        if (area.assignment_summary && area.assignment_summary.mechanics && area.assignment_summary.mechanics.length > 0) {
          html += `<td><span class='badge badge-success'>${area.assignment_summary.mechanics.length}</span></td>`;
        } else {
          html += '<td>-</td>';
        }
        
        // Helper
        if (area.assignment_summary && area.assignment_summary.helpers && area.assignment_summary.helpers.length > 0) {
          html += `<td><span class='badge badge-success'>${area.assignment_summary.helpers.length}</span></td>`;
        } else {
          html += '<td>-</td>';
        }
        
        html += '</tr>';
      });
      html += '</tbody></table>';
      $('#roleCoverageMatrix').html(html);
    }
  });
}

/* ===================== ASSIGNMENT RENDERER ===================== */
function renderAssignmentSummary(summary) {
  if (!summary || typeof summary !== 'object') {
    return '<span class="text-muted">No assignments</span>';
  }
  
  let output = [];
  
  // Add foreman if exists (check if it's a string or number)
  if (summary.foreman && (typeof summary.foreman === 'string' || summary.foreman > 0)) {
    if (typeof summary.foreman === 'string') {
      output.push(`<span class='badge badge-warning mr-1'>1 Foreman</span>`);
    } else {
      output.push(`<span class='badge badge-warning mr-1'>${summary.foreman} Foreman</span>`);
    }
  }
  
  // Add mechanics if exist
  if (summary.mechanics && Array.isArray(summary.mechanics) && summary.mechanics.length > 0) {
    output.push(`<span class='badge badge-primary mr-1'>${summary.mechanics.length} Mechanic${summary.mechanics.length > 1 ? 's' : ''}</span>`);
  }
  
  // Add helpers if exist
  if (summary.helpers && Array.isArray(summary.helpers) && summary.helpers.length > 0) {
    output.push(`<span class='badge badge-success mr-1'>${summary.helpers.length} Helper${summary.helpers.length > 1 ? 's' : ''}</span>`);
  }
  
  return output.length > 0 ? output.join('') : '<span class="text-muted">No assignments</span>';
}

function roleBadgeColor(role) {
  switch(role) {
    case 'SUPERVISOR': return 'dark';      // Hitam - untuk supervisor
    case 'FOREMAN': return 'warning';      // Kuning - untuk foreman
    case 'ADMIN': return 'info';           // Biru muda - untuk admin
    case 'MECHANIC': return 'success';     // Hijau - untuk mechanic
    case 'HELPER': return 'secondary';     // Abu-abu - untuk helper
    default: return 'light';               // Putih - untuk role yang tidak dikenal
  }
}

/* ===================== FORMS BINDING ===================== */
function bindForms() {
  function showFormErrors($form, errors) {
    const $errorDiv = $form.find('.form-errors');
    $errorDiv.html('').hide();
    if (!errors) return;
    
    let html = '<ul class="mb-0">';
    for (const key in errors) {
      html += `<li><strong>${key.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong> ${errors[key]}</li>`;
    }
    html += '</ul>';
    $errorDiv.html(html).show();
  }

  $('#addAreaForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    const $errorDiv = $form.find('.form-errors');
    $errorDiv.html('').hide();
    
    $.post('<?= base_url('service/area-management/saveArea') ?>', $form.serialize(), function(resp){
      console.log('📝 Add area response:', resp); // Debug response
      if (resp.success) {
        $('#addAreaModal').modal('hide');
        notify('Area created successfully','success');
        
        console.log('🔄 Manual refresh after area creation...');
        // Use manual refresh only - no auto page reload
        refreshAreas();
        
        $form[0].reset();
      } else {
        if (resp.errors) {
          showFormErrors($form, resp.errors);
        }
        notify(resp.message || 'Failed to create area','error');
      }
    }, 'json').fail(function(xhr, status, error) {
      notify('Network error: ' + error, 'error');
    });
  });

  $('#addEmployeeForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    $form.find('.form-errors').html('');
    $.post('<?= base_url('service/area-management/saveEmployee') ?>', $form.serialize(), function(resp){
      console.log('📝 Add employee response:', resp); // Debug response
      if (resp.success) {
        $('#addEmployeeModal').modal('hide');
        notify('Employee created','success');
        
        console.log('🔄 Manual refresh after employee creation...');
        // Use manual refresh only - no auto page reload
        refreshEmployees();
        
        $form[0].reset();
      } else {
        console.log('❌ Add employee validation errors:', resp.errors);
        if (resp.errors) {
          showFormErrors($form, resp.errors);
          // Also show detailed error in notification
          let errorDetails = Object.keys(resp.errors).map(field => `${field}: ${resp.errors[field]}`).join('\\n');
          notify(`Validation errors:\\n${errorDetails}`, 'error');
        } else {
          notify(resp.message || 'Failed to create employee','error');
        }
      }
    }, 'json').fail(function(xhr, status, error) {
      console.log('❌ AJAX Error:', xhr.responseText);
      notify('Network error: ' + error, 'error');
    });
  });

  $('#addAssignmentForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    $form.find('.form-errors').html('');
    
    // Pre-check for PRIMARY assignment
    const assignmentType = $form.find('select[name="assignment_type"]').val();
    const areaId = $form.find('select[name="area_id"]').val();
    const staffId = $form.find('select[name="staff_id"]').val();
    
    if (assignmentType === 'PRIMARY' && areaId && staffId) {
      // Get staff role for PRIMARY check
      const staffOption = $form.find(`select[name="staff_id"] option[value="${staffId}"]`).text();
      const roleMatch = staffOption.match(/\(([^)]+)\)$/);
      const role = roleMatch ? roleMatch[1] : '';
      
      if (role) {
        // Check if PRIMARY already exists for this role in this area
        $.getJSON(`<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}`, function(resp){
          if (resp.success) {
            const existingPrimary = resp.data.find(a => 
              a.role === role && 
              a.assignment_type === 'PRIMARY' && 
              a.is_active == 1
            );
            
            if (existingPrimary) {
              const confirmMsg = `⚠️ WARNING: There is already a PRIMARY ${role} (${existingPrimary.staff_name}) assigned to this area.\n\nDo you want to continue creating another PRIMARY assignment for the same role?`;
              if (!confirm(confirmMsg)) {
                return;
              }
            }
          }
          
          // Proceed with submission
          submitAssignmentForm($form);
        }).fail(function(){
          // If check fails, proceed anyway
          submitAssignmentForm($form);
        });
      } else {
        submitAssignmentForm($form);
      }
    } else {
      submitAssignmentForm($form);
    }
  });
  
  function submitAssignmentForm($form) {
    $.post('<?= base_url('service/area-management/storeAssignment') ?>', $form.serialize(), function(resp){
      if (resp.success) {
        $('#addAssignmentModal').modal('hide');
        notify('Assignment created','success');
        const areaId = $('#assignAreaSelect').val();
        if (areaId) loadAreaAssignments();
        buildRoleCoverageMatrix();
        
        console.log('🔄 Manual refresh after assignment creation...');
        // Use manual refresh only - no auto page reload
        forceRefreshAssignments();
        
        $form[0].reset();
      } else {
        showFormErrors($form, resp.errors);
        notify(resp.message || 'Failed to create assignment','error');
      }
    }, 'json');
  }
}

/* ===================== MODAL HELPERS ===================== */
function showAddAreaModal(){ 
  $('#addAreaForm')[0].reset();
  $('#addAreaForm .form-errors').html('').hide();
  $('#addAreaModal').modal('show'); 
}
function showAddEmployeeModal(){ $('#addEmployeeModal').modal('show'); }
function showAddAssignmentModal(){
  const selectedArea = $('#assignAreaSelect').val();
  if (selectedArea) {
    $('#assignment_area_id').val(selectedArea);
    loadAvailableEmployeesForAssignment();
  } else {
    // Clear employee dropdown if no area selected
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Area First --</option>');
  }
  $('#addAssignmentModal').modal('show');
}

/* ===================== AREA DETAILS & ACTIONS ===================== */
function viewArea(id) {
  $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${id}`, function(resp){
    if (!resp.success) return notify(resp.message,'error');
    const area = resp.data.area;
    const customers = resp.data.customers || [];
    const assignments = resp.data.assignments || [];
    const stats = resp.data.stats || [];
    let html = `<h5>${area.area_code} - ${area.area_name}</h5>`;
    html += `<p>${area.description || ''}</p>`;
    html += `<h6>Assignments</h6>`;
    if (assignments.length === 0) {
      html += '<div class="text-muted">No assignments found</div>';
    } else {
      html += '<ul class="list-group mb-3">';
      assignments.forEach(a => {
        html += `<li class=\'list-group-item p-2\'><strong>${a.role}</strong> - ${a.staff_name} <span class=\'badge badge-${a.assignment_type==='PRIMARY'?'success':(a.assignment_type==='BACKUP'?'warning':'info')}\'>${a.assignment_type}</span></li>`;
      });
      html += '</ul>';
    }
    html += `<h6>Customers in Area</h6>`;
    if (customers.length === 0) {
      html += '<div class="text-muted">No customers assigned to this area</div>';
    } else {
      html += '<ul class="list-group">';
      customers.forEach(c => {
        html += `<li class=\'list-group-item p-2\'>${c.customer_code} - ${c.customer_name}</li>`;
      });
      html += '</ul>';
    }
    $('#areaDetailsContent').html(html);
    $('#viewAreaModal').modal('show');
  });
}

function deleteArea(id) {
  if (!confirm('Delete this area?')) return;
  
  $.ajax({
    url: `<?= base_url('service/area-management/deleteArea') ?>/${id}`,
    type: 'DELETE',
    success: function(resp){
      if (resp.success) {
        notify('Area deleted','success');
        
        console.log('🔄 Manual refresh after area deletion...');
        // Use manual refresh only - no auto page reload
        refreshAreas();
        
      } else {
        notify(resp.message,'error');
      }
    },
    error: function() {
      notify('Error deleting area', 'error');
    }
  });
}

/* ===================== EMPLOYEE DETAILS & ACTIONS ===================== */
function viewEmployee(id) {
  $.getJSON(`<?= base_url('service/area-management/showEmployee') ?>/${id}`, function(resp){
    if (!resp.success) return notify(resp.message,'error');
    const e = resp.data.employee;
    const assignments = resp.data.assignments || [];
    let html = `<h5>${e.staff_code} - ${e.staff_name}</h5>`;
    html += `<p><span class='badge badge-${roleBadgeColor(e.role)}'>${e.role}</span></p>`;
    html += `<p>${e.description || ''}</p>`;
    html += `<h6>Assignments</h6>`;
    if (assignments.length === 0) html += '<div class="text-muted">No assignments</div>';
    else {
      html += '<ul class="list-group">';
      assignments.forEach(a => {
        html += `<li class=\'list-group-item p-2\'>${a.area_code} - ${a.area_name} <span class=\'badge badge-${a.assignment_type==='PRIMARY'?'success':(a.assignment_type==='BACKUP'?'warning':'info')}\'>${a.assignment_type}</span></li>`;
      });
      html += '</ul>';
    }
    $('#employeeDetailsContent').html(html);
    $('#viewEmployeeModal').modal('show');
  });
}

function deleteEmployee(id) {
  if (!confirm('Deactivate this employee? This will set the employee as inactive instead of permanently deleting.')) return;
  
  $.ajax({
    url: `<?= base_url('service/area-management/deleteEmployee') ?>/${id}`,
    type: 'DELETE',
    success: function(resp){
      if (resp.success) {
        notify('Employee deactivated successfully','success');
        
        console.log('🔄 Manual refresh after employee deactivation...');
        // Use manual refresh only - no auto page reload
        refreshEmployees();
        
      } else {
        notify(resp.message,'error');
      }
    },
    error: function() {
      notify('Error deactivating employee', 'error');
    }
  });
}

/* ===================== ASSIGNMENTS ===================== */
function loadAreaAssignments() {
  const areaId = $('#assignAreaSelect').val();
  console.log('Loading assignments for area ID:', areaId);
  
  if (!areaId) {
    $('#areaAssignmentsTable').html('<div class="text-center text-muted">Select an area to view assignments</div>');
    $('#areaAssignmentSummary').html('');
    return;
  }
  
  // Show loading state
  $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i><br>Loading assignments...</div>');
  
  // Add cache busting to ensure fresh data
  const timestamp = Date.now();
  const url = `<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}?_=${timestamp}`;
  
  $.getJSON(url, function(resp){
    console.log('Area assignments response:', resp);
    if (!resp.success) {
      console.error('Failed to load assignments:', resp);
      $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Error loading assignments</div>');
      return;
    }
    const assignments = resp.data || [];
    console.log('Assignments data:', assignments);
    renderAssignmentsTable(assignments);
    updateAreaAssignmentSummary(assignments);
  }).fail(function(xhr, status, error) {
    console.error('AJAX Error loading assignments:', error);
    console.error('Response:', xhr.responseText);
    $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Network error loading assignments<br><button class="btn btn-sm btn-primary mt-2" onclick="loadAreaAssignments()">Retry</button></div>');
  });
}

// Force refresh assignments - useful when data gets out of sync
function forceRefreshAssignments() {
  console.log('🔄 Force refreshing assignments...');
  const areaId = $('#assignAreaSelect').val();
  if (areaId) {
    // Clear current table first
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-sync fa-spin"></i><br>Force refreshing...</div>');
    
    // Reload with strong cache busting
    setTimeout(function() {
      loadAreaAssignments();
    }, 300);
  }
}



function renderAssignmentsTable(assignments) {
  console.log('📊 Rendering assignments table with data:', assignments);
  console.log('📊 Assignment count:', assignments.length);
  
  const filterRole = $('#filterRoleAssignments').val();
  let filtered = assignments;
  if (filterRole) filtered = assignments.filter(a => a.staff_role === filterRole);
  
  console.log('📊 Filtered assignment count:', filtered.length);
  
  if (filtered.length === 0) {
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-users-slash"></i><br>No assignments found for the selected role</div>');
    return;
  }
  let html = '<div class="table-responsive"><table class="table table-sm table-hover table-bordered">';
  html += '<thead class="thead-light"><tr><th>Staff Name</th><th>Role</th><th>Assignment Type</th><th>Start Date</th><th>End Date</th><th>Actions</th></tr></thead><tbody>';
  filtered.forEach(a => {
    const assignmentTypeColor = a.assignment_type === 'PRIMARY' ? 'success' : (a.assignment_type === 'BACKUP' ? 'warning' : 'info');
    // Fix: Handle undefined role
    const role = a.staff_role || a.role || 'UNKNOWN';
    html += `<tr id="assignment-row-${a.id}">
      <td><strong>${a.staff_name}</strong><br><small class="text-muted">${a.staff_code || ''}</small></td>
      <td><span class='badge badge-${roleBadgeColor(role)}'>${role}</span></td>
      <td><span class='badge badge-${assignmentTypeColor}'>${a.assignment_type}</span></td>
      <td>${a.start_date ? new Date(a.start_date).toLocaleDateString('en-GB') : '-'}</td>
      <td>${a.end_date ? new Date(a.end_date).toLocaleDateString('en-GB') : '-'}</td>
      <td>
        <button class='btn btn-sm btn-outline-primary mr-1' onclick='editAssignment(${a.id})' title="Edit Assignment"><i class='fas fa-edit'></i></button>
        <button class='btn btn-sm btn-outline-danger' onclick='removeAssignment(${a.id})' title="Remove Assignment"><i class='fas fa-trash'></i></button>
      </td>
    </tr>`;
  });
  html += '</tbody></table></div>';
  $('#areaAssignmentsTable').html(html);
}

function updateAreaAssignmentSummary(assignments) {
  if (!assignments || assignments.length === 0) {
    $('#areaAssignmentSummary').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> No assignments found for this area</div>');
    return;
  }
  const roles = {};
  assignments.forEach(a => {
    // Fix: Use staff_role instead of role, and handle undefined values
    const role = a.staff_role || a.role || 'UNKNOWN';
    if (!roles[role]) roles[role] = { primary: 0, backup: 0, temporary: 0 };
    if (a.assignment_type === 'PRIMARY') roles[role].primary++;
    if (a.assignment_type === 'BACKUP') roles[role].backup++;
    if (a.assignment_type === 'TEMPORARY') roles[role].temporary++;
  });
  
  let html = '<div class="card border-0 bg-light">';
  html += '<div class="card-body p-3">';
  html += '<h6 class="card-title text-primary mb-3"><i class="fas fa-chart-pie"></i> Assignment Summary</h6>';
  
  Object.keys(roles).forEach(r => {
    const v = roles[r];
    const total = v.primary + v.backup + v.temporary;
    html += `<div class="mb-2">
      <div class="d-flex justify-content-between align-items-center">
        <strong class="text-dark">${r}</strong>
        <span class="badge badge-primary">${total} Assigned</span>
      </div>
      <div class="small text-muted">
        <span class='badge badge-success mr-1'>Primary: ${v.primary}</span>
        <span class='badge badge-warning mr-1'>Backup: ${v.backup}</span>
        <span class='badge badge-info'>Temporary: ${v.temporary}</span>
      </div>
    </div>`;
  });
  
  html += '</div></div>';
  $('#areaAssignmentSummary').html(html);
}

function filterAssignments() { loadAreaAssignments(); }
function filterAssignments() { loadAreaAssignments(); }

function loadAvailableEmployeesForAssignment() {
  const areaId = $('#assignment_area_id').val();
  const role = $('#assignment_role_filter').val();
  
  // Don't load if area is not selected
  if (!areaId) {
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Area First --</option>');
    return;
  }
  
  $.getJSON(`<?= base_url('service/area-management/getAvailableEmployees') ?>/${areaId}/${role || ''}`, function(resp){
    if (!resp.success) return;
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Employee --</option>');
    resp.data.forEach(e => select.append(`<option value='${e.id}'>${e.staff_code} - ${e.staff_name} (${e.staff_role || e.role || 'N/A'})</option>`));
  });
}

function removeAssignment(id) {
  if (!confirm('Remove this assignment?\n\nThis will unassign the employee from this area.\nThis action cannot be undone.')) return;
  
  console.log('🗑️ Attempting to delete assignment ID:', id);
  
  // Immediately disable the delete button to prevent duplicate clicks
  $(`button[onclick*="removeAssignment(${id})"]`).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
  
  $.ajax({
    url: `<?= base_url('service/area-management/deleteAssignment') ?>/${id}`,
    type: 'DELETE',
    success: function(resp){
      console.log('🗑️ Delete assignment response:', resp);
      if (resp.success) {
        // Immediately remove the row from table with fade animation
        $(`#assignment-row-${id}`).fadeOut(300, function() {
          $(this).remove();
          console.log('✅ Assignment row removed from table');
          
          // Check if table is now empty
          const remainingRows = $('#areaAssignmentsTable tbody tr').length;
          if (remainingRows === 0) {
            $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-users-slash"></i><br>No assignments found</div>');
          }
        });
        
        notify('Assignment removed successfully','success');
        
        // Update related data
        buildRoleCoverageMatrix();
        
        // Update areas table to reflect new assignment count
        if (areasTable) {
          areasTable.ajax.reload(null, false);
        }
        
        // Update assignment summary
        setTimeout(function() {
          const areaId = $('#assignAreaSelect').val();
          if (areaId) {
            $.getJSON(`<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}?_=${Date.now()}`, function(resp){
              if (resp.success) {
                updateAreaAssignmentSummary(resp.data || []);
              }
            });
          }
        }, 500);
        
      } else {
        console.error('❌ Delete failed:', resp.message);
        
        // Special handling for "Assignment not found" - probably already deleted
        if (resp.message.includes('not found') || resp.message.includes('Assignment not found')) {
          console.log('📄 Assignment already deleted, removing from table anyway...');
          
          // Remove from table since it's already gone from database
          $(`#assignment-row-${id}`).fadeOut(300, function() {
            $(this).remove();
            console.log('✅ Stale assignment row removed from table');
            
            // Check if table is now empty
            const remainingRows = $('#areaAssignmentsTable tbody tr').length;
            if (remainingRows === 0) {
              $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-users-slash"></i><br>No assignments found</div>');
            }
          });
          
          notify('Assignment was already removed', 'info');
          
          // Update related data
          buildRoleCoverageMatrix();
          if (areasTable) {
            areasTable.ajax.reload(null, false);
          }
          
        } else {
          // Re-enable button on actual error
          $(`button[onclick*="removeAssignment(${id})"]`).prop('disabled', false).html('<i class="fas fa-trash"></i>');
          notify(resp.message,'error');
        }
      }
    },
    error: function(xhr, status, error) {
      console.error('❌ AJAX Error deleting assignment:', xhr.responseText);
      // Re-enable button on network error
      $(`button[onclick*="removeAssignment(${id})"]`).prop('disabled', false).html('<i class="fas fa-trash"></i>');
      notify('Network error: ' + error, 'error');
    }
  });
}


// Immediate removal from table without full reload
function removeAssignmentFromTable(assignmentId) {
  console.log('🗑️ Removing assignment ID from table:', assignmentId);
  
  // Find and remove the row with the specific assignment ID
  $(`#areaAssignmentsTable tr`).each(function() {
    const $row = $(this);
    const $removeBtn = $row.find(`button[onclick*="removeAssignment(${assignmentId})"]`);
    if ($removeBtn.length > 0) {
      console.log('✅ Found assignment row, removing with fade effect...');
      $row.fadeOut(300, function() {
        $row.remove();
        
        // Check if table is now empty
        const remainingRows = $('#areaAssignmentsTable tbody tr').length;
        if (remainingRows === 0) {
          $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-users-slash"></i><br>No assignments found</div>');
        }
      });
      return false; // Break the loop
    }
  });
}

/* ===================== EDIT PLACEHOLDERS ===================== */
function editArea(id) {
  $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${id}`, function(resp){
    if(!resp.success) return notify(resp.message,'error');
    const a = resp.data.area;
    $('#edit_area_id').val(a.id);
    $('#edit_area_code').val(a.area_code);
    $('#edit_area_name').val(a.area_name);
    $('#edit_area_description').val(a.description || '');
    $('#editAreaModal').modal('show');
  });
}
function editEmployee(id) {
  $.getJSON(`<?= base_url('service/area-management/showEmployee') ?>/${id}`, function(resp){
    if(!resp.success) return notify(resp.message,'error');
    const e = resp.data.employee;
    $('#edit_staff_id').val(e.id);
    $('#edit_staff_code').val(e.staff_code);
    $('#edit_staff_name').val(e.staff_name);
    $('#edit_staff_role').val(e.role);
    $('#edit_staff_departemen_id').val(e.departemen_id || '');
    $('#edit_staff_phone').val(e.phone || '');
    $('#edit_staff_email').val(e.email || '');
    $('#edit_staff_address').val(e.address || '');
    $('#edit_staff_description').val(e.description || '');
    $('#editEmployeeModal').modal('show');
  });
}
function editAssignment(id) {
  $.getJSON(`<?= base_url('service/area-management/showAssignment') ?>/${id}`, function(resp){
    if(!resp.success) {
      console.log('Edit assignment error:', resp);
      return notify(resp.message,'error');
    }
    const a = resp.data;
    $('#edit_assignment_id').val(a.id);
    $('#edit_assignment_area').val(`${a.area_code} - ${a.area_name}`);
    $('#edit_assignment_staff').val(a.staff_name);
    $('#edit_assignment_role').val(a.staff_role || a.role);
    $('#edit_assignment_type').val(a.assignment_type);
    $('#edit_assignment_start').val(a.start_date ? a.start_date.substring(0,10) : '');
    $('#edit_assignment_end').val(a.end_date ? a.end_date.substring(0,10) : '');
    $('#edit_assignment_active').val(a.is_active);
    $('#edit_assignment_notes').val(a.notes || '');
    $('#editAssignmentModal').modal('show');
  }).fail(function(xhr, status, error) {
    console.log('AJAX Error for assignment:', xhr.responseText);
    notify('Network error: ' + error, 'error');
  });
}

// Submit handlers edit forms
$('#editAreaForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_area_id').val();
  const formData = $(this).serialize();
  
  $.post(`<?= base_url('service/area-management/updateArea') ?>/${id}`, formData, function(resp){
    console.log('📝 Edit area response:', resp); // Debug response
    if(resp.success){
      notify('Area updated','success');
      $('#editAreaModal').modal('hide');
      
      console.log('🔄 Manual refresh after area edit...');
      // Use manual refresh only - no auto page reload
      refreshAreas();
      
    } else {
      console.log('❌ Update area error:', resp);
      if (resp.errors) {
        let errorMsg = 'Validation errors:\n';
        Object.keys(resp.errors).forEach(key => {
          errorMsg += `- ${resp.errors[key]}\n`;
        });
        notify(errorMsg, 'error');
      } else {
        notify(resp.message || 'Failed to update area','error');
      }
    }
  }, 'json').fail(function(xhr, status, error) {
    console.log('AJAX Error:', xhr.responseText);
    notify('Network error: ' + error, 'error');
  });
});

$('#editEmployeeForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_staff_id').val();
  const formData = $(this).serialize();
  
  $.post(`<?= base_url('service/area-management/updateEmployee') ?>/${id}`, formData, function(resp){
    console.log('📝 Edit employee response:', resp); // Debug response
    if(resp.success){
      notify('Employee updated','success');
      $('#editEmployeeModal').modal('hide');
      
      console.log('🔄 Manual refresh after employee edit...');
      // Use manual refresh only - no auto page reload
      refreshEmployees();
      
    } else {
      console.log('❌ Update employee error:', resp);
      if (resp.errors) {
        let errorMsg = 'Validation errors:\n';
        Object.keys(resp.errors).forEach(key => {
          errorMsg += `- ${resp.errors[key]}\n`;
        });
        notify(errorMsg, 'error');
      } else {
        notify(resp.message || 'Failed to update employee','error');
      }
    }
  }, 'json').fail(function(xhr, status, error) {
    console.log('AJAX Error:', xhr.responseText);
    notify('Network error: ' + error, 'error');
  });
});

$('#editAssignmentForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_assignment_id').val();
  $.post(`<?= base_url('service/area-management/updateAssignment') ?>/${id}`, $(this).serialize(), function(resp){
    if(resp.success){
      notify('Assignment updated','success');
      $('#editAssignmentModal').modal('hide');
      loadAreaAssignments();
      buildRoleCoverageMatrix();
      
      console.log('🔄 Manual refresh after assignment update...');
      // Use manual refresh only - no auto page reload
      forceRefreshAssignments();
      
    } else {
      notify(resp.message || 'Failed to update assignment','error');
    }
  }, 'json');
});

/* ===================== EMPLOYEE DETAILS ===================== */
let currentEmployeeId = null;
let currentEmployeeData = null;

function viewEmployeeDetail(employeeId) {
  currentEmployeeId = employeeId;
  
  // Show modal first
  $('#employeeDetailModal').modal('show');
  
  // Show loading
  $('#detail_staff_code, #detail_staff_name, #detail_departemen, #detail_phone, #detail_email, #detail_address, #detail_hire_date').text('Loading...');
  $('#detail_staff_role').html('<span class="text-muted">Loading...</span>');
  $('#detail_assignments').html('<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading assignments...</div>');
  
  // Load employee data
  $.ajax({
    url: `<?= base_url('service/area-management/getEmployeeDetail') ?>/${employeeId}`,
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      console.log('Employee detail response:', response);
      if (response.success && response.data) {
        const emp = response.data;
        $('#detail_staff_code').text(emp.staff_code || '-');
        $('#detail_staff_name').text(emp.staff_name || '-');
        $('#detail_staff_role').html(emp.staff_role ? `<span class="badge badge-pill badge-${roleBadgeColor(emp.staff_role)}">${emp.staff_role}</span>` : '<span class="text-muted">-</span>');
        $('#detail_departemen').text(emp.departemen || '-');
        $('#detail_phone').text(emp.phone || '-');
        $('#detail_email').text(emp.email || '-');
        $('#detail_address').text(emp.address || '-');
        $('#detail_hire_date').text(emp.hire_date ? new Date(emp.hire_date).toLocaleDateString('en-GB') : '-');
        
        // Load assignments
        if (emp.assignments && emp.assignments.length > 0) {
          let assignmentsHtml = '<div class="table-responsive"><table class="table table-sm table-bordered"><thead class="thead-light"><tr><th>Area</th><th>Type</th><th>Status</th></tr></thead><tbody>';
          emp.assignments.forEach(assign => {
            assignmentsHtml += `<tr>
              <td><strong>${assign.area_name || '-'}</strong></td>
              <td><span class="badge badge-${assign.assignment_type === 'PRIMARY' ? 'primary' : 'secondary'}">${assign.assignment_type || '-'}</span></td>
              <td><span class="badge badge-${assign.is_active ? 'success' : 'danger'}">${assign.is_active ? 'Active' : 'Inactive'}</span></td>
            </tr>`;
          });
          assignmentsHtml += '</tbody></table></div>';
          $('#detail_assignments').html(assignmentsHtml);
        } else {
          $('#detail_assignments').html('<div class="alert alert-info">No area assignments found</div>');
        }
      } else {
        notify(response.message || 'Failed to load employee details', 'error');
        $('#employeeDetailModal').modal('hide');
      }
    },
    error: function(xhr, status, error) {
      console.error('Error loading employee details:', error);
      console.error('Response:', xhr.responseText);
      notify('Error loading employee details', 'error');
      $('#employeeDetailModal').modal('hide');
    }
  });
}

/* ===================== AREA DETAILS ===================== */
let currentAreaId = null;
let currentAreaData = null;

function viewAreaDetail(areaCode, areaData = null) {
  // If we have area data from the table, use it directly
  if (areaData) {
    currentAreaId = areaData.id || areaData.area_id;
    currentAreaData = areaData;
    
    // Populate modal with available data
    $('#area_detail_code').text(areaData.area_code || '-');
    $('#area_detail_name').text(areaData.area_name || '-');
    $('#area_detail_description').text(areaData.description || '-');
    $('#area_detail_customers').text(areaData.customers_count || 0);
    $('#area_detail_employees').text(areaData.employees_count || 0);
    $('#area_detail_created').text(areaData.created_at ? new Date(areaData.created_at).toLocaleDateString('en-GB') : '-');
    
    // Load assignments for this area
    loadAreaDetailAssignments(currentAreaId);
  } else {
    // Fallback - load from server
    notify(`Loading area details for ${areaCode}...`, 'info');
    // You can implement server-side loading here if needed
  }
  
  $('#areaDetailModal').modal('show');
}

function loadAreaDetailAssignments(areaId) {
  $('#area_detail_assignments').html('<div class="text-muted">Loading assignments...</div>');
  
  $.get(`<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}`, function(response) {
    if (response.success && response.data && response.data.length > 0) {
      let assignmentsHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Employee</th><th>Role</th><th>Type</th><th>Status</th></tr></thead><tbody>';
      response.data.forEach(assign => {
        assignmentsHtml += `<tr>
          <td>${assign.staff_name}</td>
          <td><span class="badge badge-pill badge-${roleBadgeColor(assign.staff_role)}">${assign.staff_role}</span></td>
          <td><span class="badge badge-${assign.assignment_type === 'PRIMARY' ? 'primary' : 'secondary'}">${assign.assignment_type}</span></td>
          <td><span class="badge badge-${assign.is_active ? 'success' : 'danger'}">${assign.is_active ? 'Active' : 'Inactive'}</span></td>
        </tr>`;
      });
      assignmentsHtml += '</tbody></table></div>';
      $('#area_detail_assignments').html(assignmentsHtml);
    } else {
      $('#area_detail_assignments').html('<div class="text-muted">No employee assignments found</div>');
    }
  }, 'json').fail(function() {
    $('#area_detail_assignments').html('<div class="text-danger">Error loading assignments</div>');
  });
}

/* ===================== MODAL ACTION FUNCTIONS ===================== */
// Area Detail Modal Actions
function editAreaFromDetail() {
  if (!currentAreaId || !currentAreaData) {
    notify('Area data not available', 'error');
    return;
  }
  
  // Close detail modal
  $('#areaDetailModal').modal('hide');
  
  // Populate edit form
  $('#edit_area_id').val(currentAreaId);
  $('#edit_area_code').val(currentAreaData.area_code || '');
  $('#edit_area_name').val(currentAreaData.area_name || '');
  $('#edit_area_description').val(currentAreaData.description || '');
  
  // Show edit modal
  $('#editAreaModal').modal('show');
}

function deleteAreaFromDetail() {
  if (!currentAreaId) {
    notify('Area ID not available', 'error');
    return;
  }
  
  if (!confirm(`Are you sure you want to delete area "${currentAreaData?.area_name || 'this area'}"?\n\nThis action cannot be undone.`)) {
    return;
  }
  
  // Close detail modal
  $('#areaDetailModal').modal('hide');
  
  // Call delete function
  deleteArea(currentAreaId);
}

// Employee Detail Modal Actions
function editEmployeeFromDetail() {
  if (!currentEmployeeId) {
    notify('Employee ID not available', 'error');
    return;
  }
  
  // Close detail modal
  $('#employeeDetailModal').modal('hide');
  
  // Load employee data and show edit modal
  $.getJSON(`<?= base_url('service/area-management/getEmployeeDetail') ?>/${currentEmployeeId}`, function(resp) {
    if (resp.success && resp.data) {
      const emp = resp.data;
      $('#edit_staff_id').val(emp.id);
      $('#edit_staff_code').val(emp.staff_code || '');
      $('#edit_staff_name').val(emp.staff_name || '');
      $('#edit_staff_role').val(emp.staff_role || '');
      $('#edit_staff_departemen_id').val(emp.departemen_id || '');
      $('#edit_staff_phone').val(emp.phone || '');
      $('#edit_staff_email').val(emp.email || '');
      $('#edit_staff_address').val(emp.address || '');
      $('#edit_staff_description').val(emp.description || '');
      
      // Show edit modal
      $('#editEmployeeModal').modal('show');
    } else {
      notify('Failed to load employee data', 'error');
    }
  }).fail(function() {
    notify('Error loading employee data', 'error');
  });
}

function deleteEmployeeFromDetail() {
  if (!currentEmployeeId) {
    notify('Employee ID not available', 'error');
    return;
  }
  
  if (!confirm(`Are you sure you want to delete this employee?\n\nThis will deactivate the employee instead of permanently deleting.`)) {
    return;
  }
  
  // Close detail modal
  $('#employeeDetailModal').modal('hide');
  
  // Call delete function
  deleteEmployee(currentEmployeeId);
}

/* ===================== REFRESHERS ===================== */
function refreshAreaTable(){ areasTable.ajax.reload(); buildRoleCoverageMatrix(); }
function refreshEmployeeTable(){ employeesTable.ajax.reload(); }

/* ===================== UTILITIES ===================== */
// Smart tab refresh - only refresh current active tab data
function refreshCurrentTab() {
  const activeTab = window.currentActiveTab || 'areasTab';
  console.log('🔄 Refreshing active tab:', activeTab);
  
  switch(activeTab) {
    case 'areasTab':
      if (areasTable) {
        console.log('📊 Reloading areas table...');
        // Force cache busting by adding timestamp
        const originalUrl = areasTable.ajax.url();
        const bustCache = originalUrl.includes('?') ? '&_=' : '?_=';
        const cacheBustUrl = originalUrl + bustCache + Date.now();
        
        console.log('🔄 Cache bust URL for areas:', cacheBustUrl);
        
        areasTable.ajax.url(cacheBustUrl).load(function(json) {
          console.log('✅ Areas table reload completed with cache busting:', json);
          // Reset URL back to original
          areasTable.ajax.url(originalUrl);
          buildRoleCoverageMatrix();
        }, false);
      } else {
        console.error('❌ areasTable is not initialized');
      }
      break;
      
    case 'employeesTab':
      if (employeesTable) {
        console.log('📊 Reloading employees table...');
        // Also add cache busting for employees
        const originalUrl = employeesTable.ajax.url();
        const bustCache = originalUrl.includes('?') ? '&_=' : '?_=';
        const cacheBustUrl = originalUrl + bustCache + Date.now();
        
        console.log('🔄 Cache bust URL for employees:', cacheBustUrl);
        
        employeesTable.ajax.url(cacheBustUrl).load(function(json) {
          console.log('✅ Employees table reload completed with cache busting:', json);
          // Reset URL back to original
          employeesTable.ajax.url(originalUrl);
        }, false);
      } else {
        console.error('❌ employeesTable is not initialized');
      }
      break;
      
    case 'assignmentsTab':
      // Refresh assignments if area is selected
      const selectedArea = $('#assignAreaSelect').val();
      if (selectedArea) {
        console.log('📊 Reloading assignments for area:', selectedArea);
        loadAreaAssignments();
      }
      break;
      
    case 'analyticsTab':
      // Refresh charts and matrix
      console.log('📊 Reloading analytics...');
      buildRoleCoverageMatrix();
      if (typeof initializeCharts === 'function') {
        initializeCharts();
      }
      break;
  }
}

// Alternative refresh method - page reload with tab preservation
function refreshWithPageReload() {
  const activeTab = window.currentActiveTab || 'areasTab';
  console.log('🔄 Using page reload method, preserving tab:', activeTab);
  
  // Save current tab to localStorage
  localStorage.setItem('area_management_active_tab', activeTab);
  
  // Reload page
  window.location.reload();
}

// Restore tab on page load
function restoreActiveTab() {
  const savedTab = localStorage.getItem('area_management_active_tab');
  if (savedTab && savedTab !== 'areasTab') {
    setTimeout(function() {
      console.log('🔄 Restoring saved tab:', savedTab);
      $(`a[href="#${savedTab}"]`).tab('show');
      window.currentActiveTab = savedTab;
      // Clear saved tab
      localStorage.removeItem('area_management_active_tab');
    }, 500);
  }
}

// Cross-tab refresh - refresh other tabs that might be affected
function refreshRelatedTabs(excludeCurrentTab = false) {
  const activeTab = window.currentActiveTab || 'areasTab';
  console.log('Refreshing related tabs, current active:', activeTab);
  
  // Always refresh the current active tab first
  if (!excludeCurrentTab) {
    refreshCurrentTab();
  }
  
  // Also refresh areas table if we're not already on it (for cross-tab consistency)
  if (activeTab !== 'areasTab' && areasTable) {
    console.log('Also refreshing areas table for cross-tab consistency...');
    areasTable.ajax.reload(null, false);
  }
  
  // Always refresh matrix for consistency
  buildRoleCoverageMatrix();
}

// Refresh utilities
function refreshAllTables() {
  if (areasTable) {
    const originalUrl = areasTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    areasTable.ajax.url(cacheBustUrl).load(function(json) {
      areasTable.ajax.url(originalUrl);
    }, false);
  }
  if (employeesTable) {
    const originalUrl = employeesTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    employeesTable.ajax.url(cacheBustUrl).load(function(json) {
      employeesTable.ajax.url(originalUrl);
    }, false);
  }
  loadAreaAssignments(); // For assignments table
}

function refreshAreasTable() {
  if (areasTable) {
    const originalUrl = areasTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    areasTable.ajax.url(cacheBustUrl).load(function(json) {
      areasTable.ajax.url(originalUrl);
      buildRoleCoverageMatrix();
    }, false);
  }
}

function refreshEmployeesTable() {
  if (employeesTable) {
    const originalUrl = employeesTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    employeesTable.ajax.url(cacheBustUrl).load(function(json) {
      employeesTable.ajax.url(originalUrl);
    }, false);
  }
}

function refreshAssignmentsTable() {
  loadAreaAssignments();
}

// Unified notifier (same as SPK service for consistency)
function notify(msg, type='success'){
	if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
	if (typeof showNotification==='function') return showNotification(msg, type);
	// Use SweetAlert2 for consistent notification across all pages
	if (typeof Swal !== 'undefined') {
		const iconMap = { 'success': 'success', 'error': 'error', 'info': 'info', 'warning': 'warning' };
		Swal.fire({
			icon: iconMap[type] || 'info',
			text: msg,
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 4000,
			timerProgressBar: true
		});
	} else {
		alert(msg);
	}
}


// Refresh method - destroy and recreate everything
function refreshAreas() {
  console.log('🔄 Refreshing areas...');
  if (areasTable) {
    // Show loading indicator
    $('#areasTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Refreshing...</td></tr>');
    
    // Destroy table completely
    areasTable.destroy();
    
    // Restore original table HTML structure
    $('#areasTable').html(`
      <thead>
        <tr>
          <th>Area Code</th>
          <th>Area Name</th>
          <th>Description</th>
          <th>Customers</th>
          <th>Employees</th>
          <th>Assignments</th>
        </tr>
      </thead>
      <tbody></tbody>
    `);
    
    // Clear any cached data
    if (typeof areasTable !== 'undefined') {
      areasTable = null;
    }
    
    // Reinitialize table from scratch
    setTimeout(function() {
      initializeAreaTable();
      buildRoleCoverageMatrix();
    }, 200);
  }
}

function refreshEmployees() {
  console.log('🔄 Refreshing employees...');
  if (employeesTable) {
    // Show loading indicator
    $('#employeesTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Refreshing...</td></tr>');
    
    // Destroy table completely
    employeesTable.destroy();
    
    // Restore original table HTML structure
    $('#employeesTable').html(`
      <thead>
        <tr>
          <th>Staff Code</th>
          <th>Name</th>
          <th>Role</th>
          <th>Department</th>
          <th>Phone</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody></tbody>
    `);
    
    // Clear any cached data
    if (typeof employeesTable !== 'undefined') {
      employeesTable = null;
    }
    
    // Reinitialize table from scratch
    setTimeout(function() {
      initializeEmployeeTable();
    }, 200);
  }
}

// Ultimate refresh method with multiple fallbacks
function ultimateRefreshAreas() {
  console.log('🔄 Ultimate refresh for areas...');
  if (areasTable) {
    // Show loading indicator
    $('#areasTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Refreshing...</td></tr>');
    
    // Method 1: Force reload with cache busting
    const originalUrl = areasTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    
    console.log('🔄 Original URL:', originalUrl);
    console.log('🔄 Cache bust URL:', cacheBustUrl);
    
    // Temporarily change URL for cache busting
    areasTable.ajax.url(cacheBustUrl);
    
    // Reload with cache busting
    areasTable.ajax.reload(function(json) {
      console.log('✅ Areas table force refreshed with cache busting:', json);
      
      // Restore original URL
      areasTable.ajax.url(originalUrl);
      
      // Force redraw the table
      areasTable.draw(false);
      
      // Update related components
      buildRoleCoverageMatrix();
      notify('Areas refreshed successfully', 'success');
    }, false); // false = keep paging position
    
    // Method 2: Also try direct AJAX call as backup
    setTimeout(function() {
      console.log('🔄 Backup refresh method for areas...');
      $.ajax({
        url: originalUrl,
        type: 'POST',
        data: { draw: 1, start: 0, length: 25, _t: Date.now() },
        success: function(response) {
          console.log('✅ Backup areas refresh successful:', response);
          if (response && response.data) {
            // Force table redraw with new data
            areasTable.clear().rows.add(response.data).draw();
            buildRoleCoverageMatrix();
          }
        },
        error: function(xhr, status, error) {
          console.error('❌ Backup areas refresh failed:', error);
        }
      });
    }, 1000);
    
  } else {
    console.error('❌ Areas table not initialized');
    notify('Areas table not initialized', 'error');
  }
}

function ultimateRefreshEmployees() {
  console.log('🔄 Ultimate refresh for employees...');
  if (employeesTable) {
    // Show loading indicator
    $('#employeesTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Refreshing...</td></tr>');
    
    // Method 1: Force reload with cache busting
    const originalUrl = employeesTable.ajax.url();
    const cacheBustUrl = originalUrl + (originalUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
    
    console.log('🔄 Original URL:', originalUrl);
    console.log('🔄 Cache bust URL:', cacheBustUrl);
    
    // Temporarily change URL for cache busting
    employeesTable.ajax.url(cacheBustUrl);
    
    // Reload with cache busting
    employeesTable.ajax.reload(function(json) {
      console.log('✅ Employees table force refreshed with cache busting:', json);
      
      // Restore original URL
      employeesTable.ajax.url(originalUrl);
      
      // Force redraw the table
      employeesTable.draw(false);
      
      notify('Employees refreshed successfully', 'success');
    }, false); // false = keep paging position
    
    // Method 2: Also try direct AJAX call as backup
    setTimeout(function() {
      console.log('🔄 Backup refresh method for employees...');
      $.ajax({
        url: originalUrl,
        type: 'POST',
        data: { draw: 1, start: 0, length: 25, _t: Date.now() },
        success: function(response) {
          console.log('✅ Backup employees refresh successful:', response);
          if (response && response.data) {
            // Force table redraw with new data
            employeesTable.clear().rows.add(response.data).draw();
          }
        },
        error: function(xhr, status, error) {
          console.error('❌ Backup employees refresh failed:', error);
        }
      });
    }, 1000);
    
  } else {
    console.error('❌ Employees table not initialized');
    notify('Employees table not initialized', 'error');
  }
}

// Ultimate refresh method - page reload with tab preservation
function ultimateRefresh() {
  const activeTab = window.currentActiveTab || 'areasTab';
  console.log('🔄 Ultimate refresh - page reload preserving tab:', activeTab);
  
  // Save current tab to localStorage
  localStorage.setItem('area_management_active_tab', activeTab);
  
  // Reload page
  window.location.reload();
}

// Debug function to check table status
function debugTableStatus() {
  console.log('🔍 Debug Table Status:');
  console.log('Areas Table:', areasTable ? 'Initialized' : 'Not Initialized');
  console.log('Employees Table:', employeesTable ? 'Initialized' : 'Not Initialized');
  
  if (areasTable) {
    console.log('Areas Table URL:', areasTable.ajax.url());
    console.log('Areas Table Data:', areasTable.data());
  }
  
  if (employeesTable) {
    console.log('Employees Table URL:', employeesTable.ajax.url());
    console.log('Employees Table Data:', employeesTable.data());
  }
}

// Test refresh function with detailed logging
function testRefreshAreas() {
  console.log('🧪 Testing Areas Refresh...');
  console.log('Current Areas Table Status:', areasTable ? 'OK' : 'NULL');
  
  if (areasTable) {
    console.log('Areas Table URL:', areasTable.ajax.url());
    console.log('Areas Table Settings:', areasTable.settings());
    
    // Try to get current data
    const currentData = areasTable.data();
    console.log('Current Areas Data Count:', currentData.length);
    
    // Try refresh
    areasTable.ajax.reload(function(json) {
      console.log('✅ Test refresh successful:', json);
      console.log('New data count:', json.data ? json.data.length : 'No data');
    }, false);
  } else {
    console.error('❌ Areas table not available for testing');
  }
}

function testRefreshEmployees() {
  console.log('🧪 Testing Employees Refresh...');
  console.log('Current Employees Table Status:', employeesTable ? 'OK' : 'NULL');
  
  if (employeesTable) {
    console.log('Employees Table URL:', employeesTable.ajax.url());
    console.log('Employees Table Settings:', employeesTable.settings());
    
    // Try to get current data
    const currentData = employeesTable.data();
    console.log('Current Employees Data Count:', currentData.length);
    
    // Try refresh
    employeesTable.ajax.reload(function(json) {
      console.log('✅ Test refresh successful:', json);
      console.log('New data count:', json.data ? json.data.length : 'No data');
    }, false);
  } else {
    console.error('❌ Employees table not available for testing');
  }
}

</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<style>
.mini-stats-wid { border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.mini-stats-wid .avatar-sm { height: 3rem; width: 3rem; }
.mini-stats-wid .mini-stat-icon { line-height: 3rem; text-align:center; }
.badge { font-size: 0.70rem; }
.table-sm td, .table-sm th { padding: .4rem; }
.nav-tabs .nav-link { padding: .5rem 1rem; }
.toast { opacity: 0.95; }
</style>
<?= $this->endSection() ?>