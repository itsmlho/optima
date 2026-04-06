<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

  <!-- Page Header -->
  <div class="mb-3">
      <h4 class="fw-bold mb-1">
          <i class="bi bi-people me-2 text-primary"></i>
          Service Area & Employee Management
      </h4>
      <p class="text-muted mb-0">Manage service areas, employees, and their assignments for operational coverage</p>
  </div>

  <!-- Statistics Cards -->
  <div class="row mt-3 mb-4">
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-primary-soft">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-globe stat-icon text-primary"></i>
                  </div>
                  <div>
                      <div class="stat-value"><?= $totalAreas ?></div>
                      <div class="text-muted"><?= lang('App.total_areas') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-success-soft">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-people stat-icon text-success"></i>
                  </div>
                  <div>
                      <div class="stat-value"><?= $totalEmployees ?></div>
                      <div class="text-muted"><?= lang('App.total_employees') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-warning-soft">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-link stat-icon text-warning"></i>
                  </div>
                  <div>
                      <div class="stat-value"><?= $totalAssignments ?></div>
                      <div class="text-muted"><?= lang('App.active_assignments') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
          <div class="stat-card bg-info-soft">
              <div class="d-flex align-items-center">
                  <div class="me-3">
                      <i class="bi bi-pie-chart stat-icon text-info"></i>
                  </div>
                  <div>
                      <div class="stat-value" id="roleDistribution">-</div>
                      <div class="text-muted"><?= lang('App.role_distribution') ?></div>
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
                      <i class="fas fa-map-marked-alt mr-1"></i> <?= lang('App.service_areas') ?>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="employees-tab" data-bs-toggle="tab" href="#employeesTab" role="tab" aria-controls="employeesTab" aria-selected="false">
                      <i class="fas fa-users mr-1"></i> <?= lang('App.employees') ?>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="assignments-tab" data-bs-toggle="tab" href="#assignmentsTab" role="tab" aria-controls="assignmentsTab" aria-selected="false">
                      <i class="fas fa-link mr-1"></i> <?= lang('App.assignments') ?>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#analyticsTab" role="tab" aria-controls="analyticsTab" aria-selected="false">
                      <i class="fas fa-chart-bar mr-1"></i> <?= lang('App.analytics') ?>
                  </a>
              </li>
          </ul>
      </div>
      <div class="card-body">
          <div class="tab-content" id="managementTabsContent">
                      <!-- Areas Tab -->
                      <div class="tab-pane fade show active" id="areasTab" role="tabpanel">
                          <div class="card-header bg-light d-flex justify-content-between align-items-center mb-3">
                              <h6 class="mb-0"><i class="fas fa-map-marked-alt text-primary"></i> <?= lang('App.service_areas') ?></h6>
                              <div>
                                  <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="refreshAreas()" title="<?= lang('App.refresh_areas_data') ?>">
                                      <i class="fas fa-sync-alt"></i> <?= lang('Common.refresh') ?>
                                  </button>
                                  <a href="<?= base_url('service/export_area') ?>" class="btn btn-outline-success btn-sm">
                                      <i class="fas fa-file-excel"></i> <?= lang('Common.export') ?> <?= lang('App.area') ?>
                                  </a>
                                  <button type="button" class="btn btn-primary btn-sm" onclick="showAddAreaModal()">
                                      <i class="fas fa-plus"></i> <?= lang('App.add_new_area') ?>
                                  </button>
                              </div>
                          </div>
                          <div class="table-responsive">
                              <table id="areasTable" class="table table-striped dt-responsive nowrap">
                                  <thead>
                                      <tr>
                                          <th><?= lang('App.area_code') ?></th>
                                          <th><?= lang('App.area_name') ?></th>
                                          <th><?= lang('Common.type') ?></th>
                                          <th><?= lang('Common.description') ?></th>
                                          <th><?= lang('App.customers') ?></th>
                                          <th><?= lang('App.employee_details') ?></th>
                                          <th><?= lang('Common.status') ?></th>
                                      </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>
                      </div>

                      <!-- Employees Tab -->
                      <div class="tab-pane fade" id="employeesTab" role="tabpanel">
                          <div class="card-header bg-light d-flex justify-content-between align-items-center mb-3">
                              <h6 class="mb-0"><i class="fas fa-users text-success"></i> <?= lang('App.employees') ?></h6>
                              <div>
                                  <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="refreshEmployees()" title="<?= lang('App.refresh_employees_data') ?>">
                                      <i class="fas fa-sync-alt"></i> <?= lang('Common.refresh') ?>
                                  </button>
                                  <a href="<?= base_url('service/export_employee') ?>" class="btn btn-outline-success btn-sm">
                                      <i class="fas fa-file-excel"></i> <?= lang('Common.export') ?> <?= lang('App.employee') ?>
                                  </a>
                                  <button type="button" class="btn btn-primary btn-sm" onclick="showAddEmployeeModal()">
                                      <i class="fas fa-plus"></i> <?= lang('App.add_new_employee') ?>
                                  </button>
                              </div>
                          </div>
                          
                          <div class="table-responsive">
                              <table id="employeesTable" class="table table-striped dt-responsive nowrap">
                                  <thead>
                                      <tr>
                                          <th><?= lang('App.staff_code') ?></th>
                                          <th><?= lang('Common.name') ?></th>
                                          <th><?= lang('App.role') ?></th>
                                          <th><?= lang('App.work_location') ?></th>
                                          <th><?= lang('App.department') ?></th>
                                          <th><?= lang('App.assigned_to') ?></th>
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
                                  <h5 class="card-title mb-0"><?= lang('App.area_assignments_management') ?></h5>
                                  <p class="text-muted small mb-0"><?= lang('App.manage_employee_assignments') ?></p>
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
                                          <h6 class="mb-0"><i class="fas fa-map-marker-alt text-primary"></i> <?= lang('App.select_area') ?></h6>
                                      </div>
                                      <div class="card-body">
                                          <select id="assignAreaSelect" class="form-control mb-3" onchange="loadAreaAssignments()">
                                              <option value="">-- <?= lang('App.select_area') ?> --</option>
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
                                          <h6 class="mb-0"><i class="fas fa-users text-info"></i> <?= lang('App.area_assignments') ?></h6>
                                          <div class="d-flex align-items-center gap-2">
                                              <select id="filterRoleAssignments" class="form-control form-control-sm" onchange="filterAssignments()" style="width:auto; min-width: 120px;">
                                                  <option value=""><?= lang('App.all_roles') ?></option>
                                                  <option value="SUPERVISOR"><?= lang('App.supervisor') ?></option>
                                                  <option value="FOREMAN"><?= lang('App.foreman') ?></option>
                                                  <option value="ADMIN"><?= lang('App.admin') ?></option>
                                                  <option value="MECHANIC"><?= lang('App.mechanic') ?></option>
                                                  <option value="HELPER"><?= lang('App.helper') ?></option>
                                              </select>
                                              <button type="button" class="btn btn-primary btn-sm" onclick="showAddAssignmentModal()">
                                                  <i class="fas fa-link"></i> <?= lang('App.new_assignment') ?>
                                              </button>
                                              <button type="button" class="btn btn-secondary btn-sm" onclick="forceRefreshAssignments()" title="<?= lang('App.refresh_assignments_data') ?>">
                                                  <i class="fas fa-sync-alt"></i> <?= lang('Common.refresh') ?>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="addAreaForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="form-errors text-danger small mb-3" style="display: none;"></div>
          <div class="mb-3">
            <label>Area Code <span class="text-danger">*</span></label>
            <input type="text" name="area_code" class="form-control" required maxlength="10" placeholder="Enter area code">
          </div>
          <div class="mb-3">
            <label>Area Name <span class="text-danger">*</span></label>
            <input type="text" name="area_name" class="form-control" required maxlength="255" placeholder="Enter area name">
          </div>
          <div class="mb-3">
            <label>Area Type <span class="text-danger">*</span></label>
            <select name="area_type" id="add_area_type" class="form-control" required>
              <option value="MILL">MILL - Site / Plant Location</option>
              <option value="CENTRAL">CENTRAL - Head Office / Department Specific</option>
            </select>
            <small class="form-text text-muted">
              <strong>MILL:</strong> Area lokasi site/pabrik — karyawan menangani semua departemen (department scope = ALL)<br>
              <strong>CENTRAL:</strong> Area kantor pusat/HQ — karyawan fokus pada departemen tertentu (DIESEL/ELECTRIC)
            </small>
          </div>
          <div class="mb-3" id="add_dept_group" style="display:none;">
            <label>Department <span class="text-danger">*</span></label>
            <select name="departemen_id" id="add_departemen_id" class="form-control">
              <option value="">-- Pilih Departemen --</option>
              <?php foreach ($departemen as $dept): ?>
                <?php if ($dept['id_departemen'] == 3) continue; // GASOLINE digabung ke DIESEL ?>
                <option value="<?= $dept['id_departemen'] ?>">
                  <?= $dept['id_departemen'] == 1 ? 'DIESEL & GASOLINE' : esc($dept['nama_departemen']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Pilih departemen yang ditangani area CENTRAL ini</small>
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="area_description" class="form-control" rows="3" placeholder="Enter area description (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
      <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="fas fa-user-plus text-primary"></i> Add New Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="addEmployeeForm">
        <?= csrf_field() ?>
        <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
          <div class="form-errors alert alert-danger" style="display: none;"></div>
          <div class="mb-3">
            <label>Staff Code <span class="text-danger">*</span></label>
            <input type="text" name="staff_code" id="staff_code" class="form-control" required maxlength="20" readonly>
            <small class="form-text text-muted">Auto-generated based on role</small>
          </div>
          <div class="mb-3">
            <label>Staff Name <span class="text-danger">*</span></label>
            <input type="text" name="staff_name" class="form-control" required maxlength="255">
          </div>
          <div class="mb-3">
            <label>Role <span class="text-danger">*</span></label>
            <select name="staff_role" id="staff_role" class="form-control" required onchange="updateJobDescriptionOptions(); generateStaffCode();">
              <option value="">-- Select Role --</option>
              <option value="ADMIN">Admin</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="MECHANIC_SERVICE_AREA">Mechanic - Service Area</option>
              <option value="MECHANIC_UNIT_PREP">Mechanic - Unit Preparation</option>
              <option value="MECHANIC_FABRICATION">Mechanic - Fabrication</option>
              <option value="HELPER">Helper</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label>Job Description <span class="text-danger">*</span></label>
            <textarea name="job_description" id="job_description" class="form-control" rows="3" required 
                      placeholder="Describe the main responsibilities and tasks for this position"></textarea>
            <small class="form-text text-muted">Will auto-populate based on role selection, but you can customize it.</small>
          </div>
          
          <div class="mb-3">
            <label>Work Location <span class="text-danger">*</span></label>
            <select name="work_location" class="form-control" required>
              <option value="">-- Select Work Location --</option>
              <option value="CENTRAL">Central (Head Office)</option>
              <option value="MILL">Mill</option>
              <option value="BOTH">Both (Flexible)</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Department</label>
            <select name="departemen_id" class="form-control">
              <option value="">-- Select Department --</option>
              <option value="1">DIESEL</option>
              <option value="2">ELECTRIC</option>
              <option value="3">GASOLINE</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" maxlength="20">
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" maxlength="100">
          </div>
          <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Employee</button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="addAssignmentForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div id="assignment_area_info" class="alert alert-light border py-2 mb-2" style="display:none;">
            <small>Area Type: <strong id="assignment_area_type_badge"></strong></small>
          </div>
          <div class="mb-3">
            <label>Area <span class="text-danger">*</span></label>
            <select name="area_id" id="assignment_area_id" class="form-control" required onchange="loadAvailableEmployeesForAssignment()">
              <option value="">-- Select Area --</option>
              <?php foreach ($areas as $area): ?>
                <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
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
          <div class="mb-3">
            <label>Employee <span class="text-danger">*</span></label>
            <select name="staff_id" id="assignment_staff_id" class="form-control" required>
              <option value="">-- Select Employee --</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Assignment Type <span class="text-danger">*</span></label>
            <select name="assignment_type" class="form-control" required>
              <option value="PRIMARY">PRIMARY</option>
              <option value="BACKUP">BACKUP</option>
              <option value="TEMPORARY">TEMPORARY</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Department Scope <span class="text-danger">*</span></label>
            <select name="department_scope" id="assignment_dept_scope" class="form-control" required>
              <option value="ALL">ALL - All Departments</option>
              <option value="ELECTRIC">ELECTRIC - Electric Only</option>
              <option value="DIESEL">DIESEL - Diesel Only</option>
              <option value="GASOLINE">GASOLINE - Gasoline Only</option>
              <option value="DIESEL,GASOLINE">DIESEL & GASOLINE</option>
            </select>
            <small class="form-text text-muted">
              <strong>ALL:</strong> Untuk area MILL (karyawan menangani semua departemen)<br>
              <strong>Specific:</strong> Untuk area CENTRAL (fokus DIESEL atau ELECTRIC saja)
            </small>
          </div>
          <div class="mb-3">
            <label>Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-3">
            <label>End Date (optional)</label>
            <input type="date" name="end_date" class="form-control">
          </div>
          <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Placeholder Modals (Edit/View) -->
<div class="modal fade" id="viewAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Area Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <div class="modal-body" id="areaDetailsContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Employee Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <div class="modal-body" id="employeeDetailsContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="editAreaForm">
        <input type="hidden" name="id" id="edit_area_id">
        <div class="modal-body">
          <div class="mb-3">
            <label>Area Code <span class="text-danger">*</span></label>
            <input type="text" name="area_code" id="edit_area_code" class="form-control" required maxlength="10">
          </div>
          <div class="mb-3">
            <label>Area Name <span class="text-danger">*</span></label>
            <input type="text" name="area_name" id="edit_area_name" class="form-control" required maxlength="255">
          </div>
          <div class="mb-3">
            <label>Area Type <span class="text-danger">*</span></label>
            <select name="area_type" id="edit_area_type" class="form-control" required>
              <option value="MILL">MILL - Site / Plant Location</option>
              <option value="CENTRAL">CENTRAL - Head Office / Department Specific</option>
            </select>
          </div>
          <div class="mb-3" id="edit_dept_group" style="display:none;">
            <label>Department <span class="text-danger">*</span></label>
            <select name="departemen_id" id="edit_departemen_id" class="form-control">
              <option value="">-- Pilih Departemen --</option>
              <?php foreach ($departemen as $dept): ?>
                <?php if ($dept['id_departemen'] == 3) continue; // GASOLINE digabung ke DIESEL ?>
                <option value="<?= $dept['id_departemen'] ?>">
                  <?= $dept['id_departemen'] == 1 ? 'DIESEL & GASOLINE' : esc($dept['nama_departemen']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Pilih departemen yang ditangani area CENTRAL ini</small>
          </div>
            <div class="mb-3">
            <label>Description</label>
            <textarea name="area_description" id="edit_area_description" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="editEmployeeForm">
        <input type="hidden" name="id" id="edit_staff_id">
        <div class="modal-body">
          <div class="mb-3">
            <label>Staff Code <span class="text-danger">*</span></label>
            <input type="text" name="staff_code" id="edit_staff_code" class="form-control" required maxlength="20">
          </div>
          <div class="mb-3">
            <label>Staff Name <span class="text-danger">*</span></label>
            <input type="text" name="staff_name" id="edit_staff_name" class="form-control" required maxlength="255">
          </div>
          <div class="mb-3">
            <label>Role <span class="text-danger">*</span></label>
            <select name="staff_role" id="edit_staff_role" class="form-control" required onchange="updateEditJobDescription()">
              <option value="">-- Select Role --</option>
              <option value="ADMIN">Admin</option>
              <option value="SUPERVISOR">Supervisor</option>
              <option value="FOREMAN">Foreman</option>
              <option value="MECHANIC">Mechanic</option>
              <option value="MECHANIC_SERVICE_AREA">Mechanic - Service Area</option>
              <option value="MECHANIC_UNIT_PREP">Mechanic - Unit Preparation</option>
              <option value="MECHANIC_FABRICATION">Mechanic - Fabrication</option>
              <option value="HELPER">Helper</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label>Job Description <span class="text-danger">*</span></label>
            <textarea name="job_description" id="edit_job_description" class="form-control" rows="3" required 
                      placeholder="Describe the main responsibilities and tasks for this position"></textarea>
          </div>
          
          <div class="mb-3">
            <label>Work Location <span class="text-danger">*</span></label>
            <select name="work_location" id="edit_work_location" class="form-control" required>
              <option value="">-- Select Work Location --</option>
              <option value="CENTRAL">Central (Head Office)</option>
              <option value="MILL">Mill</option>
              <option value="BOTH">Both (Flexible)</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label>Department</label>
            <select name="departemen_id" id="edit_staff_departemen_id" class="form-control">
              <option value="">-- Select Department --</option>
              <option value="1">DIESEL</option>
              <option value="2">ELECTRIC</option>
              <option value="3">GASOLINE</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>
      <form id="editAssignmentForm">
        <input type="hidden" name="id" id="edit_assignment_id">
        <div class="modal-body">
          <div class="mb-3">
            <label>Area</label>
            <input type="text" id="edit_assignment_area" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label>Employee</label>
            <input type="text" id="edit_assignment_staff" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <input type="text" id="edit_assignment_role" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label>Assignment Type <span class="text-danger">*</span></label>
            <select name="assignment_type" id="edit_assignment_type" class="form-control" required>
              <option value="PRIMARY">PRIMARY</option>
              <option value="BACKUP">BACKUP</option>
              <option value="TEMPORARY">TEMPORARY</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" id="edit_assignment_start" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>End Date</label>
            <input type="date" name="end_date" id="edit_assignment_end" class="form-control">
          </div>
          <div class="mb-3">
            <label>Status Active</label>
            <select name="is_active" id="edit_assignment_active" class="form-control">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" id="edit_assignment_notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Employee Detail Modal -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Area</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

<?= $this->section('styles') ?>
<style>
/* DataTables Search Styling */
.dataTables_wrapper .dataTables_filter {
    text-align: right;
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    padding: 0.375rem 0.75rem;
    margin-left: 0.5em;
    width: 250px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #4e73df;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.dataTables_wrapper .dataTables_length {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    margin: 0 0.5em;
}

/* DataTables Info & Pagination */
.dataTables_wrapper .dataTables_info {
    padding-top: 0.85em;
    color: #858796;
}

.dataTables_wrapper .dataTables_paginate {
    text-align: right;
    padding-top: 0.25em;
}

/* Employee Breakdown Styling */
.employee-breakdown {
    font-size: 0.85rem;
    line-height: 1.3;
}

.employee-breakdown small {
    margin: 2px 0;
    font-weight: 500;
}

.employee-breakdown .fas {
    width: 14px;
    text-align: center;
    margin-right: 4px;
}

.employee-breakdown .text-muted {
    font-weight: 600;
    border-top: 1px solid #e3e6f0;
    padding-top: 2px;
    margin-top: 4px;
}

/* Processing indicator - OPTIMA Theme */
.dataTables_wrapper .dataTables_processing {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    width: 240px;
    margin-left: -120px;
    margin-top: -40px;
    text-align: center;
    padding: 1.5em 1em;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
    z-index: 9999 !important;
    font-weight: 500;
    font-size: 14px;
    animation: processingPulse 1.5s ease-in-out infinite;
}

@keyframes processingPulse {
    0%, 100% { opacity: 0.9; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.02); }
}

.dataTables_wrapper .dataTables_processing:before {
    content: "⏳ ";
    font-size: 18px;
    margin-right: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_filter input {
        width: 100%;
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length {
        float: none;
        text-align: left;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let areasTable, employeesTable;
let employeesByRoleChart, assignmentsByAreaChart;
// Filter functionality removed for simplicity

$(document).ready(function() {
  // Initialize ONLY Areas table on page load
  if (!areasTable) {
    initializeAreaTable();
  }
  
  // DON'T initialize employees table yet - wait for tab click
  
  initializeCharts();
  bindForms();
  buildRoleCoverageMatrix();
  
  // Restore tab if page was reloaded for refresh
  restoreActiveTab();
  
  // Track current active tab
  window.currentActiveTab = 'areasTab';
  
  // Explicitly activate the first tab
  setTimeout(function() {
    $('#areas-tab').tab('show');
  }, 100);
  
  // Tab change tracking - LAZY LOAD employees table
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const targetTab = $(e.target).attr('href').substring(1);
    window.currentActiveTab = targetTab;
    
    // Initialize employees table ONLY when user clicks the tab
    if (targetTab === 'employeesTab' && !employeesTable) {
      console.log('Lazy loading employees table...');
      initializeEmployeeTable();
    }
    
    // Adjust columns when switching tabs
    if (targetTab === 'areasTab' && areasTable) {
      areasTable.columns.adjust().responsive.recalc();
    } else if (targetTab === 'employeesTab' && employeesTable) {
      employeesTable.columns.adjust().responsive.recalc();
    }
  });
  
  
  // Bootstrap 5 handles [data-bs-dismiss="modal"] natively.
  // ESC key and backdrop click are also handled by Bootstrap 5 natively.
  // Keep only the jQuery fallback for any programmatic hides.
  
  // Cancel button handler (fallback for btn-secondary inside modals without data-bs-dismiss)
  $(document).on('click', '.modal .btn-secondary', function(e) {
    const $btn = $(this);
    if ($btn.attr('data-bs-dismiss') || $btn.attr('type') === 'submit') return;
    const text = $btn.text().toLowerCase().trim();
    if (text.includes('cancel') || text.includes('close') || text.includes('tutup')) {
      const modalEl = $btn.closest('.modal')[0];
      if (modalEl) {
        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.hide();
      }
    }
  });
  
  // Ensure modal events are properly bound
  $('.modal').on('hidden.bs.modal', function() {
    $(this).find('form')[0]?.reset();
    $(this).find('.form-errors').html('').hide();
    // Re-enable submit buttons
    $(this).find('button[type="submit"]').prop('disabled', false);
  });
  
  // Specific handler for employee modal
  $('#addEmployeeModal').on('hidden.bs.modal', function() {
    $('#addEmployeeForm')[0].reset();
    $('#addEmployeeForm .form-errors').html('').hide();
    const $submitBtn = $('#addEmployeeForm button[type="submit"]');
    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Employee');
  });
});

/* ===================== TABLE INITIALIZATIONS ===================== */
function initializeAreaTable() {
  if ($.fn.DataTable.isDataTable('#areasTable')) {
    return;
  }
  
  try {
    areasTable = $('#areasTable').DataTable({
      processing: false, // DISABLED - Loading indicator annoying untuk user
      serverSide: true,
      searching: true,
      searchDelay: 500,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
      ajax: {
        url: '<?= base_url('service/area-management/getAreas') ?>',
        type: 'POST',
        timeout: 30000, // 30 second timeout
        data: function(d) {
          d[window.csrfTokenName] = window.getCsrfToken();
          return d;
        },
        dataSrc: function(json) {
          if (!json) {
            console.error('No response from server');
            return [];
          }
          if (json.error) {
            console.error('Server error:', json.message);
            OptimaNotify.error('Error loading areas: ' + json.message);
            return [];
          }
          return (json && json.data) ? json.data : [];
        },
        error: function(xhr, error, code) {
          console.error('Areas AJAX error:', error, xhr.responseText);
          if (error === 'timeout') {
            OptimaNotify.error('Request timeout. Coba refresh halaman.');
          } else {
            OptimaNotify.error('Error loading data: ' + error);
          }
          return [];
        }
      },
    columns: [
      { 
        data: 'area_code',
        render: function(d, type, row, meta) {
          let label = d || '';
          if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
            label = OptimaSearch.highlightForMeta(meta, label);
          }
          return `<span class="employee-code">${label}</span>`;
        }
      },
      { 
        data: 'area_name',
        render: function(d, type, row, meta) {
          let label = d || '';
          if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
            label = OptimaSearch.highlightForMeta(meta, label);
          }
          return `<span class="text-dark font-weight-medium">${label}</span>`;
        }
      },
      { 
        data: 'area_type', 
        render: function(data, type, row) {
          if (!data) return '<span class="text-muted">-</span>';
          const typeBadge = data === 'CENTRAL'
            ? '<span class="badge badge-soft-blue">CENTRAL</span>'
            : '<span class="badge badge-soft-green">MILL</span>';
          
          let deptBadge = '';
          if (data === 'CENTRAL') {
            if (row.departemen_name) {
              const deptColors = { 'ELECTRIC': 'badge-soft-cyan', 'DIESEL': 'badge-soft-orange', 'GASOLINE': 'badge-soft-orange' };
              const deptLabel = (row.departemen_name === 'DIESEL' || row.departemen_name === 'GASOLINE') ? 'DIESEL & GASOLINE' : row.departemen_name;
              const cls = deptColors[row.departemen_name] || 'badge-soft-blue';
              deptBadge = ` <span class="badge ${cls}">${deptLabel}</span>`;
            }
          } else {
            deptBadge = ' <span class="badge badge-soft-gray">All Depts</span>';
          }
          
          return typeBadge + deptBadge;
        }
      },
      { 
        data: 'description',
        render: function(d, type, row, meta) {
          if (!d) return '<span class="text-muted">-</span>';
          let short = d.length > 50 ? d.substring(0,50) : d;
          if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
            short = OptimaSearch.highlightForMeta(meta, short);
          }
          if (d.length > 50) {
            return `<span class="text-dark">${short}</span><span class="text-muted">…</span>`;
          }
          return `<span class="text-dark">${short}</span>`;
        }
      },
      { 
        data: 'customers_count',
        render: function(d, type, row, meta) {
          let label = String(d || 0);
          if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
            label = OptimaSearch.highlightForMeta(meta, label);
          }
          return `<strong class="text-dark">${label}</strong>`;
        }
      },
      { 
        data: null, 
        orderable: false,
        render: function(data, type, row) {
          const foreman = row.employees_breakdown?.foreman || 0;
          const mechanic = row.employees_breakdown?.mechanic || 0;
          const helper = row.employees_breakdown?.helper || 0;
          
          return `<div class="employee-breakdown">
            <small class="d-block text-success"><i class="fas fa-user-tie"></i> Foreman: ${foreman}</small>
            <small class="d-block text-primary"><i class="fas fa-wrench"></i> Mechanic: ${mechanic}</small>
            <small class="d-block text-info"><i class="fas fa-hand-holding"></i> Helper: ${helper}</small>
          </div>`;
        }
      },
      { 
        data: 'is_active',
        render: function(data, type, row) {
          return data == 1 
            ? '<span class="badge badge-soft-green">Active</span>' 
            : '<span class="badge badge-soft-gray">Inactive</span>';
        }
      }
    ],
    order: [[1,'asc']],
    pageLength: 25,
    language: {
      emptyTable: "No areas found",
      info: "Showing _START_ to _END_ of _TOTAL_ areas",
      infoEmpty: "Showing 0 to 0 of 0 areas",
      search: "Search:",
      searchPlaceholder: "Search areas...",
      lengthMenu: "Show _MENU_ entries"
    },
    drawCallback: function(settings) {
      console.log('📊 Areas table draw completed');
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
  
  // Add search event listener for debugging

  
  // Ensure search input is properly bound
  setTimeout(function() {
    $('div.dataTables_filter input').attr('placeholder', 'Search areas...');
  }, 100);
  
  } catch (error) {
    console.error('Error initializing Areas DataTable:', error);
    // Fallback: show error message
    $('#areasTable').html('<div class="alert alert-danger">Error loading areas data. Please refresh the page.</div>');
  }
}

function initializeEmployeeTable() {
  if ($.fn.DataTable.isDataTable('#employeesTable')) {
    $('#employeesTable').DataTable().destroy();
  }

  try {
    employeesTable = $('#employeesTable').DataTable({
      processing: false,
      serverSide: false, // Client-side processing — data kecil, lebih reliable untuk search & pagination
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
      ajax: {
        url: '<?= base_url('service/area-management/getEmployees') ?>',
        type: 'POST',
        data: function(d) {
          d[window.csrfTokenName] = window.getCsrfToken();
          return d;
        },
        dataSrc: function(json) {
          if (!json) { return []; }
          if (json.error) {
            console.error('Server error:', json.message);
            if (window.OptimaNotify) OptimaNotify.error('Error loading employees: ' + json.message);
            return [];
          }
          return json.data || [];
        },
        error: function(xhr, error, thrown) {
          console.error('Employees AJAX error:', error, xhr.responseText);
          if (window.OptimaNotify) {
            OptimaNotify.error(error === 'timeout'
              ? 'Request timeout. Coba refresh halaman.'
              : 'Error loading employees: ' + error);
          }
        }
      },
      columns: [
        { 
          data: 'staff_code',
          render: function(d, type, row, meta) {
            let label = d || '';
            if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
              label = OptimaSearch.highlightForMeta(meta, label);
            }
            return `<span class="employee-code">${label}</span>`;
          }
        },
        { 
          data: 'staff_name',
          render: function(d, type, row, meta) {
            let label = d || '';
            if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
              label = OptimaSearch.highlightForMeta(meta, label);
            }
            return `<span class="text-dark font-weight-medium">${label}</span>`;
          }
        },
        {
          data: 'staff_role',
          render: function(data, type, row) {
            if (!data) return '<span class="text-muted">N/A</span>';
            return `<strong class="text-${roleBadgeColor(data)}">${data}</strong>`;
          }
        },
        {
          data: 'work_location',
          render: function(data, type, row, meta) {
            if (!data || data === '-') return '<span class="text-muted">-</span>';
            let label = data;
            if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
              label = OptimaSearch.highlightForMeta(meta, label);
            }
            return `<strong class="text-${locationBadgeColor(data)}">${label}</strong>`;
          }
        },
        { 
          data: 'departemen',
          render: function(d, type, row, meta) {
            if (!d) return '<span class="text-muted">-</span>';
            let label = d;
            if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
              label = OptimaSearch.highlightForMeta(meta, label);
            }
            return `<span class="text-dark">${label}</span>`;
          }
        },
        {
          data: 'area_assignments',
          orderable: false,
          searchable: false,
          render: function(data, type, row) {
            if (!data || data.length === 0) {
              return '<span class="text-warning">⚠️ Unassigned</span>';
            }
            const central = data.filter(a => a.area_type === 'CENTRAL');
            const mill = data.filter(a => a.area_type === 'MILL');
            let output = [];
            if (central.length > 0) output.push(`<strong class="text-primary">${central.length} Central</strong>`);
            if (mill.length > 0) output.push(`<strong class="text-success">${mill.length} Mill</strong>`);
            return output.join(' | ');
          }
        }
      ],
      order: [[1, 'asc']],
      pageLength: 25,
      language: {
        emptyTable: "No employees found",
        info: "Showing _START_ to _END_ of _TOTAL_ employees",
        infoEmpty: "Showing 0 to 0 of 0 employees",
        search: "Search:",
        searchPlaceholder: "Search employees...",
        lengthMenu: "Show _MENU_ entries"
      },
      drawCallback: function(settings) {
        $('#employeesTable tbody').off('click', 'tr').on('click', 'tr', function() {
          const data = employeesTable.row(this).data();
          if (data && data.id) viewEmployeeDetail(data.id);
        });
        $('#employeesTable tbody tr').hover(
          function() { $(this).addClass('table-hover-row'); },
          function() { $(this).removeClass('table-hover-row'); }
        );
      }
    });

    setTimeout(function() {
      $('#employeesTable_wrapper div.dataTables_filter input').attr('placeholder', 'Search employees...');
    }, 100);

  } catch (error) {
    console.error('Error initializing Employees DataTable:', error);
    $('#employeesTable').html('<div class="alert alert-danger">Error loading employees data. Please refresh the page.</div>');
  }
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
  const matrixData = { draw: 1, start: 0, length: 1000 };
  matrixData[window.csrfTokenName] = window.getCsrfToken();
  $.ajax({
    url: '<?= base_url('service/area-management/getAreas') ?>',
    type: 'POST',
    data: matrixData,
    success: function(resp) {
      if (!resp.data) return;
      const roles = ['FOREMAN','MECHANIC','HELPER'];
      let html = '<table class="table table-sm table-bordered"><thead><tr><th>Area</th>' + roles.map(r => `<th>${r}</th>`).join('') + '</tr></thead><tbody>';
      resp.data.forEach(area => {
        const bd = area.employees_breakdown || {};
        html += `<tr><td>${area.area_code}</td>`;
        html += bd.foreman  > 0 ? `<td><strong class='text-success'>${bd.foreman}</strong></td>`  : '<td>-</td>';
        html += bd.mechanic > 0 ? `<td><strong class='text-success'>${bd.mechanic}</strong></td>` : '<td>-</td>';
        html += bd.helper   > 0 ? `<td><strong class='text-success'>${bd.helper}</strong></td>`   : '<td>-</td>';
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
      output.push(`<span class='text-warning font-weight-bold mr-2'>👷 1 Foreman</span>`);
    } else {
      output.push(`<span class='text-warning font-weight-bold mr-2'>👷 ${summary.foreman} Foreman</span>`);
    }
  }
  
  // Add mechanics if exist
  if (summary.mechanics && Array.isArray(summary.mechanics) && summary.mechanics.length > 0) {
    output.push(`<span class='text-primary font-weight-bold mr-2'>🔧 ${summary.mechanics.length} Mechanic${summary.mechanics.length > 1 ? 's' : ''}</span>`);
  }
  
  // Add helpers if exist
  if (summary.helpers && Array.isArray(summary.helpers) && summary.helpers.length > 0) {
    output.push(`<span class='text-success font-weight-bold mr-2'>🔨 ${summary.helpers.length} Helper${summary.helpers.length > 1 ? 's' : ''}</span>`);
  }
  
  return output.length > 0 ? output.join('') : '<span class="text-muted">No assignments</span>';
}

function roleBadgeColor(role) {
  switch(role) {
    case 'ADMIN': return 'primary';          // Biru - untuk admin
    case 'SUPERVISOR': return 'danger';      // Merah - untuk supervisor (tertinggi)
    case 'FOREMAN': return 'warning';        // Kuning - untuk foreman
    case 'MECHANIC': return 'success';       // Hijau - untuk mechanic
    case 'MECHANIC_SERVICE_AREA': return 'info';     // Cyan - untuk mechanic area
    case 'MECHANIC_UNIT_PREP': return 'purple';      // Ungu - untuk mechanic prep
    case 'MECHANIC_FABRICATION': return 'dark';      // Hitam - untuk mechanic fab
    case 'HELPER': return 'secondary';       // Abu-abu - untuk helper
    default: return 'muted';                 // Abu muda - untuk role yang tidak dikenal
  }
}

function locationBadgeColor(location) {
  switch(location) {
    case 'CENTRAL': return 'primary';       // Blue - for central
    case 'MILL': return 'success';          // Green - for mill (legacy work_location value)
    case 'BRANCH': return 'success';        // Green - for branch/mill
    case 'BOTH': return 'info';             // Cyan - untuk both
    default: return 'muted';               // Abu muda - untuk default
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

  // Show/hide department dropdown based on area_type selection
  $('#add_area_type').on('change', function() {
    const isCentral = $(this).val() === 'CENTRAL';
    $('#add_dept_group').toggle(isCentral);
    if (!isCentral) $('#add_departemen_id').val('');
  });

  $('#edit_area_type').on('change', function() {
    const isCentral = $(this).val() === 'CENTRAL';
    $('#edit_dept_group').toggle(isCentral);
    if (!isCentral) $('#edit_departemen_id').val('');
  });

  $('#addAreaForm').on('submit', function(e){
    e.preventDefault();
    const $form = $(this);
    const $errorDiv = $form.find('.form-errors');
    $errorDiv.html('').hide();
    
    $.post('<?= base_url('service/area-management/saveArea') ?>', $form.serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken(), function(resp){
      if (resp.success) {
        $('#addAreaModal').modal('hide');
        notify('Area created successfully','success');
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
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.html();
    
    // Disable submit button and show loading
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
    
    $form.find('.form-errors').html('').hide();
    
    $.post('<?= base_url('service/area-management/saveEmployee') ?>', $form.serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken(), function(resp){
      if (resp.success) {
        // Success: close modal, show notification, refresh table
        $('#addEmployeeModal').modal('hide');
        notify('Employee created successfully!','success');
        
        // FORCE reload employees table - MULTIPLE STRATEGIES
        console.log('🔄 Attempting to refresh employees table...');
        console.log('employeesTable exists?', !!employeesTable);
        console.log('employeesTable is DataTable?', employeesTable ? $.fn.DataTable.isDataTable('#employeesTable') : false);
        
        if (employeesTable && $.fn.DataTable.isDataTable('#employeesTable')) {
          console.log('✅ Reloading employees table via ajax.reload()...');
          employeesTable.ajax.reload(function() {
            console.log('✅ Employees table reloaded successfully!');
          }, false); // false = stay on current page
        } else {
          console.log('⚠️ Employees table not initialized, initializing now...');
          // Ensure we're on employees tab
          if (window.currentActiveTab !== 'employeesTab') {
            $('#employees-tab').tab('show');
          }
          // Initialize or re-initialize
          setTimeout(function() {
            if (!employeesTable || !$.fn.DataTable.isDataTable('#employeesTable')) {
              initializeEmployeeTable();
            } else {
              employeesTable.ajax.reload(null, false);
            }
          }, 300);
        }
        
        $form[0].reset();
      } else {
        // Error: show validation errors
        if (resp.errors) {
          showFormErrors($form, resp.errors);
          let errorDetails = Object.keys(resp.errors).map(field => `${field}: ${resp.errors[field]}`).join('\\n');
          notify(`Validation errors:\\n${errorDetails}`, 'error');
        } else {
          notify(resp.message || 'Failed to create employee','error');
        }
      }
    }, 'json').fail(function(xhr, status, error) {
      console.error('Save employee error:', error, xhr.responseText);
      notify('Network error: ' + error, 'error');
    }).always(function() {
      // Re-enable submit button
      $submitBtn.prop('disabled', false).html(originalBtnText);
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
              OptimaConfirm.generic({
                title: 'Duplikat PRIMARY?',
                text: `Sudah ada PRIMARY ${role} (${existingPrimary.staff_name}) di area ini. Lanjutkan membuat assignment PRIMARY baru?`,
                icon: 'warning',
                confirmText: 'Ya, Lanjutkan',
                cancelText: window.lang('cancel'),
                confirmButtonColor: 'warning',
                onConfirm: function() { submitAssignmentForm($form); }
              });
              return;
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
    $.post('<?= base_url('service/area-management/storeAssignment') ?>', $form.serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken(), function(resp){
      if (resp.success) {
        $('#addAssignmentModal').modal('hide');
        notify('Assignment created','success');
        const areaId = $('#assignAreaSelect').val();
        if (areaId) loadAreaAssignments();
        buildRoleCoverageMatrix();
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
  $('#add_dept_group').hide();
  $('#add_departemen_id').val('');
  $('#addAreaModal').modal('show'); 
}

function showAddEmployeeModal(){ 
  $('#addEmployeeForm')[0].reset();
  $('#addEmployeeForm .form-errors').html('').hide();
  // Generate initial staff code
  generateStaffCode();
  $('#addEmployeeModal').modal('show'); 
}
function showAddAssignmentModal(){
  const selectedArea = $('#assignAreaSelect').val();
  $('#assignment_area_info').hide();
  $('#assignment_area_type_badge').html('');
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
  OptimaConfirm.danger({
      title: 'Hapus Area',
      text: 'Apakah Anda yakin ingin menghapus area ini?',
      onConfirm: function() {
          $.ajax({
    url: `<?= base_url('service/area-management/deleteArea') ?>/${id}`,
    type: 'POST',
    data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
    success: function(resp){
      if (resp.success) {
        notify('Area berhasil dihapus','success');
        refreshAreas();
      } else {
        notify(resp.message,'error');
      }
    },
    error: function(xhr) {
      notify('Error deleting area: ' + (xhr.responseJSON?.message || xhr.status), 'error');
    }
  });
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
  OptimaConfirm.generic({
      title: 'Nonaktifkan Karyawan',
      text: 'Karyawan akan dinonaktifkan (bukan dihapus permanen). Lanjutkan?',
      icon: 'warning',
      confirmText: '<i class="fas fa-user-slash me-1"></i>Ya, Nonaktifkan',
      confirmButtonColor: '#fd7e14',
      onConfirm: function() {
          $.ajax({
    url: `<?= base_url('service/area-management/deleteEmployee') ?>/${id}`,
    type: 'POST',
    data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
    success: function(resp){
      if (resp.success) {
        notify('Karyawan berhasil dinonaktifkan','success');
        refreshEmployees();
      } else {
        notify(resp.message,'error');
      }
    },
    error: function(xhr) {
      notify('Error menonaktifkan karyawan: ' + (xhr.responseJSON?.message || xhr.status), 'error');
    }
  });
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
  
  $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i><br>Loading assignments...</div>');
  
  const timestamp = Date.now();
  const url = `<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}?_=${timestamp}`;
  
  $.getJSON(url, function(resp){
    if (!resp.success) {
      $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Error loading assignments</div>');
      return;
    }
    const assignments = resp.data || [];
    renderAssignmentsTable(assignments);
    updateAreaAssignmentSummary(assignments);
  }).fail(function(xhr, status, error) {
    $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Network error loading assignments<br><button class="btn btn-sm btn-primary mt-2" onclick="loadAreaAssignments()">Retry</button></div>');
  });
}

function forceRefreshAssignments() {
  const areaId = $('#assignAreaSelect').val();
  if (areaId) {
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-sync fa-spin"></i><br>Refreshing...</div>');
    setTimeout(function() {
      loadAreaAssignments();
    }, 300);
  }
}



function renderAssignmentsTable(assignments) {
  const filterRole = $('#filterRoleAssignments').val();
  let filtered = assignments;
  if (filterRole) filtered = assignments.filter(a => a.staff_role === filterRole);
  
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
      <td><strong class='text-${roleBadgeColor(role)}'>${role}</strong></td>
      <td><strong class='text-${assignmentTypeColor}'>${a.assignment_type}</strong></td>
      <td>${a.start_date ? new Date(a.start_date).toLocaleDateString('en-GB') : '-'}</td>
      <td>${a.end_date ? new Date(a.end_date).toLocaleDateString('en-GB') : '-'}</td>
      <td>
        <button class='btn btn-sm btn-outline-primary mr-1' onclick='editAssignment(${a.id})' title="Edit Assignment" aria-label="Edit assignment"><i class='fas fa-edit' aria-hidden='true'></i></button>
        <button class='btn btn-sm btn-outline-danger' onclick='removeAssignment(${a.id})' title="Remove Assignment" aria-label="Hapus assignment"><i class='fas fa-trash' aria-hidden='true'></i></button>
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
        <strong class="text-primary">${total} Assigned</strong>
      </div>
      <div class="small text-muted">
        <span class='text-success mr-2'>✅ Primary: ${v.primary}</span>
        <span class='text-warning mr-2'>⚠️ Backup: ${v.backup}</span>
        <span class='text-info'>⏱️ Temporary: ${v.temporary}</span>
      </div>
    </div>`;
  });
  
  html += '</div></div>';
  $('#areaAssignmentSummary').html(html);
}

function filterAssignments() { loadAreaAssignments(); }

function loadAvailableEmployeesForAssignment() {
  const areaId = $('#assignment_area_id').val();
  const role = $('#assignment_role_filter').val();
  
  // Don't load if area is not selected
  if (!areaId) {
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Area First --</option>');
    $('#assignment_area_info').hide();
    return;
  }

  // Load area info to show area type badge and auto-set department_scope
  $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${areaId}`, function(areaResp) {
    if (areaResp.success && areaResp.data && areaResp.data.area) {
      const atype = areaResp.data.area.area_type || 'MILL';
      const color = atype === 'CENTRAL' ? 'text-primary' : 'text-success';
      const hint = atype === 'CENTRAL'
        ? '— pilih departemen spesifik (DIESEL/ELECTRIC)'
        : '— karyawan menangani semua departemen (ALL)';
      $('#assignment_area_type_badge').html(`<span class="${color}">${atype}</span> <small class="text-muted">${hint}</small>`);
      $('#assignment_area_info').show();

      // Auto-set department_scope based on area_type
      const $deptScope = $('#assignment_dept_scope');
      if (atype === 'MILL') {
        $deptScope.val('ALL');
      } else if (atype === 'CENTRAL') {
        // Only change if currently ALL — nudge user to pick specific dept
        if ($deptScope.val() === 'ALL') {
          $deptScope.val('DIESEL');
        }
      }
    }
  });
  
  $.getJSON(`<?= base_url('service/area-management/getAvailableEmployees') ?>/${areaId}/${role || ''}`, function(resp){
    if (!resp.success) return;
    const select = $('#assignment_staff_id');
    select.empty().append('<option value="">-- Select Employee --</option>');
    resp.data.forEach(e => select.append(`<option value='${e.id}'>${e.staff_code} - ${e.staff_name} (${e.staff_role || e.role || 'N/A'})</option>`));
  });
}

function removeAssignment(id) {
  OptimaConfirm.danger({
      title: 'Hapus Assignment',
      text: 'Karyawan akan di-unassign dari area ini. Tindakan ini tidak dapat dibatalkan.',
      confirmText: '<i class="fas fa-user-minus me-1"></i>Ya, Hapus Assignment',
      onConfirm: function() {
          console.log('🗑️ Attempting to delete assignment ID:', id);
  
  // Immediately disable the delete button to prevent duplicate clicks
  $(`button[onclick*="removeAssignment(${id})"]`).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
  
  $.ajax({
    url: `<?= base_url('service/area-management/deleteAssignment') ?>/${id}`,
    type: 'POST',
    data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
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
    $('#edit_area_type').val(a.area_type || 'MILL').trigger('change');
    $('#edit_departemen_id').val(a.departemen_id || '');
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
    $('#edit_staff_role').val(e.role); // 'role' = alias for staff_role from showEmployee
    $('#edit_job_description').val(e.description || ''); // 'description' = alias for job_description
    $('#edit_work_location').val(e.work_location || '');
    $('#edit_staff_departemen_id').val(e.departemen_id || '');
    $('#editEmployeeModal').modal('show');
  });
}
function editAssignment(id) {
  $.getJSON(`<?= base_url('service/area-management/showAssignment') ?>/${id}`, function(resp){
    if(!resp.success) {
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
    notify('Network error: ' + error, 'error');
  });
}

// Submit handlers edit forms
$('#editAreaForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_area_id').val();
  const formData = $(this).serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken();
  
  $.post(`<?= base_url('service/area-management/updateArea') ?>/${id}`, formData, function(resp){
    if(resp.success){
      notify('Area updated','success');
      $('#editAreaModal').modal('hide');
      refreshAreas();
      
    } else {
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
    notify('Network error: ' + error, 'error');
  });
});

$('#editEmployeeForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_staff_id').val();
  const formData = $(this).serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken();
  
  $.post(`<?= base_url('service/area-management/updateEmployee') ?>/${id}`, formData, function(resp){
    if(resp.success){
      notify('Employee updated','success');
      $('#editEmployeeModal').modal('hide');
      refreshEmployees();
      
    } else {
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
    notify('Network error: ' + error, 'error');
  });
});

$('#editAssignmentForm').on('submit', function(e){
  e.preventDefault();
  const id = $('#edit_assignment_id').val();
  $.post(`<?= base_url('service/area-management/updateAssignment') ?>/${id}`, $(this).serialize() + '&' + window.csrfTokenName + '=' + window.getCsrfToken(), function(resp){
    if(resp.success){
      notify('Assignment updated','success');
      $('#editAssignmentModal').modal('hide');
      loadAreaAssignments();
      buildRoleCoverageMatrix();
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
      if (response.success && response.data) {
        const emp = response.data;
        $('#detail_staff_code').text(emp.staff_code || '-');
        $('#detail_staff_name').text(emp.staff_name || '-');
        $('#detail_staff_role').html(emp.staff_role ? `<strong class="text-${roleBadgeColor(emp.staff_role)}">${emp.staff_role}</strong>` : '<span class="text-muted">-</span>');
        $('#detail_work_location').html(emp.work_location ? `<strong class="text-${locationBadgeColor(emp.work_location)}">${emp.work_location}</strong>` : '<span class="text-muted">-</span>');
        $('#detail_job_description').text(emp.job_description || '-');
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
              <td><strong class="text-${assign.assignment_type === 'PRIMARY' ? 'success' : 'secondary'}">${assign.assignment_type || '-'}</strong></td>
              <td><strong class="text-${assign.is_active ? 'success' : 'danger'}">${assign.is_active ? '✅ Active' : '❌ Inactive'}</strong></td>
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
          <td><strong class="text-${roleBadgeColor(assign.staff_role)}">${assign.staff_role}</strong></td>
          <td><strong class="text-${assign.assignment_type === 'PRIMARY' ? 'success' : 'secondary'}">${assign.assignment_type}</strong></td>
          <td><strong class="text-${assign.is_active ? 'success' : 'danger'}">${assign.is_active ? '✅ Active' : '❌ Inactive'}</strong></td>
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
  $('#edit_area_type').val(currentAreaData.area_type || 'MILL').trigger('change');
  $('#edit_departemen_id').val(currentAreaData.departemen_id || '');
  $('#edit_area_description').val(currentAreaData.description || '');
  
  // Show edit modal
  $('#editAreaModal').modal('show');
}

function deleteAreaFromDetail() {
  if (!currentAreaId) {
    notify('Area ID not available', 'error');
    return;
  }
  
  OptimaConfirm.danger({
    title: 'Hapus Area?',
    text: `Area "${currentAreaData?.area_name || 'this area'}" akan dihapus. Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Ya, Hapus!',
    cancelText: window.lang('cancel'),
    onConfirm: function() {
      $('#areaDetailModal').modal('hide');
      deleteArea(currentAreaId);
    }
  });
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
      $('#edit_job_description').val(emp.job_description || '');
      $('#edit_work_location').val(emp.work_location || '');
      $('#edit_staff_departemen_id').val(emp.departemen_id || '');
      
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
  
  OptimaConfirm.danger({
    title: 'Hapus Karyawan?',
    text: 'Karyawan akan dinonaktifkan (bukan dihapus permanen).',
    confirmText: 'Ya, Hapus!',
    cancelText: window.lang('cancel'),
    onConfirm: function() {
      $('#employeeDetailModal').modal('hide');
      deleteEmployee(currentEmployeeId);
    }
  });
}

/* ===================== REFRESH FUNCTIONS ===================== */
// Unified refresh functions - simplified and optimized

function refreshAreas() {
  if (areasTable) {
    areasTable.ajax.reload(function() {
      buildRoleCoverageMatrix();
    }, false);
  } else {
    initializeAreaTable();
  }
}

function refreshEmployees() {
  if (employeesTable) {
    employeesTable.ajax.reload(null, false);
  } else {
    initializeEmployeeTable();
  }
}

function refreshAssignments() {
  const areaId = $('#assignAreaSelect').val();
  if (areaId) {
    loadAreaAssignments();
  }
}

function refreshCurrentTab() {
  const activeTab = window.currentActiveTab || 'areasTab';
  
  switch(activeTab) {
    case 'areasTab':
      refreshAreas();
      break;
    case 'employeesTab':
      refreshEmployees();
      break;
    case 'assignmentsTab':
      refreshAssignments();
      break;
    case 'analyticsTab':
      buildRoleCoverageMatrix();
      if (typeof initializeCharts === 'function') initializeCharts();
      break;
  }
}

// Legacy function names - for backward compatibility
function refreshAreaTable() { refreshAreas(); }
function refreshEmployeeTable() { refreshEmployees(); }
function refreshAssignmentsTable() { refreshAssignments(); }

/* ===================== UTILITIES ===================== */

// Auto-populate job description based on selected role
function updateJobDescriptionOptions() {
  const roleSelect = document.querySelector('select[name="staff_role"]');
  const jobDescTextarea = document.getElementById('job_description');
  
  if (!roleSelect || !jobDescTextarea) return;
  
  populateJobDescription(roleSelect.value, jobDescTextarea);
}

// For edit modal
function updateEditJobDescription() {
  const roleSelect = document.getElementById('edit_staff_role');
  const jobDescTextarea = document.getElementById('edit_job_description');
  
  if (!roleSelect || !jobDescTextarea) return;
  
  populateJobDescription(roleSelect.value, jobDescTextarea);
}

// Common function to populate job description
function populateJobDescription(role, textarea) {
  const jobDescriptions = {
    'ADMIN': 'Administrator - Mengelola operasional administrasi, dokumentasi, dan koordinasi dengan berbagai departemen. Dapat bekerja di central office maupun branch office.',
    'SUPERVISOR': 'Supervisor - Mengawasi dan mengkoordinir aktivitas operasional serta memastikan target kinerja tercapai. Bertanggung jawab terhadap manajemen tim di branch yang ditugaskan.',
    'FOREMAN': 'Foreman - Memimpin tim teknis, mengawasi pekerjaan lapangan, dan bertanggung jawab terhadap kualitas hasil kerja. Mengkoordinir aktivitas harian tim mekanik.',
    'MECHANIC': 'Mechanic - Melakukan perbaikan, maintenance, dan service unit forklift secara umum.',
    'MECHANIC_SERVICE_AREA': 'Mechanic Service Area - Bertanggung jawab untuk service dan maintenance rutin unit forklift di area service branch. Melakukan diagnostic, repair, dan quality check sebelum unit diserahkan ke customer.',
    'MECHANIC_UNIT_PREP': 'Mechanic Unit Preparation - Mempersiapkan unit baru atau unit return untuk di-deploy ke customer. Melakukan instalasi attachment, modifikasi, kalibrasi, dan final inspection.',
    'MECHANIC_FABRICATION': 'Mechanic Fabrication - Bertanggung jawab di workshop fabrikasi untuk pembuatan dan modifikasi attachment/parts custom. Melakukan welding, cutting, fitting, painting, dan assembly komponen.',
    'HELPER': 'Helper - Membantu aktivitas teknis dan operasional, mendukung mechanic dalam pekerjaan service dan maintenance. Dapat ditempatkan di central office atau branch office sesuai kebutuhan.'
  };
  
  if (role && jobDescriptions[role]) {
    textarea.value = jobDescriptions[role];
  } else {
    textarea.value = '';
  }
}

// Auto-generate staff code based on role
function generateStaffCode() {
  const role = document.getElementById('staff_role')?.value;
  if (!role) return;
  
  const prefixes = {
    'ADMIN': 'ADM',
    'SUPERVISOR': 'SPV',
    'FOREMAN': 'FRM',
    'MECHANIC': 'MEC',
    'MECHANIC_SERVICE_AREA': 'MSA',
    'MECHANIC_UNIT_PREP': 'MUP',
    'MECHANIC_FABRICATION': 'MFB',
    'HELPER': 'HLP'
  };
  
  const prefix = prefixes[role] || 'STF';
  const timestamp = Date.now().toString().slice(-6);
  const code = `${prefix}${timestamp}`;
  
  const staffCodeInput = document.getElementById('staff_code');
  if (staffCodeInput) {
    staffCodeInput.value = code;
  }
}

function restoreActiveTab() {
  const savedTab = localStorage.getItem('area_management_active_tab');
  if (savedTab && savedTab !== 'areasTab') {
    setTimeout(function() {
      $(`a[href="#${savedTab}"]`).tab('show');
      window.currentActiveTab = savedTab;
      localStorage.removeItem('area_management_active_tab');
    }, 500);
  }
}

function notify(msg, type='success'){
	if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
		return OptimaNotify[type](msg);
	}
	if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
}

</script>
<?= $this->endSection() ?>
  
