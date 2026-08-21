<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
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
