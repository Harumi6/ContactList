<?php

?>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?= base_url('assets/icon-images/logo.png') ?>">
  <title>ATTG Contact</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">
  <style>
    body {
      font-size: 0.9rem;
    }

    .table-scrollable {
      max-height: calc(100vh - 230px);
      overflow-y: auto;
      overflow-x: auto;
    }

    .table-scrollable thead th {
      position: sticky;
      top: 0;
      background-color: #0069a4ff;
      color: white;
      /* ป้องกันเนื้อหาซ้อนทับ th */
      z-index: 1;
      /* ให้ th อยู่ด้านบนเสมอ */
      box-shadow: inset 0 -2px 0 #dee2e6;
      /* เส้นขอบด้านล่างของ header */
    }

    .tr-function {
      background-color: #93E4F2 !important;
      /* color: #0c5460; */
      font-weight: bold;
    }

    .tr-department {
      background-color: #DDDDDD !important;
      /* color: #0c5460; */
      font-weight: bold;
    }

    .tr-section {
      background-color: #EFEFEF !important;
      /* color: #721c24; */
      font-weight: bold;
    }

    /* ===== Login Form ===== */
    #loginSection {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 1040;
      /* Lower than SweetAlert2 (1060) so alerts are visible */
      background: linear-gradient(135deg, #444444 0%, #111111 100%);
    }

    .login-container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      width: 100%;
    }

    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(255, 255, 255, 0.2);
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
      animation: loginSlideUp .4s ease-out;
    }

    @keyframes loginSlideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-card .login-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-card .login-header i {
      font-size: 48px;
      color: #444444;
      margin-bottom: 12px;
    }

    .login-card .login-header h3 {
      font-weight: 700;
      color: #333;
      margin-bottom: 4px;
    }

    .login-card .login-header p {
      color: #999;
      font-size: 14px;
    }

    .login-card .form-group label {
      font-weight: 600;
      color: #555;
      font-size: 14px;
    }

    .login-card .form-control {
      border-radius: 8px;
      padding: 10px 14px;
      border: 1.5px solid #e0e0e0;
      transition: border-color .2s, box-shadow .2s;
    }

    .login-card .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, .15);
    }

    .login-card .btn-login {
      background: linear-gradient(135deg, #444444 0%, #222222 100%);
      border: none;
      border-radius: 8px;
      padding: 10px;
      font-size: 16px;
      font-weight: 600;
      color: #fff;
      width: 100%;
      transition: opacity .2s, transform .1s;
    }

    .login-card .btn-login:hover {
      opacity: .9;
    }

    .login-card .btn-login:active {
      transform: scale(.98);
    }

    .login-card .btn-back {
      display: block;
      text-align: center;
      margin-top: 16px;
      color: #999;
      font-size: 13px;
      cursor: pointer;
      transition: color .2s;
    }

    .login-card .btn-back:hover {
      color: #667eea;
    }

    #fullScreenLoader {
      display: flex;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      flex-direction: column;
    }
  </style>
</head>

<body class="hold-transition layout-top-nav">
  <div id="fullScreenLoader">
    <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
    <div>กำลังโหลดข้อมูล...</div>
  </div>
  <div class="wrapper" style="position: fixed;top: 0;left: 0; height: 100%; width: 100%;">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
      <div class="container-fluid">
        <a href="#" class="navbar-brand ">
          <img src="<?= base_url('assets/icon-images/logo.png') ?>" alt="logo" width="100" height="30"> 
          <span class="brand-text font-weight-bold">Contact List</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3 w-100" id="navbarCollapse">
          <!-- Middle: selections -->
          <form class="form-inline m-auto" style="flex-wrap: nowrap;">
            <div class="form-group mr-3">
              <label class="mr-2 font-weight-normal mb-0" style="white-space: nowrap;">Company :</label>
              <select id="companySelect" class="form-control form-control-sm custom-select" style="width: 140px;">
                <?php if (!empty($companies)): ?>
                  <?php foreach ($companies as $c): ?>
                    <option value="<?= html_escape($c->CompanyID) ?>" 
                            data-fullname="<?= html_escape($c->FullName) ?>" 
                            data-address="<?= html_escape($c->Address) ?>" 
                            data-tel="<?= html_escape($c->Remark) ?>" 
                            <?= ($c->Company == 'AT-A') ? 'selected' : '' ?>><?= html_escape($c->Company) ?></option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option value="">No companies found</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="mr-2 font-weight-normal mb-0" style="white-space: nowrap;">Function :</label>
              <select class="form-control form-control-sm custom-select" id="dept" style="width: 250px;">
                <option>-All-</option>
                <?php if (!empty($departments)): ?>
                  <?php foreach ($departments as $d): ?>
                    <option value="<?= html_escape($d->FuncName) ?>" <?= ($d->FuncName == 'AT-A') ? 'selected' : '' ?>><?= html_escape($d->FuncName) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </form>

          <!-- Right: Search Form -->
          <form class="form-inline ml-auto my-auto">
            <div class="form-group position-relative">
              <input class="form-control form-control-sm mr-2 w-100" type="search" placeholder="Search Employee Name" aria-label="Search" id="SearchBar" style="max-width: 250px; background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 4px; padding-left: 10px;" autocomplete="off">
              <div id="searchResults" class="dropdown-menu w-100" style="max-width: 250px !important; display: none; position: absolute; top: calc(100% + 5px); left: 0; max-height: 300px; overflow-y: auto; z-index: 1050; padding: 0.5rem 0; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);"></div>
            </div>
            <button class="btn btn-sm btn-secondary" type="button" id="SearchButton">
              <i class="fas fa-search"></i> Search
            </button>
            <!-- <button type="button" class="btn btn-sm btn-primary ml-2" id="export"><i class="fas fa-file-excel"></i> Export To Excel</button> -->
          </form>
          <!-- User Dropdown Menu -->
          <ul class="navbar-nav ml-2">
            <li class="nav-item dropdown">
              <a class="nav-link" data-toggle="dropdown" href="#" style="padding-top: 0.2rem; padding-bottom: 0;">
                <i class="fa fa-user-circle-o fa-2x text-secondary" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <?php if ($this->session->userdata('admin_logged_in')): ?>
                  <!-- สำหรับ Admin -->
                  <div class="dropdown-header text-primary font-weight-bold text-left">
                    <i class="fas fa-user-shield mr-1"></i> <?= html_escape($this->session->userdata('admin_username')) ?>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a href="<?= site_url('send_data/admin_dashboard') ?>" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> จัดการระบบ
                  </a>
                  <a href="<?= site_url('send_data/logout') ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                  </a>

                <?php elseif ($this->session->userdata('employee_logged_in')): ?>
                  <!-- สำหรับ พนักงาน -->
                  <div class="dropdown-header text-success font-weight-bold text-left">
                    <i class="fas fa-user mr-1"></i> <?= html_escape($this->session->userdata('employee_fullname') ?: $this->session->userdata('employee_username')) ?>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a href="<?= site_url('send_data/employee_dashboard') ?>" class="dropdown-item">
                    <i class="fas fa-id-badge mr-2"></i> โปรไฟล์ของฉัน
                  </a>
                  <a href="<?= site_url('send_data/logout') ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                  </a>

                <?php else: ?>
                  <!-- หากยังไม่ Login -->
                  <a href="#" class="dropdown-item" id="btnShowLogin">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                  </a>
                <?php endif; ?>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

      <!-- Main content -->
      <section class="content">
        <div class="card mt-4">
          <div class="card-header bg-dark">
            <?php if (!empty($company)): ?>
              <h4 id="companyFullname" class="card-title text-white font-weight-bold"><?= html_escape($company->FullName) ?></h4><br>
              <h6 class="card-title text-white font-weight-light">Address : <span id="companyAddress"><?= html_escape($company->Address) ?></span></h6>
              <h5 class="card-title float-right text-white font-weight-normal mt-n4"><span id="companyTel"><?= html_escape($company->Remark) ?></span></h5>
            <?php else: ?>
              <h4 class="card-title text-white font-weight-bold">No company selected</h4>
            <?php endif; ?>
          </div>



          <!-- /.card-header -->
          <div class="card-body p-0 table-scrollable table-responsive">
            <table id="example1" class="table table-bordered table-striped m-0">
              <thead style="text-align: center;">
                <tr>
                  <th>EmpID</th>
                  <th>Name</th>
                  <th>Position</th>
                  <!-- <th>Mobile No.</th> -->
                  <th>Internal No.</th>
                  <th>Email</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // 1. ตั้งค่าเริ่มต้นตัวแปรจดจำสถานะ (State) เป็นค่าว่าง
                $current_function = null;   // 🌟 เพิ่มตัวแปร Function เข้ามา
                $current_department = null;
                $current_section = null;
                // echo "<pre>";
                // print_r($employees);
                // echo "</pre>";
                if (!empty($employees)):
                  foreach ($employees as $row):

                    // ----------------------------------------------------
                    // Step 1: ตรวจสอบ Function (สายงานหลัก - ระดับสูงสุด)
                    // ----------------------------------------------------
                    if (!empty($row->FuncName) && $row->FuncName !== $current_function) {
                      echo '<tr class="tr-function">';
                      echo '<td colspan="5" style="border: 1px solid #6c757d;">Function : ' . html_escape($row->FuncName) . '</td>';
                      echo '</tr>';

                      $current_function = $row->FuncName;
                      $current_department = null;
                      $current_section = null;
                    }

                    // ----------------------------------------------------
                    // Step 2 & 3: ตรวจสอบ Department และ Section ตามระดับพนักงาน
                    // ----------------------------------------------------

                    if ($row->OrganizeLevel >= 2) {
                      if (!empty($row->DeptName) && $row->DeptName !== $current_department) {
                        echo '<tr class="tr-department">';
                        echo '<td colspan="5" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' . html_escape($row->DeptName) . '</td>';
                        echo '</tr>';

                        $current_department = $row->DeptName;
                        $current_section = null;
                      }
                    }

                    if ($row->OrganizeLevel >= 3) {
                      if (!empty($row->SecName) && $row->SecName !== $current_section) {
                        echo '<tr class="tr-section">';
                        echo '<td colspan="5" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' . html_escape($row->SecName) . '</td>';
                        echo '</tr>';

                        $current_section = $row->SecName;
                      }
                    }

                    // ----------------------------------------------------
                    // Step 4: แสดงข้อมูลพนักงาน
                    // ----------------------------------------------------

                    echo '<tr style="background-color: #ffffff;">';
                    echo '<td id="empid">' . (!empty($row->UserLogOn) ? html_escape($row->UserLogOn) : '') . '</td>';
                    echo '<td id="fullname">' . html_escape($row->Fullname) . (!empty($row->ThaiName) ? ' (' . html_escape($row->ThaiName) . ')' : '') . '</td>';
                    if ($row->OrganizeLevel == 3 && $row->OrganizeOrder > 4) {
                      echo '<td id="position"></td>';
                    } else {
                      echo '<td id="position">' . html_escape($row->Position) . '</td>';
                    }
                    // echo '<td id="mobile">' . html_escape($row->MobilePhone) . '</td>';
                    $telDisplay = html_escape($row->TelePhone);
                    if (!empty($row->internal_no)) {
                        $telDisplay .= ($telDisplay !== '' ? ' ' : '') . html_escape($row->internal_no);
                    }
                    echo '<td id="telephone">' . $telDisplay . '</td>';
                    echo '<td id="email">' . html_escape($row->EmailAddress) . '</td>';
                    echo '</tr>';

                  endforeach;
                else:
                  // แสดงโครงสร้าง Function → Department → Section แม้ไม่มีพนักงาน
                  if (!empty($org_structure)):
                    $current_function = null;
                    $current_department = null;
                    $current_section = null;

                    foreach ($org_structure as $row):
                      // แถวหัว Function
                      if ($row->FuncName !== $current_function) {
                        $funcDisplay = !empty($row->FuncName) ? html_escape($row->FuncName) : '(ไม่ระบุ)';
                        echo '<tr class="tr-function">';
                        echo '<td colspan="5" style="border: 1px solid #6c757d;">Function : ' . $funcDisplay . '</td>';
                        echo '</tr>';
                        $current_function = $row->FuncName;
                        $current_department = null;
                        $current_section = null;
                      }
                      // แถวหัว Department
                      if ($row->DeptName !== $current_department) {
                        $deptDisplay = !empty($row->DeptName) ? html_escape($row->DeptName) : '(ไม่ระบุ)';
                        echo '<tr class="tr-department">';
                        echo '<td colspan="5" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' . $deptDisplay . '</td>';
                        echo '</tr>';
                        $current_department = $row->DeptName;
                        $current_section = null;
                      }
                      // แถวหัว Section
                      if ($row->SecName !== $current_section) {
                        $secDisplay = !empty($row->SecName) ? html_escape($row->SecName) : '(ไม่ระบุ)';
                        echo '<tr class="tr-section" style="background-color: #f1f3f5;">';
                        echo '<td colspan="5" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' . $secDisplay . '</td>';
                        echo '</tr>';
                        $current_section = $row->SecName;
                      }
                    endforeach;
                  else:
                ?>
                    <tr>
                      <td colspan="5" class="text-center">No data found.</td>
                    </tr>
                <?php
                  endif;
                endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->


    </div>
    <!-- /.card -->
    </section>
    <!-- /.content -->

    <!-- ===== Login Section (ซ่อนไว้) ===== -->
    <div id="loginSection" style="display:none;">
      <div class="login-container">
        <div class="login-card">
          <div class="login-header">
            <i class="fa fa-user-circle-o fa-4x text-secondary mb-3"></i>
            <h3>Log In</h3>
            <p class="text-muted">เข้าสู่ระบบเพื่อจัดการข้อมูล</p>
          </div>
          <form id="loginForm">
            <div class="form-group">
              <label for="loginUsername"><i class="fas fa-user mr-1 text-secondary"></i> Username</label>
              <input type="text" class="form-control" id="loginUsername" name="username" placeholder="Enter your username" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="loginPassword"><i class="fas fa-lock mr-1 text-secondary"></i> Password</label>
              <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password">
            </div>
            <div id="loginError" class="text-danger text-center mb-3" style="display:none; font-size:13px;"></div>
            <button type="submit" class="btn btn-login" id="btnLoginSubmit">
              <i class="fas fa-sign-in-alt mr-1"></i> Login
            </button>
            <span class="btn-back" id="btnBackToTable">
              <i class="fas fa-arrow-left mr-1"></i> กลับหน้าหลัก
            </span>
          </form>
        </div>
      </div>
    </div>
    <!-- /#loginSection -->

  </div>
  <!-- /.content-wrapper -->



  <footer class="main-footer text-center" style="position: fixed; bottom: 0; width: 100%; z-index: 1030; margin: 0; padding: 10px 0;" id="footer">
    <strong>Copyright &copy; 2026 ATTG</strong> All rights reserved.
    <br><small>Sync from Success Factor | Data Update (<span class="data-update-date">กำลังโหลด...</span>)</small>
  </footer>
</div>
<!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
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
  <!-- Bootstrap 4 -->
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url('assets/js/adminlte.min.js') ?>"></script>
  <!-- SweetAlert2 -->
  <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
  <!-- ExcelJS and FileSaver for Perfect Excel Export -->
  <script src="<?= base_url('assets/js/exceljs.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/FileSaver.min.js') ?>"></script>

  <script>
    $(document).ready(function() {

      /**
       * Export HTML Table to Excel (.xlsx) using ExcelJS for precise styling
       */
      $('#export').on('click', function(e) {
        e.preventDefault();

        var table = document.getElementById('example1');
        var rowCount = table.rows.length;

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (rowCount <= 1 || (rowCount == 2 && table.rows[1].cells.length == 1 && table.rows[1].innerText.includes('No data'))) {
          Swal.fire('แจ้งเตือน', 'ไม่มีข้อมูลสำหรับ Export', 'warning');
          return;
        }

        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลัง Export...');

        var wb = new ExcelJS.Workbook();
        var ws = wb.addWorksheet('Employees', {
          views: [{
            showGridLines: false
          }]
        });

        // 1. กำหนดความกว้างของคอลัมน์ (Column Widths) แบบเป๊ะๆ
        ws.columns = [{
            width: 45
          }, // Name / Hierarchy
          {
            width: 30
          }, // Position
          {
            width: 15
          }, // Mobile
          {
            width: 15
          }, // Telephone
          {
            width: 35
          } // Email
        ];

        var rows = table.rows;
        for (var i = 0; i < rows.length; i++) {
          var tr = rows[i];
          var rowData = [];

          // ดึงข้อความจากแต่ละ cell
          for (var j = 0; j < tr.cells.length; j++) {
            rowData.push(tr.cells[j].innerText.trim());
          }

          var excelRow = ws.addRow(rowData);

          // 2. จัดการ Merge Cells สำหรับแถว Function, Department, Section (colspan="5")
          if (tr.cells.length === 1 && tr.cells[0].colSpan > 1) {
            var endCol = tr.cells[0].colSpan;
            ws.mergeCells(excelRow.number, 1, excelRow.number, endCol);
          }

          // 3. กำหนดสไตล์, สีพื้นหลัง, ขนาดตัวอักษร, กรอบ
          var isHeader = (tr.closest('thead') !== null);
          var isFunction = tr.classList.contains('tr-function');
          var isDepartment = tr.classList.contains('tr-department');
          var isSection = tr.classList.contains('tr-section');

          excelRow.eachCell({
            includeEmpty: true
          }, function(cell, colNumber) {
            // Border
            cell.border = {
              top: {
                style: 'thin',
                color: {
                  argb: 'FF6C757D'
                }
              },
              left: {
                style: 'thin',
                color: {
                  argb: 'FF6C757D'
                }
              },
              bottom: {
                style: 'thin',
                color: {
                  argb: 'FF6C757D'
                }
              },
              right: {
                style: 'thin',
                color: {
                  argb: 'FF6C757D'
                }
              }
            };
            // Default Align
            cell.alignment = {
              vertical: 'middle',
              wrapText: true,
              indent: 0
            };

            if (isHeader) {
              // หัวตาราง
              cell.font = {
                name: 'Arial',
                size: 11,
                bold: true
              };
              cell.alignment = {
                vertical: 'middle',
                horizontal: 'center'
              };
              cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                  argb: 'FFD6D8DB'
                }
              };
            } else if (isFunction) {
              // Function Row (#93E4F2 - Blue)
              cell.font = {
                name: 'Arial',
                size: 11,
                bold: true
              };
              cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                  argb: 'FF93E4F2'
                }
              };
            } else if (isDepartment) {
              // Department Row (#DDDDDD - Gray)
              cell.font = {
                name: 'Arial',
                size: 11,
                bold: true
              };
              cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                  argb: 'FFDDDDDD'
                }
              };
              if (colNumber === 1) cell.alignment.indent = 2; // เลื่อนขวาเหมือน padding-left
            } else if (isSection) {
              // Section Row (#EFEFEF - Light Gray)
              cell.font = {
                name: 'Arial',
                size: 11,
                bold: true
              };
              cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                  argb: 'FFEFEFEF'
                }
              };
              if (colNumber === 1) cell.alignment.indent = 4; // เลื่อนขวาเหมือน padding-left
            } else {
              // พนักงานปกติ
              cell.font = {
                name: 'Arial',
                size: 10
              };
              cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                  argb: 'FFFFFFFF'
                }
              };
            }
          });

          // ความสูงแถว
          excelRow.height = 25;
        }

        // 4. บันทึกและสร้างไฟล์ดาวน์โหลด
        wb.xlsx.writeBuffer().then(function(data) {
          var blob = new Blob([data], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
          });
          var company = $('#companySelect option:selected').text().trim();
          var dateStr = new Date().toISOString().slice(0, 10);
          var filename = "Employee_List_" + company + "_" + dateStr + ".xlsx";
          saveAs(blob, filename);

          $btn.prop('disabled', false).html(originalHtml);
        }).catch(function(err) {
          Swal.fire('ข้อผิดพลาด', 'ไม่สามารถสร้างไฟล์ Excel ได้', 'error');
          $btn.prop('disabled', false).html(originalHtml);
        });
      });

      /**
       * โหลดพนักงานตาม company_id และ department (AJAX)
       */
      var currentCompanyData = [];
      var currentCompanyStatus = '';

      function escapeHtml(unsafe) {
        return (unsafe || '').toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
      }

      function renderEmployeesTable(dataArray, status) {
        var $tbody = $('#example1 tbody');
        $tbody.empty();

        if (status === 'error' || status === 'empty') {
          $tbody.html('<tr><td colspan="5" class="text-center">No data found.</td></tr>');
          return;
        }
        if (dataArray.length === 0) {
          $tbody.html('<tr><td colspan="5" class="text-center">No employee found.</td></tr>');
          return;
        }

        var html = '';
        var curFunc = null, curDept = null, curSec = null;

        if (status === 'org_only') {
          $.each(dataArray, function(i, row) {
            if (row.FuncName !== curFunc) {
              var fDisplay = row.FuncName ? escapeHtml(row.FuncName) : '(ไม่ระบุ)';
              html += '<tr class="tr-function"><td colspan="5" style="border: 1px solid #6c757d;">Function : ' + fDisplay + '</td></tr>';
              curFunc = row.FuncName;
              curDept = null;
              curSec = null;
            }
            if (row.DeptName !== curDept) {
              var dDisplay = row.DeptName ? escapeHtml(row.DeptName) : '(ไม่ระบุ)';
              html += '<tr class="tr-department" style="background-color: #f8f9fa;"><td colspan="5" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' + dDisplay + '</td></tr>';
              curDept = row.DeptName;
              curSec = null;
            }
            if (row.SecName !== curSec) {
              var sDisplay = row.SecName ? escapeHtml(row.SecName) : '(ไม่ระบุ)';
              html += '<tr class="tr-section"><td colspan="5" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' + sDisplay + '</td></tr>';
              curSec = row.SecName;
            }
          });
          $tbody.html(html);
          return;
        }

        // Normal display
        $.each(dataArray, function(i, row) {
          if (row.FuncName && row.FuncName !== curFunc) {
            html += '<tr class="tr-function"><td colspan="5" style="border: 1px solid #6c757d;">Function : ' + escapeHtml(row.FuncName) + '</td></tr>';
            curFunc = row.FuncName;
            curDept = null;
            curSec = null;
          }
          if (parseInt(row.OrganizeLevel) >= 2) {
            if (row.DeptName && row.DeptName !== curDept) {
              html += '<tr class="tr-department" style="background-color: #f8f9fa;"><td colspan="5" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' + escapeHtml(row.DeptName) + '</td></tr>';
              curDept = row.DeptName;
              curSec = null;
            }
          }
          if (parseInt(row.OrganizeLevel) >= 3) {
            if (row.SecName && row.SecName !== curSec) {
              html += '<tr class="tr-section"><td colspan="5" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' + escapeHtml(row.SecName) + '</td></tr>';
              curSec = row.SecName;
            }
          }

          if (row.Fullname) {
            html += '<tr style="background-color: #ffffff;">';
            html += '<td id="empid">' + (row.UserLogOn ? escapeHtml(row.UserLogOn) : '') + '</td>';
            html += '<td>' + escapeHtml(row.Fullname) + (row.ThaiName ? ' (' + escapeHtml(row.ThaiName) + ')' : '') + '</td>';
            
            var displayPos = row.Position || '';
            if (parseInt(row.OrganizeLevel) === 3 && parseInt(row.OrganizeOrder) > 4) {
              displayPos = '';
            }
            html += '<td id="position">' + escapeHtml(displayPos) + '</td>';
            
            var telDisplay = escapeHtml(row.TelePhone || '');
            if (row.internal_no && row.internal_no.trim() !== '') {
              telDisplay += (telDisplay !== '' ? ' / ' : '') + escapeHtml(row.internal_no);
            }
            html += '<td>' + telDisplay + '</td>';
            
            html += '<td>' + escapeHtml(row.EmailAddress || '') + '</td>';
            html += '</tr>';
          }
        });
        $tbody.html(html);
      }

      function filterEmployees() {
        var keyword = $('#SearchBar').val().trim().toLowerCase();
        var department = $('#dept').val();

        if (currentCompanyStatus === 'error' || currentCompanyStatus === 'empty') {
          renderEmployeesTable([], currentCompanyStatus);
          return;
        }

        var filteredData = currentCompanyData.filter(function(row) {
          var matchDept = true;
          if (department && department !== '-All-') {
            matchDept = (row.FuncName === department);
          }
          var matchKeyword = true;
          if (keyword.length > 0) {
            var fullEng = (row.Fullname || '').toLowerCase();
            var fullThai = (row.ThaiName || '').toLowerCase();
            var funcName = (row.FuncName || '').toLowerCase();
            var deptName = (row.DeptName || '').toLowerCase();
            var secName = (row.SecName || '').toLowerCase();
            
            matchKeyword = fullEng.includes(keyword) || 
                           fullThai.includes(keyword) || 
                           funcName.includes(keyword) || 
                           deptName.includes(keyword) || 
                           secName.includes(keyword);
          }
          return matchDept && matchKeyword;
        });

        // หากค้นหาแล้วไม่พบพนักงาน ให้เคลียร์ช่องค้นหาตามที่ระบุ
        if (filteredData.length === 0 && keyword.length > 0) {
          $('#SearchBar').val('');
        }

        renderEmployeesTable(filteredData, currentCompanyStatus);
      }

      function loadEmployees(isSilent = false) {
        var companyId = $('#companySelect').val();
        if (!companyId) return;

        if (!isSilent) {
          // แสดงกำลังโหลดทันทีก่อนส่ง AJAX
          $('#fullScreenLoader').css('display', 'flex');
          $('#example1 tbody').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');
        }

        $.ajax({
          url: '<?= site_url("send_data/get_employees") ?>',
          type: 'GET',
          dataType: 'json',
          data: {
            company_id: companyId,
            department: '-All-', // บังคับดึงทั้งหมดของบริษัทมาเก็บไว้ใน RAM
            keyword: ''
          },
          success: function(res) {
            currentCompanyStatus = res.status;
            currentCompanyData = res.data || [];
            
            // นำข้อมูลที่ได้ไปคัดกรองผ่านช่องค้นหา/แผนกปัจจุบัน แล้วแสดงผล
            // กรณี isSilent จะอัพเดตตารางก็ต่อเมื่อผู้ใช้เริ่มพิมพ์ค้นหาหรือเปลี่ยนแผนกระหว่างที่กำลังโหลดแบคกราวนด์
            if (!isSilent || $('#SearchBar').val().trim().length > 0 || $('#dept').val() !== '-All-') {
              filterEmployees();
            }
          },
          error: function() {
            currentCompanyStatus = 'error';
            currentCompanyData = [];
            if (!isSilent) {
              filterEmployees();
            }
          },
          complete: function() {
            if (!isSilent) {
              $('#fullScreenLoader').hide();
            }
          }
        });
      }

      // โหลดข้อมูลเข้า RAM ตั้งแต่หน้าเว็บเปิดขึ้นมาครั้งแรก (แสดง Loading จนกว่าจะโหลดเสร็จ)
      $(document).ready(function() {
        if ($('#companySelect').val()) {
          $('#companySelect').trigger('change');
        } else {
          $('#fullScreenLoader').hide(); // ถ้าไม่มีบริษัทเลย ให้ซ่อน loader
        }
      });

      /**
       * เมื่อเปลี่ยนบริษัท → อัพเดตข้อมูลบริษัท + แผนก + พนักงาน
       */
      $('#companySelect').on('change', function() {
        var companyId = $(this).val();
        if (!companyId) return;

        // เคลียร์ช่องค้นหาเมื่อเปลี่ยนบริษัท
        $('#SearchBar').val('');

        // 1. อัพเดตข้อมูลบริษัทในส่วน header ทันทีจาก data attributes (ไม่ต้องรอ AJAX)
        var $selected = $(this).find('option:selected');
        $('#companyFullname').text($selected.data('fullname'));
        $('#companyAddress').text($selected.data('address'));
        $('#companyTel').text($selected.data('tel'));

        // แสดงหน้าจอโหลด
        $('#fullScreenLoader').css('display', 'flex');
        $('#dept').html('<option selected>-All-</option>');
        $('#example1 tbody').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        // 2. โหลดข้อมูลแผนกแบบ Asynchronous
        $.ajax({
          url: '<?= site_url("send_data/get_company_info") ?>',
          type: 'GET',
          dataType: 'json',
          data: { id: companyId },
          success: function(res) {
            if (res.status === 'success') {
              $('#dept').empty().append('<option selected>-All-</option>');
              $.each(res.dept, function(i, item) {
                $('#dept').append('<option value="' + item.FuncName + '">' + item.FuncName + '</option>');
              });
            }
          }
        });

        // 3. เริ่มโหลดพนักงานพร้อมกันทันที ไม่ต้องรอให้โหลดแผนกเสร็จก่อน!
        loadEmployees();
      });

      /**
       * เมื่อเปลี่ยนแผนก → ให้คัดกรองข้อมูลจาก RAM (และเคลียร์ช่องค้นหา)
       */
      $('#dept').on('change', function() {
        $('#SearchBar').val('');
        filterEmployees();
      });

      /**
       * Live Search: แสดง Autocomplete แต่ยังไม่คัดกรองตารางจนกว่าจะกดเลือก
       */
      let searchTimer;
      $('#SearchBar').on('input', function() {
        var keyword = $(this).val().trim();
        var companyId = $('#companySelect').val();
        var $resultsBox = $('#searchResults');

        // Autocomplete Dropdown เรียก AJAX หน่วงเวลา
        clearTimeout(searchTimer);
        
        if (keyword.length === 0) {
          $resultsBox.hide().empty();
          // ผู้ใช้บอกว่าต้องกด Search ก่อนถึงจะรีตาราง เลยเอาการรีเซ็ตอัตโนมัติออก
          return;
        }

        searchTimer = setTimeout(function() {
          $.ajax({
            url: '<?= site_url("send_data/search_autocomplete") ?>',
            type: 'GET',
            dataType: 'json',
            data: {
              company_id: companyId,
              keyword: keyword
            },
            success: function(res) {
              $resultsBox.empty();
              if (res.length > 0) {
                $.each(res, function(index, emp) {
                  var regex = new RegExp("(" + keyword + ")", "gi");
                  // ป้องกัน Error กรณีที่ Fullname หรือ ThaiName เป็น null
                  var fName = emp.Fullname || '';
                  var tName = emp.ThaiName || '';

                  // ใช้ span สีฟ้าขีดเส้นใต้ให้ดูโมเดิร์นขึ้น
                  var highlightFormat = "<span class='text-primary font-weight-bold' style='text-decoration: underline; text-decoration-thickness: 2px;'>$1</span>";
                  var displayFullname = fName ? fName.replace(regex, highlightFormat) : '';
                  var displayThaiName = tName ? tName.replace(regex, highlightFormat) : '';

                  var displayText = '<div class="d-flex align-items-center">';
                  displayText += '<div class="mr-2 text-secondary"><i class="fas fa-user-circle"></i></div>';
                  displayText += '<div>' + displayFullname;
                  if (displayThaiName) {
                    displayText += ' <small class="text-muted">(' + displayThaiName + ')</small>';
                  }
                  displayText += '</div></div>';

                  var $item = $('<a href="#" class="dropdown-item search-item" style="white-space: normal; font-size: 0.85rem; padding: 0.4rem 1rem; border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;"></a>')
                    .html(displayText)
                    .data('userid', emp.UserID)
                    .data('name', fName)
                    // เพิ่ม hover effect
                    .on('mouseenter', function() {
                      $(this).css('background-color', '#f8fafc');
                    })
                    .on('mouseleave', function() {
                      $(this).css('background-color', 'transparent');
                    });

                  $resultsBox.append($item);
                });
                // ลบ border bottom ของไอเท็มสุดท้าย
                $resultsBox.find('a:last').css('border-bottom', 'none');
                $resultsBox.addClass('show').show();
              } else {
                $resultsBox.html('<div class="dropdown-item text-muted" style="font-size: 0.85rem; padding: 0.5rem 1rem;"><i class="fas fa-info-circle mr-1"></i>ไม่พบข้อมูลพนักงาน</div>').addClass('show').show();
              }
            },
            error: function(xhr, status, error) {
              console.error("AJAX Error: ", error);
              $resultsBox.html('<div class="dropdown-item text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>').addClass('show').show();
            }
          });
        }, 300); // หน่วงเวลา 300ms 
      });

      // ซ่อน dropdown เมื่อคลิกที่อื่น
      $(document).on('click', function(e) {
        if (!$(e.target).closest('#SearchBar, #searchResults').length) {
          $('#searchResults').hide();
        }
      });

      // เมื่อเลือกพนักงานจาก dropdown
      $(document).on('click', '.search-item', function(e) {
        e.preventDefault();
        var empName = $(this).data('name');

        $('#SearchBar').val(empName);
        $('#searchResults').hide().empty();
        
        // เคลียร์ filter แผนกเพื่อให้ค้นหาเจอแน่นอน
        $('#dept').val('-All-');

        // คัดกรองข้อมูลจาก RAM ทันที
        filterEmployees();
      });

      // เมื่อกดปุ่ม Search หรือ Enter ไม่ต้องโหลดใหม่จากเซิร์ฟเวอร์
      $('#SearchButton').on('click', function(e) {
        e.preventDefault();
        $('#searchResults').hide();
        // เคลียร์ filter แผนกเมื่อกดค้นหาเพื่อให้เจอพนักงานทุกคนที่ตรงเงื่อนไข
        $('#dept').val('-All-');
        filterEmployees();
      });

      $('#SearchBar').on('keypress', function(e) {
        if (e.which === 13) {
          e.preventDefault();
          $('#searchResults').hide();
          // เคลียร์ filter แผนกเมื่อกดค้นหาเพื่อให้เจอพนักงานทุกคนที่ตรงเงื่อนไข
          $('#dept').val('-All-');
          filterEmployees();
        }
      });

      // ===================================================
      // Login: สลับระหว่าง Table View ↔ Login Form
      // ===================================================

      $('#btnShowLogin').on('click', function(e) {
        e.preventDefault();
        $('.main-header').hide();
        $('#footer').hide();
        $('section.content').hide();
        $('#loginSection').fadeIn(300);
      });

      $('#btnBackToTable').on('click', function() {
        $('#loginSection').fadeOut(250, function() {
          $('.main-header').show();
          $('#footer').show();
          $('section.content').show();
        });
        $('#loginUsername').val('');
        $('#loginPassword').val('');
        $('#loginError').hide();
      });

      $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        var username = $.trim($('#loginUsername').val());
        var password = $.trim($('#loginPassword').val());
        var $btn = $('#btnLoginSubmit');

        if (!username || !password) {
          Swal.fire({
            title: "Warning!",
            text: "Please fill in your username and password",
            icon: "warning"
          });
          return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> กำลังเข้าสู่ระบบ...');

        $.ajax({
          url: '<?= site_url("send_data/login") ?>',
          type: 'POST',
          dataType: 'json',
          data: {
            username: username,
            password: password
          },
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Login Success',
                text: 'Welcome ' + (res.fullname || username),
                timer: 1500,
                showConfirmButton: false
              }).then(function() {
                if (res.redirect) {
                  window.location.href = res.redirect;
                } else {
                  window.location.href = '<?= site_url("send_data/admin_dashboard") ?>';
                }
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Username or Password is not correct",
              });
            }
          },
          error: function() {
            Swal.fire({
              text: "An error occurred, please try again.",
              icon: "warning"
            });
          },
          complete: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt mr-1"></i> Login');
          }
        });
      });

    });
  </script>
</body>

</html>
