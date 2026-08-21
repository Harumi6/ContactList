<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
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
