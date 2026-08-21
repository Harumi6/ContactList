<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
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
