<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var array $companies
 * @var object|null $company
 * @var array $departments
 * @var array $employees
 * @var array $org_structure
 */

$this->load->view('partials/header', ['page_title' => 'ATTG Contact']);
?>

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
    z-index: 1;
    box-shadow: inset 0 -2px 0 #dee2e6;
  }
  .tr-function {
    background-color: #93E4F2 !important;
    font-weight: bold;
  }
  .tr-department {
    background-color: #DDDDDD !important;
    font-weight: bold;
  }
  .tr-section {
    background-color: #EFEFEF !important;
    font-weight: bold;
  }
  #loginSection {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 1040;
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
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
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
  .login-card .btn-login:hover { opacity: .9; }
  .login-card .btn-login:active { transform: scale(.98); }
  .login-card .btn-back {
    display: block;
    text-align: center;
    margin-top: 16px;
    color: #999;
    font-size: 13px;
    cursor: pointer;
    transition: color .2s;
  }
  .login-card .btn-back:hover { color: #667eea; }
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

<body class="hold-transition layout-top-nav">
  <!-- Fullscreen Loading Overlay -->
  <div id="fullScreenLoader">
    <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
    <div>กำลังโหลดข้อมูล...</div>
  </div>

  <div class="wrapper" style="position: fixed; top: 0; left: 0; height: 100%; width: 100%;">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
      <div class="container-fluid">
        <a href="#" class="navbar-brand">
          <img src="<?= base_url('assets/icon-images/logo.png') ?>" alt="logo" width="100" height="30"> 
          <span class="brand-text font-weight-bold">Contact List</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3 w-100" id="navbarCollapse">
          <!-- Selections (Company & Function) -->
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
                            <?= (trim($c->Company) == 'ATA' || trim($c->Company) == 'AT-A') ? 'selected' : '' ?>><?= html_escape(trim($c->Company)) ?></option>
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
                    <option value="<?= html_escape($d->FuncName) ?>"><?= html_escape($d->FuncName) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </form>

          <!-- Search Form -->
          <form class="form-inline ml-auto my-auto">
            <div class="form-group position-relative">
              <input class="form-control form-control-sm mr-2 w-100" type="search" placeholder="Search Name, EmpID, Tel, Email..." aria-label="Search" id="SearchBar" style="max-width: 250px; background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 4px; padding-left: 10px;" autocomplete="off">
              <div id="searchResults" class="dropdown-menu w-100" style="max-width: 250px !important; display: none; position: absolute; top: calc(100% + 5px); left: 0; max-height: 300px; overflow-y: auto; z-index: 1050; padding: 0.5rem 0; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);"></div>
            </div>
            <button class="btn btn-sm btn-secondary" type="button" id="SearchButton">
              <i class="fas fa-search"></i> Search
            </button>
          </form>

          <!-- User Menu Dropdown -->
          <ul class="navbar-nav ml-2">
            <li class="nav-item dropdown">
              <a class="nav-link" data-toggle="dropdown" href="#" style="padding-top: 0.2rem; padding-bottom: 0;">
                <i class="fa fa-user-circle-o fa-2x text-secondary" aria-hidden="true"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <?php if ($this->session->userdata('admin_logged_in')): ?>
                  <div class="dropdown-header text-primary font-weight-bold text-left">
                    <i class="fas fa-user-shield mr-1"></i> <?= html_escape($this->session->userdata('admin_username')) ?>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a href="<?= site_url('send_data/admin_dashboard') ?>" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> จัดการระบบ
                  </a>
                  <a href="#" onclick="confirmLogout()" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                  </a>
                <?php elseif ($this->session->userdata('employee_logged_in')): ?>
                  <div class="dropdown-header text-success font-weight-bold text-left">
                    <i class="fas fa-user mr-1"></i> <?= html_escape($this->session->userdata('employee_fullname') ?: $this->session->userdata('employee_username')) ?>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a href="<?= site_url('send_data/employee_dashboard') ?>" class="dropdown-item">
                    <i class="fas fa-id-badge mr-2"></i> โปรไฟล์ของฉัน
                  </a>
                  <a href="#" onclick="confirmLogout()" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                  </a>
                <?php else: ?>
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

    <!-- Content Wrapper -->
    <div class="content-wrapper">
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

          <div class="card-body p-0 table-scrollable table-responsive">
            <table id="example1" class="table table-bordered table-striped m-0">
              <thead style="text-align: center;">
                <tr>
                  <th>EmpID</th>
                  <th>Name</th>
                  <th>Position</th>
                  <th>Mobile No.</th>
                  <th>Internal No.</th>
                  <th>Email</th>
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
      </section>

      <!-- Login Section Modal -->
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

  </div>
  <!-- /.wrapper -->

  <!-- Load Footer & Base Scripts -->
  <?php $this->load->view('partials/footer'); ?>

  <!-- ExcelJS and FileSaver -->
  <script src="<?= base_url('assets/js/exceljs.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/FileSaver.min.js') ?>"></script>

  <script>
    $(document).ready(function() {
      var currentCompanyData = [];
      var currentCompanyStatus = "";

      function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
      }

      function renderEmployeesTable(dataArray, status) {
        var $tbody = $("#example1 tbody");
        $tbody.empty();

        if (status === "error" || status === "empty") {
          $tbody.html('<tr><td colspan="6" class="text-center">No data found.</td></tr>');
          return;
        }
        if (dataArray.length === 0) {
          $tbody.html('<tr><td colspan="6" class="text-center">No employee found.</td></tr>');
          return;
        }

        var html = "";
        var curFunc = null, curDept = null, curSec = null;

        if (status === "org_only") {
          $.each(dataArray, function(i, row) {
            if (row.FuncName !== curFunc) {
              var fDisplay = row.FuncName ? escapeHtml(row.FuncName) : "(ไม่ระบุ)";
              html += '<tr class="tr-function"><td colspan="6" style="border: 1px solid #6c757d;">Function : ' + fDisplay + '</td></tr>';
              curFunc = row.FuncName;
              curDept = null;
              curSec = null;
            }
            if (row.DeptName !== curDept) {
              var dDisplay = row.DeptName ? escapeHtml(row.DeptName) : "(ไม่ระบุ)";
              html += '<tr class="tr-department" style="background-color: #f8f9fa;"><td colspan="6" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' + dDisplay + '</td></tr>';
              curDept = row.DeptName;
              curSec = null;
            }
            if (row.SecName !== curSec) {
              var sDisplay = row.SecName ? escapeHtml(row.SecName) : "(ไม่ระบุ)";
              html += '<tr class="tr-section"><td colspan="6" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' + sDisplay + '</td></tr>';
              curSec = row.SecName;
            }
          });
          $tbody.html(html);
          return;
        }

        // Normal display with employees
        $.each(dataArray, function(i, row) {
          if (row.FuncName && row.FuncName !== curFunc) {
            html += '<tr class="tr-function"><td colspan="6" style="border: 1px solid #6c757d;">Function : ' + escapeHtml(row.FuncName) + '</td></tr>';
            curFunc = row.FuncName;
            curDept = null;
            curSec = null;
          }
          if (parseInt(row.OrganizeLevel) >= 2) {
            if (row.DeptName && row.DeptName !== curDept) {
              html += '<tr class="tr-department" style="background-color: #f8f9fa;"><td colspan="6" style="padding-left: 20px; border: 1px solid #6c757d;">↳ Department : ' + escapeHtml(row.DeptName) + '</td></tr>';
              curDept = row.DeptName;
              curSec = null;
            }
          }
          if (parseInt(row.OrganizeLevel) >= 3) {
            if (row.SecName && row.SecName !== curSec) {
              html += '<tr class="tr-section"><td colspan="6" style="padding-left: 40px; border: 1px solid #6c757d;">↳ Section : ' + escapeHtml(row.SecName) + '</td></tr>';
              curSec = row.SecName;
            }
          }

          if (row.Fullname) {
            html += '<tr style="background-color: #ffffff;">';
            html += '<td id="empid">' + (row.UserLogOn ? escapeHtml(row.UserLogOn) : "") + '</td>';
            html += '<td>' + escapeHtml(row.Fullname) + (row.ThaiName ? " (" + escapeHtml(row.ThaiName) + ")" : "") + '</td>';
            
            var displayPos = row.Position || "";
            if (parseInt(row.OrganizeLevel) === 3 && parseInt(row.OrganizeOrder) > 4) {
              displayPos = "";
            }
            html += '<td id="position">' + escapeHtml(displayPos) + '</td>';
            html += '<td id="mobile">' + escapeHtml(row.MobilePhone || "") + '</td>';
            
            var telDisplay = escapeHtml(row.TelePhone || "");
            if (row.internal_no && row.internal_no.trim() !== "") {
              telDisplay += (telDisplay !== "" ? " " : "") + escapeHtml(row.internal_no);
            }
            html += '<td>' + telDisplay + '</td>';
            html += '<td>' + escapeHtml(row.EmailAddress || "") + '</td>';
            html += '</tr>';
          }
        });
        $tbody.html(html);
      }

      function filterEmployees() {
        var keyword = $("#SearchBar").val().trim().toLowerCase();
        var department = $("#dept").val();

        if (currentCompanyStatus === "error" || currentCompanyStatus === "empty") {
          renderEmployeesTable([], currentCompanyStatus);
          return;
        }

        var filteredData = currentCompanyData.filter(function(row) {
          var matchDept = true;
          if (department && department !== "-All-") {
            matchDept = (row.FuncName === department);
          }
          var matchKeyword = true;
          if (keyword.length > 0) {
            var fullEng  = (row.Fullname || "").toLowerCase();
            var fullThai = (row.ThaiName || "").toLowerCase();
            var empId    = (row.UserLogOn || "").toLowerCase();
            var mobile   = (row.MobilePhone || "").toLowerCase();
            var internal = (row.internal_no || "").toLowerCase();
            var tel      = (row.TelePhone || "").toLowerCase();
            var email    = (row.EmailAddress || "").toLowerCase();
            var funcName = (row.FuncName || "").toLowerCase();
            var deptName = (row.DeptName || "").toLowerCase();
            var secName  = (row.SecName || "").toLowerCase();
            
            matchKeyword = fullEng.includes(keyword) || 
                           fullThai.includes(keyword) || 
                           empId.includes(keyword) || 
                           mobile.includes(keyword) || 
                           internal.includes(keyword) || 
                           tel.includes(keyword) || 
                           email.includes(keyword) || 
                           funcName.includes(keyword) || 
                           deptName.includes(keyword) || 
                           secName.includes(keyword);
          }
          return matchDept && matchKeyword;
        });

        if (filteredData.length === 0 && keyword.length > 0) {
          $("#SearchBar").val("");
        }

        renderEmployeesTable(filteredData, currentCompanyStatus);
      }

      function loadEmployees(isSilent) {
        var companyId = $("#companySelect").val();
        if (!companyId) return;

        if (!isSilent) {
          $("#fullScreenLoader").css("display", "flex");
          $("#example1 tbody").html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');
        }

        $.ajax({
          url: '<?= site_url("send_data/get_employees") ?>',
          type: "GET",
          dataType: "json",
          data: {
            company_id: companyId,
            department: "-All-",
            keyword: ""
          },
          success: function(res) {
            currentCompanyStatus = res.status;
            currentCompanyData = res.data || [];
            
            if (!isSilent || $("#SearchBar").val().trim().length > 0 || $("#dept").val() !== "-All-") {
              filterEmployees();
            }
          },
          error: function() {
            currentCompanyStatus = "error";
            currentCompanyData = [];
            if (!isSilent) filterEmployees();
          },
          complete: function() {
            if (!isSilent) $("#fullScreenLoader").hide();
          }
        });
      }

      $("#companySelect").on("change", function() {
        var companyId = $(this).val();
        if (!companyId) return;

        $("#SearchBar").val("");

        var $selected = $(this).find("option:selected");
        $("#companyFullname").text($selected.data("fullname"));
        $("#companyAddress").text($selected.data("address"));
        $("#companyTel").text($selected.data("tel"));

        $("#fullScreenLoader").css("display", "flex");
        $("#dept").html("<option selected>-All-</option>");
        $("#example1 tbody").html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: '<?= site_url("send_data/get_company_info") ?>',
          type: "GET",
          dataType: "json",
          data: { id: companyId },
          success: function(res) {
            if (res.status === "success") {
              $("#dept").empty().append("<option selected>-All-</option>");
              $.each(res.dept, function(i, item) {
                $("#dept").append('<option value="' + item.FuncName + '">' + item.FuncName + '</option>');
              });
            }
          }
        });

        loadEmployees(false);
      });

      if ($("#companySelect").val()) {
        $("#companySelect").trigger("change");
      } else {
        $("#fullScreenLoader").hide();
      }

      $("#dept").on("change", function() {
        $("#SearchBar").val("");
        filterEmployees();
      });

      // Live Search Autocomplete
      var searchTimer;
      $("#SearchBar").on("input", function() {
        var keyword = $(this).val().trim();
        var companyId = $("#companySelect").val();
        var $resultsBox = $("#searchResults");

        clearTimeout(searchTimer);
        if (keyword.length === 0) {
          $resultsBox.hide().empty();
          return;
        }

        searchTimer = setTimeout(function() {
          $.ajax({
            url: '<?= site_url("send_data/search_autocomplete") ?>',
            type: "GET",
            dataType: "json",
            data: { company_id: companyId, keyword: keyword },
            success: function(res) {
              $resultsBox.empty();
              if (res && res.length > 0) {
                $.each(res, function(index, emp) {
                  var regex = new RegExp("(" + keyword + ")", "gi");
                  var fName    = emp.Fullname || "";
                  var tName    = emp.ThaiName || "";
                  var empId    = emp.UserLogOn || "";
                  var mobile   = emp.MobilePhone || "";
                  var internal = emp.internal_no || "";
                  var email    = emp.EmailAddress || "";

                  var highlightFormat = '<span class="text-primary font-weight-bold" style="text-decoration: underline;">$1</span>';
                  var displayFullname = fName ? fName.replace(regex, highlightFormat) : "";
                  var displayThaiName = tName ? tName.replace(regex, highlightFormat) : "";

                  var subDetails = [];
                  if (empId) subDetails.push("ID: " + empId.replace(regex, highlightFormat));
                  if (mobile) subDetails.push("Mob: " + mobile.replace(regex, highlightFormat));
                  if (internal) subDetails.push("Ext: " + internal.replace(regex, highlightFormat));
                  if (email) subDetails.push(email.replace(regex, highlightFormat));

                  var displayText = '<div class="d-flex align-items-center">' +
                    '<div class="mr-2 text-secondary"><i class="fas fa-user-circle"></i></div>' +
                    '<div><div>' + displayFullname + (displayThaiName ? ' <small class="text-muted">(' + displayThaiName + ')</small>' : '') + '</div>' +
                    (subDetails.length > 0 ? '<div class="text-muted" style="font-size: 0.75rem;">' + subDetails.join(' | ') + '</div>' : '') +
                    '</div></div>';

                  var $item = $('<a href="#" class="dropdown-item search-item" style="white-space: normal; font-size: 0.85rem; padding: 0.4rem 1rem; border-bottom: 1px solid #f1f5f9;"></a>')
                    .html(displayText)
                    .data("name", fName);

                  $resultsBox.append($item);
                });
                $resultsBox.find("a:last").css("border-bottom", "none");
                $resultsBox.addClass("show").show();
              } else {
                $resultsBox.html('<div class="dropdown-item text-muted" style="font-size: 0.85rem; padding: 0.5rem 1rem;"><i class="fas fa-info-circle mr-1"></i>ไม่พบข้อมูลพนักงาน</div>').addClass("show").show();
              }
            }
          });
        }, 300);
      });

      $(document).on("click", function(e) {
        if (!$(e.target).closest("#SearchBar, #searchResults").length) {
          $("#searchResults").hide();
        }
      });

      $(document).on("click", ".search-item", function(e) {
        e.preventDefault();
        var empName = $(this).data("name");
        $("#SearchBar").val(empName);
        $("#searchResults").hide().empty();
        $("#dept").val("-All-");
        filterEmployees();
      });

      $("#SearchButton").on("click", function(e) {
        e.preventDefault();
        $("#searchResults").hide();
        $("#dept").val("-All-");
        filterEmployees();
      });

      $("#SearchBar").on("keypress", function(e) {
        if (e.which === 13) {
          e.preventDefault();
          $("#searchResults").hide();
          $("#dept").val("-All-");
          filterEmployees();
        }
      });

      // Login Toggle & Submit
      $("#btnShowLogin").on("click", function(e) {
        e.preventDefault();
        $(".main-header").hide();
        $("#footer").hide();
        $("section.content").hide();
        $("#loginSection").fadeIn(300);
      });

      $("#btnBackToTable").on("click", function() {
        $("#loginSection").fadeOut(250, function() {
          $(".main-header").show();
          $("#footer").show();
          $("section.content").show();
        });
        $("#loginUsername").val("");
        $("#loginPassword").val("");
        $("#loginError").hide();
      });

      $("#loginForm").on("submit", function(e) {
        e.preventDefault();
        var username = $.trim($("#loginUsername").val());
        var password = $.trim($("#loginPassword").val());
        var $btn = $("#btnLoginSubmit");

        if (!username || !password) {
          Swal.fire({ title: "Warning!", text: "Please fill in your username and password", icon: "warning" });
          return;
        }

        $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin mr-1"></i> กำลังเข้าสู่ระบบ...');

        $.ajax({
          url: '<?= site_url("send_data/login") ?>',
          type: "POST",
          dataType: "json",
          data: { username: username, password: password },
          success: function(res) {
            if (res.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Login Success",
                text: "Welcome " + (res.fullname || username),
                timer: 1500,
                showConfirmButton: false
              }).then(function() {
                window.location.href = res.redirect || '<?= site_url("send_data/admin_dashboard") ?>';
              });
            } else {
              Swal.fire({ icon: "error", title: res.message || "Username or Password is not correct" });
            }
          },
          error: function() {
            Swal.fire({ text: "An error occurred, please try again.", icon: "warning" });
          },
          complete: function() {
            $btn.prop("disabled", false).html('<i class="fas fa-sign-in-alt mr-1"></i> Login');
          }
        });
      });
    });
  </script>
</body>
</html>
