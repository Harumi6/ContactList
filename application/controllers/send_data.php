<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * send_data Controller
 * ควบคุมการทำงานของระบบ ContactList ทั้งหน้าบ้าน (Public/Employee) และระบบจัดการหลังบ้าน (Admin CRUD)
 *
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_Upload $upload
 * @property Employee_model $Employee_model
 * @property Company_model $Company_model
 * @property Department_model $Department_model
 * @property AdminLogin_model $AdminLogin_model
 * @property Function_model $Function_model
 * @property Section_model $Section_model
 * @property Map_model $Map_model
 * @property Position_model $Position_model
 */
class send_data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model([
            'Employee_model',
            'Department_model',
            'Company_model',
            'AdminLogin_model',
            'Function_model',
            'Section_model',
            'Map_model',
            'Position_model'
        ]);
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * ส่ง JSON Response กลับไปยัง Client
     *
     * @param string $status 'success' | 'error' | 'empty' | 'org_only'
     * @param mixed $data_or_message ข้อมูล Array หรือข้อความ Message
     * @param array $extra ฟิลด์เสริมอื่นๆ เช่น redirect
     */
    private function _json_response($status, $data_or_message = null, array $extra = [])
    {
        $this->output->set_content_type('application/json');
        $response = ['status' => $status];

        if (is_string($data_or_message)) {
            $response['message'] = $data_or_message;
        } elseif (is_array($data_or_message) || is_object($data_or_message)) {
            $response['data'] = $data_or_message;
        }

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        $this->output->set_output(json_encode($response));
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าใช้งานของ Admin
     *
     * @param bool $is_ajax
     * @return bool
     */
    private function _require_admin_auth($is_ajax = true)
    {
        if (!$this->session->userdata('admin_logged_in')) {
            if ($is_ajax) {
                $this->_json_response('error', 'Unauthorized');
                return false;
            }
            redirect('send_data');
            return false;
        }
        return true;
    }

    /**
     * ตรวจสอบสิทธิ์การเข้าใช้งานของ Employee
     *
     * @param bool $is_ajax
     * @return bool
     */
    private function _require_employee_auth($is_ajax = true)
    {
        if (!$this->session->userdata('employee_logged_in')) {
            if ($is_ajax) {
                $this->_json_response('error', 'คุณไม่ได้เข้าสู่ระบบ');
                return false;
            }
            redirect('send_data');
            return false;
        }
        return true;
    }

    /**
     * จัดการอัปโหลดไฟล์รูปภาพของพนักงาน
     *
     * @param string $user_log_on
     * @return string|array|null ชื่อไฟล์ที่อัปโหลดสำเร็จ หรือ Array ข้อผิดพลาด
     */
    private function _upload_employee_picture($user_log_on)
    {
        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] == UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $filename = $_FILES['picture']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return ['error' => 'อนุญาตให้อัปโหลดเฉพาะไฟล์ .jpg, .jpeg หรือ .png เท่านั้น'];
        }

        $upload_path = FCPATH . 'assets/uploads/employee/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = '*';
        $config['file_name']     = $user_log_on;
        $config['overwrite']     = TRUE;
        $config['max_size']      = 5120; // 5MB

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('picture')) {
            $err = $this->upload->display_errors('', '');
            return ['error' => $err];
        }

        $data = $this->upload->data();
        return $data['file_name'];
    }

    // ==========================================
    // SECTION 1: PUBLIC / FRONTEND PAGES & AJAX
    // ==========================================

    /**
     * หน้าแรก (Contact List Directory)
     */
    public function index()
    {
        $data['companies']     = $this->Company_model->get_all_companies();
        $data['company']       = !empty($data['companies']) ? $data['companies'][0] : null;
        $data['departments']   = [];
        $data['employees']     = [];
        $data['org_structure'] = [];
        $data['admin_login']   = $this->AdminLogin_model->load_admin_login();

        $this->load->view('home', $data);
    }

    /**
     * AJAX GET: คืนข้อมูลบริษัทและรายการสายงาน (Function) ตาม CompanyID
     */
    public function get_company_info()
    {
        $id = $this->input->get('id');
        $company = $this->Company_model->get_company_by_id((int)$id);

        if ($company) {
            $dept = $this->Department_model->get_department_by_company_name($company->Company);
            $this->_json_response('success', null, [
                'fullname' => $company->FullName,
                'address'  => $company->Address,
                'tel'      => $company->Remark,
                'dept'     => $dept
            ]);
        } else {
            $this->_json_response('error', 'Company not found');
        }
    }

    /**
     * AJAX GET: คืนข้อมูลพนักงานตามบริษัทและสายงาน (JSON)
     */
    public function get_employees()
    {
        $company_id = $this->input->get('company_id');
        $department = $this->input->get('department');
        $keyword    = $this->input->get('keyword');

        $company = $this->Company_model->get_company_by_id((int)$company_id);
        if (!$company) {
            $this->_json_response('error', 'No employees found.');
            return;
        }

        $func_filter = (!empty($department) && $department !== '-All-') ? $department : null;
        $employees = $this->Employee_model->get_employees_filtered($company->Company, $func_filter, $keyword);

        if (empty($employees)) {
            if (!empty($keyword) || !empty($func_filter)) {
                $this->_json_response('empty', 'No employee with "' . $keyword . '" found.');
                return;
            }

            $org = $this->Department_model->get_org_structure_by_company_name($company->Company);
            if (!empty($org)) {
                $this->_json_response('org_only', $org);
            } else {
                $this->_json_response('empty', 'No data found.');
            }
            return;
        }

        $this->_json_response('success', $employees);
    }

    /**
     * AJAX GET: สำหรับ Live Search Autocomplete
     */
    public function search_autocomplete()
    {
        $company_id = $this->input->get('company_id');
        $keyword    = $this->input->get('keyword');

        if (empty($company_id) || empty($keyword)) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $company = $this->Company_model->get_company_by_id((int)$company_id);
        if (!$company) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $results = $this->Employee_model->search_employees_by_name($company->Company, $keyword);
        $this->output->set_content_type('application/json')->set_output(json_encode($results));
    }

    /**
     * AJAX GET: คืนข้อมูลพนักงานคนเดียวตาม Fullname
     */
    public function get_single_employee_json()
    {
        $fullname   = $this->input->get('fullname');
        $company_id = $this->input->get('company_id');

        if (empty($fullname) || empty($company_id)) {
            $this->_json_response('error', 'No employee selected.');
            return;
        }

        $company = $this->Company_model->get_company_by_id((int)$company_id);
        if (!$company) {
            $this->_json_response('error', 'Company not found.');
            return;
        }

        $employees = $this->Employee_model->get_employee_by_fullname($company->Company, $fullname);
        if (empty($employees)) {
            $this->_json_response('error', 'Employee not found.');
            return;
        }

        $this->_json_response('success', $employees);
    }

    /**
     * AJAX GET: ดึงวันที่อัปเดตข้อมูลล่าสุด
     */
    public function get_last_update_date_ajax()
    {
        $last_update = $this->Employee_model->get_last_update_date();
        $this->output->set_content_type('application/json')->set_output(json_encode(['last_update' => $last_update]));
    }

    // ==========================================
    // SECTION 2: AUTHENTICATION & SESSION
    // ==========================================

    /**
     * AJAX POST: ตรวจสอบการ Login (Admin Database หรือ LDAP สำหรับพนักงาน)
     */
    public function login()
    {
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        if (empty($username) || empty($password)) {
            $this->_json_response('error', 'กรุณากรอก Username และ Password');
            return;
        }

        // 1. ตรวจสอบ Admin จากฐานข้อมูลปกติ
        $admin = $this->AdminLogin_model->check_admin_login([
            'username' => $username,
            'password' => $password
        ]);

        if (!empty($admin)) {
            $this->session->set_userdata([
                'admin_logged_in'  => TRUE,
                'admin_username'   => $admin[0]->Login,
                'admin_role'       => $admin[0]->Role,
                'admin_company_id' => $admin[0]->CompanyID
            ]);

            $this->_json_response('success', null, [
                'fullname' => $admin[0]->Login,
                'redirect' => site_url("send_data/admin_dashboard")
            ]);
            return;
        }

        // 2. ตรวจสอบผ่าน LDAP สำหรับ พนักงานทั่วไป
        if (!function_exists('ldap_connect')) {
            $this->_json_response('error', 'ระบบไม่ได้เปิดใช้งาน LDAP Extension (php_ldap) ใน PHP');
            return;
        }

        $username_lowercase = strtolower($username);
        $server    = "10.44.40.9";
        $user_ldap = $username_lowercase . "@attg.co.th";
        $ad        = @ldap_connect($server);

        $ldap_success   = false;
        $ldap_error_msg = "";

        if ($ad) {
            @ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
            @ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

            if ($password !== "") {
                $b = @ldap_bind($ad, $user_ldap, $password);
                if ($b) {
                    $ldap_success = true;
                } else {
                    $ldap_error_msg = @ldap_error($ad);
                }
            } else {
                $ldap_error_msg = "รหัสผ่านว่างเปล่า";
            }
        } else {
            $ldap_error_msg = "ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ LDAP ($server) ได้";
        }

        if ($ldap_success) {
            $employee = $this->Employee_model->get_employee_by_email_like($username_lowercase);
            if ($employee && empty($employee->UserID)) {
                $employee->UserID = 0;
            }

            $this->session->set_userdata([
                'employee_logged_in' => TRUE,
                'employee_username'  => $username_lowercase,
                'employee_id'        => $employee ? $employee->UserID : null,
                'employee_fullname'  => $employee ? $employee->Fullname : $username_lowercase
            ]);

            $this->_json_response('success', null, [
                'fullname' => $employee ? $employee->Fullname : $username_lowercase,
                'redirect' => site_url("send_data/employee_dashboard")
            ]);
            return;
        }

        // 3. ไม่ถูกต้องทั้งสองแบบ
        $this->_json_response('error', 'เข้าสู่ระบบไม่สำเร็จ: ' . ($ldap_error_msg ? "LDAP Error: " . $ldap_error_msg : 'Username หรือ Password ไม่ถูกต้อง'));
    }

    /**
     * ออกจากระบบ ทำลาย Session
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('send_data');
    }

    // ==========================================
    // SECTION 3: EMPLOYEE DASHBOARD
    // ==========================================

    /**
     * หน้าโปรไฟล์ของพนักงาน
     */
    public function employee_dashboard()
    {
        if (!$this->_require_employee_auth(false)) return;

        $username = $this->session->userdata('employee_username');
        $employee = $this->Employee_model->get_employee_by_email_like($username);

        if ($employee && empty($employee->UserID)) {
            $employee->UserID = 0;
        }

        $data['username'] = $username;
        $data['fullname'] = $this->session->userdata('employee_fullname');
        $data['employee'] = $employee;

        $this->load->view('employee_crud', $data);
    }

    /**
     * AJAX POST: อัปเดตข้อมูลส่วนตัวของพนักงาน
     */
    public function update_employee_profile()
    {
        if (!$this->_require_employee_auth()) return;

        $employee_id = $this->session->userdata('employee_id');
        $username    = $this->session->userdata('employee_username');

        $employee = $this->Employee_model->get_employee_by_email_like($username);
        if (!$employee || empty($employee->EmailAddress)) {
            $this->_json_response('error', 'ไม่พบข้อมูล Email ของคุณ ไม่สามารถบันทึกข้อมูลได้');
            return;
        }

        $internal_no  = $this->input->post('internal_no', TRUE);
        $mobile_phone = $this->input->post('mobile_phone', TRUE);

        $profile_data = [
            'ThaiName'    => $this->input->post('thainame', TRUE),
            'Fullname'    => $this->input->post('fullname', TRUE),
            'MobilePhone' => $mobile_phone
        ];

        $success = $this->Employee_model->update_employee_profile_and_internal(
            $employee_id,
            $employee->EmailAddress,
            $profile_data,
            $internal_no,
            $mobile_phone
        );

        if ($success) {
            $this->_json_response('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
        } else {
            $this->_json_response('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    }

    // ==========================================
    // SECTION 4: ADMIN DASHBOARD & EMPLOYEE CRUD
    // ==========================================

    /**
     * หน้าแดชบอร์ดหลักของ Admin
     */
    public function admin_dashboard()
    {
        if (!$this->_require_admin_auth(false)) return;

        $admin_role       = $this->session->userdata('admin_role');
        $admin_company_id = $this->session->userdata('admin_company_id');

        if ($admin_role === null) {
            $this->session->sess_destroy();
            redirect('send_data');
            return;
        }

        if ($admin_role == 1 && !empty($admin_company_id)) {
            $company = $this->Company_model->get_company_by_id((int)$admin_company_id);
            $data['companies'] = $company ? [$company] : [];
        } else {
            $data['companies'] = $this->Company_model->get_all_companies();
        }

        $data['admin_username']   = $this->session->userdata('admin_username');
        $data['admin_role']       = $admin_role;
        $data['admin_company_id'] = $admin_company_id;

        $this->load->view('admin_crud', $data);
    }

    /**
     * AJAX GET: โหลดรายการพนักงานสำหรับหน้า Admin (HTML Rows)
     */
    public function admin_get_employees()
    {
        if (!$this->session->userdata('admin_logged_in')) return;

        $company_id = $this->input->get('company_id');
        $company    = $this->Company_model->get_company_by_id((int)$company_id);

        if (!$company) {
            echo '<tr><td colspan="8" class="text-center">ไม่พบบริษัท</td></tr>';
            return;
        }

        $employees = $this->Employee_model->get_employees_admin($company->Company);
        if (empty($employees)) {
            echo '<tr><td colspan="8" class="text-center">ไม่มีข้อมูลพนักงาน</td></tr>';
            return;
        }

        $html = '';
        foreach ($employees as $row) {
            $user_id    = isset($row->UserID)     ? $row->UserID     : '';
            $map_id     = isset($row->MapID)      ? $row->MapID      : '';
            $sec_id     = isset($row->SecID)      ? $row->SecID      : '';
            $pos_id     = isset($row->PositionID) ? $row->PositionID : '';
            $usr_status = isset($row->UserStatus) ? (int)trim((string)$row->UserStatus) : 1;
            $user_logon = isset($row->UserLogOn)  ? $row->UserLogOn  : '';
            $picture    = isset($row->picture)    ? $row->picture    : '';

            // ตรวจสอบไฟล์รูปภาพถ้าใน Database ว่าง
            if (empty($picture) && !empty($user_logon)) {
                $jpg_path = 'assets/uploads/employee/' . $user_logon . '.jpg';
                $png_path = 'assets/uploads/employee/' . $user_logon . '.png';
                if (file_exists(FCPATH . $jpg_path)) {
                    $picture = $jpg_path;
                } elseif (file_exists(FCPATH . $png_path)) {
                    $picture = $png_path;
                }
            }

            $status_badge = ($usr_status == 1)
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            if (!empty($picture)) {
                $pic_img = '<div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #ddd; background-color: #fff;"><img src="' . base_url($picture) . '?t=' . time() . '" alt="รูป" style="width: 100%; height: 100%; object-fit: cover;"></div>';
            } else {
                $pic_img = '<div style="width: 32px; height: 32px; border-radius: 50%; background-color: #eee; display: inline-flex; align-items: center; justify-content: center; color: #999; border: 1px solid #ddd; font-size: 14px;"><i class="fas fa-user"></i></div>';
            }

            $html .= '<tr>';
            $html .= '<td class="text-center align-middle" style="padding: 4px;">' . $pic_img . '</td>';
            $html .= '<td class="align-middle">' . html_escape($row->Fullname) . (!empty($row->ThaiName) ? ' (' . html_escape($row->ThaiName) . ')' : '') . '</td>';
            $html .= '<td class="align-middle">' . html_escape(isset($row->SecName) ? $row->SecName : '-') . '</td>';
            $html .= '<td class="align-middle">' . html_escape(isset($row->Position) ? $row->Position : '-') . '</td>';
            $html .= '<td class="text-center align-middle">' . $status_badge . '</td>';
            $html .= '<td class="align-middle">' . html_escape($row->TelePhone) . '</td>';
            $html .= '<td class="align-middle">' . html_escape($row->EmailAddress) . '</td>';
            $html .= '<td class="text-center align-middle">';
            $html .= '<div class="d-flex justify-content-center flex-nowrap" style="gap: 5px; white-space: nowrap;">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-emp"
                        data-id="' . $user_id . '"
                        data-map-id="' . $map_id . '"
                        data-fullnameen="' . html_escape($row->Fullname) . '"
                        data-fullnameth="' . html_escape(isset($row->ThaiName) ? $row->ThaiName : '') . '"
                        data-status="' . $usr_status . '"
                        data-sec="' . $sec_id . '"
                        data-pos="' . $pos_id . '"
                        data-phone="' . html_escape($row->TelePhone) . '"
                        data-email="' . html_escape($row->EmailAddress) . '"
                        data-userlogon="' . html_escape($user_logon) . '"
                        data-picture="' . html_escape($picture) . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-emp"
                        data-id="' . $user_id . '"
                        data-map-id="' . $map_id . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX GET: ดึง Section ทั้งหมดในบริษัทสำหรับ Dropdown ในฟอร์มพนักงาน
     */
    public function admin_get_sections_by_company()
    {
        $company_id = $this->input->get('company_id');
        $sections = $this->Section_model->get_sections_by_company_id((int)$company_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($sections));
    }

    /**
     * AJAX GET: ดึง Position ทั้งหมดสำหรับ Dropdown ในฟอร์มพนักงาน
     */
    public function admin_get_positions()
    {
        $positions = $this->Employee_model->get_all_positions();
        $this->output->set_content_type('application/json')->set_output(json_encode($positions));
    }

    /**
     * AJAX POST: เพิ่มพนักงานใหม่
     */
    public function admin_add_employee()
    {
        if (!$this->_require_admin_auth()) return;

        $user_log_on = $this->input->post('user_log_on');
        $sec_id      = $this->input->post('sec_id');
        $position_id = $this->input->post('position_id');

        $data = [
            'SecID'        => empty($sec_id) ? NULL : $sec_id,
            'PositionID'   => empty($position_id) ? NULL : $position_id,
            'Fullname'     => $this->input->post('fullname'),
            'ThaiName'     => $this->input->post('thainame'),
            'StaffID'      => $this->input->post('staff_id'),
            'Office'       => $this->input->post('office'),
            'MobilePhone'  => $this->input->post('mobile_phone'),
            'TelePhone'    => $this->input->post('telephone'),
            'EmailAddress' => $this->input->post('email'),
            'UserLogOn'    => $user_log_on,
            'UserStatus'   => 1
        ];

        if (!empty($user_log_on)) {
            $upload_result = $this->_upload_employee_picture($user_log_on);
            if (is_array($upload_result) && isset($upload_result['error'])) {
                $this->_json_response('error', 'Upload error: ' . $upload_result['error']);
                return;
            } elseif ($upload_result !== null) {
                $data['picture'] = 'assets/uploads/employee/' . $upload_result;
            }
        }

        $success = $this->Employee_model->insert_employee($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มพนักงานล้มเหลว');
        }
    }

    /**
     * AJAX POST: แก้ไขข้อมูลพนักงาน
     */
    public function admin_update_employee()
    {
        if (!$this->_require_admin_auth()) return;

        $user_id     = $this->input->post('user_id');
        $fullnameEN  = $this->input->post('fullnameen');
        $fullnameTH  = $this->input->post('fullnameth');
        $status      = $this->input->post('status');
        $map_id      = $this->input->post('map_id');
        $sec_id      = $this->input->post('sec_id');
        $position_id = $this->input->post('position_id');
        $phone       = $this->input->post('telephone');
        $email       = $this->input->post('email');
        $user_log_on = $this->input->post('user_log_on');
        $delete_pic  = $this->input->post('delete_picture');

        if (empty($user_id)) {
            $this->_json_response('error', 'ไม่พบรหัสพนักงาน');
            return;
        }

        $data = [
            'Fullname'     => $fullnameEN,
            'ThaiName'     => $fullnameTH,
            'UserStatus'   => $status,
            'MapID'        => empty($map_id) ? NULL : $map_id,
            'SecID'        => empty($sec_id) ? NULL : $sec_id,
            'PositionID'   => empty($position_id) ? NULL : $position_id,
            'TelePhone'    => $phone,
            'EmailAddress' => $email,
            'UserLogOn'    => $user_log_on
        ];

        $pic_info = $this->Employee_model->get_employee_picture_info($user_id);
        if ($pic_info) {
            $old_picture     = isset($pic_info->picture) ? $pic_info->picture : '';
            $old_user_log_on = isset($pic_info->UserLogOn) ? $pic_info->UserLogOn : '';

            // จัดการการลบรูปภาพ
            if ($delete_pic == 1) {
                if (!empty($old_picture) && file_exists(FCPATH . $old_picture)) {
                    unlink(FCPATH . $old_picture);
                }
                $data['picture'] = NULL;
                $old_picture = '';
            }

            $new_file_uploaded = (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK);

            if ($new_file_uploaded) {
                if (!empty($user_log_on)) {
                    $upload_result = $this->_upload_employee_picture($user_log_on);
                    if (is_array($upload_result) && isset($upload_result['error'])) {
                        $this->_json_response('error', 'Upload error: ' . $upload_result['error']);
                        return;
                    } elseif ($upload_result !== null) {
                        $data['picture'] = 'assets/uploads/employee/' . $upload_result;
                        if (!empty($old_user_log_on) && $user_log_on !== $old_user_log_on && !empty($old_picture) && file_exists(FCPATH . $old_picture)) {
                            unlink(FCPATH . $old_picture);
                        }
                    }
                }
            } else {
                // เปลี่ยนชื่อไฟล์รูปภาพเดิมกรณี UserLogOn เปลี่ยนแปลง
                if (!empty($user_log_on) && !empty($old_user_log_on) && $user_log_on !== $old_user_log_on) {
                    $ext = '';
                    $old_path = '';

                    if (!empty($old_picture) && file_exists(FCPATH . $old_picture)) {
                        $old_path = FCPATH . $old_picture;
                        $ext = pathinfo($old_path, PATHINFO_EXTENSION);
                    } elseif (file_exists(FCPATH . 'assets/uploads/employee/' . $old_user_log_on . '.jpg')) {
                        $old_path = FCPATH . 'assets/uploads/employee/' . $old_user_log_on . '.jpg';
                        $ext = 'jpg';
                    } elseif (file_exists(FCPATH . 'assets/uploads/employee/' . $old_user_log_on . '.png')) {
                        $old_path = FCPATH . 'assets/uploads/employee/' . $old_user_log_on . '.png';
                        $ext = 'png';
                    }

                    if (!empty($old_path) && !empty($ext)) {
                        $new_rel_path = 'assets/uploads/employee/' . $user_log_on . '.' . $ext;
                        if (rename($old_path, FCPATH . $new_rel_path)) {
                            $data['picture'] = $new_rel_path;
                        }
                    }
                }
            }
        }

        $success = $this->Employee_model->update_employee($user_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'อัปเดตฐานข้อมูลล้มเหลว');
        }
    }

    /**
     * AJAX POST: ลบพนักงาน
     */
    public function admin_delete_employee()
    {
        if (!$this->_require_admin_auth()) return;

        $user_id = $this->input->post('user_id');
        if (empty($user_id)) {
            $this->_json_response('error', 'ไม่มี UserID สำหรับลบข้อมูล');
            return;
        }

        $this->db->trans_start();
        $this->Map_model->delete_map_by_user($user_id);
        $this->Employee_model->delete_employee($user_id);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        } else {
            $this->_json_response('success');
        }
    }

    // ==========================================
    // SECTION 5: ADMIN FUNCTION MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: ดึงรายการ Function สำหรับ Admin (HTML Rows)
     */
    public function admin_get_functions()
    {
        $company_id = $this->input->get('company_id');
        $functions  = $this->Function_model->admin_get_functions_by_company((int)$company_id);

        if (empty($functions)) {
            echo '<tr><td colspan="4" class="text-center">ไม่มีข้อมูล Function</td></tr>';
            return;
        }

        $html = '';
        foreach ($functions as $row) {
            $status_badge = ($row->FuncStatus == 1)
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            $html .= '<tr>';
            $html .= '<td>' . html_escape($row->FuncName) . '</td>';
            $html .= '<td>' . html_escape($row->FuncCode ?? '') . '</td>';
            $html .= '<td class="text-center">' . $status_badge . '</td>';
            $html .= '<td class="text-center">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-func mr-1" 
                        data-id="' . $row->FuncID . '" 
                        data-funcname="' . html_escape($row->FuncName) . '" 
                        data-funccode="' . html_escape($row->FuncCode ?? '') . '" 
                        data-status="' . $row->FuncStatus . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-func" 
                        data-id="' . $row->FuncID . '" 
                        data-funcname="' . html_escape($row->FuncName) . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX GET: ดึง Functions ตามบริษัทเป็น JSON สำหรับ Dropdown
     */
    public function get_functions_by_company_json()
    {
        $company_id = $this->input->get('company_id');
        $functions  = $this->Function_model->get_functions_by_company((int)$company_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($functions));
    }

    /**
     * AJAX POST: เพิ่ม Function ใหม่
     */
    public function admin_add_function()
    {
        if (!$this->_require_admin_auth()) return;

        $data = [
            'CompanyID'  => $this->input->post('company_id'),
            'FuncName'   => $this->input->post('func_name'),
            'FuncCode'   => $this->input->post('func_code'),
            'FuncStatus' => 1
        ];

        $success = $this->Function_model->insert_function($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Function
     */
    public function admin_update_function()
    {
        if (!$this->_require_admin_auth()) return;

        $func_id = $this->input->post('func_id');
        $data = [
            'FuncName'   => $this->input->post('func_name'),
            'FuncCode'   => $this->input->post('func_code'),
            'FuncStatus' => $this->input->post('status')
        ];

        $success = $this->Function_model->update_function($func_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Function
     */
    public function admin_delete_function()
    {
        if (!$this->_require_admin_auth()) return;

        $func_id = $this->input->post('func_id');
        $success = $this->Function_model->delete_function($func_id);

        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }

    // ==========================================
    // SECTION 6: ADMIN DEPARTMENT MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: ดึงรายการ Department สำหรับ Admin (HTML Rows)
     */
    public function admin_get_departments()
    {
        $company_id  = $this->input->get('company_id');
        $departments = $this->Department_model->admin_get_departments_by_company((int)$company_id);

        if (empty($departments)) {
            echo '<tr><td colspan="6" class="text-center">ไม่มีข้อมูล Department</td></tr>';
            return;
        }

        $html = '';
        foreach ($departments as $row) {
            $status_badge = ($row->DeptStatus == 1)
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            $html .= '<tr>';
            $html .= '<td>' . html_escape($row->Company ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->FuncName ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->DeptName) . '</td>';
            $html .= '<td>' . html_escape($row->DeptCode ?? '') . '</td>';
            $html .= '<td class="text-center">' . $status_badge . '</td>';
            $html .= '<td class="text-center">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-dept mr-1" 
                        data-id="' . $row->DeptID . '" 
                        data-companyid="' . $row->CompanyID . '"
                        data-funcid="' . $row->FuncID . '"
                        data-deptname="' . html_escape($row->DeptName) . '" 
                        data-deptcode="' . html_escape($row->DeptCode ?? '') . '" 
                        data-status="' . $row->DeptStatus . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-dept" 
                        data-id="' . $row->DeptID . '" 
                        data-deptname="' . html_escape($row->DeptName) . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX GET: ดึง Departments ตาม FuncID เป็น JSON สำหรับ Dropdown
     */
    public function get_departments_by_function_json()
    {
        $func_id = $this->input->get('func_id');
        if (!$func_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $departments = $this->Department_model->get_departments_by_function((int)$func_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($departments));
    }

    /**
     * AJAX POST: เพิ่ม Department
     */
    public function admin_add_department()
    {
        if (!$this->_require_admin_auth()) return;

        $data = [
            'FuncID'     => $this->input->post('func_id'),
            'DeptName'   => $this->input->post('dept_name'),
            'DeptCode'   => $this->input->post('dept_code'),
            'DeptStatus' => 1
        ];

        $success = $this->Department_model->insert_department($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Department
     */
    public function admin_update_department()
    {
        if (!$this->_require_admin_auth()) return;

        $dept_id = $this->input->post('dept_id');
        $data = [
            'FuncID'     => $this->input->post('func_id'),
            'DeptName'   => $this->input->post('dept_name'),
            'DeptCode'   => $this->input->post('dept_code'),
            'DeptStatus' => $this->input->post('status')
        ];

        $success = $this->Department_model->update_department($dept_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Department
     */
    public function admin_delete_department()
    {
        if (!$this->_require_admin_auth()) return;

        $dept_id = $this->input->post('dept_id');
        $success = $this->Department_model->delete_department($dept_id);

        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }

    // ==========================================
    // SECTION 7: ADMIN SECTION MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: ดึง Sections ตาม DeptID เป็น JSON สำหรับ Dropdown
     */
    public function get_sections_by_department_json()
    {
        $dept_id = $this->input->get('dept_id');
        if (!$dept_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $sections = $this->Section_model->get_sections_by_department((int)$dept_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($sections));
    }

    /**
     * AJAX GET: ดึงรายการ Section สำหรับ Admin (HTML Rows)
     */
    public function admin_get_sections()
    {
        $company_id = $this->input->get('company_id');
        $sections   = $this->Section_model->admin_get_sections_by_company((int)$company_id);

        if (empty($sections)) {
            echo '<tr><td colspan="7" class="text-center">ไม่มีข้อมูล Section</td></tr>';
            return;
        }

        $html = '';
        foreach ($sections as $row) {
            $status_badge = ($row->SecStatus == 1)
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            $html .= '<tr>';
            $html .= '<td>' . html_escape($row->Company ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->FuncName ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->DeptName ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->SecName) . '</td>';
            $html .= '<td>' . html_escape($row->SecCode ?? '') . '</td>';
            $html .= '<td class="text-center">' . $status_badge . '</td>';
            $html .= '<td class="text-center">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-sec mr-1" 
                        data-id="' . $row->SecID . '" 
                        data-companyid="' . $row->CompanyID . '"
                        data-funcid="' . $row->FuncID . '"
                        data-deptid="' . $row->DeptID . '"
                        data-secname="' . html_escape($row->SecName) . '" 
                        data-seccode="' . html_escape($row->SecCode ?? '') . '" 
                        data-status="' . $row->SecStatus . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-sec" 
                        data-id="' . $row->SecID . '" 
                        data-secname="' . html_escape($row->SecName) . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX POST: เพิ่ม Section
     */
    public function admin_add_section()
    {
        if (!$this->_require_admin_auth()) return;

        $data = [
            'DeptID'    => $this->input->post('dept_id'),
            'SecName'   => $this->input->post('sec_name'),
            'SecCode'   => $this->input->post('sec_code'),
            'SecStatus' => 1
        ];

        $success = $this->Section_model->insert_section($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Section
     */
    public function admin_update_section()
    {
        if (!$this->_require_admin_auth()) return;

        $sec_id = $this->input->post('sec_id');
        $data = [
            'DeptID'    => $this->input->post('dept_id'),
            'SecName'   => $this->input->post('sec_name'),
            'SecCode'   => $this->input->post('sec_code'),
            'SecStatus' => $this->input->post('status')
        ];

        $success = $this->Section_model->update_section($sec_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Section
     */
    public function admin_delete_section()
    {
        if (!$this->_require_admin_auth()) return;

        $sec_id  = $this->input->post('sec_id');
        $success = $this->Section_model->delete_section($sec_id);

        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }

    // ==========================================
    // SECTION 8: ADMIN MAP MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: Dropdown พนักงาน Active ทั้งหมด
     */
    public function get_all_employees_json()
    {
        $employees = $this->Employee_model->get_active_employees_dropdown();
        $this->output->set_content_type('application/json')->set_output(json_encode($employees));
    }

    /**
     * AJAX GET: โหลดรายการ Map สำหรับ Admin (HTML Rows)
     */
    public function admin_get_maps()
    {
        if (!$this->session->userdata('admin_logged_in')) {
            echo '<tr><td colspan="4" class="text-center text-danger">Unauthorized</td></tr>';
            return;
        }

        $company_id = $this->input->get('company_id');
        if (!$company_id) {
            echo '<tr><td colspan="4" class="text-center">กรุณาเลือกบริษัท</td></tr>';
            return;
        }

        $maps = $this->Map_model->admin_get_maps_by_company((int)$company_id);
        if (empty($maps)) {
            echo '<tr><td colspan="4" class="text-center">ไม่มีข้อมูล Map</td></tr>';
            return;
        }

        $html = '';
        foreach ($maps as $row) {
            $status_badge = (trim($row->UserStatus) == '1')
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            $html .= '<tr>';
            $html .= '<td>' . html_escape($row->SecName ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->Fullname ?? '') . '</td>';
            $html .= '<td class="text-center">' . $status_badge . '</td>';
            $html .= '<td class="text-center">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-map mr-1" 
                        data-id="' . $row->MapID . '" 
                        data-userid="' . $row->UserID . '"
                        data-secid="' . $row->SecID . '"
                        data-funcid="' . $row->FuncID . '"
                        data-deptid="' . $row->DeptID . '"
                        data-companyid="' . $row->CompanyID . '"
                        data-fullname="' . html_escape($row->Fullname) . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-map" 
                        data-id="' . $row->MapID . '"
                        data-fullname="' . html_escape($row->Fullname) . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX POST: เพิ่ม Map
     */
    public function admin_add_map()
    {
        if (!$this->_require_admin_auth()) return;

        $data = [
            'UserID' => $this->input->post('user_id'),
            'SecID'  => $this->input->post('sec_id')
        ];

        $success = $this->Map_model->insert_map($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Map
     */
    public function admin_update_map()
    {
        if (!$this->_require_admin_auth()) return;

        $map_id = $this->input->post('map_id');
        $data = [
            'UserID' => $this->input->post('user_id'),
            'SecID'  => $this->input->post('sec_id')
        ];

        $success = $this->Map_model->update_map($map_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Map
     */
    public function admin_delete_map()
    {
        if (!$this->_require_admin_auth()) return;

        $map_id  = $this->input->post('map_id');
        $success = $this->Map_model->delete_map($map_id);

        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }

    // ==========================================
    // SECTION 9: ADMIN POSITION MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: Dropdown OrganizeLevel
     */
    public function get_organize_levels_json()
    {
        $levels = $this->Position_model->get_organize_levels();
        $this->output->set_content_type('application/json')->set_output(json_encode($levels));
    }

    /**
     * AJAX GET: Dropdown OrganizeOrder ตาม OrganizeLevel
     */
    public function get_orders_by_level_json()
    {
        $level = $this->input->get('level');
        if (!$level) {
            $this->output->set_content_type('application/json')->set_output(json_encode([]));
            return;
        }

        $orders = $this->Position_model->get_orders_by_level($level);
        $this->output->set_content_type('application/json')->set_output(json_encode($orders));
    }

    /**
     * AJAX GET: โหลดรายการ Position สำหรับ Admin (HTML Rows)
     */
    public function admin_get_positions_table()
    {
        if (!$this->session->userdata('admin_logged_in')) {
            echo '<tr><td colspan="11" class="text-center text-danger">Unauthorized</td></tr>';
            return;
        }

        $positions = $this->Position_model->get_all_positions();
        if (empty($positions)) {
            echo '<tr><td colspan="11" class="text-center">ไม่มีข้อมูล Position</td></tr>';
            return;
        }

        $html = '';
        foreach ($positions as $row) {
            $html .= '<tr>';
            $html .= '<td>' . html_escape($row->AbbreviateEN ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->FullNameEN ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->AbbreviateTH ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->FullnameTH ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->Position ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->OrganizeLevel ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->LevelNameEN ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->LevelNameTH ?? '') . '</td>';
            $html .= '<td>' . html_escape($row->OrganizeOrder ?? '') . '</td>';
            $html .= '<td class="text-center">' . html_escape($row->Board ?? '') . '</td>';
            $html .= '<td class="text-center" style="white-space: nowrap;">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-position mr-1" 
                        data-id="' . $row->PositionID . '"
                        data-abben="' . html_escape($row->AbbreviateEN) . '"
                        data-fullen="' . html_escape($row->FullNameEN) . '"
                        data-abbth="' . html_escape($row->AbbreviateTH) . '"
                        data-fullth="' . html_escape($row->FullnameTH) . '"
                        data-pos="' . html_escape($row->Position) . '"
                        data-level="' . html_escape($row->OrganizeLevel) . '"
                        data-levelnameen="' . html_escape($row->LevelNameEN) . '"
                        data-levelnameth="' . html_escape($row->LevelNameTH) . '"
                        data-order="' . html_escape($row->OrganizeOrder) . '"
                        data-board="' . html_escape($row->Board) . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-position" 
                        data-id="' . $row->PositionID . '"
                        data-name="' . html_escape($row->FullNameEN) . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        echo $html;
    }

    /**
     * AJAX POST: เพิ่ม Position
     */
    public function admin_add_position()
    {
        if (!$this->_require_admin_auth()) return;

        $level_data = explode('|', $this->input->post('organize_level'));

        $data = [
            'AbbreviateEN'  => $this->input->post('abbreviate_en'),
            'FullNameEN'    => $this->input->post('fullname_en'),
            'AbbreviateTH'  => $this->input->post('abbreviate_th'),
            'FullnameTH'    => $this->input->post('fullname_th'),
            'Position'      => $this->input->post('position'),
            'OrganizeLevel' => isset($level_data[0]) ? $level_data[0] : null,
            'LevelNameEN'   => isset($level_data[1]) ? $level_data[1] : null,
            'LevelNameTH'   => isset($level_data[2]) ? $level_data[2] : null,
            'OrganizeOrder' => $this->input->post('organize_order'),
            'Board'         => $this->input->post('board')
        ];

        if ($this->Position_model->check_duplicate_order($data['OrganizeLevel'], $data['OrganizeOrder'])) {
            $this->_json_response('error', 'Organize Order นี้มีข้อมูลอยู่แล้ว กรุณาเลือกลำดับอื่น');
            return;
        }

        $success = $this->Position_model->insert_position($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Position
     */
    public function admin_update_position()
    {
        if (!$this->_require_admin_auth()) return;

        $position_id = $this->input->post('position_id');
        $level_data  = explode('|', $this->input->post('organize_level'));

        $data = [
            'AbbreviateEN'  => $this->input->post('abbreviate_en'),
            'FullNameEN'    => $this->input->post('fullname_en'),
            'AbbreviateTH'  => $this->input->post('abbreviate_th'),
            'FullnameTH'    => $this->input->post('fullname_th'),
            'Position'      => $this->input->post('position'),
            'OrganizeLevel' => isset($level_data[0]) ? $level_data[0] : null,
            'LevelNameEN'   => isset($level_data[1]) ? $level_data[1] : null,
            'LevelNameTH'   => isset($level_data[2]) ? $level_data[2] : null,
            'OrganizeOrder' => $this->input->post('organize_order'),
            'Board'         => $this->input->post('board')
        ];

        if ($this->Position_model->check_duplicate_order($data['OrganizeLevel'], $data['OrganizeOrder'], $position_id)) {
            $this->_json_response('error', 'Organize Order นี้มีข้อมูลอยู่แล้ว กรุณาเลือกลำดับอื่น');
            return;
        }

        $success = $this->Position_model->update_position($position_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Position
     */
    public function admin_delete_position()
    {
        if (!$this->_require_admin_auth()) return;

        $position_id = $this->input->post('position_id');
        $success     = $this->Position_model->delete_position($position_id);

        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }

    // ==========================================
    // SECTION 10: ADMIN ACCOUNT MANAGEMENT
    // ==========================================

    /**
     * AJAX GET: โหลดรายการ Admin (HTML Rows)
     */
    public function admin_get_admins()
    {
        if (!$this->session->userdata('admin_logged_in')) return;

        $admin_role       = $this->session->userdata('admin_role');
        $admin_company_id = $this->session->userdata('admin_company_id');

        if ($admin_role == 0) {
            $admins = $this->AdminLogin_model->get_all_admins();
        } else {
            $admins = $this->AdminLogin_model->get_admins_by_company($admin_company_id);
        }

        $html = '';
        foreach ($admins as $row) {
            $company = empty($row->CompanyID) ? 'Master' : html_escape($row->Company);
            $role    = ($row->Role == 0) ? 'Master' : 'Company Admin';

            $html .= '<tr>';
            $html .= '<td class="align-middle">' . html_escape($row->Login) . '</td>';
            $html .= '<td class="align-middle">' . $company . '</td>';
            $html .= '<td class="align-middle">' . $role . '</td>';
            $html .= '<td class="text-center align-middle">';
            $html .= '<div class="d-flex justify-content-center flex-nowrap" style="gap: 5px; white-space: nowrap;">';
            $html .= '<button class="btn btn-warning btn-sm btn-edit-admin" data-id="' . $row->LoginID . '" data-login="' . html_escape($row->Login) . '" data-company="' . html_escape($row->CompanyID) . '" data-role="' . $row->Role . '"><i class="fas fa-edit"></i> แก้ไข</button>';
            $html .= '<button class="btn btn-danger btn-sm btn-delete-admin" data-id="' . $row->LoginID . '"><i class="fas fa-trash"></i> ลบ</button>';
            $html .= '</div></td>';
            $html .= '</tr>';
        }

        if (empty($html)) {
            $html = '<tr><td colspan="4" class="text-center">ไม่มีข้อมูล Admin</td></tr>';
        }
        echo $html;
    }

    /**
     * AJAX POST: เพิ่ม Admin
     */
    public function admin_add_admin()
    {
        if (!$this->_require_admin_auth()) return;

        $login      = $this->input->post('login');
        $password   = $this->input->post('password');
        $company_id = $this->input->post('company_id');
        $role       = $this->input->post('role');

        if ($this->session->userdata('admin_role') == 1) {
            $company_id = $this->session->userdata('admin_company_id');
            $role = 1;
        }

        if (empty($login) || empty($password)) {
            $this->_json_response('error', 'ข้อมูลไม่ครบถ้วน');
            return;
        }

        $existing = $this->AdminLogin_model->get_admin_by_username($login);
        if (!empty($existing)) {
            $this->_json_response('error', 'ชื่อผู้ใช้นี้มีในระบบแล้ว');
            return;
        }

        $data = [
            'Login'     => $login,
            'Password'  => md5($password),
            'CompanyID' => empty($company_id) ? null : $company_id,
            'Role'      => $role
        ];

        $success = $this->AdminLogin_model->insert_admin($data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'เพิ่มข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: แก้ไข Admin
     */
    public function admin_update_admin()
    {
        if (!$this->_require_admin_auth()) return;

        $login_id   = $this->input->post('login_id');
        $login      = $this->input->post('login');
        $password   = $this->input->post('password');
        $company_id = $this->input->post('company_id');
        $role       = $this->input->post('role');

        if ($this->session->userdata('admin_role') == 1) {
            $target_admin = $this->AdminLogin_model->get_admin_by_id($login_id);
            if (!$target_admin || $target_admin->CompanyID != $this->session->userdata('admin_company_id') || $target_admin->Role == 0) {
                $this->_json_response('error', 'Permission Denied');
                return;
            }
            $company_id = $this->session->userdata('admin_company_id');
            $role = 1;
        }

        if (empty($login_id) || empty($login)) {
            $this->_json_response('error', 'ข้อมูลไม่ครบถ้วน');
            return;
        }

        $existing = $this->AdminLogin_model->get_admin_by_username($login);
        if (!empty($existing) && $existing[0]->LoginID != $login_id) {
            $this->_json_response('error', 'ชื่อผู้ใช้นี้มีในระบบแล้ว');
            return;
        }

        $data = [
            'Login'     => $login,
            'CompanyID' => empty($company_id) ? null : $company_id,
            'Role'      => $role
        ];
        if (!empty($password)) {
            $data['Password'] = md5($password);
        }

        $success = $this->AdminLogin_model->update_admin($login_id, $data);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'แก้ไขข้อมูลไม่สำเร็จ');
        }
    }

    /**
     * AJAX POST: ลบ Admin
     */
    public function admin_delete_admin()
    {
        if (!$this->_require_admin_auth()) return;

        $login_id = $this->input->post('login_id');
        if (empty($login_id)) {
            $this->_json_response('error', 'ไม่มี ID สำหรับลบข้อมูล');
            return;
        }

        if ($this->session->userdata('admin_role') == 1) {
            $target_admin = $this->AdminLogin_model->get_admin_by_id($login_id);
            if (!$target_admin || $target_admin->CompanyID != $this->session->userdata('admin_company_id') || $target_admin->Role == 0) {
                $this->_json_response('error', 'Permission Denied');
                return;
            }
        }

        $success = $this->AdminLogin_model->delete_admin($login_id);
        if ($success) {
            $this->_json_response('success');
        } else {
            $this->_json_response('error', 'ลบข้อมูลไม่สำเร็จ');
        }
    }
}