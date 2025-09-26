<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #6f7fff 0%, #4e73df 100%); color: white; border-radius: 15px 15px 0 0; }
    .mini-stats-wid { border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px; }
    .mini-stats-wid .avatar-sm { height: 3rem; width: 3rem; }
    .mini-stats-wid .mini-stat-icon { line-height: 3rem; text-align:center; }
    .badge { font-size: 0.70rem; }
    .table-sm td, .table-sm th { padding: .4rem; }
    .nav-tabs .nav-link { padding: .5rem 1rem; border-radius: 10px 10px 0 0; }
    .nav-tabs .nav-link.active { background-color: #4e73df; color: white; border-color: #4e73df; }
    .toast { opacity: 0.95; }
    .form-errors { background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px; }
    .btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
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
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Service Area & Employee Management</h1>
                    <nav aria-label="breadcrumb" class="d-none d-sm-inline-block">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item">Service</li>
                            <li class="breadcrumb-item active" aria-current="page">Area Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary mr-2" onclick="showAddAreaModal()">
                        <i class="fas fa-plus"></i> Add New Area
                    </button>
                    <button type="button" class="btn btn-success" onclick="showAddEmployeeModal()">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card table-card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs" role="tablist">
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">Area List</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchArea" placeholder="Search areas...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="refreshAreaTable()">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </div>
                                    </div>
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
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Employees Tab -->
                        <div class="tab-pane fade" id="employeesTab" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">Employee List</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchEmployee" placeholder="Search employees...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="refreshEmployeeTable()">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="employeesTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Staff Code</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Areas</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Assignments Tab -->
                        <div class="tab-pane fade" id="assignmentsTab" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">Area Assignments</h5>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddAssignmentModal()">
                                        <i class="fas fa-link"></i> New Assignment
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Select Area</h6>
                                        </div>
                                        <div class="card-body">
                                            <select id="assignAreaSelect" class="form-control mb-3" onchange="loadAreaAssignments()">
                                                <option value="">-- Select Area --</option>
                                                <?php foreach ($areas as $area): ?>
                                                    <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div id="areaAssignmentSummary" class="mb-3"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Assignments</h6>
                                            <div>
                                                <select id="filterRoleAssignments" class="form-control form-control-sm" onchange="filterAssignments()" style="display:inline-block; width:auto;">
                                                    <option value="">All Roles</option>
                                                    <option value="SUPERVISOR">Supervisor</option>
                                                    <option value="FOREMAN">Foreman</option>
                                                    <option value="ADMIN">Admin</option>
                                                    <option value="MECHANIC">Mechanic</option>
                                                    <option value="HELPER">Helper</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="areaAssignmentsTable">
                                                <div class="text-center text-muted">Select an area to view assignments</div>
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
</div>

<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Area</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="addAreaForm">
        <div class="modal-body">
          <div class="form-errors text-danger small mb-2"></div>
          <div class="form-group">
            <label>Area Code <span class="text-danger">*</span></label>
            <input type="text" name="area_code" class="form-control" required maxlength="10">
          </div>
          <div class="form-errors text-danger small mb-2"></div>
          <div class="form-group">
            <label>Area Name <span class="text-danger">*</span></label>
            <input type="text" name="area_name" class="form-control" required maxlength="255">
          </div>
          <div class="form-errors text-danger small mb-2"></div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="addEmployeeForm">
        <div class="modal-body">
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
            <select name="role" class="form-control" required>
              <option value="">-- Select Role --</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="ADMIN">Admin</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="HELPER">Helper</option>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="addAssignmentForm">
        <div class="modal-body">
          <div class="form-group">
            <label>Area <span class="text-danger">*</span></label>
            <select name="area_id" id="assignment_area_id" class="form-control" required>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
            <textarea name="description" id="edit_area_description" class="form-control" rows="2"></textarea>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
            <select name="role" id="edit_staff_role" class="form-control" required>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="ADMIN">Admin</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="HELPER">Helper</option>
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
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
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
});

/* ===================== TABLE INITIALIZATIONS ===================== */
function initializeAreaTable() {
  areasTable = $('#areasTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '<?= base_url('service/area-management/getAreas') ?>', type: 'POST' },
    columns: [
      { data: 'area_code' },
      { data: 'area_name' },
      { data: 'description', render: d => d ? (d.length > 40 ? d.substring(0,40)+'…' : d) : '' },
      { data: 'customers_count', render: d => `<span class="badge badge-secondary">${d}</span>` },
      { data: 'employees_count', render: d => `<span class="badge badge-info">${d}</span>` },
      { data: 'assignment_summary', orderable:false, render: renderAssignmentSummary },
      { data: 'created_at', render: d => d ? moment(d).format('DD/MM/YYYY') : '' },
      { data: 'actions', orderable:false, searchable:false }
    ],
    order: [[1,'asc']],
    pageLength: 25
  });
}

function initializeEmployeeTable() {
  employeesTable = $('#employeesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '<?= base_url('service/area-management/getEmployees') ?>', type: 'POST' },
    columns: [
      { data: 'staff_code' },
      { data: 'staff_name' },
      { data: 'role', render: r => `<span class="badge badge-pill badge-${roleBadgeColor(r)}">${r}</span>` },
      { data: 'phone' },
      { data: 'email' },
      { data: 'areas_count', render: d => `<span class="badge badge-primary">${d}</span>` },
      { data: 'created_at', render: d => d ? moment(d).format('DD/MM/YYYY') : '' },
      { data: 'actions', orderable:false, searchable:false }
    ],
    order: [[1,'asc']],
    pageLength: 25
  });
}

/* ===================== CHARTS ===================== */
function initializeCharts() {
  const roleCtx = document.getElementById('employeesByRoleChart');
  const assignmentsCtx = document.getElementById('assignmentsByAreaChart');

  const employeesByRoleData = <?= json_encode($employeesByRole) ?>;
  const assignmentsByAreaData = <?= json_encode($assignmentsByArea) ?>;

  employeesByRoleChart = new Chart(roleCtx, {
    type: 'bar',
    data: {
      labels: employeesByRoleData.map(r => r.role),
      datasets: [{
        label: 'Employees',
        data: employeesByRoleData.map(r => r.employee_count),
        backgroundColor: '#4e73df'
      }]
    },
    options: { responsive:true, maintainAspectRatio:false }
  });

  assignmentsByAreaChart = new Chart(assignmentsCtx, {
    type: 'horizontalBar',
    data: {
      labels: assignmentsByAreaData.map(a => a.area_name || 'Unknown'),
      datasets: [{
        label: 'Assignments',
        data: assignmentsByAreaData.map(a => a.assignment_count),
        backgroundColor: '#1cc88a'
      }]
    },
    options: { responsive:true, maintainAspectRatio:false }
  });
}

/* ===================== ROLE COVERAGE MATRIX ===================== */
function buildRoleCoverageMatrix() {
  $.ajax({
    url: '<?= base_url('service/area-management/getAreas') ?>',
    type: 'POST',
    data: { draw:1, start:0, length:1000 },
    success: function(resp) {
      if (!resp.data) return;
      const roles = ['SUPERVISOR','FOREMAN','ADMIN','MECHANIC','HELPER'];
      let html = '<table class="table table-sm table-bordered"><thead><tr><th>Area</th>' + roles.map(r => `<th>${r}</th>`).join('') + '</tr></thead><tbody>';
      resp.data.forEach(area => {
        html += `<tr><td>${area.area_code}</td>`;
        roles.forEach(role => {
          const summary = area.assignment_summary.find(s => s.role === role);
          const primary = summary ? summary.primary_count : 0;
          const total = summary ? summary.count : 0;
          html += `<td>${primary > 0 ? '<span class=\'badge badge-success\'>P</span>' : ''} ${total > primary ? '<span class=\'badge badge-warning\'>B:'+(total-primary)+'</span>' : ''}</td>`;
        });
        html += '</tr>';
      });
      html += '</tbody></table>';
      $('#roleCoverageMatrix').html(html);
    }
  });
}

/* ===================== ASSIGNMENT RENDERER ===================== */
function renderAssignmentSummary(summary) {
  if (!summary || summary.length === 0) return '<span class="text-muted">-</span>';
  return summary.map(s => {
    const primary = s.primary_count > 0 ? `<span class=\'badge badge-success\'>P:${s.primary_count}</span>` : '';
    const backup = (s.count - s.primary_count) > 0 ? `<span class=\'badge badge-warning\'>B:${s.count - s.primary_count}</span>` : '';
    return `<div class=\'d-inline-block mr-1 mb-1\'><small>${s.role}</small><br>${primary} ${backup}</div>`;
  }).join('');
}

function roleBadgeColor(role) {
  switch(role) {
    case 'SUPERVISOR': return 'dark';
    case 'FOREMAN': return 'primary';
    case 'ADMIN': return 'info';
    case 'MECHANIC': return 'success';
    case 'HELPER': return 'warning';
    default: return 'secondary';
  }
}

/* ===================== FORMS BINDING ===================== */
function bindForms() {
  function showFormErrors($form, errors) {
    $form.find('.form-errors').html('');
    if (!errors) return;
    let html = '<ul class="mb-0">';
    for (const key in errors) {
      html += `<li><strong>${key.replace('_',' ')}:</strong> ${errors[key]}</li>`;
    }
    html += '</ul>';
    $form.find('.form-errors').html(html);
  }

  $('#addAreaForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    $form.find('.form-errors').html('');
    $.post('<?= base_url('service/area-management/storeArea') ?>', $form.serialize(), function(resp){
      if (resp.success) {
        $('#addAreaModal').modal('hide');
        showToast('Area created successfully','success');
        areasTable.ajax.reload();
        buildRoleCoverageMatrix();
        $form[0].reset();
      } else {
        showFormErrors($form, resp.errors);
        showToast(resp.message || 'Failed to create area','danger');
      }
    }, 'json');
  });

  $('#addEmployeeForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    $form.find('.form-errors').html('');
    $.post('<?= base_url('service/area-management/storeEmployee') ?>', $form.serialize(), function(resp){
      if (resp.success) {
        $('#addEmployeeModal').modal('hide');
        showToast('Employee created','success');
        employeesTable.ajax.reload();
        $form[0].reset();
      } else {
        showFormErrors($form, resp.errors);
        showToast(resp.message || 'Failed to create employee','danger');
      }
    }, 'json');
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
        showToast('Assignment created','success');
        const areaId = $('#assignAreaSelect').val();
        if (areaId) loadAreaAssignments();
        buildRoleCoverageMatrix();
        areasTable.ajax.reload(null,false);
        $form[0].reset();
      } else {
        showFormErrors($form, resp.errors);
        showToast(resp.message || 'Failed to create assignment','danger');
      }
    }, 'json');
  }
}

/* ===================== MODAL HELPERS ===================== */
function showAddAreaModal(){ $('#addAreaModal').modal('show'); }
function showAddEmployeeModal(){ $('#addEmployeeModal').modal('show'); }
function showAddAssignmentModal(){
  const selectedArea = $('#assignAreaSelect').val();
  if (selectedArea) $('#assignment_area_id').val(selectedArea);
  loadAvailableEmployeesForAssignment();
  $('#addAssignmentModal').modal('show');
}

/* ===================== AREA DETAILS & ACTIONS ===================== */
function viewArea(id) {
  $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${id}`, function(resp){
    if (!resp.success) return showToast(resp.message,'danger');
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
        showToast('Area deleted','success');
        areasTable.ajax.reload();
        buildRoleCoverageMatrix();
      } else {
        showToast(resp.message,'danger');
      }
    }
  });
}

/* ===================== EMPLOYEE DETAILS & ACTIONS ===================== */
function viewEmployee(id) {
  $.getJSON(`<?= base_url('service/area-management/showEmployee') ?>/${id}`, function(resp){
    if (!resp.success) return showToast(resp.message,'danger');
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
        showToast('Employee deactivated successfully','success');
        employeesTable.ajax.reload();
        buildRoleCoverageMatrix();
        areasTable.ajax.reload(null,false);
      } else {
        showToast(resp.message,'danger');
      }
    }
  });
}

/* ===================== ASSIGNMENTS ===================== */
function loadAreaAssignments() {
  const areaId = $('#assignAreaSelect').val();
  if (!areaId) {
    $('#areaAssignmentsTable').html('<div class="text-center text-muted">Select an area to view assignments</div>');
    $('#areaAssignmentSummary').html('');
    return;
  }
  $.getJSON(`<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}`, function(resp){
    if (!resp.success) return;
    const assignments = resp.data || [];
    renderAssignmentsTable(assignments);
    updateAreaAssignmentSummary(assignments);
  });
}

function renderAssignmentsTable(assignments) {
  const filterRole = $('#filterRoleAssignments').val();
  let filtered = assignments;
  if (filterRole) filtered = assignments.filter(a => a.role === filterRole);
  if (filtered.length === 0) {
    $('#areaAssignmentsTable').html('<div class="text-muted">No assignments found</div>');
    return;
  }
  let html = '<table class="table table-sm table-hover">';
  html += '<thead><tr><th>Staff</th><th>Role</th><th>Type</th><th>Start</th><th>End</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
  filtered.forEach(a => {
    const status = a.is_active == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
    html += `<tr>
      <td>${a.staff_name}</td>
      <td><span class='badge badge-${roleBadgeColor(a.role)}'>${a.role}</span></td>
      <td><span class='badge badge-${a.assignment_type==='PRIMARY'?'success':(a.assignment_type==='BACKUP'?'warning':'info')}'>${a.assignment_type}</span></td>
      <td>${a.start_date || ''}</td>
      <td>${a.end_date || ''}</td>
      <td>${status}</td>
      <td>
        <button class='btn btn-xs btn-warning' onclick='editAssignment(${a.id})'><i class='fas fa-edit'></i></button>
        <button class='btn btn-xs btn-danger' onclick='deleteAssignment(${a.id})'><i class='fas fa-trash'></i></button>
      </td>
    </tr>`;
  });
  html += '</tbody></table>';
  $('#areaAssignmentsTable').html(html);
}

function updateAreaAssignmentSummary(assignments) {
  if (!assignments || assignments.length === 0) {
    $('#areaAssignmentSummary').html('<div class="alert alert-info">No assignments</div>');
    return;
  }
  const roles = {};
  assignments.forEach(a => {
    if (!roles[a.role]) roles[a.role] = { primary:0, backup:0, temporary:0 };
    if (a.assignment_type === 'PRIMARY') roles[a.role].primary++;
    if (a.assignment_type === 'BACKUP') roles[a.role].backup++;
    if (a.assignment_type === 'TEMPORARY') roles[a.role].temporary++;
  });
  let html = '<div class="small">';
  Object.keys(roles).forEach(r => {
    const v = roles[r];
    html += `<div><strong>${r}</strong>: <span class='badge badge-success'>P:${v.primary}</span> <span class='badge badge-warning'>B:${v.backup}</span> <span class='badge badge-info'>T:${v.temporary}</span></div>`;
  });
  html += '</div>';
  $('#areaAssignmentSummary').html(html);
}

function filterAssignments() { loadAreaAssignments(); }

function loadAvailableEmployeesForAssignment() {
  const areaId = $('#assignment_area_id').val();
  const role = $('#assignment_role_filter').val();
  $.getJSON(`<?= base_url('service/area-management/getAvailableEmployees') ?>/${areaId}/${role}`, function(resp){
    if (!resp.success) return;
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Employee --</option>');
    resp.data.forEach(e => select.append(`<option value='${e.id}'>${e.staff_code} - ${e.staff_name} (${e.role})</option>`));
  });
}

function deleteAssignment(id) {
  if (!confirm('Deactivate this assignment? This will set the assignment as inactive instead of permanently deleting.')) return;
  $.ajax({
    url: `<?= base_url('service/area-management/deleteAssignment') ?>/${id}`,
    type: 'DELETE',
    success: function(resp){
      if (resp.success) {
        showToast('Assignment deactivated successfully','success');
        loadAreaAssignments();
        buildRoleCoverageMatrix();
        areasTable.ajax.reload(null,false);
      } else {
        showToast(resp.message,'danger');
      }
    }
  });
}

/* ===================== EDIT PLACEHOLDERS ===================== */
function editAssignment(id) { showToast('Edit assignment form coming soon','info'); }
function editArea(id) {
  $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${id}`, function(resp){
    if(!resp.success) return showToast(resp.message,'danger');
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
    if(!resp.success) return showToast(resp.message,'danger');
    const e = resp.data.employee;
    $('#edit_staff_id').val(e.id);
    $('#edit_staff_code').val(e.staff_code);
    $('#edit_staff_name').val(e.staff_name);
    $('#edit_staff_role').val(e.role);
    $('#edit_staff_phone').val(e.phone || '');
    $('#edit_staff_email').val(e.email || '');
    $('#edit_staff_address').val(e.address || '');
    $('#edit_staff_description').val(e.description || '');
    $('#editEmployeeModal').modal('show');
  });
}
function editAssignment(id) {
  $.getJSON(`<?= base_url('service/area-management/showAssignment') ?>/${id}`, function(resp){
    if(!resp.success) return showToast(resp.message,'danger');
    const a = resp.data;
    $('#edit_assignment_id').val(a.id);
    $('#edit_assignment_area').val(`${a.area_code} - ${a.area_name}`);
    $('#edit_assignment_staff').val(a.staff_name);
    $('#edit_assignment_role').val(a.role);
    $('#edit_assignment_type').val(a.assignment_type);
    $('#edit_assignment_start').val(a.start_date ? a.start_date.substring(0,10) : '');
    $('#edit_assignment_end').val(a.end_date ? a.end_date.substring(0,10) : '');
    $('#edit_assignment_active').val(a.is_active);
    $('#edit_assignment_notes').val(a.notes || '');
    $('#editAssignmentModal').modal('show');
  });
}

// Submit handlers edit forms
$('#editAreaForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_area_id').val();
  $.post(`<?= base_url('service/area-management/updateArea') ?>/${id}`, $(this).serialize(), function(resp){
    if(resp.success){
      showToast('Area updated','success');
      $('#editAreaModal').modal('hide');
      areasTable.ajax.reload(null,false);
      buildRoleCoverageMatrix();
    } else {
      showToast(resp.message || 'Failed to update area','danger');
    }
  }, 'json');
});

$('#editEmployeeForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_staff_id').val();
  $.post(`<?= base_url('service/area-management/updateEmployee') ?>/${id}`, $(this).serialize(), function(resp){
    if(resp.success){
      showToast('Employee updated','success');
      $('#editEmployeeModal').modal('hide');
      employeesTable.ajax.reload(null,false);
      areasTable.ajax.reload(null,false);
    } else {
      showToast(resp.message || 'Failed to update employee','danger');
    }
  }, 'json');
});

$('#editAssignmentForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_assignment_id').val();
  $.post(`<?= base_url('service/area-management/updateAssignment') ?>/${id}`, $(this).serialize(), function(resp){
    if(resp.success){
      showToast('Assignment updated','success');
      $('#editAssignmentModal').modal('hide');
      loadAreaAssignments();
      buildRoleCoverageMatrix();
      areasTable.ajax.reload(null,false);
    } else {
      showToast(resp.message || 'Failed to update assignment','danger');
    }
  }, 'json');
});

/* ===================== REFRESHERS ===================== */
function refreshAreaTable(){ areasTable.ajax.reload(); buildRoleCoverageMatrix(); }
function refreshEmployeeTable(){ employeesTable.ajax.reload(); }

/* ===================== UTILITIES ===================== */
function showToast(message, type='info') {
  const id = 'toast-'+Date.now();
  const colors = { success:'bg-success', danger:'bg-danger', info:'bg-info', warning:'bg-warning', dark:'bg-dark' };
  const toast = `<div id='${id}' class='toast ${colors[type]||'bg-info'} text-white' data-delay='4000' style='position: fixed; top: 1rem; right: 1rem; min-width: 250px; z-index:2000;'>
    <div class='toast-body d-flex justify-content-between align-items-center'>
      <span>${message}</span>
      <button type='button' class='ml-2 mb-1 close text-white' data-dismiss='toast'>&times;</button>
    </div>
  </div>`;
  $('body').append(toast);
  $('#'+id).toast('show');
  setTimeout(()=>$('#'+id).remove(),4500);
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