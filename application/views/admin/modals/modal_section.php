<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var array $companies
 */
?>
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
              <?php endforeach; endif; ?>
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
              <?php endforeach; endif; ?>
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
