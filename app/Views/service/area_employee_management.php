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
          <div class="stat-card bg-danger-soft h-100">
              <div class="d-flex align-items-center">
                  <div class="me-3"><i class="bi bi-geo-alt stat-icon text-danger"></i></div>
                  <div>
                      <div class="stat-value" id="statLocationsNoArea"><?= $unitStats['locations_without_area'] ?? 0 ?></div>
                      <div class="text-muted small"><?= lang('App.locations_without_area') ?></div>
                  </div>
              </div>
          </div>
      </div>

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

                          <!-- Area unit drill-down panel -->
                          <div id="panelAreaUnits" class="d-none mt-3">
                              <div class="card border-primary">
                                  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                                      <span id="panelAreaTitle"><i class="bi bi-list-ul me-1"></i> <?= lang('App.area_units_panel') ?></span>
                                      <button class="btn btn-sm btn-outline-light" id="btnClosePanelUnits"><i class="bi bi-x-lg"></i></button>
                                  </div>
                                  <div class="card-body p-2">
                                      <div class="table-responsive">
                                          <table class="table table-sm table-striped mb-0" id="tableAreaUnits">
                                              <thead class="table-light">
                                                  <tr>
                                                      <th><?= lang('App.unit_number') ?></th><th>Model</th><th><?= lang('Common.status') ?></th>
                                                      <th>Customer</th><th><?= lang('App.customer_location') ?></th>
                                                      <th><?= lang('App.contract_number') ?></th><th><?= lang('App.contract_end') ?></th>
                                                  </tr>
                                              </thead>
                                              <tbody id="bodyAreaUnits">
                                                  <tr><td colspan="7" class="text-center py-3 text-muted"><?= lang('App.select_area') ?></td></tr>
                                              </tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
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
                                          <i class="bi bi-building me-1"></i> <?= lang('App.input_area_per_location') ?>
                                          <span class="badge badge-soft-orange ms-1" id="badgeUnassignedLoc"><?= $unitStats['locations_without_area'] ?? 0 ?></span>
                                      </a>
                                  </li>
                                  <li class="nav-item">
                                      <a class="nav-link nav-link-sm" data-bs-toggle="pill" href="#subtabUnassigned" id="subtabUnassignedLink">
                                          <i class="bi bi-question-circle me-1"></i> <?= lang('App.unassigned_units') ?>
                                          <span class="badge badge-soft-orange ms-1" id="badgeUnassigned"><?= $unitStats['units_without_area'] ?? 0 ?></span>
                                      </a>
                                  </li>
                              </ul>
                          </div>

                          <div class="tab-content px-1 pb-3">

                              <!-- ─── Sub-tab 1: Input Area per Lokasi ──────────────── -->
                              <div class="tab-pane fade show active" id="subtabLocations">
                                  <!-- Bulk bar -->
                                  <div class="bg-light border rounded px-3 py-2 mb-3" id="bulkLocBar">
                                      <div class="d-flex align-items-center gap-2 flex-wrap">
                                          <span class="fw-semibold small"><i class="bi bi-check2-square text-warning me-1"></i> <span id="bulkLocationCount">0 terpilih</span></span>
                                          <select class="form-select form-select-sm" id="bulkLocationArea" style="width:230px">
                                              <option value="">-- Pilih Area --</option>
                                              <?php foreach ($areas as $a): ?>
                                                  <option value="<?= $a['id'] ?>"><?= esc($a['area_code']) ?> — <?= esc($a['area_name']) ?></option>
                                              <?php endforeach; ?>
                                          </select>
                                          <button class="btn btn-sm btn-warning fw-semibold" id="btnBulkAssignLocations" disabled>
                                              <i class="bi bi-check-all me-1"></i> Assign Terpilih
                                          </button>
                                          <a href="#" class="small text-muted ms-2" id="btnSelectAllLocations">Pilih Semua</a>
                                          <a href="#" class="small text-muted" id="btnDeselectAllLocations">Hapus Pilihan</a>
                                      </div>
                                  </div>
                                  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                      <div class="d-flex gap-2">
                                          <select class="form-select form-select-sm" id="filterLocationArea" style="width:180px">
                                              <option value="all"><?= lang('App.all_locations') ?></option>
                                              <option value="unassigned" selected><?= lang('App.unassigned_area_label') ?></option>
                                              <option value="assigned"><?= lang('App.assigned_area_label') ?></option>
                                          </select>
                                          <button class="btn btn-sm btn-outline-secondary" id="btnLoadLocations">
                                              <i class="bi bi-funnel me-1"></i> <?= lang('Common.filter') ?>
                                          </button>
                                      </div>
                                      <div class="d-flex gap-2">
                                          <button class="btn btn-sm btn-success" id="btnSyncFromContracts">
                                              <i class="bi bi-arrow-repeat me-1"></i> <?= lang('App.auto_sync_from_contracts') ?>
                                          </button>
                                          <button class="btn btn-sm btn-outline-info" id="btnRefreshLocations">
                                              <i class="bi bi-arrow-clockwise me-1"></i> <?= lang('Common.refresh') ?>
                                          </button>
                                      </div>
                                  </div>
                                  <div class="alert alert-info border-0 py-2 small mb-3">
                                      <i class="bi bi-lightbulb me-1"></i>
                                      <strong><?= lang('App.input_area_label') ?></strong> <?= lang('App.input_area_hint') ?>
                                  </div>
                                  <div class="table-responsive">
                                      <table class="table table-hover align-middle" id="tableLocations">
                                          <thead class="table-light">
                                              <tr>
                                                  <th style="width:36px"><input type="checkbox" id="chkSelectAllLocations"></th>
                                                  <th>Customer</th><th><?= lang('App.customer_location') ?></th><th><?= lang('App.location_code') ?></th>
                                                  <th class="text-center"><?= lang('App.active_units') ?></th>
                                                  <th style="min-width:200px"><?= lang('App.area') ?></th>
                                                  <th class="text-center"><?= lang('Common.action') ?></th>
                                              </tr>
                                          </thead>
                                          <tbody id="bodyLocations">
                                              <tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
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
                                                  <th>Customer</th><th><?= lang('App.customer_location') ?></th><th><?= lang('App.contract_number') ?></th>
                                              </tr>
                                          </thead>
                                          <tbody id="bodyUnassigned">
                                              <tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
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
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let areasTable, employeesTable, locationsTable, unassignedTable;
let unassignedLocationIds = [];
let allUnassignedIds = [];
const selectedUnits = new Set();
const selectedLocations = new Set();
let allLocationIds = [];
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
      if ($('#bodyLocations tr td[colspan]').length) loadLocations();
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
        render: function(d, type, row) {
          const count = d || 0;
          const names = row.foremans || '';
          const badge = count > 0
            ? `<span class="badge badge-soft-green">${count}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
          const nameHtml = names
            ? `<div class="small text-success mt-1" style="white-space:normal;max-width:140px">${names}</div>`
            : '';
          return badge + nameHtml;
        }
      },
      // Column 4: Mekanik
      {
        data: 'mechanic_count',
        className: 'text-center',
        render: function(d, type, row) {
          const count = d || 0;
          const names = row.mechanics || '';
          const badge = count > 0
            ? `<span class="badge badge-soft-blue">${count}</span>`
            : `<span class="badge badge-soft-gray">0</span>`;
          const nameHtml = names
            ? `<div class="small text-primary mt-1" style="white-space:normal;max-width:140px">${names}</div>`
            : '';
          return badge + nameHtml;
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
                    onclick="event.stopPropagation(); viewAreaDetail('${row.area_code}', null)">
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
      // Row click → show unit drill-down
      $('#areasTable tbody').off('click', 'tr').on('click', 'tr', function(e) {
        if ($(e.target).closest('.btn-edit-area').length) return;
        const data = areasTable.row(this).data();
        if (!data) return;
        // Highlight selection
        $('#areasTable tbody tr').removeClass('table-active');
        $(this).addClass('table-active');
        // Show unit drill-down panel
        $('#panelAreaTitle').html(`<i class="bi bi-list-ul me-1"></i> Unit di [${data.area_code}] ${data.area_name}`);
        $('#bodyAreaUnits').html('<tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></td></tr>');
        $('#panelAreaUnits').removeClass('d-none');
        loadAreaUnits(data.id);
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
      if ($('#bodyLocations tr td[colspan]').length) loadLocations();
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

function buildAreaOptions(selectedId) {
    let html = '<option value="">-- Tidak Ada --</option>';
    allAreas.forEach(a => {
        const sel = (selectedId && parseInt(selectedId) === a.id) ? ' selected' : '';
        html += `<option value="${a.id}"${sel}>[${a.area_code}] ${a.area_name}</option>`;
    });
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
    // .area-row exists only in Unit Mapping tab (Unit Mapping Controller)
    const areaId   = $(this).data('area-id');
    const areaName = $(this).data('area-name');
    $('.area-row').removeClass('table-active');
    $(this).addClass('table-active');
    $('#panelAreaTitle').html(`<i class="bi bi-list-ul me-1"></i> Unit di ${areaName}`);
    $('#bodyAreaUnits').html('<tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></td></tr>');
    $('#panelAreaUnits').removeClass('d-none');
    loadAreaUnits(areaId);
});

$('#btnClosePanelUnits').on('click', function() {
    $('#panelAreaUnits').addClass('d-none');
    $('#areasTable tbody tr').removeClass('table-active');
    $('.area-row').removeClass('table-active');
});

// ----------------------------------------------------------------
// Unit Mapping sub-tab 1: Customer Locations
// ----------------------------------------------------------------
function loadLocations() {
    const filter = $('#filterLocationArea').val();
    if ($.fn.DataTable.isDataTable('#tableLocations')) { $('#tableLocations').DataTable().destroy(); }
    $('#bodyLocations').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.post(BASE_URL + 'service/area-management/unit-mapping/getCustomerLocations', csrfData({area_filter: filter}), function(resp) {
        const tbody = $('#bodyLocations');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada data</td></tr>');
            return;
        }
        unassignedLocationIds = [];
        selectedLocations.clear();
        allLocationIds = resp.data.map(l => l.id);

        // Count locations per customer for multi-location visual indicator
        const custLocCount = {};
        resp.data.forEach(loc => {
            custLocCount[loc.customer_name] = (custLocCount[loc.customer_name] || 0) + 1;
        });

        resp.data.forEach(loc => {
            if (!loc.area_id) unassignedLocationIds.push(loc.id);
            const isMulti  = custLocCount[loc.customer_name] > 1;
            const multiTag = isMulti
                ? ` <span class="badge badge-soft-orange ms-1" title="${custLocCount[loc.customer_name]} lokasi terdaftar">${custLocCount[loc.customer_name]}x</span>`
                : '';
            tbody.append(`
                <tr data-assigned="${loc.area_id ? '1' : '0'}" data-loc-id="${loc.id}">
                    <td class="text-center"><input type="checkbox" class="chk-loc" data-loc-id="${loc.id}"></td>
                    <td><strong>${loc.customer_name}</strong>${multiTag}</td>
                    <td>${loc.location_name}</td>
                    <td><small class="text-muted">${loc.location_code || '-'}</small></td>
                    <td class="text-center">
                        ${loc.active_units > 0
                            ? `<span class="badge badge-soft-green">${loc.active_units} unit</span>`
                            : `<span class="text-muted">0</span>`}
                    </td>
                    <td>
                        <select class="form-select form-select-sm loc-area-select" data-loc-id="${loc.id}">
                            ${buildAreaOptions(loc.area_id)}
                        </select>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary btn-save-location" data-loc-id="${loc.id}">
                            <i class="bi bi-check-lg"></i> Save
                        </button>
                    </td>
                </tr>
            `);
        });

        // DataTables — destroy first if already initialized
        if ($.fn.DataTable.isDataTable('#tableLocations')) {
            $('#tableLocations').DataTable().destroy();
        }
        locationsTable = $('#tableLocations').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            columnDefs: [{ orderable: false, targets: [0, 5, 6] }],
            language: {
                emptyTable: 'Tidak ada data',
                info: 'Menampilkan _START_ – _END_ dari _TOTAL_ lokasi',
                infoEmpty: '0 lokasi',
                search: 'Cari:',
                searchPlaceholder: 'Cari customer / lokasi...',
                lengthMenu: 'Tampilkan _MENU_ entri',
                paginate: { previous: '&laquo;', next: '&raquo;' }
            },
            drawCallback: function() {
                $('#bodyLocations .chk-loc').each(function() {
                    $(this).prop('checked', selectedLocations.has(parseInt($(this).data('loc-id'))));
                });
                const total   = $('#bodyLocations .chk-loc').length;
                const checked = $('#bodyLocations .chk-loc:checked').length;
                $('#chkSelectAllLocations')
                    .prop('indeterminate', checked > 0 && checked < total)
                    .prop('checked', total > 0 && checked === total);
                updateBulkLocBar();
            }
        });
        updateBulkLocBar();
    });
}

// ── Bulk location select & assign (cross-page via Set) ──────────────────────
function updateBulkLocBar() {
    const count = selectedLocations.size;
    $('#bulkLocationCount').text(count + ' terpilih');
    $('#btnBulkAssignLocations').prop('disabled', count === 0);
}

$(document).on('change', '#chkSelectAllLocations', function() {
    const isChecked = $(this).is(':checked');
    $('#bodyLocations .chk-loc').each(function() {
        const id = parseInt($(this).data('loc-id'));
        $(this).prop('checked', isChecked);
        if (isChecked) selectedLocations.add(id); else selectedLocations.delete(id);
    });
    updateBulkLocBar();
});

$(document).on('change', '.chk-loc', function() {
    const id = parseInt($(this).data('loc-id'));
    if ($(this).is(':checked')) { selectedLocations.add(id); }
    else { selectedLocations.delete(id); }
    updateBulkLocBar();
    const total   = $('#bodyLocations .chk-loc').length;
    const checked = $('#bodyLocations .chk-loc:checked').length;
    $('#chkSelectAllLocations')
        .prop('indeterminate', checked > 0 && checked < total)
        .prop('checked', total > 0 && checked === total);
});

$('#btnSelectAllLocations').on('click', function(e) {
    e.preventDefault();
    if (locationsTable) {
        locationsTable.rows({ filter: 'applied' }).nodes().each(function() {
            const id = parseInt($(this).find('.chk-loc').data('loc-id'));
            if (id) selectedLocations.add(id);
            $(this).find('.chk-loc').prop('checked', true);
        });
    } else {
        allLocationIds.forEach(id => selectedLocations.add(id));
        $('#bodyLocations .chk-loc').prop('checked', true);
    }
    $('#chkSelectAllLocations').prop('checked', true).prop('indeterminate', false);
    updateBulkLocBar();
});

$('#btnDeselectAllLocations').on('click', function(e) {
    e.preventDefault();
    selectedLocations.clear();
    $('#bodyLocations .chk-loc').prop('checked', false);
    $('#chkSelectAllLocations').prop('checked', false).prop('indeterminate', false);
    updateBulkLocBar();
});

// Bulk assign locations
$('#btnBulkAssignLocations').on('click', function() {
    const areaId = $('#bulkLocationArea').val();
    if (!areaId) { showToast('warning', 'Pilih area terlebih dahulu'); return; }

    const locIds = Array.from(selectedLocations);
    if (!locIds.length) { showToast('info', 'Pilih lokasi terlebih dahulu'); return; }

    const areaName = $('#bulkLocationArea option:selected').text();
    showConfirm(
        `Assign ${locIds.length} lokasi ke area <strong>${areaName}</strong>?`,
        'Konfirmasi Bulk Assign',
        function() {
            const btn = $('#btnBulkAssignLocations');
            btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Menyimpan...');

            $.post(BASE_URL + 'service/area-management/unit-mapping/batchAssignLocations',
                csrfData({location_ids: JSON.stringify(locIds), area_id: areaId}),
                function(resp) {
                    btn.html('<i class="bi bi-check-all me-1"></i> Assign Terpilih');
                    if (resp.success) {
                        showToast('success', resp.message);
                        selectedLocations.clear();
                        loadLocations();
                        updateStats();
                    } else {
                        btn.prop('disabled', false);
                        showToast('danger', resp.message || 'Gagal menyimpan');
                    }
                }
            );
        }
    );
});

$(document).on('click', '.btn-save-location', function() {
    const locId  = $(this).data('loc-id');
    const areaId = $(`.loc-area-select[data-loc-id="${locId}"]`).val();
    const btn    = $(this);
    const row    = btn.closest('tr');

    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm"></div>');

    $.post(BASE_URL + 'service/area-management/unit-mapping/assignAreaToLocation',
        csrfData({location_id: locId, area_id: areaId}),
        function(resp) {
            if (resp.success) {
                showToast('success', resp.message);
                // Auto-remove row if viewing unassigned filter
                const filter = $('#filterLocationArea').val();
                if (filter === 'unassigned' && locationsTable) {
                    selectedLocations.delete(parseInt(locId));
                    unassignedLocationIds = unassignedLocationIds.filter(id => id !== parseInt(locId));
                    locationsTable.row(row).remove().draw(false);
                    updateBulkLocBar();
                } else {
                    // Just mark row as assigned visually
                    row.attr('data-assigned', '1');
                    btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save');
                    btn.removeClass('btn-primary').addClass('btn-success');
                    setTimeout(() => btn.removeClass('btn-success').addClass('btn-primary'), 2000);
                }
                updateStats();
            } else {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save');
                showToast('danger', resp.message || 'Gagal menyimpan');
            }
        }
    ).fail(function() {
        btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save');
        showToast('danger', 'Koneksi gagal. Coba lagi.');
    });
});

$('#btnSyncFromContracts').on('click', function() {
    showConfirm(
        'Sync area unit dari semua kontrak aktif?<br><small class="text-muted">Hanya unit yang lokasi kontraknya sudah memiliki area yang akan ter-update.</small>',
        'Auto-Sync dari Kontrak',
        function() {
            const btn = $('#btnSyncFromContracts');
            btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Syncing...');

            $.post(BASE_URL + 'service/area-management/unit-mapping/syncFromContracts', csrfData({}), function(resp) {
                btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Auto-Sync dari Kontrak');
                if (resp.success) {
                    showToast('success', resp.message);
                    updateStats();
                } else {
                    showToast('danger', resp.message);
                }
            }).fail(function() {
                btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Auto-Sync dari Kontrak');
                showToast('danger', 'Koneksi gagal. Coba lagi.');
            });
        }
    );
});
// ----------------------------------------------------------------
// Unit Mapping sub-tab 2: Unassigned Units
// ----------------------------------------------------------------
function loadUnassigned() {
    $('#bodyUnassigned').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.post(BASE_URL + 'service/area-management/unit-mapping/getUnassignedUnits', csrfData({}), function(resp) {
        const tbody = $('#bodyUnassigned');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-3 text-success"><i class="bi bi-check-circle me-1"></i> Semua unit sudah ter-mapping ke area</td></tr>');
            selectedUnits.clear();
            updateBulkUnitBar();
            return;
        }
        allUnassignedIds = resp.data.map(u => parseInt(u.id_inventory_unit));
        selectedUnits.clear();
        resp.data.forEach(u => {
            const hasContract = u.no_kontrak ? '1' : '0';
            tbody.append(`
                <tr data-contract="${hasContract}">
                    <td class="text-center"><input type="checkbox" class="chk-unit" data-unit-id="${u.id_inventory_unit}"></td>
                    <td><strong>${u.no_unit}</strong></td>
                    <td>${u.model || '-'}</td>
                    <td><span class="badge badge-soft-blue">${u.status || '-'}</span></td>
                    <td>${u.customer_name || '<span class="text-muted">Tanpa Kontrak</span>'}</td>
                    <td><small>${u.location_name || '-'}</small></td>
                    <td><small>${u.no_kontrak || '-'}</small></td>
                </tr>
            `);
        });
        // DataTables + custom contract filter
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(f => f._id !== 'contractFilter');
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
        // DataTables
        if ($.fn.DataTable.isDataTable('#tableUnassigned')) { $('#tableUnassigned').DataTable().destroy(); }
        unassignedTable = $('#tableUnassigned').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
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
    if ($('#bodyLocations tr td[colspan]').length) loadLocations();
});
$('#subtabUnassignedLink').on('shown.bs.tab', function() {
    if ($('#bodyUnassigned tr td[colspan]').length) loadUnassigned();
});

// ----------------------------------------------------------------
// Load units for a given area id (shared drill-down helper)
// ----------------------------------------------------------------
function loadAreaUnits(areaId) {
    $.post(BASE_URL + 'service/area-management/unit-mapping/getAreaUnits', csrfData({area_id: areaId}), function(resp) {
        const tbody = $('#bodyAreaUnits');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada unit di area ini</td></tr>');
            return;
        }
        resp.data.forEach(u => {
            tbody.append(`
                <tr>
                    <td><strong>${u.no_unit}</strong></td>
                    <td>${u.model || '-'}</td>
                    <td><span class="badge badge-soft-blue">${u.status || '-'}</span></td>
                    <td>${u.customer_name || '<span class="text-muted">-</span>'}</td>
                    <td>${u.location_name || '<span class="text-muted">-</span>'}</td>
                    <td><small>${u.no_kontrak || '-'}</small></td>
                    <td><small>${u.tanggal_berakhir || '-'}</small></td>
                </tr>
            `);
        });
    });
}

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

</script>
<?= $this->endSection() ?>
  
