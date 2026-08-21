<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var string $admin_username
 * @var int $admin_role
 * @var int $admin_company_id
 * @var array $companies
 */
$admin_role = isset($admin_role) ? $admin_role : 1;

$this->load->view('partials/header', ['page_title' => 'Admin Dashboard - ATTG Contact']);
?>

<!-- Select2 CSS -->
<link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('assets/css/select2-bootstrap4.min.css') ?>">
<style>
  body {
    font-size: 0.9rem;
  }
  .modal-backdrop,
  .swal2-container {
    width: 100vw !important;
    height: 100vh !important;
  }
  .nav-tabs .nav-link.active {
    font-weight: bold;
    color: #495057;
    background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 #f8f9fa;
  }
  .table-scrollable {
    max-height: calc(100vh - 350px);
    overflow-y: auto;
    overflow-x: auto;
  }
  .table-scrollable thead th {
    position: sticky;
    top: 0;
    background-color: #e9ecef;
    z-index: 1;
    box-shadow: inset 0 -2px 0 #dee2e6, inset 0 1px 0 #dee2e6;
  }
</style>

<body class="hold-transition layout-top-nav bg-light">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a href="<?= site_url('send_data/admin_dashboard') ?>" class="navbar-brand">
          <span class="brand-text font-weight-light">ATTG Contact <b>Admin</b></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <span class="nav-link text-white"><i class="fas fa-user-circle mr-1"></i> <?= html_escape($admin_username) ?></span>
            </li>
            <li class="nav-item">
              <button type="button" onclick="confirmLogout()" class="nav-link text-danger" style="background: none; border: none; padding: 0.5rem 1rem; cursor: pointer;">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- /.navbar -->

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 font-weight-bold text-dark">ระบบจัดการข้อมูล</h1>
            </div>
            <div class="col-sm-6 text-right">
              <a href="<?= site_url('send_data') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i> ดูหน้าเว็บผู้ใช้ทั่วไป</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="tab-employee-tab" data-toggle="pill" href="#tab-employee" role="tab" aria-controls="tab-employee" aria-selected="true">Employee</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-function-tab" data-toggle="pill" href="#tab-function" role="tab" aria-controls="tab-function" aria-selected="false">Function</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-department-tab" data-toggle="pill" href="#tab-department" role="tab" aria-controls="tab-department" aria-selected="false">Department</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-section-tab" data-toggle="pill" href="#tab-section" role="tab" aria-controls="tab-section" aria-selected="false">Section</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-position-tab" data-toggle="pill" href="#tab-position" role="tab" aria-controls="tab-position" aria-selected="false">Position</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-admin-tab" data-toggle="pill" href="#tab-admin" role="tab" aria-controls="tab-admin" aria-selected="false">Admin</a>
                </li>
              </ul>
            </div>

            <div class="card-body">
              <div class="tab-content" id="custom-tabs-four-tabContent">

                <!-- Tab 1: Employee -->
                <div class="tab-pane fade show active" id="tab-employee" role="tabpanel" aria-labelledby="tab-employee-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-users mr-2"></i>รายชื่อพนักงาน</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addEmployeeModal"><i class="fas fa-plus"></i> เพิ่มพนักงาน</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="crudCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach; endif; ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover" id="employeeTable">
                      <thead class="thead-light">
                        <tr>
                          <th style="width: 60px;" class="text-center">รูปภาพ</th>
                          <th>ชื่อ - นามสกุล</th>
                          <th>แผนก/ส่วน (Section)</th>
                          <th>ตำแหน่ง</th>
                          <th class="text-center">สถานะ</th>
                          <th>เบอร์โทร</th>
                          <th>อีเมล</th>
                          <th style="width: 120px;" class="text-center">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="8" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab 2: Function -->
                <div class="tab-pane fade" id="tab-function" role="tabpanel" aria-labelledby="tab-function-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-building mr-2"></i>จัดการ Function</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addFunctionModal"><i class="fas fa-plus"></i> เพิ่ม Function</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="functionCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach; endif; ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover" id="functionTable">
                      <thead class="thead-light">
                        <tr>
                          <th>ชื่อ Function (FuncName)</th>
                          <th>Function Code</th>
                          <th class="text-center" style="width: 150px;">สถานะ</th>
                          <th class="text-center" style="width: 150px;">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="4" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab 3: Department -->
                <div class="tab-pane fade" id="tab-department" role="tabpanel" aria-labelledby="tab-department-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>จัดการ Department</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addDepartmentModal"><i class="fas fa-plus"></i> เพิ่ม Department</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="departmentCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach; endif; ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover" id="departmentTable">
                      <thead class="thead-light">
                        <tr>
                          <th>บริษัท (Company)</th>
                          <th>สายงาน (Function)</th>
                          <th>ชื่อ Department (DeptName)</th>
                          <th>Department Code</th>
                          <th class="text-center" style="width: 150px;">สถานะ</th>
                          <th class="text-center" style="width: 150px;">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="6" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab 4: Section -->
                <div class="tab-pane fade" id="tab-section" role="tabpanel" aria-labelledby="tab-section-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>จัดการ Section</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addSectionModal"><i class="fas fa-plus"></i> เพิ่ม Section</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="sectionCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach; endif; ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover" id="sectionTable">
                      <thead class="thead-light">
                        <tr>
                          <th>บริษัท (Company)</th>
                          <th>สายงาน (Function)</th>
                          <th>ชื่อแผนก (DeptName)</th>
                          <th>ส่วนงาน (Section Name)</th>
                          <th>Section Code</th>
                          <th class="text-center" style="width: 150px;">สถานะ</th>
                          <th class="text-center" style="width: 150px;">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="7" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab 5: Position -->
                <div class="tab-pane fade" id="tab-position" role="tabpanel" aria-labelledby="tab-position-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-id-card-alt mr-2"></i>จัดการ Position</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPositionModal"><i class="fas fa-plus"></i> เพิ่ม Position</button>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover text-nowrap" id="positionTable" style="min-width: 1600px; white-space: nowrap;">
                      <thead class="thead-light">
                        <tr>
                          <th>AbbreviateEN</th>
                          <th>FullNameEN</th>
                          <th>AbbreviateTH</th>
                          <th>FullNameTH</th>
                          <th>Position</th>
                          <th>OrganizeLevel</th>
                          <th>LevelNameEN</th>
                          <th>LevelNameTH</th>
                          <th>OrganizeOrder</th>
                          <th class="text-center">Board</th>
                          <th class="text-center" style="width: 150px;">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="11" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab 6: Admin -->
                <div class="tab-pane fade" id="tab-admin" role="tabpanel" aria-labelledby="tab-admin-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-user-shield mr-2"></i>จัดการ Admin</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAdminModal"><i class="fas fa-plus"></i> เพิ่ม Admin</button>
                  </div>
                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover table-sm">
                      <thead class="thead-light">
                        <tr>
                          <th style="width: 25%;">Username</th>
                          <th style="width: 35%;">Company</th>
                          <th style="width: 25%;">Role</th>
                          <th style="width: 15%;" class="text-center">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody id="table-body-admin">
                        <tr>
                          <td colspan="4" class="text-center">กำลังโหลดข้อมูล...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
      </section>
    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /.wrapper -->

  <!-- Include Modals -->
  <?php
    $this->load->view('admin/modals/modal_employee');
    $this->load->view('admin/modals/modal_function');
    $this->load->view('admin/modals/modal_department', ['companies' => $companies]);
    $this->load->view('admin/modals/modal_section', ['companies' => $companies]);
    $this->load->view('admin/modals/modal_map');
    $this->load->view('admin/modals/modal_position');
    $this->load->view('admin/modals/modal_admin', [
        'companies'        => $companies,
        'admin_role'       => $admin_role,
        'admin_company_id' => $admin_company_id
    ]);
  ?>

  <!-- Load Footer & Base Scripts -->
  <?php $this->load->view('partials/footer'); ?>

  <!-- Select2 JS -->
  <script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
  <!-- Global URL Variables for External JS -->
  <script>
    var BASE_URL = "<?= base_url() ?>";
    var SITE_URL = "<?= site_url() ?>";
  </script>
  <script src="<?= base_url('assets/js/admin-crud.js?v=' . time()) ?>"></script>
</body>
</html>