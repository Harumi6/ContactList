<?php

/**
 * @var string $admin_username
 * @var int $admin_role
 * @var int $admin_company_id
 */
$admin_role = isset($admin_role) ? $admin_role : 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?= base_url('assets/icon-images/logo.png') ?>">
  <title>Admin Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">
  <!-- SweetAlert2 -->
  <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
  <!-- Select2 CSS -->
  <link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
  <!-- Select2 Bootstrap 4 Theme -->
  <link rel="stylesheet" href="<?= base_url('assets/css/select2-bootstrap4.min.css') ?>">

  <style>
    body {
      font-size: 0.9rem;
    }



    /* ชดเชยขนาดของ Modal, Backdrop และ SweetAlert ให้เต็มหน้าจอจริง ๆ หลังจากซูม 90% */
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
      /* match thead-light */
      z-index: 1;
      box-shadow: inset 0 -2px 0 #dee2e6, inset 0 1px 0 #dee2e6;
    }
  </style>
</head>

<body class="hold-transition layout-top-nav bg-light">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a href="#" class="navbar-brand">
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
              <!-- <a href="<?= site_url('send_data/logout') ?>" class="nav-link text-danger"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a> -->
              <button type="button" onclick="confirmLogout()" class="nav-link text-danger" style="background: none; border: none; padding: 0; cursor: pointer;">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
              </button>

              <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              <script>
                function confirmLogout() {
                  Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to logout?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Logout"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      // พอพนักงานกดยืนยันปุ๊บ ให้โชว์แจ้งเตือนสำเร็จ
                      Swal.fire({
                        title: "Logout Success!",
                        icon: "success",
                        timer: 1500, // โชว์ค้างไว้ 1.5 วินาที
                        showConfirmButton: false
                      }).then(() => {
                        // จากนั้นค่อยสั่งให้บราวเซอร์วิ่งไปหา Controller ฝั่ง PHP
                        window.location.href = "<?php echo site_url('send_data/logout'); ?>";
                      });
                    }
                  });
                }
              </script>
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
              <h1 class="m-0">ระบบจัดการข้อมูล</h1>
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
                <!-- <li class="nav-item">
                  <a class="nav-link" id="tab-map-tab" data-toggle="pill" href="#tab-map" role="tab" aria-controls="tab-map" aria-selected="false">Map</a>
                </li> -->
                <li class="nav-item">
                  <a class="nav-link" id="tab-position-tab" data-toggle="pill" href="#tab-position" role="tab" aria-controls="tab-position" aria-selected="false">Position</a>
                </li>
                <!-- <li class="nav-item">
                  <a class="nav-link" id="tab-importexport-tab" data-toggle="pill" href="#tab-importexport" role="tab" aria-controls="tab-importexport" aria-selected="false">Import</a>
                </li> -->
                <li class="nav-item">
                  <a class="nav-link" id="tab-admin-tab" data-toggle="pill" href="#tab-admin" role="tab" aria-controls="tab-admin" aria-selected="false">Admin</a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="custom-tabs-four-tabContent">

                <!-- Tab: Employee -->
                <div class="tab-pane fade show active" id="tab-employee" role="tabpanel" aria-labelledby="tab-employee-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-users mr-2"></i>รายชื่อพนักงาน</h5>
                    <div class="form-inline">
                      <button type="submit" class="btn btn-primary btn-sm" style="margin-right: 10px;" data-toggle="modal" data-target="#addEmployeeModal">เพิ่มพนักงาน</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="crudCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach;
                        endif; ?>
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
                          <th style="width: 100px;" class="text-center">จัดการ</th>
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

                <!-- Tab: Function -->
                <div class="tab-pane fade" id="tab-function" role="tabpanel" aria-labelledby="tab-function-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-building mr-2"></i>จัดการ Function</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addFunctionModal">เพิ่ม Function</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="functionCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach;
                        endif; ?>
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

                <!-- Tab: Department -->
                <div class="tab-pane fade" id="tab-department" role="tabpanel" aria-labelledby="tab-department-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>จัดการ Department</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addDepartmentModal">เพิ่ม Department</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="departmentCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach;
                        endif; ?>
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

                <!-- Tab: Section -->
                <div class="tab-pane fade" id="tab-section" role="tabpanel" aria-labelledby="tab-section-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>จัดการ Section</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addSectionModal">เพิ่ม Section</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="sectionCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach;
                        endif; ?>
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

                <!-- Tab: Map -->
                <div class="tab-pane fade" id="tab-map" role="tabpanel" aria-labelledby="tab-map-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-users mr-2"></i>map employee to section</h5>
                    <div class="form-inline">
                      <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addMapModal">เพิ่ม Map</button>
                      <label class="mr-2">เลือกบริษัท: </label>
                      <select id="mapCompanySelect" class="form-control form-control-sm">
                        <?php if (!empty($companies)): foreach ($companies as $c): ?>
                            <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                        <?php endforeach;
                        endif; ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive table-scrollable">
                    <table class="table table-bordered table-hover" id="mapTable">
                      <thead class="thead-light">
                        <tr>
                          <th>ส่วนงาน (Section Name)</th>
                          <th>พนักงาน (Employee Name)</th>
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

                <!-- Tab: Position -->
                <div class="tab-pane fade" id="tab-position" role="tabpanel" aria-labelledby="tab-position-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>จัดการ position</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPositionModal">เพิ่ม Position</button>
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

                <!-- Tab: Import / Export -->
                <div class="tab-pane fade" id="tab-importexport" role="tabpanel" aria-labelledby="tab-importexport-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-sitemap mr-2"></i>Import ข้อมูลของพนักงาน</h5>
                    <div class="form-inline" style="gap: 5px;">
                      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addImportModal"> Import</button>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="tab-admin" role="tabpanel" aria-labelledby="tab-admin-tab">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary"><i class="fas fa-user-shield mr-2"></i>จัดการ Admin</h5>
                    <div class="form-inline" style="gap: 5px;">
                      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAdminModal"><i class="fas fa-plus"></i> เพิ่ม Admin</button>
                    </div>
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

  </div>

  <!-- Modal Edit Employee -->
  <div class="modal fade" id="modalEditEmployee" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">แก้ไขข้อมูลพนักงาน</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditEmployee" enctype="multipart/form-data">
            <input type="hidden" id="editEmpId" name="user_id">
            <input type="hidden" id="editEmpMapId" name="map_id">

            <div class="form-group">
              <label>ชื่อพนักงาน (ภาษาอังกฤษ)</label>
              <input type="text" class="form-control" id="fullnameEN" name="fullnameen">
            </div>

            <div class="form-group">
              <label>ชื่อพนักงาน (ภาษาไทย)</label>
              <input type="text" class="form-control" id="fullnameTH" name="fullnameth">
            </div>

            <div class="form-group">
              <label>แผนก/หน่วยงาน (Section)</label>
              <select class="form-control" id="editEmpSection" name="sec_id">
                <option value="">-- โหลดข้อมูล --</option>
              </select>
            </div>

            <div class="form-group">
              <label>ตำแหน่ง (Position)</label>
              <select class="form-control" id="editEmpPosition" name="position_id">
                <option value="">-- โหลดข้อมูล --</option>
              </select>
            </div>

            <div class="form-group">
              <label>สถานะ (Status)</label>
              <select class="form-control" id="editEmpStatus" name="status">
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

            <div class="form-group">
              <label>เบอร์โทรศัพท์</label>
              <input type="text" class="form-control" id="editEmpPhone" name="telephone" placeholder="เบอร์โทร">
            </div>

            <div class="form-group">
              <label>อีเมล</label>
              <input type="email" class="form-control" id="editEmpEmail" name="email" placeholder="อีเมล">
            </div>

            <div class="form-group">
              <label>UserLogOn</label>
              <input type="text" class="form-control" id="editEmpUserLogOn" name="user_log_on">
            </div>

            <div class="form-group">
              <label>รูปภาพพนักงาน (jpg, png, jpeg)</label>
              <div class="mb-2 text-center" id="editEmpPicPreviewContainer" style="display: none;">
                <div style="position: relative; display: inline-block;">
                  <img id="editEmpPicPreview" src="" alt="ไม่มีรูปภาพ" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                  <button type="button" class="btn btn-danger btn-sm" id="btnDeletePic" style="position: absolute; bottom: 0; right: 0; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" title="ลบรูปภาพ">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
              <input type="file" class="form-control-file mb-2" id="editEmpPicture" name="picture" accept=".jpg,.png,.jpeg">
              <input type="hidden" id="editEmpDeletePic" name="delete_picture" value="0">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveEmployee">บันทึก</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Employee -->
  <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่มพนักงาน</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEmployee" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 form-group">
                <label>แผนก/หน่วยงาน (Section)</label>
                <select class="form-control" id="addEmpSection" name="sec_id" required>
                  <option value="">-- กำลังโหลดข้อมูล --</option>
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>ตำแหน่ง (Position)</label>
                <select class="form-control" id="addEmpPosition" name="position_id" required>
                  <option value="">-- กำลังโหลดข้อมูล --</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>ชื่อพนักงาน (ภาษาอังกฤษ) - EngName</label>
                <input type="text" class="form-control" id="addEmpFullnameEN" name="fullname" required>
              </div>
              <div class="col-md-6 form-group">
                <label>ชื่อพนักงาน (ภาษาไทย) - ThaiName</label>
                <input type="text" class="form-control" id="addEmpFullnameTH" name="thainame">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>รหัสพนักงาน (StaffID)</label>
                <input type="text" class="form-control" id="addEmpStaffID" name="staff_id">
              </div>
              <div class="col-md-6 form-group">
                <label>ออฟฟิศ (Office)</label>
                <input type="text" class="form-control" id="addEmpOffice" name="office">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>เบอร์มือถือ (MobilePhone)</label>
                <input type="text" class="form-control" id="addEmpMobile" name="mobile_phone">
              </div>
              <div class="col-md-6 form-group">
                <label>เบอร์โต๊ะ (Telephone)</label>
                <input type="text" class="form-control" id="addEmpPhone" name="telephone">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label>อีเมล (EmailAddress)</label>
                <input type="email" class="form-control" id="addEmpEmail" name="email">
              </div>
              <div class="col-md-6 form-group">
                <label>UserLogOn</label>
                <input type="text" class="form-control" id="addEmpUserLogOn" name="user_log_on">
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 form-group">
                <label>รูปภาพพนักงาน (jpg, png, jpeg)</label>
                <div class="mb-2 text-center">
                  <img id="addEmpPicPreview" src="" alt="รูปพรีวิว" style="width: 120px; height: 120px; display: none; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </div>
                <input type="file" class="form-control-file" id="addEmpPicture" name="picture" accept=".jpg,.png,.jpeg">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewEmployee">บันทึกเพิ่มพนักงาน</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Function -->
  <div class="modal fade" id="addFunctionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่ม Function</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddFunction">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="addFuncCompany" name="company_id" readonly>
                <!-- Option จะถูกดึงมาใส่ด้วย JS ตอนเปิด Modal เพื่อให้ตรงกับ dropdown ข้างนอก -->
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อ Function (FuncName)</label>
              <input type="text" class="form-control" id="addFuncName" name="func_name" required>
            </div>
            <div class="form-group">
              <label>Function Code ตัวอย่าง(INNO-001)</label>
              <input type="text" class="form-control" id="addFuncCode" name="func_code" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewFunction">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Function -->
  <div class="modal fade" id="editFunctionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">แก้ไข Function</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditFunction">
            <input type="hidden" id="editFuncId" name="func_id">
            <div class="form-group">
              <label>ชื่อ Function (FuncName)</label>
              <input type="text" class="form-control" id="editFuncName" name="func_name" required>
            </div>
            <div class="form-group">
              <label>Function Code</label>
              <input type="text" class="form-control" id="editFuncCode" name="func_code">
            </div>
            <div class="form-group">
              <label>สถานะ</label>
              <select class="form-control" id="editFuncStatus" name="status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-warning" id="btnSaveEditFunction">บันทึกแก้ไข</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Add Department -->
  <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่ม Department</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddDepartment">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="addDeptCompany" name="company_id" required>
                <option value="">-- เลือกบริษัท --</option>
                <?php if (!empty($companies)): foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกสายงาน (Function)</label>
              <select class="form-control" id="addDeptFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อ Department (DeptName)</label>
              <input type="text" class="form-control" id="addDeptName" name="dept_name" required>
            </div>
            <div class="form-group">
              <label>Department Code (DeptCode)</label>
              <input type="text" class="form-control" id="addDeptCode" name="dept_code">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewDepartment">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Department -->
  <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">แก้ไข Department</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditDepartment">
            <input type="hidden" id="editDeptId" name="dept_id">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="editDeptCompany" name="company_id" required>
                <option value="">-- เลือกบริษัท --</option>
                <?php if (!empty($companies)): foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกสายงาน (Function)</label>
              <select class="form-control" id="editDeptFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อ Department (DeptName)</label>
              <input type="text" class="form-control" id="editDeptName" name="dept_name" required>
            </div>
            <div class="form-group">
              <label>Department Code (DeptCode)</label>
              <input type="text" class="form-control" id="editDeptCode" name="dept_code">
            </div>
            <div class="form-group">
              <label>สถานะ</label>
              <select class="form-control" id="editDeptStatus" name="status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-warning" id="btnSaveEditDepartment">บันทึกแก้ไข</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Section -->
  <div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่ม Section</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddSection">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="addSecCompany" name="company_id" required>
                <option value="">-- เลือกบริษัท --</option>
                <?php if (!empty($companies)): foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกสายงาน (Function)</label>
              <select class="form-control" id="addSecFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกแผนก (DeptName)</label>
              <select class="form-control" id="addSecDepartment" name="dept_id" required>
                <option value="">-- กรุณาเลือกสายงานก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อ Section (SectionName)</label>
              <input type="text" class="form-control" id="addSecName" name="sec_name" required>
            </div>
            <div class="form-group">
              <label>Section Code (SectionCode)</label>
              <input type="text" class="form-control" id="addSecCode" name="sec_code">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewSection">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Section -->
  <div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่ม Section</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddSection">
            <input type="hidden" name="sec_status" value="1">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="addSecCompany" name="company_id" required>
                <option value="">-- เลือกบริษัท --</option>
                <?php if (!empty($companies)): foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกสายงาน (Function)</label>
              <select class="form-control" id="addSecFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกแผนก (Department)</label>
              <select class="form-control" id="addSecDepartment" name="dept_id" required>
                <option value="">-- กรุณาเลือกสายงานก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อส่วนงาน (Section Name)</label>
              <input type="text" class="form-control" id="addSecName" name="sec_name" required>
            </div>
            <div class="form-group">
              <label>Section Code</label>
              <input type="text" class="form-control" id="addSecCode" name="sec_code">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewSection">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Section -->
  <div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">แก้ไข Section</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditSection">
            <input type="hidden" id="editSecId" name="sec_id">
            <div class="form-group">
              <label>เลือกบริษัท (Company)</label>
              <select class="form-control" id="editSecCompany" name="company_id" required>
                <option value="">-- เลือกบริษัท --</option>
                <?php if (!empty($companies)): foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>"><?= html_escape($c->Company) ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกสายงาน (Function)</label>
              <select class="form-control" id="editSecFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>เลือกแผนก (Department)</label>
              <select class="form-control" id="editSecDepartment" name="dept_id" required>
                <option value="">-- กรุณาเลือกสายงานก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ชื่อส่วนงาน (Section Name)</label>
              <input type="text" class="form-control" id="editSecName" name="sec_name" required>
            </div>
            <div class="form-group">
              <label>Section Code</label>
              <input type="text" class="form-control" id="editSecCode" name="sec_code">
            </div>
            <div class="form-group">
              <label>สถานะ</label>
              <select class="form-control" id="editSecStatus" name="status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-warning" id="btnSaveEditSection">บันทึกแก้ไข</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Map -->
  <div class="modal fade" id="addMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่มข้อมูลการ Map</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddMap">
            <div class="form-group">
              <label>บริษัท (Company)</label>
              <select class="form-control" id="addMapCompany" name="company_id" required>
                <option value="">-- กำลังโหลด --</option>
              </select>
            </div>
            <div class="form-group">
              <label>สายงาน (Function)</label>
              <select class="form-control" id="addMapFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>แผนก (Department)</label>
              <select class="form-control" id="addMapDepartment" name="dept_id" required>
                <option value="">-- กรุณาเลือกสายงานก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ส่วนงาน (Section)</label>
              <select class="form-control" id="addMapSection" name="sec_id" required>
                <option value="">-- กรุณาเลือกแผนกก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ค้นหาชื่อพนักงาน (ค้นหาก่อนเลือก)</label>
              <input type="text" id="searchEmpAdd" class="form-control mb-2" placeholder="พิมพ์ชื่อภาษาอังกฤษ หรือ ภาษาไทย เพื่อค้นหา...">

              <label>พนักงาน (Employee)</label>
              <select class="form-control select2" id="addMapEmployee" name="user_id" required style="width: 100%;">
                <option value="">-- กำลังโหลด --</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewMap">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Map -->
  <div class="modal fade" id="editMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title">แก้ไขข้อมูลการ Map</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditMap">
            <input type="hidden" id="editMapId" name="map_id">
            <div class="form-group">
              <label>บริษัท (Company)</label>
              <select class="form-control" id="editMapCompany" name="company_id" required>
                <option value="">-- กำลังโหลด --</option>
              </select>
            </div>
            <div class="form-group">
              <label>สายงาน (Function)</label>
              <select class="form-control" id="editMapFunction" name="func_id" required>
                <option value="">-- กรุณาเลือกบริษัทก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>แผนก (Department)</label>
              <select class="form-control" id="editMapDepartment" name="dept_id" required>
                <option value="">-- กรุณาเลือกสายงานก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>ส่วนงาน (Section)</label>
              <select class="form-control" id="editMapSection" name="sec_id" required>
                <option value="">-- กรุณาเลือกแผนกก่อน --</option>
              </select>
            </div>
            <div class="form-group">
              <label>พนักงาน (Employee)</label>
              <select class="form-control select2" id="editMapEmployee" name="user_id" required style="width: 100%;">
                <option value="">-- กำลังโหลด --</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-warning" id="btnSaveEditMap">บันทึกแก้ไข</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add Position -->
  <div class="modal fade" id="addPositionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">เพิ่ม Position</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddPosition">
            <div class="row">
              <div class="col-md-6 form-group">
                <label>AbbreviateEN</label>
                <input type="text" class="form-control" name="abbreviate_en">
              </div>
              <div class="col-md-6 form-group">
                <label>FullNameEN</label>
                <input type="text" class="form-control" name="fullname_en">
              </div>
              <div class="col-md-6 form-group">
                <label>AbbreviateTH</label>
                <input type="text" class="form-control" name="abbreviate_th">
              </div>
              <div class="col-md-6 form-group">
                <label>FullNameTH</label>
                <input type="text" class="form-control" name="fullname_th">
              </div>
              <div class="col-md-12 form-group">
                <label>Position</label>
                <input type="text" class="form-control" name="position">
              </div>
              <div class="col-md-4 form-group">
                <label>OrganizeLevel</label>
                <select class="form-control" id="addPosLevel" name="organize_level">
                  <option value="">-- เลือก --</option>
                </select>
              </div>
              <div class="col-md-4 form-group">
                <label>OrganizeOrder</label>
                <select class="form-control" id="addPosOrder" name="organize_order">
                  <option value="">-- กรุณาเลือก Level ก่อน --</option>
                </select>
                <small class="form-text text-muted" id="addPosOrderHint"></small>
              </div>
              <div class="col-md-4 form-group">
                <label>Board</label>
                <select class="form-control" name="board">
                  <option value="0">0 (ไม่ใช่บอร์ด)</option>
                  <option value="1">1 (เป็นบอร์ด)</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-primary" id="btnSaveNewPosition">บันทึกเพิ่ม</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Position -->
  <div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title">แก้ไข Position</h5>
          <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditPosition">
            <input type="hidden" id="editPosId" name="position_id">
            <div class="row">
              <div class="col-md-6 form-group">
                <label>AbbreviateEN</label>
                <input type="text" class="form-control" id="editPosAbbEN" name="abbreviate_en">
              </div>
              <div class="col-md-6 form-group">
                <label>FullNameEN</label>
                <input type="text" class="form-control" id="editPosFullEN" name="fullname_en">
              </div>
              <div class="col-md-6 form-group">
                <label>AbbreviateTH</label>
                <input type="text" class="form-control" id="editPosAbbTH" name="abbreviate_th">
              </div>
              <div class="col-md-6 form-group">
                <label>FullNameTH</label>
                <input type="text" class="form-control" id="editPosFullTH" name="fullname_th">
              </div>
              <div class="col-md-12 form-group">
                <label>Position</label>
                <input type="text" class="form-control" id="editPosPosition" name="position">
              </div>
              <div class="col-md-4 form-group">
                <label>OrganizeLevel</label>
                <select class="form-control" id="editPosLevel" name="organize_level">
                  <option value="">-- เลือก --</option>
                </select>
              </div>
              <div class="col-md-4 form-group">
                <label>OrganizeOrder</label>
                <select class="form-control" id="editPosOrder" name="organize_order">
                  <option value="">-- กรุณาเลือก Level ก่อน --</option>
                </select>
                <small class="form-text text-muted" id="editPosOrderHint"></small>
              </div>
              <div class="col-md-4 form-group">
                <label>Board</label>
                <select class="form-control" id="editPosBoard" name="board">
                  <option value="0">0 (ไม่ใช่บอร์ด)</option>
                  <option value="1">1 (เป็นบอร์ด)</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-warning" id="btnSaveEditPosition">บันทึกแก้ไข</button>
        </div>
      </div>
    </div>
  </div>

    <!-- Modal Add Admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">เพิ่ม Admin</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formAddAdmin">
              <div class="form-group">
                <label>Username (Login)</label>
                <input type="text" class="form-control" id="addAdminLogin" name="login" required>
              </div>
              <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="addAdminPassword" name="password" required>
              </div>
              <div class="form-group">
                <label>Company</label>
                <select class="form-control" id="addAdminCompany" name="company_id" <?php if ($admin_role == 1) echo 'disabled'; ?>>
                  <?php if ($admin_role == 0): ?>
                    <option value="">Master (ดูแลทุกบริษัท)</option>
                  <?php endif; ?>
                  <?php if (!empty($companies)): foreach ($companies as $c): ?>
                      <option value="<?= html_escape($c->CompanyID) ?>" <?php if ($admin_role == 1 && $admin_company_id == $c->CompanyID) echo 'selected'; ?>><?= html_escape($c->Company) ?></option>
                  <?php endforeach;
                  endif; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Role</label>
                <select class="form-control" id="addAdminRole" name="role" <?php if ($admin_role == 1) echo 'disabled'; ?>>
                  <?php if ($admin_role == 0): ?>
                    <option value="0">0 = Master</option>
                  <?php endif; ?>
                  <option value="1" <?php if ($admin_role == 1) echo 'selected'; ?>>1 = Company Admin</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
            <button type="button" class="btn btn-primary" id="btnSaveNewAdmin">บันทึกเพิ่ม</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Edit Admin -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h5 class="modal-title">แก้ไข Admin</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="formEditAdmin">
              <input type="hidden" id="editAdminId" name="login_id">
              <div class="form-group">
                <label>Username (Login)</label>
                <input type="text" class="form-control" id="editAdminLogin" name="login" required>
              </div>
              <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="editAdminPassword" name="password" placeholder="เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยน">
              </div>
              <div class="form-group">
                <label>Company</label>
                <select class="form-control" id="editAdminCompany" name="company_id" <?php if ($admin_role == 1) echo 'disabled'; ?>>
                  <?php if ($admin_role == 0): ?>
                    <option value="">Master (ดูแลทุกบริษัท)</option>
                  <?php endif; ?>
                  <?php if (!empty($companies)): foreach ($companies as $c): ?>
                      <option value="<?= html_escape($c->CompanyID) ?>" <?php if ($admin_role == 1 && $admin_company_id == $c->CompanyID) echo 'selected'; ?>><?= html_escape($c->Company) ?></option>
                  <?php endforeach;
                  endif; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Role</label>
                <select class="form-control" id="editAdminRole" name="role" <?php if ($admin_role == 1) echo 'disabled'; ?>>
                  <?php if ($admin_role == 0): ?>
                    <option value="0">0 = Master</option>
                  <?php endif; ?>
                  <option value="1" <?php if ($admin_role == 1) echo 'selected'; ?>>1 = Company Admin</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
            <button type="button" class="btn btn-warning" id="btnSaveEditAdmin">บันทึกแก้ไข</button>
          </div>
        </div>
      </div>
    </div>

  <!-- /.content-wrapper -->
  <footer class="main-footer text-center" style="position: fixed; bottom: 0; width: 100%; z-index: 1030; margin: 0; padding: 10px 0;">
    <strong>Copyright &copy; 2026 ATTG.</strong> All rights reserved.
    <br><small>Sync from Success Factor | Data Update (<span class="data-update-date">กำลังโหลด...</span>)</small>
  </footer>
</div>
<!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
  <script>
    $(document).ready(function() {
      $.get('<?= site_url("send_data/get_last_update_date_ajax") ?>', function(res) {
        try {
          var data = JSON.parse(res);
          $('.data-update-date').text(data.last_update);
        } catch (e) {}
      });
    });
  </script>
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/adminlte.min.js') ?>"></script>
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