<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
  <!-- Main Footer -->
  <footer class="main-footer text-center" style="position: fixed; bottom: 0; width: 100%; z-index: 1030; margin: 0; padding: 10px 0;" id="footer">
    <strong>Copyright &copy; 2026 ATTG.</strong> All rights reserved.
    <br><small>Sync from Success Factor | Data Update (<span class="data-update-date">กำลังโหลด...</span>)</small>
  </footer>

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url('assets/js/adminlte.min.js') ?>"></script>
  <!-- SweetAlert2 -->
  <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>

  <script>
    // โหลดวันที่อัปเดตข้อมูลล่าสุดอัตโนมัติ
    $(document).ready(function() {
      $.get('<?= site_url("send_data/get_last_update_date_ajax") ?>', function(res) {
        try {
          var data = (typeof res === 'object') ? res : JSON.parse(res);
          if (data && data.last_update) {
            $('.data-update-date').text(data.last_update);
          }
        } catch (e) {}
      });
    });

    // ฟังก์ชัน Logout สากล
    function confirmLogout() {
      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Logout",
        cancelButtonText: "Cancel"
      }).then(function(result) {
        if (result.isConfirmed) {
          Swal.fire({
            title: "Logout Success!",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          }).then(function() {
            window.location.href = "<?= site_url('send_data/logout') ?>";
          });
        }
      });
    }
  </script>
