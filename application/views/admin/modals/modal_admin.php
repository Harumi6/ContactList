<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var array $companies
 * @var int $admin_role
 * @var int $admin_company_id
 */
?>
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
              <?php endforeach; endif; ?>
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
              <?php endforeach; endif; ?>
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
