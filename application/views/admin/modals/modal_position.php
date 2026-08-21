<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Modal Add Position -->
<div class="modal fade" id="addPositionModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">เพิ่ม Position</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formAddPosition">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>AbbreviateEN</label>
              <input type="text" class="form-control" name="abbreviate_en">
            </div>
            <div class="col-md-6 form-group">
              <label>FullNameEN</label>
              <input type="text" class="form-control" name="fullname_en">
            </div>
            <div class="col-md-6 form-group">
              <label>AbbreviateTH</label>
              <input type="text" class="form-control" name="abbreviate_th">
            </div>
            <div class="col-md-6 form-group">
              <label>FullNameTH</label>
              <input type="text" class="form-control" name="fullname_th">
            </div>
            <div class="col-md-12 form-group">
              <label>Position</label>
              <input type="text" class="form-control" name="position">
            </div>
            <div class="col-md-4 form-group">
              <label>OrganizeLevel</label>
              <select class="form-control" id="addPosLevel" name="organize_level">
                <option value="">-- เลือก --</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>OrganizeOrder</label>
              <select class="form-control" id="addPosOrder" name="organize_order">
                <option value="">-- กรุณาเลือก Level ก่อน --</option>
              </select>
              <small class="form-text text-muted" id="addPosOrderHint"></small>
            </div>
            <div class="col-md-4 form-group">
              <label>Board</label>
              <select class="form-control" name="board">
                <option value="0">0 (ไม่ใช่บอร์ด)</option>
                <option value="1">1 (เป็นบอร์ด)</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button" class="btn btn-primary" id="btnSaveNewPosition">บันทึกเพิ่ม</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Position -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">แก้ไข Position</h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditPosition">
          <input type="hidden" id="editPosId" name="position_id">
          <div class="row">
            <div class="col-md-6 form-group">
              <label>AbbreviateEN</label>
              <input type="text" class="form-control" id="editPosAbbEN" name="abbreviate_en">
            </div>
            <div class="col-md-6 form-group">
              <label>FullNameEN</label>
              <input type="text" class="form-control" id="editPosFullEN" name="fullname_en">
            </div>
            <div class="col-md-6 form-group">
              <label>AbbreviateTH</label>
              <input type="text" class="form-control" id="editPosAbbTH" name="abbreviate_th">
            </div>
            <div class="col-md-6 form-group">
              <label>FullNameTH</label>
              <input type="text" class="form-control" id="editPosFullTH" name="fullname_th">
            </div>
            <div class="col-md-12 form-group">
              <label>Position</label>
              <input type="text" class="form-control" id="editPosPosition" name="position">
            </div>
            <div class="col-md-4 form-group">
              <label>OrganizeLevel</label>
              <select class="form-control" id="editPosLevel" name="organize_level">
                <option value="">-- เลือก --</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>OrganizeOrder</label>
              <select class="form-control" id="editPosOrder" name="organize_order">
                <option value="">-- กรุณาเลือก Level ก่อน --</option>
              </select>
              <small class="form-text text-muted" id="editPosOrderHint"></small>
            </div>
            <div class="col-md-4 form-group">
              <label>Board</label>
              <select class="form-control" id="editPosBoard" name="board">
                <option value="0">0 (ไม่ใช่บอร์ด)</option>
                <option value="1">1 (เป็นบอร์ด)</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button" class="btn btn-warning" id="btnSaveEditPosition">บันทึกแก้ไข</button>
      </div>
    </div>
  </div>
</div>
