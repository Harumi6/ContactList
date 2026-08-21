<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var array $companies
 */
?>
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
              <?php endforeach; endif; ?>
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
              <?php endforeach; endif; ?>
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
