<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- PAGE HEADER                                                      -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
          <h4 class="fw-bold mb-1">
              <span class="material-symbols-rounded me-2 align-middle text-primary" style="font-size:1.6rem">map</span>
              <?= lang('App.area_management') ?>
          </h4>
          <p class="text-muted mb-0 small"><?= lang('App.area_management_subtitle') ?></p>
      </div>
      <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" onclick="refreshCurrentTab()">
              <i class="fas fa-sync-alt me-1"></i> <?= lang('Common.refresh') ?>
          </button>
          <a href="<?= base_url('service/export_area') ?>" class="btn btn-sm btn-outline-success">
              <i class="fas fa-file-excel me-1"></i> <?= lang('Common.export') ?>
          </a>
      </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- STAT CARDS — 2 rows, 3 cards each                               -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div class="row g-3 mb-4">

      <!-- Row 1: Staff & Area -->
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-primary-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-globe stat-icon text-primary"></i></div>
                  <div>
                      <div class="stat-value"><?= $totalAreas ?></div>
                      <div class="text-muted small"><?= lang('App.total_areas') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-success-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-people stat-icon text-success"></i></div>
                  <div>
                      <div class="stat-value"><?= $totalEmployees ?></div>
                      <div class="text-muted small"><?= lang('App.total_employees') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-warning-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-link stat-icon text-warning"></i></div>
                  <div>
                      <div class="stat-value"><?= $totalAssignments ?></div>
                      <div class="text-muted small"><?= lang('App.active_assignments') ?></div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Row 1 continued: Unit Mapping -->
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-success-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-check-circle stat-icon text-success"></i></div>
                  <div>
                      <div class="stat-value" id="statWithArea"><?= $unitStats['units_with_area'] ?? 0 ?></div>
                      <div class="text-muted small"><?= lang('App.units_with_area') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-danger-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-exclamation-circle stat-icon text-danger"></i></div>
                  <div>
                      <div class="stat-value" id="statWithoutArea"><?= $unitStats['units_without_area'] ?? 0 ?></div>
                      <div class="text-muted small"><?= lang('App.units_without_area') ?></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-2 col-md-4 col-6">
          <div class="stat-card bg-info-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-file-earmark-check stat-icon text-info"></i></div>
                  <div>
                      <div class="stat-value" id="statActiveContractUnits"><?= $unitStats['active_contract_units'] ?? 0 ?></div>
                      <div class="text-muted small"><?= lang('App.active_contract_units') ?></div>
                  </div>
              </div>
          </div>
      </div>

  </div>

  <!-- Global Department Filter -->
  <div class="d-flex align-items-center justify-content-end gap-2 mb-3">
      <label class="form-label mb-0 fw-semibold small text-muted"><i class="fas fa-filter me-1"></i>Filter Departemen:</label>
      <select class="form-select form-select-sm" id="globalDeptFilter" style="width:200px;" onchange="onGlobalDeptFilterChange()">
          <option value="">Semua Departemen</option>
          <?php foreach ($departemen as $d): ?>
          <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
          <?php endforeach; ?>
      </select>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MAIN TABBED CARD                                                  -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div class="card table-card shadow mb-4">
      <div class="card-header p-0 border-bottom">
          <ul class="nav nav-tabs flex-nowrap overflow-auto border-0 px-3 pt-2" role="tablist" id="mainTabs">
              <li class="nav-item me-1">
                  <a class="nav-link active d-flex align-items-center gap-1 py-2 px-3" id="areas-tab" data-bs-toggle="tab" href="#areasTab" role="tab">
                      <i class="fas fa-map-marked-alt"></i>
                      <span><?= lang('App.service_areas') ?></span>
                  </a>
              </li>
              <li class="nav-item me-1">
                  <a class="nav-link d-flex align-items-center gap-1 py-2 px-3" id="employees-tab" data-bs-toggle="tab" href="#employeesTab" role="tab">
                      <i class="fas fa-users"></i>
                      <span><?= lang('App.employees') ?></span>
                  </a>
              </li>
              <li class="nav-item me-1">
                  <a class="nav-link d-flex align-items-center gap-1 py-2 px-3" id="assignments-tab" data-bs-toggle="tab" href="#assignmentsTab" role="tab">
                      <i class="fas fa-link"></i>
                      <span><?= lang('App.assignments') ?></span>
                  </a>
              </li>
              <li class="nav-item me-1">
                  <a class="nav-link d-flex align-items-center gap-1 py-2 px-3" id="unitmap-tab" data-bs-toggle="tab" href="#unitMapTab" role="tab">
                      <span class="material-symbols-rounded" style="font-size:1rem;line-height:1">pin_drop</span>
                      <span><?= lang('App.unit_mapping') ?></span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link d-flex align-items-center gap-1 py-2 px-3" id="analytics-tab" data-bs-toggle="tab" href="#analyticsTab" role="tab">
                      <i class="fas fa-chart-bar"></i>
                      <span><?= lang('App.analytics') ?></span>
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
                              <table id="areasTable" class="table table-hover align-middle dt-responsive">
                                  <thead class="table-light">
                                      <tr>
                                          <th><?= lang('App.area') ?></th>
                                          <th><?= lang('Common.type') ?></th>
                                          <th class="text-center"><?= lang('App.foreman') ?></th>
                                          <th class="text-center"><?= lang('App.mechanic') ?></th>
                                          <th class="text-center"><?= lang('App.customer_location') ?></th>
                                          <th class="text-center"><?= lang('App.unit_count') ?></th>
                                          <th class="text-center"><?= lang('Common.status') ?></th>
                                          <th class="text-center no-sort"><?= lang('Common.actions') ?></th>
                                      </tr>
                                  </thead>
                                  <tbody></tbody>
                              </table>
                          </div>

                          <!-- Area unit detail is now shown in #modalAreaUnits (at bottom of page) -->

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
                              <table id="employeesTable" class="table table-hover align-middle dt-responsive">
                                  <thead class="table-light">
                                      <tr>
                                          <th>NIK</th>
                                          <th><?= lang('App.employee') ?></th>
                                          <th><?= lang('App.role') ?></th>
                                          <th><?= lang('App.department') ?></th>
                                          <th><?= lang('App.assigned_to') ?></th>
                                          <th><?= lang('App.contact') ?></th>
                                          <th class="text-center"><?= lang('Common.status') ?></th>
                                          <th class="text-center no-sort"><?= lang('Common.actions') ?></th>
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
                                                  <p class="mb-0">Pilih area dari panel kiri untuk melihat penugasan</p>
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
                          <div class="row mt-3">
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

                      <!-- ═══════════════════════════════════════════════════════════ -->
                      <!-- UNIT MAPPING TAB (fused from unit_area_mapping.php)         -->
                      <!-- ═══════════════════════════════════════════════════════════ -->
                      <div class="tab-pane fade" id="unitMapTab" role="tabpanel">

                          <!-- Sub-tab navigation -->
                          <div class="mb-3 border-bottom pb-0">
                              <ul class="nav nav-pills gap-1 px-1 pt-1" id="unitMapSubTabs">
                                  <li class="nav-item">
                                      <a class="nav-link nav-link-sm active" data-bs-toggle="pill" href="#subtabLocations" id="subtabLocationsLink">
                                          <i class="bi bi-building me-1"></i> Assign Area per Unit
                                          <span class="badge badge-soft-orange ms-1" id="badgeUnassignedLoc" title="Unit kontrak aktif belum ada area"><?= $unitStats['contract_units_without_area'] ?? 0 ?></span>
                                      </a>
                                  </li>
                                  <li class="nav-item">
                                      <a class="nav-link nav-link-sm" data-bs-toggle="pill" href="#subtabUnassigned" id="subtabUnassignedLink">
                                          <i class="bi bi-question-circle me-1"></i> Semua Unit Tanpa Area
                                          <span class="badge badge-soft-orange ms-1" id="badgeUnassigned" title="Semua unit (termasuk tidak dikontrak) belum ada area"><?= $unitStats['units_without_area'] ?? 0 ?></span>
                                      </a>
                                  </li>
                              </ul>
                          </div>

                          <div class="tab-content px-1 pb-3">

                              <!-- ─── Sub-tab 1: Assign Area per Unit ─────────────────── -->
                              <div class="tab-pane fade show active" id="subtabLocations">

                                  <!-- Bulk assign bar -->
                                  <div class="bg-light border rounded px-3 py-2 mb-3" id="bulkLocBar">
                                      <div class="d-flex align-items-center gap-2 flex-wrap">
                                          <span class="fw-semibold small"><i class="bi bi-check2-square text-primary me-1"></i> <span id="bulkLocCount">0 terpilih</span></span>
                                          <select class="form-select form-select-sm" id="bulkLocArea" style="width:250px">
                                              <option value="">-- Pilih Area --</option>
                                              <?php foreach ($areas as $a): ?>
                                                  <option value="<?= $a['id'] ?>"><?= esc($a['area_code']) ?> — <?= esc($a['area_name']) ?></option>
                                              <?php endforeach; ?>
                                          </select>
                                          <button class="btn btn-sm btn-primary" id="btnBulkAssignLoc" disabled>
                                              <i class="bi bi-check-all me-1"></i> Assign Terpilih
                                          </button>
                                          <a href="#" class="small text-muted ms-2" id="btnSelectAllLoc">Pilih Semua</a>
                                          <a href="#" class="small text-muted" id="btnDeselectAllLoc">Hapus Pilihan</a>
                                      </div>
                                  </div>

                                  <!-- Filters -->
                                  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                      <div class="d-flex gap-2 flex-wrap align-items-center">
                                          <select class="form-select form-select-sm" id="filterLocAreaAssign" style="width:165px">
                                              <option value="unassigned" selected>Belum ter-assign</option>
                                              <option value="assigned">Sudah ter-assign</option>
                                              <option value="all">Semua Unit</option>
                                          </select>
                                          <select class="form-select form-select-sm" id="filterLocCustomer" style="width:190px">
                                              <option value="">Semua Customer</option>
                                          </select>
                                          <select class="form-select form-select-sm" id="filterLocLocation" style="width:190px" disabled>
                                              <option value="">Semua Lokasi</option>
                                          </select>
                                          <select class="form-select form-select-sm" id="filterLocDept" style="width:160px">
                                              <option value="">Semua Departemen</option>
                                              <?php foreach ($departemen as $d): ?>
                                                  <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
                                              <?php endforeach; ?>
                                          </select>
                                      </div>
                                      <button class="btn btn-sm btn-outline-secondary" id="btnRefreshLocationUnits">
                                          <i class="bi bi-arrow-clockwise me-1"></i> <?= lang('Common.refresh') ?>
                                      </button>
                                  </div>

                                  <div class="alert alert-info border-0 py-2 small mb-3">
                                      <i class="bi bi-lightbulb me-1"></i>
                                      <strong>Mapping area per unit:</strong> Setiap unit memiliki area-nya sendiri sesuai departemen. Unit DIESEL dan ELECTRIC di lokasi yang sama dapat memiliki area berbeda.
                                  </div>

                                  <div class="table-responsive">
                                      <table class="table table-hover align-middle" id="tableLocationUnits">
                                          <thead class="table-light">
                                              <tr>
                                                  <th style="width:36px"><input type="checkbox" id="chkSelectAllLoc"></th>
                                                  <th>Customer</th>
                                                  <th><?= lang('App.customer_location') ?></th>
                                                  <th><?= lang('App.unit_number') ?></th>
                                                  <th>Model</th>
                                                  <th>Departemen</th>
                                                  <th style="min-width:200px"><?= lang('App.area') ?></th>
                                                  <th class="text-center"><?= lang('Common.save') ?></th>
                                              </tr>
                                          </thead>
                                          <tbody id="bodyLocationUnits">
                                              <tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
                                          </tbody>
                                      </table>
                                  </div>
                              </div>

                              <!-- ─── Sub-tab 2: Unit Belum Ter-mapping ─────────────── -->
                              <div class="tab-pane fade" id="subtabUnassigned">
                                  <!-- Bulk bar -->
                                  <div class="bg-light border rounded px-3 py-2 mb-3" id="bulkUnitBar">
                                      <div class="d-flex align-items-center gap-2 flex-wrap">
                                          <span class="fw-semibold small"><i class="bi bi-check2-square text-primary me-1"></i> <span id="bulkUnitCount">0 terpilih</span></span>
                                          <select class="form-select form-select-sm" id="bulkUnitArea" style="width:230px">
                                              <option value="">-- Pilih Area --</option>
                                              <?php foreach ($areas as $a): ?>
                                                  <option value="<?= $a['id'] ?>"><?= esc($a['area_code']) ?> — <?= esc($a['area_name']) ?></option>
                                              <?php endforeach; ?>
                                          </select>
                                          <button class="btn btn-sm btn-primary" id="btnBulkAssignUnits" disabled>
                                              <i class="bi bi-check-all me-1"></i> Assign Terpilih
                                          </button>
                                          <a href="#" class="small text-muted ms-2" id="btnSelectAllUnits">Pilih Semua</a>
                                          <a href="#" class="small text-muted" id="btnDeselectAllUnits">Hapus Pilihan</a>
                                      </div>
                                  </div>
                                  <div class="d-flex justify-content-between align-items-center mb-3">
                                      <div class="d-flex gap-2 align-items-center">
                                          <select class="form-select form-select-sm" id="filterUnitContract" style="width:180px">
                                              <option value="all">Semua Unit</option>
                                              <option value="with_contract">Ada Kontrak</option>
                                              <option value="without_contract">Tanpa Kontrak</option>
                                          </select>
                                          <select class="form-select form-select-sm" id="filterUnitDept" style="width:160px">
                                              <option value="all">Semua Departemen</option>
                                              <option value="DIESEL">DIESEL</option>
                                              <option value="ELECTRIC">ELECTRIC</option>
                                              <option value="GASOLINE">GASOLINE</option>
                                              <option value="LPG">LPG</option>
                                              <option value="">Tanpa Dept</option>
                                          </select>
                                      </div>
                                      <button class="btn btn-sm btn-outline-secondary" id="btnRefreshUnassigned">
                                          <i class="bi bi-arrow-clockwise me-1"></i> <?= lang('Common.refresh') ?>
                                      </button>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table table-hover align-middle" id="tableUnassigned">
                                          <thead class="table-light">
                                              <tr>
                                                  <th style="width:36px"><input type="checkbox" id="chkSelectAllUnits"></th>
                                                  <th><?= lang('App.unit_number') ?></th><th>Model</th><th><?= lang('Common.status') ?></th>
                                                  <th>Departemen</th>
                                                  <th>Customer</th><th><?= lang('App.customer_location') ?></th><th><?= lang('App.contract_number') ?></th>
                                              </tr>
                                          </thead>
                                          <tbody id="bodyUnassigned">
                                              <tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
                                          </tbody>
                                      </table>
                                  </div>
                              </div>

                          </div><!-- /tab-content unit map -->
                      </div><!-- /unitMapTab -->

                  </div><!-- /tab-content main -->
              </div><!-- /card-body -->
          </div><!-- /card -->


<!-- Add Area Modal -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
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
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-dialog-scrollable" role="document">
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
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

<!-- Edit Location Info Modal (must be inside content section to be rendered) -->
<div class="modal fade" id="modalEditPic" tabindex="-1" aria-labelledby="modalEditPicLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPicLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Info Lokasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border py-2 mb-3 small">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    <span id="picModalSubtitle" class="text-muted"></span>
                </div>
                <input type="hidden" id="picLocId">
                <p class="fw-semibold text-primary mb-2"><i class="bi bi-person-vcard me-1"></i>Kontak PIC</p>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama PIC <small class="text-muted fw-normal">(Person In Charge)</small></label>
                        <input type="text" id="picContactPerson" class="form-control" placeholder="Contoh: Budi Santoso" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jabatan / Posisi</label>
                        <input type="text" id="picPosition" class="form-control" placeholder="Contoh: Site Manager" maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Telepon</label>
                        <input type="text" id="picPhone" class="form-control" placeholder="Contoh: 0812-xxxx-xxxx" maxlength="20">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" id="picEmail" class="form-control" placeholder="Contoh: pic@company.com" maxlength="128">
                    </div>
                </div>
                <hr class="my-1">
                <p class="fw-semibold text-secondary mb-2 mt-3"><i class="bi bi-geo-alt me-1"></i>Alamat Lokasi</p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea id="picAddress" class="form-control" rows="2" placeholder="Jl. ..." maxlength="500"></textarea>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Kota</label>
                        <input type="text" id="picCity" class="form-control" placeholder="Contoh: Bandung" maxlength="100">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Provinsi</label>
                        <input type="text" id="picProvince" class="form-control" placeholder="Contoh: Jawa Barat" maxlength="100">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Kode Pos</label>
                        <input type="text" id="picPostalCode" class="form-control" placeholder="40xxx" maxlength="10">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSavePic">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Location Info Modal -->

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL: Area Units Detail                                          -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAreaUnits" tabindex="-1" aria-labelledby="modalAreaUnitsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:95vw !important; width:95vw !important;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0" id="modalAreaUnitsLabel">
                    <i class="bi bi-list-ul me-1"></i> Unit di Area
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light" id="btnPrintAreaUnits" title="Print">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-3" id="modalAreaUnitsBody">
                <div class="text-center py-4 text-muted">Memuat data...</div>
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

/* Areas table — rich summary rows */
#areasTable tbody tr {
    cursor: pointer;
}
#areasTable tbody tr td:last-child {
    cursor: default;
}
#areasTable tbody tr.table-active {
    background-color: rgba(13, 110, 253, .08);
}
#areasTable tbody td {
    vertical-align: middle;
}
/* area_code badge inside area column */
.area-code-badge {
    font-family: monospace;
    font-size: .75rem;
    color: #6c757d;
    background: #f0f2f5;
    border-radius: 4px;
    padding: 1px 5px;
    margin-left: 4px;
}

/* Employees table */
#employeesTable tbody tr {
    cursor: pointer;
}
#employeesTable tbody tr td:last-child {
    cursor: default;
}
#employeesTable tbody td {
    vertical-align: middle;
}

/* Unit mapping: green highlight for rows that already have an area */
#tableLocationUnits tbody tr.row-has-area td {
    background-color: rgba(25, 135, 84, 0.06) !important;
}
#tableLocationUnits tbody tr.row-has-area .unit-area-select + .select2-container .select2-selection {
    border-color: #198754;
    background-color: rgba(25, 135, 84, 0.08);
}
.unit-area-assigned-icon {
    color: #198754;
    font-size: 1rem;
    margin-left: 4px;
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let areasTable, employeesTable, locationUnitsTable, unassignedTable;
let allUnassignedIds = [];
const selectedUnits = new Set();
let locInfoMap = {}; // cache of loc data keyed by location_id, for PIC modal
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
    
    // Lazy load unit mapping when that tab is opened
    if (targetTab === 'unitMapTab') {
      // Ensure sub-tab 1 is active — the pill's shown.bs.tab will fire loadLocationUnits
      const $locLink = $('#subtabLocationsLink');
      if ($locLink.hasClass('active')) {
        // Already active — pill event won't fire, so load manually
        if ($('#bodyLocationUnits tr td[colspan]').length) loadLocationUnits();
      } else {
        bootstrap.Tab.getOrCreateInstance($locLink[0]).show();
        // loadLocationUnits will be triggered by the pill's shown.bs.tab handler
      }
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
          d.departemen_id = $('#globalDeptFilter').val();
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
      // Column 1: Area (code + name combined)
      { 
        data: 'area_name',
        render: function(d, type, row) {
          return `<span class="fw-medium">${d || ''}</span><span class="area-code-badge ms-1">${row.area_code || ''}</span>`;
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
      // Column 3: Foreman
      {
        data: 'foreman_count',
        className: 'text-center',
        render: function(d) {
          const count = d || 0;
          return count > 0
            ? `<span class="badge badge-soft-green">${count}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
        }
      },
      // Column 4: Mekanik
      {
        data: 'mechanic_count',
        className: 'text-center',
        render: function(d) {
          const count = d || 0;
          return count > 0
            ? `<span class="badge badge-soft-blue">${count}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
        }
      },
      // Column 5: Lokasi Customer
      {
        data: 'location_count',
        className: 'text-center',
        render: function(d) {
          const n = d || 0;
          return n > 0
            ? `<span class="badge badge-soft-cyan">${n}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
        }
      },
      // Column 6: Jumlah Unit
      {
        data: 'unit_count',
        className: 'text-center',
        render: function(d) {
          const n = d || 0;
          return n > 0
            ? `<span class="badge badge-soft-blue" style="font-size:.85em">${n}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
        }
      },
      // Column 7: Status
      { 
        data: 'is_active',
        className: 'text-center',
        render: function(data) {
          return data == 1 
            ? '<span class="badge badge-soft-green">Active</span>' 
            : '<span class="badge badge-soft-gray">Inactive</span>';
        }
      },
      // Column 8: Actions
      {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: function(data, type, row) {
          return `<button class="btn btn-sm btn-outline-primary btn-edit-area"
                    data-area-code="${row.area_code}" title="Edit Area"
                    onclick="event.stopPropagation(); viewAreaDetail('${row.area_code}', null, ${row.id})">
                    <i class="fas fa-pencil-alt"></i>
                  </button>`;
        }
      }
    ],
    order: [[0, 'asc']],
    pageLength: 25,
    language: {
      emptyTable: "Belum ada area",
      info: "Menampilkan _START_ – _END_ dari _TOTAL_ area",
      infoEmpty: "Menampilkan 0 area",
      search: "Cari:",
      searchPlaceholder: "Cari area...",
      lengthMenu: "Tampilkan _MENU_ entri"
    },
    columnDefs: [{ orderable: false, targets: [2, 3, 7] }],
    drawCallback: function() {
      // Row click → show unit modal
      $('#areasTable tbody').off('click', 'tr').on('click', 'tr', function(e) {
        if ($(e.target).closest('.btn-edit-area').length) return;
        const data = areasTable.row(this).data();
        if (!data) return;
        $('#areasTable tbody tr').removeClass('table-active');
        $(this).addClass('table-active');
        showAreaUnitsModal(data.id, data.area_code, data.area_name);
      });
    }
  });

  setTimeout(function() {
    $('div.dataTables_filter input').attr('placeholder', 'Cari area...');
  }, 100);

  } catch (error) {
    console.error('Error initializing Areas DataTable:', error);
    $('#areasTable').html('<div class="alert alert-danger">Error memuat data area. Silakan refresh halaman.</div>');
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
        // Column 1: NIK
        {
          data: 'staff_code',
          render: function(d) {
            return d ? `<span class="area-code-badge">${d}</span>` : '<span class="text-muted">-</span>';
          }
        },
        // Column 2: Nama
        {
          data: 'staff_name',
          render: function(d) {
            return `<span class="fw-medium">${d || '-'}</span>`;
          }
        },
        // Column 3: Role
        {
          data: 'staff_role',
          render: function(data) {
            if (!data) return '<span class="badge badge-soft-gray">-</span>';
            const roleColors = {
              'ADMIN': 'blue', 'SUPERVISOR': 'red', 'FOREMAN': 'yellow',
              'MECHANIC': 'green', 'MECHANIC_SERVICE_AREA': 'cyan',
              'MECHANIC_UNIT_PREP': 'purple', 'MECHANIC_FABRICATION': 'gray',
              'HELPER': 'orange'
            };
            const cls = roleColors[data] || 'gray';
            const label = data.replace(/_/g, ' ');
            return `<span class="badge badge-soft-${cls}">${label}</span>`;
          }
        },
        // Column 3: Departemen
        {
          data: 'departemen',
          render: function(d) {
            return d && d !== '-'
              ? `<span class="text-dark">${d}</span>`
              : '<span class="text-muted">-</span>';
          }
        },
        // Column 4: Penugasan Area
        {
          data: 'area_assignments',
          orderable: false,
          searchable: false,
          render: function(data) {
            if (!data || data.length === 0) {
              return '<span class="badge badge-soft-yellow"><i class="bi bi-exclamation-circle me-1"></i>Unassigned</span>';
            }
            return data.map(a => {
              const cls = a.area_type === 'MILL' ? 'badge-soft-green' : 'badge-soft-blue';
              return `<span class="badge ${cls} me-1" title="${a.area_name}">${a.area_code}</span>`;
            }).join('');
          }
        },
        // Column 5: Kontak
        {
          data: 'phone',
          orderable: false,
          render: function(d, type, row) {
            const phone = d ? `<div class="small"><i class="fas fa-phone text-muted me-1"></i>${d}</div>` : '';
            const email = row.email ? `<div class="small"><i class="fas fa-envelope text-muted me-1"></i>${row.email}</div>` : '';
            return (phone || email) ? phone + email : '<span class="text-muted">-</span>';
          }
        },
        // Column 6: Status
        {
          data: 'is_active',
          className: 'text-center',
          render: function(data) {
            return data == 1
              ? '<span class="badge badge-soft-green">Active</span>'
              : '<span class="badge badge-soft-gray">Inactive</span>';
          }
        },
        // Column 7: Aksi
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          render: function(data, type, row) {
            return `<button class="btn btn-sm btn-outline-primary"
                      title="Detail / Edit"
                      onclick="event.stopPropagation(); viewEmployeeDetail(${row.id})">
                      <i class="fas fa-pencil-alt"></i>
                    </button>`;
          }
        }
      ],
      order: [[1, 'asc']],
      pageLength: 25,
      columnDefs: [{ orderable: false, targets: [4, 5, 7] }],
      language: {
        emptyTable: "Belum ada karyawan",
        info: "Menampilkan _START_ – _END_ dari _TOTAL_ karyawan",
        infoEmpty: "Menampilkan 0 karyawan",
        search: "Cari:",
        searchPlaceholder: "Cari karyawan...",
        lengthMenu: "Tampilkan _MENU_ entri"
      },
      drawCallback: function() {
        $('#employeesTable tbody').off('click', 'tr').on('click', 'tr', function(e) {
          if ($(e.target).closest('.btn').length) return;
          const data = employeesTable.row(this).data();
          if (data && data.id) viewEmployeeDetail(data.id);
        });
      }
    });

    setTimeout(function() {
      $('#employeesTable_wrapper div.dataTables_filter input').attr('placeholder', 'Cari karyawan...');
    }, 100);

  } catch (error) {
    console.error('Error initializing Employees DataTable:', error);
    $('#employeesTable').html('<div class="alert alert-danger">Error memuat data karyawan. Silakan refresh halaman.</div>');
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
    dataType: 'json',
    data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
    success: function(resp){
      if (resp && resp.success) {
        notify('Area berhasil dihapus','success');
        refreshAreas();
      } else {
        notify((resp && resp.message) || 'Gagal menghapus area','error');
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
    dataType: 'json',
    data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
    success: function(resp){
      if (resp && resp.success) {
        notify('Karyawan berhasil dinonaktifkan','success');
        refreshEmployees();
      } else {
        notify((resp && resp.message) || 'Gagal menonaktifkan karyawan','error');
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
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-arrow-left"></i> Pilih area dari panel kiri</div>');
    $('#areaAssignmentSummary').html('');
    return;
  }
  
  $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i><br>Memuat data penugasan...</div>');
  
  const timestamp = Date.now();
  const url = `<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}?_=${timestamp}`;
  
  $.getJSON(url, function(resp){
    if (!resp.success) {
      $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Gagal memuat data penugasan</div>');
      return;
    }
    const assignments = resp.data || [];
    renderAssignmentsTable(assignments);
    updateAreaAssignmentSummary(assignments);
  }).fail(function(xhr, status, error) {
    $('#areaAssignmentsTable').html('<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i><br>Koneksi gagal<br><button class="btn btn-sm btn-primary mt-2" onclick="loadAreaAssignments()">Coba Lagi</button></div>');
  });
}

function forceRefreshAssignments() {
  const areaId = $('#assignAreaSelect').val();
  if (areaId) {
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-4"><i class="fas fa-sync fa-spin"></i><br>Memperbarui...</div>');
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
    $('#areaAssignmentsTable').html('<div class="text-center text-muted py-5"><i class="fas fa-users-slash fa-2x mb-2"></i><br>Tidak ada penugasan untuk role yang dipilih</div>');
    return;
  }

  const roleSoftClass = r => ({
    'ADMIN': 'badge-soft-blue', 'SUPERVISOR': 'badge-soft-red',
    'FOREMAN': 'badge-soft-yellow', 'MECHANIC': 'badge-soft-green',
    'MECHANIC_SERVICE_AREA': 'badge-soft-cyan', 'MECHANIC_UNIT_PREP': 'badge-soft-purple',
    'MECHANIC_FABRICATION': 'badge-soft-gray', 'HELPER': 'badge-soft-orange'
  })[r] || 'badge-soft-gray';

  const assignTypeSoftClass = t => ({
    'PRIMARY': 'badge-soft-green', 'BACKUP': 'badge-soft-yellow', 'TEMPORARY': 'badge-soft-cyan'
  })[t] || 'badge-soft-gray';

  let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
  html += '<thead class="table-light"><tr><th>Karyawan</th><th>Role</th><th>Tipe</th><th>Departemen</th><th>Mulai</th><th>Selesai</th><th>Aksi</th></tr></thead><tbody>';

  filtered.forEach(a => {
    const role = a.staff_role || a.role || 'UNKNOWN';
    const roleLabel = role.replace(/_/g, ' ');
    const deptScope = a.department_scope || '-';
    html += `<tr id="assignment-row-${a.id}">
      <td>
        <span class="fw-medium">${a.staff_name}</span>
        <span class="area-code-badge ms-1">${a.staff_code || ''}</span>
      </td>
      <td><span class="badge ${roleSoftClass(role)}">${roleLabel}</span></td>
      <td><span class="badge ${assignTypeSoftClass(a.assignment_type)}">${a.assignment_type}</span></td>
      <td><small class="text-muted">${deptScope}</small></td>
      <td><small>${a.start_date ? new Date(a.start_date).toLocaleDateString('id-ID') : '-'}</small></td>
      <td><small>${a.end_date ? new Date(a.end_date).toLocaleDateString('id-ID') : '<span class="badge badge-soft-cyan">Open</span>'}</small></td>
      <td>
        <button class='btn btn-sm btn-outline-primary me-1' onclick='editAssignment(${a.id})' title="Edit" aria-label="Edit"><i class='fas fa-edit'></i></button>
        <button class='btn btn-sm btn-outline-danger' onclick='removeAssignment(${a.id})' title="Hapus" aria-label="Hapus"><i class='fas fa-trash'></i></button>
      </td>
    </tr>`;
  });
  html += '</tbody></table></div>';
  $('#areaAssignmentsTable').html(html);
}

function updateAreaAssignmentSummary(assignments) {
  if (!assignments || assignments.length === 0) {
    $('#areaAssignmentSummary').html('<div class="text-muted small py-2"><i class="fas fa-info-circle"></i> Belum ada penugasan</div>');
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
  
  const roleSoftClass = r => ({
    'ADMIN': 'badge-soft-blue', 'SUPERVISOR': 'badge-soft-red',
    'FOREMAN': 'badge-soft-yellow', 'MECHANIC': 'badge-soft-green',
    'MECHANIC_SERVICE_AREA': 'badge-soft-cyan', 'MECHANIC_UNIT_PREP': 'badge-soft-purple',
    'MECHANIC_FABRICATION': 'badge-soft-gray', 'HELPER': 'badge-soft-orange'
  })[r] || 'badge-soft-gray';

  let html = '<div class="mt-2">';

  Object.keys(roles).forEach(r => {
    const v = roles[r];
    const total = v.primary + v.backup + v.temporary;
    const roleLabel = r.replace(/_/g, ' ');
    html += `<div class="d-flex justify-content-between align-items-center py-1 border-bottom">
      <span class="badge ${roleSoftClass(r)} me-1">${roleLabel}</span>
      <div class="d-flex gap-1">
        <span class="badge badge-soft-green" title="Primary">${v.primary}P</span>
        <span class="badge badge-soft-yellow" title="Backup">${v.backup}B</span>
        <span class="badge badge-soft-cyan" title="Temporary">${v.temporary}T</span>
      </div>
    </div>`;
  });

  html += '</div>';
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

function viewAreaDetail(areaCode, areaData = null, areaId = null) {
  if (areaData) {
    currentAreaId = areaData.id || areaData.area_id;
    currentAreaData = areaData;
    $('#area_detail_code').text(areaData.area_code || '-');
    $('#area_detail_name').text(areaData.area_name || '-');
    $('#area_detail_description').text(areaData.description || '-');
    $('#area_detail_customers').text(areaData.customers_count || 0);
    $('#area_detail_employees').text(areaData.employees_count || 0);
    $('#area_detail_created').text(areaData.created_at ? new Date(areaData.created_at).toLocaleDateString('en-GB') : '-');
    loadAreaDetailAssignments(currentAreaId);
    $('#areaDetailModal').modal('show');
  } else if (areaId) {
    // Reset fields while loading
    $('#area_detail_code').text('-');
    $('#area_detail_name').text('-');
    $('#area_detail_description').text('-');
    $('#area_detail_customers').text('-');
    $('#area_detail_employees').text('-');
    $('#area_detail_created').text('-');
    $('#area_detail_assignments').html('<div class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Memuat data...</div>');
    $('#areaDetailModal').modal('show');
    $.getJSON(`<?= base_url('service/area-management/showArea') ?>/${areaId}`, function(resp) {
      if (!resp.success) { notify(resp.message || 'Gagal memuat data area', 'error'); return; }
      const area = resp.data.area;
      currentAreaId = area.id;
      currentAreaData = {
        id: area.id,
        area_code: area.area_code,
        area_name: area.area_name,
        description: area.description || '',
        area_type: area.area_type || 'MILL',
        departemen_id: area.departemen_id || '',
        customers_count: 0,
        created_at: area.created_at
      };
      $('#area_detail_code').text(area.area_code || '-');
      $('#area_detail_name').text(area.area_name || '-');
      $('#area_detail_description').text(area.description || '-');
      $('#area_detail_customers').text('-');
      $('#area_detail_created').text(area.created_at ? new Date(area.created_at).toLocaleDateString('en-GB') : '-');
      loadAreaDetailAssignments(currentAreaId);
    }).fail(function() {
      notify('Gagal memuat data area', 'error');
    });
  }
}

function loadAreaDetailAssignments(areaId) {
  $('#area_detail_assignments').html('<div class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Memuat data...</div>');
  
  $.get(`<?= base_url('service/area-management/getAreaAssignments') ?>/${areaId}`, function(response) {
    if (response.success && response.data && response.data.length > 0) {
      $('#area_detail_employees').text(response.data.length);
      let assignmentsHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Karyawan</th><th>Role</th><th>Tipe</th><th>Status</th></tr></thead><tbody>';
      response.data.forEach(assign => {
        assignmentsHtml += `<tr>
          <td>${assign.staff_name}</td>
          <td><strong class="text-${roleBadgeColor(assign.staff_role)}">${assign.staff_role}</strong></td>
          <td><strong class="text-${assign.assignment_type === 'PRIMARY' ? 'success' : 'secondary'}">${assign.assignment_type}</strong></td>
          <td><strong class="text-${assign.is_active ? 'success' : 'danger'}">${assign.is_active ? 'Aktif' : 'Nonaktif'}</strong></td>
        </tr>`;
      });
      assignmentsHtml += '</tbody></table></div>';
      $('#area_detail_assignments').html(assignmentsHtml);
    } else {
      $('#area_detail_employees').text(0);
      $('#area_detail_assignments').html('<div class="text-muted">Belum ada karyawan yang ditugaskan.</div>');
    }
  }, 'json').fail(function() {
    $('#area_detail_assignments').html('<div class="text-danger">Gagal memuat data karyawan.</div>');
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
  
  const areaName = currentAreaData?.area_name || 'this area';
  const idToDelete = currentAreaId;

  OptimaConfirm.danger({
    title: 'Hapus Area?',
    text: `Area "${areaName}" akan dihapus. Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Ya, Hapus!',
    cancelText: window.lang('cancel'),
    onConfirm: function() {
      // Close the detail modal first, then wait for the animation to finish
      // before sending the AJAX to avoid Bootstrap modal reuse conflicts
      const detailModal = document.getElementById('areaDetailModal');
      const doDelete = function() {
        $.ajax({
          url: `<?= base_url('service/area-management/deleteArea') ?>/${idToDelete}`,
          type: 'POST',
          data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
          success: function(resp) {
            if (resp && resp.success) {
              notify('Area berhasil dihapus', 'success');
              refreshAreas();
            } else {
              notify((resp && resp.message) || 'Gagal menghapus area', 'error');
            }
          },
          error: function(xhr) {
            notify('Error: ' + (xhr.responseJSON?.message || xhr.status), 'error');
          }
        });
      };

      if (detailModal && bootstrap) {
        const instance = bootstrap.Modal.getInstance(detailModal);
        if (instance) {
          detailModal.addEventListener('hidden.bs.modal', doDelete, { once: true });
          instance.hide();
        } else {
          doDelete();
        }
      } else {
        doDelete();
      }
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

  const idToDelete = currentEmployeeId;

  OptimaConfirm.danger({
    title: 'Hapus Karyawan?',
    text: 'Karyawan akan dinonaktifkan (bukan dihapus permanen).',
    confirmText: 'Ya, Hapus!',
    cancelText: window.lang('cancel'),
    onConfirm: function() {
      const detailModal = document.getElementById('employeeDetailModal');
      const doDelete = function() {
        $.ajax({
          url: `<?= base_url('service/area-management/deleteEmployee') ?>/${idToDelete}`,
          type: 'POST',
          data: {[window.csrfTokenName]: window.getCsrfToken(), '_method': 'DELETE'},
          success: function(resp) {
            if (resp && resp.success) {
              notify('Karyawan berhasil dinonaktifkan', 'success');
              refreshEmployees();
            } else {
              notify((resp && resp.message) || 'Gagal menonaktifkan karyawan', 'error');
            }
          },
          error: function(xhr) {
            notify('Error: ' + (xhr.responseJSON?.message || xhr.status), 'error');
          }
        });
      };

      if (detailModal && bootstrap) {
        const instance = bootstrap.Modal.getInstance(detailModal);
        if (instance) {
          detailModal.addEventListener('hidden.bs.modal', doDelete, { once: true });
          instance.hide();
        } else {
          doDelete();
        }
      } else {
        doDelete();
      }
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

function onGlobalDeptFilterChange() {
  if (areasTable) areasTable.ajax.reload();
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
    case 'unitMapTab':
      if ($('#bodyLocationUnits tr td[colspan]').length) loadLocationUnits();
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

/* ===================== UNIT MAPPING FUNCTIONS ===================== */

const allAreas = <?= json_encode($areas) ?>;

// User dept scope from server: null = full access, otherwise {areas:[], departments:[], has_full_access: bool}
const userDeptScope = <?= json_encode($userDeptScope) ?>;

/**
 * Build <option> HTML for area select, filtered by user dept scope.
 * Logic: MILL areas always visible; CENTRAL areas only if dept matches user scope.
 */
function buildAreaOptions(selectedId, selectedCode = null, selectedName = null) {
    let html = '<option value="">-- Tidak Ada --</option>';
    let selectedExists = false;
    allAreas.forEach(a => {
        // Apply dept scope filter: null = full access, show all
        if (userDeptScope !== null && !userDeptScope.has_full_access) {
            if (a.area_type === 'CENTRAL') {
                // Only show CENTRAL area if departemen_id is in user's allowed departments
                const allowedDepts = userDeptScope.departments || [];
                if (allowedDepts.length > 0 && !allowedDepts.includes(parseInt(a.departemen_id))) {
                    return; // skip this area
                }
            }
            // MILL areas: always include
        }
        const isSel = (selectedId && parseInt(selectedId) === a.id);
        if (isSel) {
            selectedExists = true;
        }
        const sel = isSel ? ' selected' : '';
        const typeTag = a.area_type === 'CENTRAL' ? ' [C]' : '';
        html += `<option value="${a.id}"${sel}>[${a.area_code}${typeTag}] ${a.area_name}</option>`;
    });
    // Jika area terpasang ada tetapi tidak masuk scope dropdown user, tetap tampilkan agar user tahu area existing.
    if (selectedId && !selectedExists) {
        const label = (selectedCode && selectedName)
            ? `[${selectedCode}] ${selectedName}`
            : `Area ID ${selectedId}`;
        html += `<option value="${selectedId}" selected>${label}</option>`;
    }
    return html;
}

function csrfData(extra) {
    const d = {};
    d[window.csrfTokenName] = window.getCsrfToken();
    return Object.assign(d, extra);
}

// ----------------------------------------------------------------
// loadAreaSummary — kept as stub (table removed; DataTable now shows summary inline)
// ----------------------------------------------------------------
function loadAreaSummary() {
    if (areasTable) areasTable.ajax.reload(null, false);
}

$(document).on('click', '.area-row', function() {
    const areaId   = $(this).data('area-id');
    const areaName = $(this).data('area-name');
    const areaCode = $(this).data('area-code') || '';
    $('.area-row').removeClass('table-active');
    $(this).addClass('table-active');
    showAreaUnitsModal(areaId, areaCode, areaName);
});

// ----------------------------------------------------------------
// Unit Mapping sub-tab 1: Assign Area per Unit
// ----------------------------------------------------------------
let locSelectedUnits = new Set(); // unit IDs selected via checkbox
let locLoadXhr = null;
let locLoadReqId = 0;
const TABLE_LOCATION_LOADING_HTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div><span class="text-muted">Memuat data...</span></td></tr>';

function updateBulkLocBar() {
    const count = locSelectedUnits.size;
    $('#bulkLocCount').text(count + ' terpilih');
    $('#btnBulkAssignLoc').prop('disabled', count === 0);
}

function loadLocationUnits() {
    if ($.fn.DataTable.isDataTable('#tableLocationUnits')) {
        $('#tableLocationUnits').DataTable().destroy();
    }
    locSelectedUnits.clear();
    updateBulkLocBar();
    locInfoMap = {};

    locationUnitsTable = $('#tableLocationUnits').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: BASE_URL + 'service/area-management/unit-mapping/getCustomerLocations',
            type: 'POST',
            data: function(d) {
                // Read live filter values on every request (sort, page, search all re-use these)
                d[window.csrfTokenName] = window.getCsrfToken();
                d.dept_filter   = $('#filterLocDept').val();
                d.area_filter   = $('#filterLocAreaAssign').val();
                d.customer_name = $('#filterLocCustomer').val();
                d.location_id   = $('#filterLocLocation').val();
                return d;
            },
            dataSrc: function(json) {
                // Populate customer dropdown from server metadata (all customers for current base filter)
                if (json.allCustomers && json.allCustomers.length) {
                    const prevCust = $('#filterLocCustomer').val();
                    $('#filterLocCustomer').find('option[value!=""]').remove();
                    json.allCustomers.forEach(function(c) {
                        const opt = $('<option>', { value: c, text: c });
                        if (c === prevCust) { opt.prop('selected', true); }
                        $('#filterLocCustomer').append(opt);
                    });
                    // Refresh Select2 display after options are rebuilt
                    if ($('#filterLocCustomer').hasClass('select2-hidden-accessible')) {
                        $('#filterLocCustomer').trigger('change.select2');
                    }
                }

                // Build locInfoMap from current page rows (accumulates across page navigations)
                (json.data || []).forEach(function(row) {
                    if (row.location_id && !locInfoMap[row.location_id]) {
                        locInfoMap[row.location_id] = {
                            location_name:  row.location_name,
                            customer_name:  row.customer_name,
                            contact_person: row.contact_person || '',
                            phone:          row.phone || '',
                        };
                    }
                });

                // Refresh location cascade dropdown silently (no event trigger)
                const currentCustomer = $('#filterLocCustomer').val();
                const $locSel = $('#filterLocLocation');
                if (currentCustomer) {
                    const prevLoc = $locSel.val();
                    $locSel.empty().append('<option value="">Semua Lokasi</option>').prop('disabled', false);
                    const seen = new Set();
                    Object.keys(locInfoMap).forEach(function(locId) {
                        const loc = locInfoMap[locId];
                        if (loc.customer_name === currentCustomer && !seen.has(locId)) {
                            seen.add(locId);
                            const opt = $('<option>', { value: locId, text: loc.location_name });
                            if (String(locId) === String(prevLoc)) { opt.prop('selected', true); }
                            $locSel.append(opt);
                        }
                    });
                }

                return json.data;
            }
        },
        pageLength: 25,
        order: [[1, 'asc'], [2, 'asc']],
        columns: [
            {
                data: null, orderable: false, className: 'text-center',
                render: function(_, __, row) {
                    return '<input type="checkbox" class="chk-loc-unit" data-unit-id="' + row.id_inventory_unit + '">';
                }
            },
            {
                data: 'customer_name',
                render: function(val) { return '<strong>' + (val || '-') + '</strong>'; }
            },
            {
                data: 'location_name',
                render: function(val, _, row) {
                    const hasPic   = row.contact_person;
                    const picTitle = hasPic
                        ? 'PIC: ' + row.contact_person + (row.phone ? ' \u00b7 ' + row.phone : '')
                        : 'Belum ada data PIC \u00b7 klik untuk mengisi';
                    const picIcon  = hasPic
                        ? '<i class="bi bi-person-check-fill text-success"></i>'
                        : '<i class="bi bi-person-plus text-muted"></i>';
                    return (val || '-')
                        + ' <button class="btn btn-link btn-sm p-0 ms-1 btn-edit-pic"'
                        + ' data-loc-id="' + row.location_id + '" title="' + picTitle + '">'
                        + picIcon + '</button>';
                }
            },
            {
                data: 'no_unit',
                render: function(val) { return '<strong>' + (val || '-') + '</strong>'; }
            },
            {
                data: 'model',
                render: function(val) { return '<small>' + (val || '-') + '</small>'; }
            },
            {
                data: 'nama_departemen',
                render: function(val) { return '<small>' + (val || '-') + '</small>'; }
            },
            {
                data: 'area_id', orderable: false,
                render: function(val, _, row) {
                    return '<select class="form-select form-select-sm unit-area-select"'
                        + ' data-unit-id="' + row.id_inventory_unit + '">'
                        + buildAreaOptions(val, row.area_code, row.area_name)
                        + '</select>';
                }
            },
            {
                data: null, orderable: false, className: 'text-center',
                render: function(_, __, row) {
                    return '<button class="btn btn-sm btn-primary btn-save-unit-area"'
                        + ' data-unit-id="' + row.id_inventory_unit + '" title="Simpan Area">'
                        + '<i class="bi bi-check-lg"></i></button>';
                }
            }
        ],
        createdRow: function(row, data) {
            $(row).attr('data-unit-id', data.id_inventory_unit)
                  .attr('data-loc-id', data.location_id);
            if (locSelectedUnits.has(parseInt(data.id_inventory_unit))) {
                $(row).find('.chk-loc-unit').prop('checked', true);
            }
            // Mark rows that already have an area with a green class
            if (data.area_id) {
                $(row).addClass('row-has-area');
            }
        },
        language: {
            emptyTable:    'Tidak ada data unit aktif',
            info:          'Menampilkan _START_ \u2013 _END_ dari _TOTAL_ unit',
            infoEmpty:     '0 unit',
            infoFiltered:  '(difilter dari _MAX_ unit)',
            search:        'Cari:',
            searchPlaceholder: 'Cari unit / customer / lokasi...',
            lengthMenu:    'Tampilkan _MENU_ entri',
            paginate:      { previous: '\u00ab', next: '\u00bb' },
            processing:    '<div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat...'
        },
        drawCallback: function() {
            const total   = $('#bodyLocationUnits .chk-loc-unit').length;
            const checked = $('#bodyLocationUnits .chk-loc-unit:checked').length;
            $('#chkSelectAllLoc')
                .prop('indeterminate', checked > 0 && checked < total)
                .prop('checked', total > 0 && checked === total);

            // Initialize Select2 on per-row area selects
            $('#tableLocationUnits .unit-area-select').each(function() {
                const $sel = $(this);
                if ($sel.hasClass('select2-hidden-accessible')) {
                    $sel.select2('destroy');
                }
                $sel.select2({
                    dropdownParent: $(document.body),
                    width: '100%',
                    placeholder: '-- Tidak Ada --',
                    allowClear: true
                });
                // Toggle green row class when area changes
                $sel.off('change.greenStyle').on('change.greenStyle', function() {
                    const $row = $(this).closest('tr');
                    if ($(this).val()) {
                        $row.addClass('row-has-area');
                    } else {
                        $row.removeClass('row-has-area');
                    }
                });
            });
        }
    });
}

// Customer → Location cascade
$('#filterLocCustomer').on('change', function() {
    const customer = $(this).val();
    const prevLoc  = $('#filterLocLocation').val();
    const $locSel  = $('#filterLocLocation');
    $locSel.empty().append('<option value="">Semua Lokasi</option>').prop('disabled', !customer);
    if (!customer) return;
    // Collect unique locations from locInfoMap
    const seen = new Set();
    Object.entries(locInfoMap).forEach(([locId, loc]) => {
        if (loc.customer_name === customer && !seen.has(locId)) {
            seen.add(locId);
            const selected = (String(locId) === String(prevLoc)) ? ' selected' : '';
            $locSel.append(`<option value="${locId}"${selected}>${loc.location_name}</option>`);
        }
    });
});

$('#btnRefreshLocationUnits').on('click', function() { loadLocationUnits(); });
$('#filterLocAreaAssign').on('change', function() {
    // Hindari filter turunan nyangkut saat ganti assigned/unassigned
    if ($('#filterLocCustomer').hasClass('select2-hidden-accessible')) {
        $('#filterLocCustomer').val(null).trigger('change');
    } else {
        $('#filterLocCustomer').val('');
    }
    $('#filterLocLocation').val('').prop('disabled', true).empty().append('<option value="">Semua Lokasi</option>');
    loadLocationUnits();
});
$('#filterLocCustomer, #filterLocLocation, #filterLocDept').on('change', function() {
    loadLocationUnits();
});

// ----------------------------------------------------------------
// Initialize Select2 on static bulk-area and customer-filter selects
// ----------------------------------------------------------------
function initStaticAreaSelect2() {
    if (typeof $.fn.select2 !== 'function') {
        // Select2 not yet loaded (deferred script), wait for window.load
        $(window).one('load.initAreaSelect2', function() { initStaticAreaSelect2(); });
        return;
    }
    $('#bulkLocArea').select2({
        placeholder: '-- Pilih Area --',
        allowClear: true,
        width: '250px'
    });
    $('#bulkUnitArea').select2({
        placeholder: '-- Pilih Area --',
        allowClear: true,
        width: '230px'
    });
    $('#filterLocCustomer').select2({
        placeholder: 'Semua Customer',
        allowClear: true,
        width: '190px'
    });
}
initStaticAreaSelect2();

// Select all (current page)
$(document).on('change', '#chkSelectAllLoc', function() {
    const checked = $(this).is(':checked');
    $('#bodyLocationUnits .chk-loc-unit').each(function() {
        const uid = parseInt($(this).data('unit-id'));
        $(this).prop('checked', checked);
        if (checked) locSelectedUnits.add(uid); else locSelectedUnits.delete(uid);
    });
    updateBulkLocBar();
});

// Individual checkbox
$(document).on('change', '.chk-loc-unit', function() {
    const uid = parseInt($(this).data('unit-id'));
    if ($(this).is(':checked')) locSelectedUnits.add(uid); else locSelectedUnits.delete(uid);
    updateBulkLocBar();
    const total   = $('#bodyLocationUnits .chk-loc-unit').length;
    const checked = $('#bodyLocationUnits .chk-loc-unit:checked').length;
    $('#chkSelectAllLoc')
        .prop('indeterminate', checked > 0 && checked < total)
        .prop('checked', total > 0 && checked === total);
});

$('#btnSelectAllLoc').on('click', function(e) {
    e.preventDefault();
    if (locationUnitsTable) {
        locationUnitsTable.rows({ filter: 'applied' }).nodes().each(function() {
            const uid = parseInt($(this).find('.chk-loc-unit').data('unit-id'));
            if (uid) locSelectedUnits.add(uid);
            $(this).find('.chk-loc-unit').prop('checked', true);
        });
    }
    $('#chkSelectAllLoc').prop('checked', true).prop('indeterminate', false);
    updateBulkLocBar();
});

$('#btnDeselectAllLoc').on('click', function(e) {
    e.preventDefault();
    locSelectedUnits.clear();
    $('#bodyLocationUnits .chk-loc-unit').prop('checked', false);
    $('#chkSelectAllLoc').prop('checked', false).prop('indeterminate', false);
    updateBulkLocBar();
});

// Bulk assign
$('#btnBulkAssignLoc').on('click', function() {
    const areaId = $('#bulkLocArea').val();
    if (!areaId) { showToast('warning', 'Pilih area terlebih dahulu'); return; }
    const unitIds = Array.from(locSelectedUnits);
    if (!unitIds.length) return;
    const areaName = $('#bulkLocArea option:selected').text();
    showConfirm(
        `Assign <strong>${unitIds.length} unit</strong> ke area <strong>${areaName}</strong>?`,
        'Konfirmasi Bulk Assign',
        function() {
            const btn = $('#btnBulkAssignLoc');
            btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Menyimpan...');
            $.post(BASE_URL + 'service/area-management/unit-mapping/batchAssignUnits',
                csrfData({ unit_ids: JSON.stringify(unitIds), area_id: areaId }),
                function(resp) {
                    btn.html('<i class="bi bi-check-all me-1"></i> Assign Terpilih');
                    if (resp.success) {
                        showToast('success', resp.message);
                        locSelectedUnits.clear();
                        loadLocationUnits();
                        updateStats();
                        // Update badges
                        const locB = parseInt($('#badgeUnassignedLoc').text()) || 0;
                        const all  = parseInt($('#badgeUnassigned').text()) || 0;
                        const n    = unitIds.length;
                        if (locB > 0) $('#badgeUnassignedLoc').text(Math.max(0, locB - n));
                        if (all  > 0) $('#badgeUnassigned').text(Math.max(0, all - n));
                    } else {
                        btn.prop('disabled', false);
                        showToast('danger', resp.message || 'Gagal menyimpan');
                    }
                }
            ).fail(() => {
                btn.prop('disabled', false).html('<i class="bi bi-check-all me-1"></i> Assign Terpilih');
                showToast('danger', 'Koneksi gagal. Coba lagi.');
            });
        }
    );
});

// Save area for a single unit
$(document).on('click', '.btn-save-unit-area', function() {
    const unitId = $(this).data('unit-id');
    const areaId = $(`.unit-area-select[data-unit-id="${unitId}"]`).val();
    const btn    = $(this);
    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm"></div>');

    $.post(BASE_URL + 'service/area-management/unit-mapping/assignUnitArea',
        csrfData({ unit_id: unitId, area_id: areaId }),
        function(resp) {
            if (resp.success) {
                showToast('success', resp.message);
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
                btn.removeClass('btn-primary').addClass('btn-success');
                setTimeout(() => btn.removeClass('btn-success').addClass('btn-primary'), 2000);
                updateStats();
                // Decrement both badges by 1 (unit just got assigned)
                const locBadge = parseInt($('#badgeUnassignedLoc').text()) || 0;
                if (locBadge > 0) $('#badgeUnassignedLoc').text(locBadge - 1);
                const allBadge = parseInt($('#badgeUnassigned').text()) || 0;
                if (allBadge > 0) $('#badgeUnassigned').text(allBadge - 1);
                // Uncheck the saved unit's checkbox (if any)
                const chkBox = $(`.chk-loc-unit[data-unit-id="${unitId}"]`);
                if (chkBox.is(':checked')) {
                    chkBox.prop('checked', false);
                    locSelectedUnits.delete(parseInt(unitId));
                    updateBulkLocBar();
                }
            } else {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
                showToast('danger', resp.message || 'Gagal menyimpan');
            }
        }
    ).fail(function() {
        btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
        showToast('danger', 'Koneksi gagal. Coba lagi.');
    });
});
// ----------------------------------------------------------------
// Unit Mapping sub-tab 2: Unassigned Units
// ----------------------------------------------------------------
function loadUnassigned() {
    $('#bodyUnassigned').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.post(BASE_URL + 'service/area-management/unit-mapping/getUnassignedUnits', csrfData({}), function(resp) {
        const tbody = $('#bodyUnassigned');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="8" class="text-center py-3 text-success"><i class="bi bi-check-circle me-1"></i> Semua unit sudah ter-mapping ke area</td></tr>');
            selectedUnits.clear();
            updateBulkUnitBar();
            return;
        }
        allUnassignedIds = resp.data.map(u => parseInt(u.id_inventory_unit));
        selectedUnits.clear();

        // Build ordered location list per customer (same logic as sub-tab 1)
        const uCustLocOrder = {};
        resp.data.forEach(u => {
            if (!u.customer_name || !u.location_id) return;
            if (!uCustLocOrder[u.customer_name]) uCustLocOrder[u.customer_name] = [];
            const list = uCustLocOrder[u.customer_name];
            if (!list.includes(String(u.location_id))) list.push(String(u.location_id));
        });

        resp.data.forEach(u => {
            const hasContract = u.no_kontrak ? '1' : '0';
            const fuelType    = u.fuel_type  || '';
            const fuelBadge   = fuelType
                ? `<span class="badge badge-soft-${fuelType === 'ELECTRIC' ? 'blue' : fuelType === 'DIESEL' ? 'yellow' : fuelType === 'GASOLINE' ? 'orange' : 'gray'} me-1">${fuelType}</span>`
                : '';
            const deptName = u.nama_departemen || '<span class="text-muted">-</span>';

            // "Lokasi 1" / "Lokasi 2" badge on customer column
            const uLocList  = uCustLocOrder[u.customer_name] || [];
            const uLocIndex = uLocList.indexOf(String(u.location_id)) + 1;
            const uLocTag   = uLocList.length > 1
                ? `<span class="badge badge-soft-blue ms-1" title="${u.location_name || ''}">Lokasi ${uLocIndex}</span>`
                : '';
            const custCell  = u.customer_name
                ? `<strong>${u.customer_name}</strong>${uLocTag}`
                : '<span class="text-muted">Tanpa Kontrak</span>';

            tbody.append(`
                <tr data-contract="${hasContract}" data-fuel="${fuelType}">
                    <td class="text-center"><input type="checkbox" class="chk-unit" data-unit-id="${u.id_inventory_unit}"></td>
                    <td><strong>${u.no_unit}</strong></td>
                    <td>${u.model || '-'}</td>
                    <td><span class="badge badge-soft-blue">${u.status || '-'}</span></td>
                    <td>${fuelBadge}${deptName}</td>
                    <td>${custCell}</td>
                    <td><small>${u.location_name || '-'}</small></td>
                    <td><small>${u.no_kontrak || '-'}</small></td>
                </tr>
            `);
        });
        // DataTables + custom contract filter
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(f => f._id !== 'contractFilter' && f._id !== 'deptFilter');
        const contractFilterFn = function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableUnassigned') return true;
            const val = $('#filterUnitContract').val();
            if (val === 'all') return true;
            const row = settings.aoData[dataIndex].nTr;
            const hasContract = row ? $(row).data('contract') : null;
            if (val === 'with_contract')    return hasContract === 1 || hasContract === '1';
            if (val === 'without_contract') return hasContract === 0 || hasContract === '0';
            return true;
        };
        contractFilterFn._id = 'contractFilter';
        $.fn.dataTable.ext.search.push(contractFilterFn);
        const deptFilterFn = function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableUnassigned') return true;
            const val = $('#filterUnitDept').val();
            if (val === 'all') return true;
            const row = settings.aoData[dataIndex].nTr;
            const fuel = row ? $(row).data('fuel') : '';
            return fuel === val || (val === '' && !fuel);
        };
        deptFilterFn._id = 'deptFilter';
        $.fn.dataTable.ext.search.push(deptFilterFn);
        // DataTables
        if ($.fn.DataTable.isDataTable('#tableUnassigned')) { $('#tableUnassigned').DataTable().destroy(); }
        unassignedTable = $('#tableUnassigned').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            searching: true,
            columnDefs: [{ orderable: false, targets: [0] }],
            language: {
                emptyTable: 'Semua unit sudah ter-mapping',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ unit',
                infoEmpty: '0 unit',
                search: 'Cari:',
                searchPlaceholder: 'Cari unit / customer...',
                lengthMenu: 'Tampilkan _MENU_ entri',
                paginate: { previous: '&laquo;', next: '&raquo;' }
            },
            drawCallback: function() {
                // Restore checkbox state on each page draw
                $('#bodyUnassigned .chk-unit').each(function() {
                    $(this).prop('checked', selectedUnits.has(parseInt($(this).data('unit-id'))));
                });
                const total   = $('#bodyUnassigned .chk-unit').length;
                const checked = $('#bodyUnassigned .chk-unit:checked').length;
                $('#chkSelectAllUnits')
                    .prop('indeterminate', checked > 0 && checked < total)
                    .prop('checked', total > 0 && checked === total);
                updateBulkUnitBar();
            }
        });
        updateBulkUnitBar();
    });
}

$('#filterUnitContract').on('change', function() {
    if (unassignedTable) unassignedTable.draw();
});

$('#filterUnitDept').on('change', function() {
    if (unassignedTable) unassignedTable.draw();
});

$('#btnRefreshUnassigned').on('click', loadUnassigned);

// ── Bulk unit select & assign (cross-page via Set) ────────────────────────────
function updateBulkUnitBar() {
    const count = selectedUnits.size;
    $('#bulkUnitCount').text(count + ' terpilih');
    $('#btnBulkAssignUnits').prop('disabled', count === 0);
}

$(document).on('change', '#chkSelectAllUnits', function() {
    const isChecked = $(this).is(':checked');
    $('#bodyUnassigned .chk-unit').each(function() {
        const id = parseInt($(this).data('unit-id'));
        $(this).prop('checked', isChecked);
        if (isChecked) selectedUnits.add(id); else selectedUnits.delete(id);
    });
    updateBulkUnitBar();
});

$(document).on('change', '.chk-unit', function() {
    const id = parseInt($(this).data('unit-id'));
    if ($(this).is(':checked')) { selectedUnits.add(id); }
    else { selectedUnits.delete(id); }
    updateBulkUnitBar();
    const total   = $('#bodyUnassigned .chk-unit').length;
    const checked = $('#bodyUnassigned .chk-unit:checked').length;
    $('#chkSelectAllUnits')
        .prop('indeterminate', checked > 0 && checked < total)
        .prop('checked', total > 0 && checked === total);
});

$('#btnSelectAllUnits').on('click', function(e) {
    e.preventDefault();
    // Only select IDs visible in current filter
    if (unassignedTable) {
        unassignedTable.rows({ filter: 'applied' }).nodes().each(function() {
            const id = parseInt($(this).find('.chk-unit').data('unit-id'));
            if (id) selectedUnits.add(id);
            $(this).find('.chk-unit').prop('checked', true);
        });
    } else {
        allUnassignedIds.forEach(id => selectedUnits.add(id));
        $('#bodyUnassigned .chk-unit').prop('checked', true);
    }
    $('#chkSelectAllUnits').prop('checked', true).prop('indeterminate', false);
    updateBulkUnitBar();
});

$('#btnDeselectAllUnits').on('click', function(e) {
    e.preventDefault();
    selectedUnits.clear();
    $('#bodyUnassigned .chk-unit').prop('checked', false);
    $('#chkSelectAllUnits').prop('checked', false).prop('indeterminate', false);
    updateBulkUnitBar();
});

$('#btnBulkAssignUnits').on('click', function() {
    const areaId = $('#bulkUnitArea').val();
    if (!areaId) { showToast('warning', 'Pilih area terlebih dahulu'); return; }

    const unitIds = Array.from(selectedUnits);
    if (!unitIds.length) return;

    const btn = $(this);
    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Menyimpan...');

    $.post(BASE_URL + 'service/area-management/unit-mapping/batchAssignUnits',
        csrfData({unit_ids: JSON.stringify(unitIds), area_id: areaId}),
        function(resp) {
            btn.html('<i class="bi bi-check-all me-1"></i> Assign Terpilih');
            if (resp.success) {
                showToast('success', resp.message);
                loadUnassigned();
                updateStats();
            } else {
                btn.prop('disabled', false);
                showToast('danger', resp.message);
            }
        }
    );
});

// ----------------------------------------------------------------
// Sub-tab lazy load triggers
// ----------------------------------------------------------------
$('#subtabLocationsLink').on('shown.bs.tab', function() {
    if ($('#bodyLocationUnits tr td[colspan]').length) loadLocationUnits();
});
$('#subtabUnassignedLink').on('shown.bs.tab', function() {
    if ($('#bodyUnassigned tr td[colspan]').length) loadUnassigned();
});

// ----------------------------------------------------------------
// ----------------------------------------------------------------
// HTML escape helper
// ----------------------------------------------------------------
function esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ----------------------------------------------------------------
// Load units for a given area id (shared drill-down helper)
// ----------------------------------------------------------------
function loadAreaUnits(areaId) {
    $.post(BASE_URL + 'service/area-management/unit-mapping/getAreaUnits', csrfData({area_id: areaId}), function(resp) {
        const body = $('#modalAreaUnitsBody');
        body.empty();
        if (!resp.success || !resp.data.length) {
            body.html('<div class="text-center py-4 text-muted">Tidak ada unit di area ini</div>');
            return;
        }

        // Group by customer → location
        const groups = {};
        resp.data.forEach(u => {
            const custKey = u.customer_name || '(Tanpa Customer)';
            const locKey  = u.location_name || '(Tanpa Lokasi)';
            if (!groups[custKey]) groups[custKey] = {};
            if (!groups[custKey][locKey]) groups[custKey][locKey] = { pic: u.location_pic || '-', phone: u.location_phone || '-', units: [] };
            groups[custKey][locKey].units.push(u);
        });

        let html = '<div id="areaUnitsPrintArea">';
        let totalUnits = resp.data.length;
        html += `<div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Total: <strong>${totalUnits}</strong> unit</span>
        </div>`;

        for (const [customer, locations] of Object.entries(groups)) {
            const custUnitCount = Object.values(locations).reduce((sum, loc) => sum + loc.units.length, 0);
            html += `<div class="mb-4">
                <h6 class="fw-bold text-primary mb-2">
                    <i class="bi bi-building me-1"></i>${esc(customer)}
                    <span class="badge badge-soft-blue ms-1">${custUnitCount} unit</span>
                </h6>`;

            for (const [location, locData] of Object.entries(locations)) {
                html += `<div class="ms-3 mb-3">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="fw-semibold"><i class="bi bi-geo-alt me-1 text-muted"></i>${esc(location)}</span>
                        <span class="text-muted small"><i class="bi bi-person me-1"></i>PIC: ${esc(locData.pic)}</span>
                        <span class="text-muted small"><i class="bi bi-telephone me-1"></i>${esc(locData.phone)}</span>
                        <span class="badge badge-soft-cyan">${locData.units.length} unit</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">No</th>
                                    <th>Unit No.</th>
                                    <th>Model</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>`;
                locData.units.forEach((u, idx) => {
                    html += `<tr>
                        <td class="text-center">${idx + 1}</td>
                        <td><strong>${esc(u.no_unit)}</strong></td>
                        <td>${esc(u.model) || '-'}</td>
                        <td>${esc(u.departemen) || '-'}</td>
                        <td><span class="badge badge-soft-${getStatusColor(u.status)}">${esc(u.status) || '-'}</span></td>
                    </tr>`;
                });
                html += `</tbody></table></div></div>`;
            }
            html += `</div>`;
        }
        html += '</div>';
        body.html(html);
    });
}

function getStatusColor(status) {
    if (!status) return 'gray';
    const s = status.toUpperCase();
    if (s.includes('ACTIVE') || s === 'AVAILABLE') return 'green';
    if (s === 'BREAKDOWN' || s === 'SOLD') return 'red';
    if (s === 'RETURNED') return 'yellow';
    if (s === 'STANDBY') return 'cyan';
    return 'blue';
}

function showAreaUnitsModal(areaId, areaCode, areaName) {
    $('#modalAreaUnitsLabel').html(`<i class="bi bi-list-ul me-1"></i> Unit di [${areaCode}] ${areaName}`);
    $('#modalAreaUnitsBody').html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div> Memuat data...</div>');
    const modal = new bootstrap.Modal(document.getElementById('modalAreaUnits'));
    modal.show();
    loadAreaUnits(areaId);
}

// Print area units table
$('#btnPrintAreaUnits').on('click', function() {
    const printContent = document.getElementById('areaUnitsPrintArea');
    if (!printContent) return;
    const title = $('#modalAreaUnitsLabel').text();
    const win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(`<!DOCTYPE html><html><head><title>${title}</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
            h5, h6 { margin: 8px 0; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            th, td { border: 1px solid #ccc; padding: 4px 8px; text-align: left; }
            th { background: #f0f0f0; font-weight: bold; }
            .badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; }
            .text-muted { color: #777; }
            .fw-bold { font-weight: bold; }
            .fw-semibold { font-weight: 600; }
            .text-primary { color: #0d6efd; }
            .ms-3 { margin-left: 16px; }
            .mb-2 { margin-bottom: 8px; }
            .mb-3 { margin-bottom: 12px; }
            .mb-4 { margin-bottom: 16px; }
            .d-flex { display: flex; }
            .gap-3 { gap: 12px; }
            .align-items-center { align-items: center; }
            @media print { body { padding: 0; } }
        </style>
    </head><body>
        <h5 style="text-align:center;margin-bottom:16px">${title}</h5>
        <p style="text-align:center;color:#777;margin-bottom:20px">Dicetak: ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}</p>
        ${printContent.innerHTML}
    </body></html>`);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); }, 400);
});

// ----------------------------------------------------------------
// Stats update helper
// ----------------------------------------------------------------
function updateStats() {
    // Reload areas table to refresh unit/location counts
    if (areasTable) areasTable.ajax.reload(null, false);
}

// ----------------------------------------------------------------
// Toast helper
// ----------------------------------------------------------------
function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        const iconMap = {success: 'success', danger: 'error', warning: 'warning', info: 'info'};
        Swal.fire({ icon: iconMap[type] || 'info', text: message, timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
    } else {
        alert(message);
    }
}

function showConfirm(message, title, onConfirm) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title || 'Konfirmasi',
            html: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
        }).then(result => { if (result.isConfirmed) onConfirm(); });
    } else {
        if (confirm(message.replace(/<[^>]+>/g, ''))) onConfirm();
    }
}

function notify(msg, type='success'){
	if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
		return OptimaNotify[type](msg);
	}
	if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
}

// ── Preview units at location (Bootstrap Dropdown, lazy-load) ──────────────
$(document).on('show.bs.dropdown', '[data-bs-toggle="dropdown"][data-loc-id]', function() {
    const $toggle = $(this);
    if ($toggle.data('loaded')) return; // already loaded, reuse cached content
    const locId = $toggle.data('loc-id');
    const $menu  = $(`.dropdown-menu[data-loc-menu="${locId}"]`);

    $.get(BASE_URL + 'service/area-management/unit-mapping/unitsAtLocation/' + locId, function(resp) {
        $toggle.data('loaded', true);
        if (!resp.success || !resp.data.length) {
            $menu.html('<div class="px-3 py-2 text-muted small"><i class="bi bi-inbox me-1"></i>Tidak ada unit aktif</div>');
            return;
        }
        const fuelColor = { ELECTRIC: 'blue', DIESEL: 'yellow', GASOLINE: 'orange', LPG: 'gray' };
        let html = `<div class="px-3 py-1 bg-light border-bottom d-flex justify-content-between align-items-center">
            <small class="fw-semibold text-muted">Unit Aktif</small>
            <span class="badge badge-soft-blue">${resp.data.length} unit</span>
        </div>`;
        resp.data.forEach(u => {
            const fc   = fuelColor[u.fuel_type] || 'gray';
            const fuel = u.fuel_type ? `<span class="badge badge-soft-${fc} ms-1" style="font-size:0.65rem">${u.fuel_type}</span>` : '';
            const area = u.area_code ? `<small class="text-muted ms-1">[${u.area_code}]</small>` : '';
            const exp  = u.tanggal_berakhir ? ` <small class="text-muted">· ${u.tanggal_berakhir.substring(0,10)}</small>` : '';
            html += `
                <div class="px-3 py-1 border-bottom d-flex justify-content-between align-items-center" style="font-size:0.8rem">
                    <div>
                        <strong>#${u.no_unit}</strong>
                        <span class="text-muted ms-1">${u.model || ''}</span>${area}${exp}
                    </div>
                    <div class="ms-2 text-nowrap">${fuel}</div>
                </div>`;
        });
        $menu.html(html);
    });
});

// ── Edit PIC ──────────────────────────────────────────────────────────────
$(document).on('click', '.btn-edit-pic', function(e) {
    e.preventDefault();
    const locId = $(this).data('loc-id');
    const loc   = locInfoMap[locId];
    if (!loc) return;
    $('#picLocId').val(locId);
    $('#picContactPerson').val(loc.contact_person || '');
    $('#picPhone').val(loc.phone || '');
    $('#picEmail').val(loc.email || '');
    $('#picPosition').val(loc.pic_position || '');
    $('#picAddress').val(loc.address || '');
    $('#picCity').val(loc.city || '');
    $('#picProvince').val(loc.province || '');
    $('#picPostalCode').val(loc.postal_code || '');
    $('#picModalSubtitle').text(loc.customer_name + ' · ' + loc.location_name);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditPic')).show();
});

$('#btnSavePic').on('click', function() {
    const locId = $('#picLocId').val();
    if (!locId) return;
    const btn = $(this);
    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Menyimpan...');

    $.post(BASE_URL + 'service/area-management/unit-mapping/updateLocationPic/' + locId,
        csrfData({
            contact_person: $('#picContactPerson').val().trim(),
            phone:          $('#picPhone').val().trim(),
            email:          $('#picEmail').val().trim(),
            pic_position:   $('#picPosition').val().trim(),
            address:        $('#picAddress').val().trim(),
            city:           $('#picCity').val().trim(),
            province:       $('#picProvince').val().trim(),
            postal_code:    $('#picPostalCode').val().trim(),
        }),
        function(resp) {
            btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Simpan');
            if (resp.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditPic')).hide();
                showToast('success', resp.message);
                // Update locInfoMap cache
                if (locInfoMap[locId]) {
                    locInfoMap[locId].contact_person = $('#picContactPerson').val().trim();
                    locInfoMap[locId].phone          = $('#picPhone').val().trim();
                    locInfoMap[locId].email          = $('#picEmail').val().trim();
                    locInfoMap[locId].pic_position   = $('#picPosition').val().trim();
                    locInfoMap[locId].address        = $('#picAddress').val().trim();
                    locInfoMap[locId].city           = $('#picCity').val().trim();
                    locInfoMap[locId].province       = $('#picProvince').val().trim();
                    locInfoMap[locId].postal_code    = $('#picPostalCode').val().trim();
                }
                // Update icon in table row
                const hasPic = $('#picContactPerson').val().trim();
                const $rowBtn = $(`.btn-edit-pic[data-loc-id="${locId}"]`);
                $rowBtn.attr('title', hasPic
                    ? 'PIC: ' + hasPic + ($('#picPosition').val().trim() ? ' (' + $('#picPosition').val().trim() + ')' : '') + ($('#picPhone').val().trim() ? ' · ' + $('#picPhone').val().trim() : '')
                    : 'Belum ada data PIC · klik untuk mengisi');
                $rowBtn.html(hasPic
                    ? '<i class="bi bi-person-check-fill text-success"></i>'
                    : '<i class="bi bi-person-plus text-muted"></i>');
            } else {
                showToast('danger', resp.message || 'Gagal menyimpan');
            }
        }
    ).fail(function() {
        btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Simpan');
        showToast('danger', 'Koneksi gagal. Coba lagi.');
    });
});

</script>
<?= $this->endSection() ?>
  


