$(document).ready(function() {

      // โหลดพนักงานเมื่อเข้าTable
      function loadAdminEmployees() {
        var companyId = $('#crudCompanySelect').val();
        if (!companyId) return;

        $('#employeeTable tbody').html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: SITE_URL + '/send_data/admin_get_employees',
          type: 'GET',
          data: {
            company_id: companyId
          },
          success: function(html) {
            $('#employeeTable tbody').html(html);
          }
        });
      }

      $('#crudCompanySelect').on('change', function() {
        loadAdminEmployees();
      });

      loadAdminEmployees(); // Initial load

      // คลิกปุ่มแก้ไขพนักงาน
      $(document).on('click', '.btn-edit-emp', function() {
        var userId = $(this).data('id');
        var fullnameEN = $(this).data('fullnameen');
        var fullnameTH = $(this).data('fullnameth');
        var status = $(this).data('status');
        var mapId = $(this).data('map-id');
        var name = $(this).data('name');
        var secId = $(this).data('sec');
        var posId = $(this).data('pos');
        var phone = $(this).data('phone');
        var email = $(this).data('email');
        var userLogOn = $(this).data('userlogon');
        var picture = $(this).data('picture');
        var companyId = $('#crudCompanySelect').val();

        $('#editEmpId').val(userId);
        $('#fullnameEN').val(fullnameEN);
        $('#fullnameTH').val(fullnameTH);
        $('#editEmpStatus').val(status);
        $('#editEmpMapId').val(mapId);
        $('#editEmpPhone').val(phone);
        $('#editEmpEmail').val(email);
        $('#editEmpUserLogOn').val(userLogOn);
        $('#editEmpDeletePic').val('0'); // Reset delete flag
        $('#editEmpPicture').val(''); // reset file input

        if (picture) {
          $('#editEmpPicPreview').attr('src', BASE_URL + picture + '?t=' + new Date().getTime());
          $('#editEmpPicPreviewContainer').show();
        } else {
          $('#editEmpPicPreviewContainer').hide();
          $('#editEmpPicPreview').attr('src', '');
        }

        // โหลด Section และ Position แบบขนานกัน
        $.when(
          $.ajax({
            url: SITE_URL + '/send_data/admin_get_sections_by_company',
            type: 'GET',
            data: {
              company_id: companyId
            },
            dataType: 'json'
          }),
          $.ajax({
            url: SITE_URL + '/send_data/admin_get_positions',
            type: 'GET',
            dataType: 'json'
          })
        ).done(function(secRes, posRes) {
          var sections = secRes[0];
          var positions = posRes[0];

          var $selSec = $('#editEmpSection');
          $selSec.empty();
          $selSec.append('<option value="">-- เลือก Section --</option>');
          $.each(sections, function(i, s) {
            var selected = (s.SecID == secId) ? 'selected' : '';
            $selSec.append('<option value="' + s.SecID + '" ' + selected + '>' + s.SecName + '</option>');
          });

          var $selPos = $('#editEmpPosition');
          $selPos.empty();
          $selPos.append('<option value="">-- เลือก ตำแหน่ง --</option>');
          $.each(positions, function(i, p) {
            var selected = (p.PositionID == posId) ? 'selected' : '';
            $selPos.append('<option value="' + p.PositionID + '" ' + selected + '>' + p.FullNameEN + '</option>');
          });

          $('#modalEditEmployee').modal('show');
        }).fail(function() {
          Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลแผนกหรือตำแหน่งได้', 'error');
        });
      });

      // คลิกปุ่มลบพนักงาน
      $(document).on('click', '.btn-delete-emp', function() {
        var userId = $(this).data('id');
        var mapId = $(this).data('map-id');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบพนักงานคนนี้ใช่หรือไม่? ข้อมูลจะไม่สามารถกู้คืนได้!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_employee',
              type: 'POST',
              data: {
                user_id: userId,
                map_id: mapId
              },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบพนักงานเรียบร้อยแล้ว.', 'success');
                  loadAdminEmployees(); // รีโหลดตาราง
                } else {
                  Swal.fire('ข้อผิดพลาด!', res.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
              }
            });
          }
        });
      });

      // Preview Image on Select for Add Employee
      $('#addEmpPicture').on('change', function() {
        var file = this.files[0];
        if (file) {
          var reader = new FileReader();
          reader.onload = function(e) {
            $('#addEmpPicPreview').attr('src', e.target.result).show();
          }
          reader.readAsDataURL(file);
        } else {
          $('#addEmpPicPreview').hide().attr('src', '');
        }
      });

      // Preview Image on Select for Edit Employee
      $('#editEmpPicture').on('change', function() {
        var file = this.files[0];
        if (file) {
          var reader = new FileReader();
          reader.onload = function(e) {
            $('#editEmpPicPreview').attr('src', e.target.result);
            $('#editEmpPicPreviewContainer').show();
            $('#editEmpDeletePic').val('0'); // User selected a new image, cancel any deletion
          }
          reader.readAsDataURL(file);
        } else {
          // If they cancel file selection, we should probably revert to original image if exists
          var origPic = $('.btn-edit-emp[data-id="' + $('#editEmpId').val() + '"]').data('picture');
          if (origPic && $('#editEmpDeletePic').val() !== '1') {
            $('#editEmpPicPreview').attr('src', BASE_URL + origPic + '?t=' + new Date().getTime());
            $('#editEmpPicPreviewContainer').show();
          } else {
            $('#editEmpPicPreviewContainer').hide();
            $('#editEmpPicPreview').attr('src', '');
          }
        }
      });

      // Delete Picture Button
      $('#btnDeletePic').on('click', function() {
        $('#editEmpPicPreviewContainer').hide();
        $('#editEmpPicture').val(''); // Clear selected file if any
        $('#editEmpDeletePic').val('1'); // Set flag to delete on server
      });

      // กดปุ่มบันทึกพนักงาน
      $('#btnSaveEmployee').on('click', function() {
        var data = new FormData($('#formEditEmployee')[0]);
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_employee',
          type: 'POST',
          data: data,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไขข้อมูลพนักงานเรียบร้อย', 'success');
              $('#modalEditEmployee').modal('hide');
              loadAdminEmployees();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึก');
          }
        });
      });
      // เปิด Modal เพิ่มพนักงาน
      $('#addEmployeeModal').on('show.bs.modal', function() {
        var companyId = $('#crudCompanySelect').val();
        // Clear form
        $('#formAddEmployee')[0].reset();

        // Load Sections and Positions
        $.when(
          $.ajax({
            url: SITE_URL + '/send_data/admin_get_sections_by_company',
            type: 'GET',
            data: {
              company_id: companyId
            },
            dataType: 'json'
          }),
          $.ajax({
            url: SITE_URL + '/send_data/admin_get_positions',
            type: 'GET',
            dataType: 'json'
          })
        ).done(function(secRes, posRes) {
          var sections = secRes[0];
          var positions = posRes[0];

          var $selSec = $('#addEmpSection');
          $selSec.empty();
          $selSec.append('<option value="">-- เลือก Section --</option>');
          $.each(sections, function(i, s) {
            $selSec.append('<option value="' + s.SecID + '">' + s.SecName + '</option>');
          });

          var $selPos = $('#addEmpPosition');
          $selPos.empty();
          $selPos.append('<option value="">-- เลือก ตำแหน่ง --</option>');
          $.each(positions, function(i, p) {
            $selPos.append('<option value="' + p.PositionID + '">' + p.FullNameEN + '</option>');
          });
        }).fail(function() {
          Swal.fire('ข้อผิดพลาด', 'ไม่สามารถโหลดข้อมูลแผนกหรือตำแหน่งได้', 'error');
        });
      });

      // กดปุ่มบันทึกเพิ่มพนักงาน
      $('#btnSaveNewEmployee').on('click', function() {
        var data = new FormData($('#formAddEmployee')[0]);
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_employee',
          type: 'POST',
          data: data,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่มข้อมูลพนักงานเรียบร้อย', 'success');
              $('#addEmployeeModal').modal('hide');
              loadAdminEmployees();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่มพนักงาน');
          }
        });
      });



      // -----------------------------------------
      // Function CRUD Logic
      // -----------------------------------------
      function loadAdminFunctions() {
        var companyId = $('#functionCompanySelect').val();
        if (!companyId) return;

        $('#functionTable tbody').html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: SITE_URL + '/send_data/admin_get_functions',
          type: 'GET',
          data: {
            company_id: companyId
          },
          success: function(res) {
            $('#functionTable tbody').html(res);
          },
          error: function() {
            $('#functionTable tbody').html('<tr><td colspan="4" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลด Function ตอนเปลี่ยนบริษัทใน Tab Function
      $('#functionCompanySelect').on('change', function() {
        loadAdminFunctions();
      });

      // โหลด Function เมื่อเปิด Tab Function
      $('#tab-function-tab').on('shown.bs.tab', function(e) {
        loadAdminFunctions();
      });

      // เปิด Modal เพิ่ม Function
      $('#addFunctionModal').on('show.bs.modal', function() {
        $('#formAddFunction')[0].reset();
        var companyId = $('#functionCompanySelect').val();
        var companyName = $('#functionCompanySelect option:selected').text();
        $('#addFuncCompany').html('<option value="' + companyId + '">' + companyName + '</option>');
      });

      // บันทึกเพิ่ม Function
      $('#btnSaveNewFunction').on('click', function() {
        if (!$('#formAddFunction')[0].checkValidity()) {
          $('#formAddFunction')[0].reportValidity();
          return;
        }
        var data = $('#formAddFunction').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_function',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่มข้อมูล Function เรียบร้อย', 'success');
              $('#addFunctionModal').modal('hide');
              loadAdminFunctions();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // เปิด Modal แก้ไข Function
      $(document).on('click', '.btn-edit-func', function() {
        var funcId = $(this).data('id');
        var funcName = $(this).data('funcname');
        var funcCode = $(this).data('funccode');
        var status = $(this).data('status');

        $('#editFuncId').val(funcId);
        $('#editFuncName').val(funcName);
        $('#editFuncCode').val(funcCode);
        $('#editFuncStatus').val(status);

        $('#editFunctionModal').modal('show');
      });

      // บันทึกแก้ไข Function
      $('#btnSaveEditFunction').on('click', function() {
        if (!$('#formEditFunction')[0].checkValidity()) {
          $('#formEditFunction')[0].reportValidity();
          return;
        }
        var data = $('#formEditFunction').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_function',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไขข้อมูล Function เรียบร้อย', 'success');
              $('#editFunctionModal').modal('hide');
              loadAdminFunctions();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // ลบ Function
      $(document).on('click', '.btn-delete-func', function() {
        var funcId = $(this).data('id');
        var funcName = $(this).data('funcname');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบ " + funcName + " ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_function',
              type: 'POST',
              data: {
                func_id: funcId
              },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบข้อมูล ' + funcName + ' เรียบร้อยแล้ว', 'success');
                  loadAdminFunctions();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });

      // -----------------------------------------
      // Department CRUD Logic
      // -----------------------------------------
      function loadAdminDepartments() {
        var companyId = $('#departmentCompanySelect').val();
        if (!companyId) return;

        $('#departmentTable tbody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: SITE_URL + '/send_data/admin_get_departments',
          type: 'GET',
          data: {
            company_id: companyId
          },
          success: function(res) {
            $('#departmentTable tbody').html(res);
          },
          error: function() {
            $('#departmentTable tbody').html('<tr><td colspan="6" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลด Department ตอนเปลี่ยนบริษัทใน Tab Department
      $('#departmentCompanySelect').on('change', function() {
        loadAdminDepartments();
      });

      // โหลด Department เมื่อเปิด Tab Department
      $('#tab-department-tab').on('shown.bs.tab', function(e) {
        loadAdminDepartments();
      });

      // ฟังก์ชันสำหรับโหลด Dropdown ของ สายงาน (Function) ตามบริษัท
      function loadFunctionsForSelect(companyId, targetSelectId, selectedFuncId = null) {
        var $select = $(targetSelectId);
        $select.html('<option value="">-- กำลังโหลด --</option>');
        if (!companyId) {
          $select.html('<option value="">-- กรุณาเลือกบริษัทก่อน --</option>');
          return;
        }

        $.ajax({
          url: SITE_URL + '/send_data/get_functions_by_company_json',
          type: 'GET',
          data: {
            company_id: companyId
          },
          dataType: 'json',
          success: function(functions) {
            $select.empty();
            if (functions.length === 0) {
              $select.append('<option value="">-- ไม่มี Function ในบริษัทนี้ --</option>');
            } else {
              $select.append('<option value="">-- เลือก Function --</option>');
              $.each(functions, function(index, func) {
                var selected = (selectedFuncId && selectedFuncId == func.FuncID) ? 'selected' : '';
                $select.append('<option value="' + func.FuncID + '" ' + selected + '>' + func.FuncName + '</option>');
              });
            }
          },
          error: function() {
            $select.html('<option value="">-- โหลดข้อมูลผิดพลาด --</option>');
          }
        });
      }

      // เปิด Modal เพิ่ม Department
      $('#addDepartmentModal').on('show.bs.modal', function() {
        $('#formAddDepartment')[0].reset();
        var companyId = $('#departmentCompanySelect').val();
        if (companyId) {
          $('#addDeptCompany').val(companyId);
          loadFunctionsForSelect(companyId, '#addDeptFunction');
        } else {
          $('#addDeptFunction').html('<option value="">-- กรุณาเลือกบริษัทก่อน --</option>');
        }
      });

      // เมื่อเปลี่ยนบริษัทใน Modal เพิ่ม Department
      $('#addDeptCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#addDeptFunction');
      });

      // บันทึกเพิ่ม Department
      $('#btnSaveNewDepartment').on('click', function() {
        if (!$('#formAddDepartment')[0].checkValidity()) {
          $('#formAddDepartment')[0].reportValidity();
          return;
        }
        var data = $('#formAddDepartment').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_department',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่มข้อมูล Department เรียบร้อย', 'success');
              $('#addDepartmentModal').modal('hide');
              loadAdminDepartments();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // เปิด Modal แก้ไข Department
      $(document).on('click', '.btn-edit-dept', function() {
        var deptId = $(this).data('id');
        var companyId = $(this).data('companyid');
        var funcId = $(this).data('funcid');
        var deptName = $(this).data('deptname');
        var deptCode = $(this).data('deptcode');
        var status = $(this).data('status');

        $('#editDeptId').val(deptId);
        $('#editDeptCompany').val(companyId);
        $('#editDeptName').val(deptName);
        $('#editDeptCode').val(deptCode);
        $('#editDeptStatus').val(status);

        loadFunctionsForSelect(companyId, '#editDeptFunction', funcId);

        $('#editDepartmentModal').modal('show');
      });

      // เมื่อเปลี่ยนบริษัทใน Modal แก้ไข Department
      $('#editDeptCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#editDeptFunction');
      });

      // บันทึกแก้ไข Department
      $('#btnSaveEditDepartment').on('click', function() {
        if (!$('#formEditDepartment')[0].checkValidity()) {
          $('#formEditDepartment')[0].reportValidity();
          return;
        }
        var data = $('#formEditDepartment').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_department',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไขข้อมูล Department เรียบร้อย', 'success');
              $('#editDepartmentModal').modal('hide');
              loadAdminDepartments();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'แก้ไขไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // ลบ Department
      $(document).on('click', '.btn-delete-dept', function() {
        var deptId = $(this).data('id');
        var deptName = $(this).data('deptname');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบ " + deptName + " ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_department',
              type: 'POST',
              data: {
                dept_id: deptId
              },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบข้อมูล ' + deptName + ' เรียบร้อยแล้ว', 'success');
                  loadAdminDepartments();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });

      // -----------------------------------------
      // Section CRUD Logic
      // -----------------------------------------
      function loadAdminSections() {
        var companyId = $('#sectionCompanySelect').val();
        if (!companyId) return;

        $('#sectionTable tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: SITE_URL + '/send_data/admin_get_sections',
          type: 'GET',
          data: {
            company_id: companyId
          },
          success: function(res) {
            $('#sectionTable tbody').html(res);
          },
          error: function() {
            $('#sectionTable tbody').html('<tr><td colspan="7" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลด Section ตอนเปลี่ยนบริษัทใน Tab Section
      $('#sectionCompanySelect').on('change', function() {
        loadAdminSections();
      });

      // โหลด Section เมื่อเปิด Tab Section
      $('#tab-section-tab').on('shown.bs.tab', function(e) {
        loadAdminSections();
      });

      // ฟังก์ชันสำหรับโหลด Dropdown ของ แผนก (Department) ตามสายงาน (Function)
      function loadDepartmentsForSelect(funcId, targetSelectId, selectedDeptId = null) {
        var $select = $(targetSelectId);
        $select.html('<option value="">-- กำลังโหลด --</option>');
        if (!funcId) {
          $select.html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
          return;
        }

        $.ajax({
          url: SITE_URL + '/send_data/get_departments_by_function_json',
          type: 'GET',
          data: {
            func_id: funcId
          },
          dataType: 'json',
          success: function(departments) {
            $select.empty();
            if (departments.length === 0) {
              $select.append('<option value="">-- ไม่มีแผนกในสายงานนี้ --</option>');
            } else {
              $select.append('<option value="">-- เลือกแผนก --</option>');
              $.each(departments, function(index, dept) {
                var selected = (selectedDeptId && selectedDeptId == dept.DeptID) ? 'selected' : '';
                $select.append('<option value="' + dept.DeptID + '" ' + selected + '>' + dept.DeptName + '</option>');
              });
            }
          },
          error: function() {
            $select.html('<option value="">-- โหลดข้อมูลผิดพลาด --</option>');
          }
        });
      }

      // เปิด Modal เพิ่ม Section
      $('#addSectionModal').on('show.bs.modal', function() {
        $('#formAddSection')[0].reset();
        var companyId = $('#sectionCompanySelect').val();
        if (companyId) {
          $('#addSecCompany').val(companyId);
          loadFunctionsForSelect(companyId, '#addSecFunction');
          $('#addSecDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
        } else {
          $('#addSecFunction').html('<option value="">-- กรุณาเลือกบริษัทก่อน --</option>');
          $('#addSecDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
        }
      });

      // เมื่อเปลี่ยนบริษัทใน Modal เพิ่ม Section
      $('#addSecCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#addSecFunction');
        $('#addSecDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
      });

      // เมื่อเปลี่ยนสายงานใน Modal เพิ่ม Section
      $('#addSecFunction').on('change', function() {
        loadDepartmentsForSelect($(this).val(), '#addSecDepartment');
      });

      // บันทึกเพิ่ม Section
      $('#btnSaveNewSection').on('click', function() {
        if (!$('#formAddSection')[0].checkValidity()) {
          $('#formAddSection')[0].reportValidity();
          return;
        }
        var data = $('#formAddSection').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_section',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่มข้อมูล Section เรียบร้อย', 'success');
              $('#addSectionModal').modal('hide');
              loadAdminSections();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // เปิด Modal แก้ไข Section
      $(document).on('click', '.btn-edit-sec', function() {
        var secId = $(this).data('id');
        var companyId = $(this).data('companyid');
        var funcId = $(this).data('funcid');
        var deptId = $(this).data('deptid');
        var secName = $(this).data('secname');
        var secCode = $(this).data('seccode');
        var status = $(this).data('status');

        $('#editSecId').val(secId);
        $('#editSecCompany').val(companyId);
        $('#editSecName').val(secName);
        $('#editSecCode').val(secCode);
        $('#editSecStatus').val(status);

        // Load functions and departments, wait for them to finish before selecting
        var $funcSelect = $('#editSecFunction');
        var $deptSelect = $('#editSecDepartment');

        $funcSelect.html('<option value="">-- กำลังโหลด --</option>');
        $.ajax({
          url: SITE_URL + '/send_data/get_functions_by_company_json',
          type: 'GET',
          data: {
            company_id: companyId
          },
          dataType: 'json',
          success: function(functions) {
            $funcSelect.empty();
            $funcSelect.append('<option value="">-- เลือกสายงาน --</option>');
            $.each(functions, function(index, func) {
              var selected = (funcId == func.FuncID) ? 'selected' : '';
              $funcSelect.append('<option value="' + func.FuncID + '" ' + selected + '>' + func.FuncName + '</option>');
            });

            // Now load departments
            $deptSelect.html('<option value="">-- กำลังโหลด --</option>');
            $.ajax({
              url: SITE_URL + '/send_data/get_departments_by_function_json',
              type: 'GET',
              data: {
                func_id: funcId
              },
              dataType: 'json',
              success: function(departments) {
                $deptSelect.empty();
                $deptSelect.append('<option value="">-- เลือกแผนก --</option>');
                $.each(departments, function(index, dept) {
                  var selected = (deptId == dept.DeptID) ? 'selected' : '';
                  $deptSelect.append('<option value="' + dept.DeptID + '" ' + selected + '>' + dept.DeptName + '</option>');
                });
              }
            });
          }
        });

        $('#editSectionModal').modal('show');
      });

      // เมื่อเปลี่ยนบริษัทใน Modal แก้ไข Section
      $('#editSecCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#editSecFunction');
        $('#editSecDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
      });

      // เมื่อเปลี่ยนสายงานใน Modal แก้ไข Section
      $('#editSecFunction').on('change', function() {
        loadDepartmentsForSelect($(this).val(), '#editSecDepartment');
      });

      // บันทึกแก้ไข Section
      $('#btnSaveEditSection').on('click', function() {
        if (!$('#formEditSection')[0].checkValidity()) {
          $('#formEditSection')[0].reportValidity();
          return;
        }
        var data = $('#formEditSection').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_section',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไขข้อมูล Section เรียบร้อย', 'success');
              $('#editSectionModal').modal('hide');
              loadAdminSections();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'แก้ไขไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // ลบ Section
      $(document).on('click', '.btn-delete-sec', function() {
        var secId = $(this).data('id');
        var secName = $(this).data('secname');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบ " + secName + " ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_section',
              type: 'POST',
              data: {
                sec_id: secId
              },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบข้อมูล ' + secName + ' เรียบร้อยแล้ว', 'success');
                  loadAdminSections();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });

      // -----------------------------------------
      // Map CRUD Logic
      // -----------------------------------------

      // Initialize Select2 correctly inside Bootstrap Modals
      $('#addMapModal').on('shown.bs.modal', function() {
        $('#addMapEmployee').select2({
          theme: 'bootstrap4',
          dropdownParent: $('#addMapModal'),
          minimumResultsForSearch: Infinity
        });
      });
      $('#editMapModal').on('shown.bs.modal', function() {
        $('#editMapEmployee').select2({
          theme: 'bootstrap4',
          dropdownParent: $('#editMapModal'),
          minimumResultsForSearch: Infinity
        });
      });

      var allEmployeesMap = []; // Global array

      // ฟังก์ชันสำหรับเรนเดอร์พนักงานเข้าสู่ Select (รองรับการ Filter)
      function renderEmployeeSelect(targetSelectId, employees, selectedUserId = null) {
        var $select = $(targetSelectId);
        $select.empty();
        if (employees.length === 0) {
          $select.append('<option value="">-- ไม่พบพนักงาน --</option>');
        } else {
          $select.append('<option value="">-- เลือกพนักงาน --</option>');
          $.each(employees, function(index, emp) {
            var selected = (selectedUserId && selectedUserId == emp.UserID) ? 'selected' : '';
            var displayName = emp.Fullname;
            if(emp.ThaiName) {
                displayName += " (" + emp.ThaiName + ")";
            }
            $select.append('<option value="' + emp.UserID + '" ' + selected + '>' + displayName + '</option>');
          });
        }
        $select.trigger('change.select2');
      }

      // ฟังก์ชันสำหรับโหลด Dropdown พนักงานทั้งหมดที่มีสถานะ Active
      function loadEmployeesForSelect(targetSelectId, selectedUserId = null) {
        var $select = $(targetSelectId);
        $select.html('<option value="">-- กำลังโหลด --</option>');

        $.ajax({
          url: SITE_URL + '/send_data/get_all_employees_json',
          type: 'GET',
          dataType: 'json',
          success: function(employees) {
            allEmployeesMap = employees; // เก็บไว้เผื่อค้นหา
            renderEmployeeSelect(targetSelectId, employees, selectedUserId);
          },
          error: function() {
            $select.html('<option value="">-- โหลดข้อมูลผิดพลาด --</option>');
          }
        });
      }

      // Event สำหรับช่องค้นหาพนักงานก่อนเลือก Dropdown (หน้าเพิ่ม Map)
      $('#searchEmpAdd').on('input', function() {
        var keyword = $(this).val().toLowerCase();
        var filtered = allEmployeesMap.filter(function(emp) {
          var fName = emp.Fullname ? emp.Fullname.toLowerCase() : '';
          var tName = emp.ThaiName ? emp.ThaiName.toLowerCase() : '';
          return fName.indexOf(keyword) > -1 || tName.indexOf(keyword) > -1;
        });
        renderEmployeeSelect('#addMapEmployee', filtered);
      });

      // ฟังก์ชันสำหรับโหลด Dropdown ของ บริษัท (เพื่อ Map)
      function loadCompaniesForMapSelect(targetSelectId, selectedCompanyId = null) {
        var $select = $(targetSelectId);
        // คัดลอกจาก mapCompanySelect ที่โหลดมาตอนแรก
        var html = $('#mapCompanySelect').html();
        $select.html(html);
        $select.prepend('<option value="">-- เลือกบริษัท --</option>');
        
        if (selectedCompanyId) {
            $select.val(selectedCompanyId);
        } else {
            $select.val('');
        }
      }

      // ฟังก์ชันสำหรับโหลด Dropdown ของ ส่วนงาน (Section) ตามแผนก (Department)
      function loadSectionsForSelect(deptId, targetSelectId, selectedSecId = null) {
        var $select = $(targetSelectId);
        $select.html('<option value="">-- กำลังโหลด --</option>');
        if (!deptId) {
          $select.html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
          return;
        }

        $.ajax({
          url: SITE_URL + '/send_data/get_sections_by_department_json',
          type: 'GET',
          data: {
            dept_id: deptId
          },
          dataType: 'json',
          success: function(sections) {
            $select.empty();
            if (sections.length === 0) {
              $select.append('<option value="">-- ไม่มีส่วนงานในแผนกนี้ --</option>');
            } else {
              $select.append('<option value="">-- เลือกส่วนงาน --</option>');
              $.each(sections, function(index, sec) {
                var selected = (selectedSecId && selectedSecId == sec.SecID) ? 'selected' : '';
                $select.append('<option value="' + sec.SecID + '" ' + selected + '>' + sec.SecName + '</option>');
              });
            }
          },
          error: function() {
            $select.html('<option value="">-- โหลดข้อมูลผิดพลาด --</option>');
          }
        });
      }

      function loadAdminMaps() {
        var companyId = $('#mapCompanySelect').val();
        if (!companyId) return;

        $('#mapTable tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');

        $.ajax({
          url: SITE_URL + '/send_data/admin_get_maps',
          type: 'GET',
          data: {
            company_id: companyId
          },
          success: function(res) {
            $('#mapTable tbody').html(res);
          },
          error: function() {
            $('#mapTable tbody').html('<tr><td colspan="4" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลด Map ตอนเปลี่ยนบริษัทใน Tab Map
      $('#mapCompanySelect').on('change', function() {
        loadAdminMaps();
      });

      // โหลด Map เมื่อเปิด Tab Map
      $('#tab-map-tab').on('shown.bs.tab', function(e) {
        loadAdminMaps();
      });

      // ---- ADD MAP MODAL EVENTS ----
      $('#addMapModal').on('show.bs.modal', function() {
        $('#formAddMap')[0].reset();
        
        var companyId = $('#mapCompanySelect').val();
        loadCompaniesForMapSelect('#addMapCompany', companyId);
        
        if (companyId) {
          loadFunctionsForSelect(companyId, '#addMapFunction');
          $('#addMapDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
          $('#addMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
        } else {
          $('#addMapFunction').html('<option value="">-- กรุณาเลือกบริษัทก่อน --</option>');
          $('#addMapDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
          $('#addMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
        }
        
        loadEmployeesForSelect('#addMapEmployee');
      });

      $('#addMapCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#addMapFunction');
        $('#addMapDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
        $('#addMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
      });

      $('#addMapFunction').on('change', function() {
        loadDepartmentsForSelect($(this).val(), '#addMapDepartment');
        $('#addMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
      });

      $('#addMapDepartment').on('change', function() {
        loadSectionsForSelect($(this).val(), '#addMapSection');
      });

      $('#btnSaveNewMap').on('click', function() {
        if (!$('#formAddMap')[0].checkValidity()) {
          $('#formAddMap')[0].reportValidity();
          return;
        }
        var data = $('#formAddMap').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_map',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่มข้อมูล Map เรียบร้อย', 'success');
              $('#addMapModal').modal('hide');
              loadAdminMaps();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // ---- EDIT MAP MODAL EVENTS ----
      $(document).on('click', '.btn-edit-map', function() {
        var mapId = $(this).data('id');
        var userId = $(this).data('userid');
        var secId = $(this).data('secid');
        var funcId = $(this).data('funcid');
        var deptId = $(this).data('deptid');
        var companyId = $(this).data('companyid');

        $('#editMapId').val(mapId);
        
        loadCompaniesForMapSelect('#editMapCompany', companyId);
        
        var $funcSelect = $('#editMapFunction');
        var $deptSelect = $('#editMapDepartment');
        var $secSelect = $('#editMapSection');
        
        $funcSelect.html('<option value="">-- กำลังโหลด --</option>');
        $deptSelect.html('<option value="">-- กำลังโหลด --</option>');
        $secSelect.html('<option value="">-- กำลังโหลด --</option>');

        // Load Employee first
        loadEmployeesForSelect('#editMapEmployee', userId);

        // Chain ajax loading to set selected values properly
        $.ajax({
          url: SITE_URL + '/send_data/get_functions_by_company_json',
          type: 'GET',
          data: { company_id: companyId },
          dataType: 'json',
          success: function(functions) {
            $funcSelect.empty().append('<option value="">-- เลือกสายงาน --</option>');
            $.each(functions, function(index, func) {
              var selected = (funcId == func.FuncID) ? 'selected' : '';
              $funcSelect.append('<option value="' + func.FuncID + '" ' + selected + '>' + func.FuncName + '</option>');
            });

            $.ajax({
              url: SITE_URL + '/send_data/get_departments_by_function_json',
              type: 'GET',
              data: { func_id: funcId },
              dataType: 'json',
              success: function(departments) {
                $deptSelect.empty().append('<option value="">-- เลือกแผนก --</option>');
                $.each(departments, function(index, dept) {
                  var selected = (deptId == dept.DeptID) ? 'selected' : '';
                  $deptSelect.append('<option value="' + dept.DeptID + '" ' + selected + '>' + dept.DeptName + '</option>');
                });

                $.ajax({
                  url: SITE_URL + '/send_data/get_sections_by_department_json',
                  type: 'GET',
                  data: { dept_id: deptId },
                  dataType: 'json',
                  success: function(sections) {
                    $secSelect.empty().append('<option value="">-- เลือกส่วนงาน --</option>');
                    $.each(sections, function(index, sec) {
                      var selected = (secId == sec.SecID) ? 'selected' : '';
                      $secSelect.append('<option value="' + sec.SecID + '" ' + selected + '>' + sec.SecName + '</option>');
                    });
                  }
                });
              }
            });
          }
        });

        $('#editMapModal').modal('show');
      });

      $('#editMapCompany').on('change', function() {
        loadFunctionsForSelect($(this).val(), '#editMapFunction');
        $('#editMapDepartment').html('<option value="">-- กรุณาเลือกสายงานก่อน --</option>');
        $('#editMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
      });

      $('#editMapFunction').on('change', function() {
        loadDepartmentsForSelect($(this).val(), '#editMapDepartment');
        $('#editMapSection').html('<option value="">-- กรุณาเลือกแผนกก่อน --</option>');
      });

      $('#editMapDepartment').on('change', function() {
        loadSectionsForSelect($(this).val(), '#editMapSection');
      });

      $('#btnSaveEditMap').on('click', function() {
        if (!$('#formEditMap')[0].checkValidity()) {
          $('#formEditMap')[0].reportValidity();
          return;
        }
        var data = $('#formEditMap').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_map',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไขข้อมูล Map เรียบร้อย', 'success');
              $('#editMapModal').modal('hide');
              loadAdminMaps();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'แก้ไขไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // ---- DELETE MAP EVENT ----
      $(document).on('click', '.btn-delete-map', function() {
        var mapId = $(this).data('id');
        var fullname = $(this).data('fullname');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบข้อมูลการ Map ของ " + fullname + " ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_map',
              type: 'POST',
              data: {
                map_id: mapId
              },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบข้อมูล ' + fullname + ' เรียบร้อยแล้ว', 'success');
                  loadAdminMaps();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });

      // -----------------------------------------
      // Position CRUD Logic
      // -----------------------------------------
      function loadAdminPositions() {
        $('#positionTable tbody').html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');
        $.ajax({
          url: SITE_URL + '/send_data/admin_get_positions_table',
          type: 'GET',
          success: function(res) {
            $('#positionTable tbody').html(res);
          },
          error: function() {
            $('#positionTable tbody').html('<tr><td colspan="11" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลดเมื่อเข้า Tab Position
      $('#tab-position-tab').on('shown.bs.tab', function(e) {
        loadAdminPositions();
      });

      function loadOrganizeLevels(targetSelectId, selectedValue = null) {
        var $select = $(targetSelectId);
        $select.html('<option value="">-- กำลังโหลด --</option>');
        $.ajax({
          url: SITE_URL + '/send_data/get_organize_levels_json',
          type: 'GET',
          dataType: 'json',
          success: function(levels) {
            $select.empty().append('<option value="">-- เลือก --</option>');
            $.each(levels, function(index, lvl) {
              var val = lvl.OrganizeLevel + '|' + (lvl.LevelNameEN || '') + '|' + (lvl.LevelNameTH || '');
              var text = 'Level ' + lvl.OrganizeLevel + ' : ' + (lvl.LevelNameEN || 'ไม่ระบุ');
              var isSelected = (selectedValue && selectedValue.indexOf(lvl.OrganizeLevel + '|') === 0) ? 'selected' : '';
              $select.append('<option value="' + val + '" ' + isSelected + '>' + text + '</option>');
            });
          }
        });
      }

      function loadOrganizeOrders(levelStr, targetSelectId, hintId, selectedValue = null) {
        var $select = $(targetSelectId);
        var $hint = $(hintId);
        $select.html('<option value="">-- กำลังโหลด --</option>');
        $hint.text('');
        
        if (!levelStr) {
          $select.html('<option value="">-- กรุณาเลือก Level ก่อน --</option>');
          return;
        }

        var level = levelStr.split('|')[0];

        $.ajax({
          url: SITE_URL + '/send_data/get_orders_by_level_json',
          type: 'GET',
          data: { level: level },
          dataType: 'json',
          success: function(orders) {
            var maxOrder = 0;
            var existings = [];
            $.each(orders, function(index, ord) {
                var o = parseInt(ord.OrganizeOrder);
                if (o > maxOrder) maxOrder = o;
                existings.push(o);
            });
            
            $hint.text('Order ปัจจุบันมี: ' + (existings.length > 0 ? existings.join(', ') : 'ยังไม่มี'));
            
            $select.empty().append('<option value="">-- เลือก Order --</option>');
            // Populate 1 to Max+1
            var upTo = maxOrder + 1;
            if (upTo < 1) upTo = 1; // Fallback
            for (var i = 1; i <= upTo; i++) {
                var isSelected = (selectedValue && selectedValue == i) ? 'selected' : '';
                var text = i + (existings.indexOf(i) > -1 ? ' (มีแล้ว)' : ' (ว่าง/ใหม่)');
                $select.append('<option value="' + i + '" ' + isSelected + '>' + text + '</option>');
            }
            
            // if editing and its original value is > upTo (rare), ensure it is added
            if (selectedValue && selectedValue > upTo) {
                $select.append('<option value="' + selectedValue + '" selected>' + selectedValue + ' (มีแล้ว)</option>');
            }
          }
        });
      }

      // Add Modal Events
      $('#addPositionModal').on('show.bs.modal', function() {
        $('#formAddPosition')[0].reset();
        loadOrganizeLevels('#addPosLevel');
        $('#addPosOrder').html('<option value="">-- กรุณาเลือก Level ก่อน --</option>');
        $('#addPosOrderHint').text('');
      });

      $('#addPosLevel').on('change', function() {
        loadOrganizeOrders($(this).val(), '#addPosOrder', '#addPosOrderHint');
      });

      $('#btnSaveNewPosition').on('click', function() {
        if (!$('#formAddPosition')[0].checkValidity()) {
          $('#formAddPosition')[0].reportValidity();
          return;
        }
        var data = $('#formAddPosition').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_position',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่ม Position เรียบร้อย', 'success');
              $('#addPositionModal').modal('hide');
              loadAdminPositions();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // Edit Modal Events
      $(document).on('click', '.btn-edit-position', function() {
        var posId = $(this).data('id');
        $('#editPosId').val(posId);
        $('#editPosAbbEN').val($(this).data('abben'));
        $('#editPosFullEN').val($(this).data('fullen'));
        $('#editPosAbbTH').val($(this).data('abbth'));
        $('#editPosFullTH').val($(this).data('fullth'));
        $('#editPosPosition').val($(this).data('pos'));
        $('#editPosBoard').val($(this).data('board'));
        
        var level = $(this).data('level');
        var nameEN = $(this).data('levelnameen');
        var nameTH = $(this).data('levelnameth');
        var levelStr = level + '|' + nameEN + '|' + nameTH;
        var order = $(this).data('order');

        loadOrganizeLevels('#editPosLevel', levelStr);
        loadOrganizeOrders(levelStr, '#editPosOrder', '#editPosOrderHint', order);

        $('#editPositionModal').modal('show');
      });

      $('#editPosLevel').on('change', function() {
        loadOrganizeOrders($(this).val(), '#editPosOrder', '#editPosOrderHint');
      });

      $('#btnSaveEditPosition').on('click', function() {
        if (!$('#formEditPosition')[0].checkValidity()) {
          $('#formEditPosition')[0].reportValidity();
          return;
        }
        var data = $('#formEditPosition').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_position',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไข Position เรียบร้อย', 'success');
              $('#editPositionModal').modal('hide');
              loadAdminPositions();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'แก้ไขไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // Delete Event
      $(document).on('click', '.btn-delete-position', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบ Position: " + name + " ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_position',
              type: 'POST',
              data: { position_id: id },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบข้อมูลเรียบร้อยแล้ว', 'success');
                  loadAdminPositions();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });
      // -----------------------------------------
      // Admin CRUD Logic
      // -----------------------------------------
      function loadAdminAdmins() {
        $('#table-body-admin').html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</td></tr>');
        $.ajax({
          url: SITE_URL + '/send_data/admin_get_admins',
          type: 'GET',
          success: function(res) {
            $('#table-body-admin').html(res);
          },
          error: function() {
            $('#table-body-admin').html('<tr><td colspan="4" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>');
          }
        });
      }

      // โหลดเมื่อเปิด Tab Admin
      $('#tab-admin-tab').on('shown.bs.tab', function(e) {
        loadAdminAdmins();
      });

      // เปิด Modal เพิ่ม
      $('#addAdminModal').on('show.bs.modal', function() {
        $('#formAddAdmin')[0].reset();
      });

      // บันทึกเพิ่ม
      $('#btnSaveNewAdmin').on('click', function() {
        if (!$('#formAddAdmin')[0].checkValidity()) {
          $('#formAddAdmin')[0].reportValidity();
          return;
        }
        var data = $('#formAddAdmin').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_add_admin',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'เพิ่ม Admin เรียบร้อย', 'success');
              $('#addAdminModal').modal('hide');
              loadAdminAdmins();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'บันทึกไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกเพิ่ม');
          }
        });
      });

      // เปิด Modal แก้ไข
      $(document).on('click', '.btn-edit-admin', function() {
        var id = $(this).data('id');
        var login = $(this).data('login');
        var company = $(this).data('company');
        var role = $(this).data('role');

        $('#editAdminId').val(id);
        $('#editAdminLogin').val(login);
        $('#editAdminPassword').val('');
        $('#editAdminCompany').val(company);
        $('#editAdminRole').val(role);

        $('#editAdminModal').modal('show');
      });

      // บันทึกแก้ไข
      $('#btnSaveEditAdmin').on('click', function() {
        if (!$('#formEditAdmin')[0].checkValidity()) {
          $('#formEditAdmin')[0].reportValidity();
          return;
        }
        var data = $('#formEditAdmin').serialize();
        var $btn = $(this);
        $btn.prop('disabled', true).text('กำลังบันทึก...');

        $.ajax({
          url: SITE_URL + '/send_data/admin_update_admin',
          type: 'POST',
          data: data,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              Swal.fire('สำเร็จ', 'แก้ไข Admin เรียบร้อย', 'success');
              $('#editAdminModal').modal('hide');
              loadAdminAdmins();
            } else {
              Swal.fire('ข้อผิดพลาด', res.message || 'แก้ไขไม่สำเร็จ', 'error');
            }
          },
          error: function() {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
          },
          complete: function() {
            $btn.prop('disabled', false).text('บันทึกแก้ไข');
          }
        });
      });

      // ลบ
      $(document).on('click', '.btn-delete-admin', function() {
        var id = $(this).data('id');

        Swal.fire({
          title: 'ยืนยันการลบ?',
          text: "คุณต้องการลบ Admin นี้ใช่หรือไม่?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'ใช่, ลบเลย!',
          cancelButtonText: 'ยกเลิก'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: SITE_URL + '/send_data/admin_delete_admin',
              type: 'POST',
              data: { login_id: id },
              dataType: 'json',
              success: function(res) {
                if (res.status === 'success') {
                  Swal.fire('ลบสำเร็จ!', 'ลบ Admin เรียบร้อยแล้ว', 'success');
                  loadAdminAdmins();
                } else {
                  Swal.fire('ข้อผิดพลาด', res.message || 'ลบไม่สำเร็จ', 'error');
                }
              },
              error: function() {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
              }
            });
          }
        });
      });

    });