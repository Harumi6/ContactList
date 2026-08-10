<?php

/**
 * @var string $username
 * @var string $fullname
 * @var object $employee
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?= base_url('assets/icon-images/logo.png') ?>">
  <title>Employee Profile</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">
  <!-- SweetAlert2 -->
  <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>

  <style>
    body {
      font-size: 0.9rem;
    }

    @media (min-width: 768px) {
      body {
        overflow-y: hidden;
      }
    }

    .wrapper {
      min-width: max-content;
      display: flex;
      flex-direction: column;
      min-height: 111.12vh;
    }

    .content-wrapper {
      flex: 1;
    }

    .swal2-container {
      width: 100vw !important;
      height: 100vh !important;
    }
  </style>
</head>

<body class="hold-transition layout-top-nav bg-light">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a href="<?= site_url('send_data') ?>" class="navbar-brand">
          <span class="brand-text font-weight-light">AT-A & ATTG Contact <b>Employee</b></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a href="<?= site_url('send_data') ?>" class="btn btn-secondary btn-sm mb-2 mt-1"><i class="fas fa-eye"></i> ดูหน้าเว็บผู้ใช้ทั่วไป</a>
            </li>
            <li class="nav-item">
              <span class="nav-link text-white"><i class="fas fa-user-circle mr-1"></i> <?= html_escape($employee->ThaiName ?? $fullname) ?></span>
            </li>
            <li class="nav-item">
              <button type="button" onclick="confirmLogout()" class="nav-link text-danger" style="background: none; border: none; padding: 0; cursor: pointer;">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">

      <div class="content mt-2 mb-2">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <!-- ดีไซน์ Profile Card แบบลดความสูง -->
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
              <div class="card card-primary card-outline shadow-sm d-flex flex-column" style="min-height: 560px; max-height: calc(111.11vh - 180px);">
                <div class="card-body box-profile d-flex flex-column justify-content-center">
                  <div class="text-center mb-3">
                    <h3 class="m-0 text-primary font-weight-bold mb-2">My Profile</h3>
                    <!-- Default picture handling -->
                    <div style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; border: 3px solid #dee2e6; background-color: #f8f9fa; margin: 0 auto;">
                      <?php
                      $image_path = FCPATH . 'assets/uploads/employee/' . $username . '.jpg';
                      if (file_exists($image_path)):
                      ?>
                        <img class="profile-user-img img-fluid"
                          src="<?= base_url('assets/uploads/employee/' . $username . '.jpg') ?>"
                          alt="User profile picture" style="width: 100%; height: 100%; object-fit: cover; border: none; padding: 0;">
                      <?php else: ?>
                        <i class="fas fa-user text-secondary" style="font-size: 60px;"></i>
                      <?php endif; ?>
                    </div>
                  </div>

                  <h4 class="profile-username text-center m-0"><?= html_escape($employee->ThaiName ?? $fullname) ?></h4>
                  <p class="text-muted text-center mb-2"><small><i class="fas fa-id-badge mr-1"></i> <?= html_escape($username) ?></small></p>

                  <hr class="mt-2 mb-3">

                  <form id="employeeProfileForm" class="flex-grow-1 d-flex flex-column justify-content-between">
                    <div>
                      <div class="form-group mb-2">
                        <label class="mb-1"><small>Thai Name (ชื่อภาษาไทย)</small></label>
                        <input type="text" class="form-control form-control-sm" name="thainame" value="<?= isset($employee->ThaiName) ? html_escape($employee->ThaiName) : '' ?>" placeholder="กรอกชื่อภาษาไทย" readonly>
                      </div>

                      <div class="form-group mb-2">
                        <label class="mb-1"><small>English Name (ชื่อภาษาอังกฤษ)</small></label>
                        <input type="text" class="form-control form-control-sm" name="fullname" value="<?= isset($employee->Fullname) ? html_escape($employee->Fullname) : '' ?>" placeholder="กรอกชื่อภาษาอังกฤษ" readonly>
                      </div>

                      <div class="form-group mb-2">
                        <label class="mb-1"><small><i class="fas fa-mobile-alt mr-1"></i> Mobile Phone (เบอร์มือถือ) <span class="text-danger">* ท่านยินดีให้เบอร์มือถือของท่านนั้นแสดงใน ContactList</span></small></label>
                        <input type="text" class="form-control form-control-sm" name="mobile_phone" value="<?= isset($employee->MobilePhone) ? html_escape($employee->MobilePhone) : '' ?>" placeholder="กรอกเบอร์มือถือ">
                      </div>

                      <div class="form-group mb-3">
                        <label class="mb-1"><small><i class="fas fa-tty mr-1"></i> Internal No. (เบอร์ภายใน)</small></label>
                        <input type="text" class="form-control form-control-sm" name="internal_no" value="<?= isset($employee->internal_no) ? html_escape($employee->internal_no) : '' ?>" placeholder="กรอกเบอร์ภายใน">
                      </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-sm mt-auto"><i class="fas fa-save mr-1"></i> <b>Update Profile</b></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer text-center" style="position: fixed; bottom: -6px; width: 100%; z-index: 1030; margin: 0; padding: 10px 0;">
      <strong>Copyright &copy; 2026 ATTG.</strong> All rights reserved.
      <br><small>sync from Success Factor | Data Update (<span class="data-update-date">กำลังโหลด...</span>)</small>
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

  <script>
    function confirmLogout() {
      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, logout!"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "<?= site_url('send_data/logout') ?>";
        }
      });
    }

    $(document).ready(function() {
      // การบันทึกข้อมูลแบบ AJAX
      $('#employeeProfileForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var $btn = $(this).find('button[type="submit"]');
        var originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> กำลังบันทึก...');

        $.ajax({
          url: '<?= site_url("send_data/update_employee_profile") ?>',
          type: 'POST',
          dataType: 'json',
          data: formData,
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
              }).then(function() {
                location.reload();
              });
            } else {
              Swal.fire('Error!', res.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error!', 'Cannot connect to server!', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).html(originalHtml);
          }
        });
      });
    });
  </script>
</body>

</html>