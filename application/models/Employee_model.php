<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Employee_model
 * จัดการข้อมูลพนักงานทั้งจาก TbContactUser, EmpFromSF$ (VwShowDataSuccessFactor) และ TbInternal
 *
 * @property CI_DB_query_builder $db
 */
class Employee_model extends CI_Model
{
    // ==========================================
    // CORE QUERY / UNION HELPER
    // ==========================================

    /**
     * Helper สำหรับสร้าง Subquery ของข้อมูลพนักงานจาก 3 แหล่ง:
     * 1. VwShowData (พนักงานใน TbContactUser)
     * 2. VwShowDataSuccessFactor (พนักงานที่ Sync มาจาก SuccessFactors)
     * 3. TbFunction / TbDepartment / TbSection (โครงสร้างแผนก)
     *
     * @return string
     */
    private function _get_union_sql()
    {
        $sql1 = "SELECT V.Company, 
             CASE WHEN (V.FuncName IS NULL OR LTRIM(RTRIM(V.FuncName)) = '') 
                   AND (V.DeptName IS NULL OR LTRIM(RTRIM(V.DeptName)) = '') 
                   AND (V.SecName IS NULL OR LTRIM(RTRIM(V.SecName)) = '') 
                  THEN 'Top Management' ELSE V.FuncName END AS FuncName, 
             V.DeptName, V.SecName, V.UserID, V.Fullname, V.ThaiName, V.Position, 
             CASE WHEN V.EmailAddress LIKE '%@sapsf.com' THEN NULL ELSE V.EmailAddress END AS EmailAddress, 
             V.TelePhone, T.UserStatus, T.UserLogOn, ISNULL(I.telephone, T.MobilePhone) AS MobilePhone, V.OrganizeLevel, V.OrganizeOrder,
             I.internal_no
             FROM VwShowData V 
             JOIN TbContactUser T ON V.UserID = T.UserID 
             LEFT JOIN TbInternal I ON V.EmailAddress = I.Email
             WHERE T.UserStatus = '1'";

        $sql2 = "SELECT 
             CAST(ISNULL(C.Company, VSF.ComCode) AS NVARCHAR(50)) AS Company, 
             CASE WHEN (Division_EN IS NULL OR LTRIM(RTRIM(Division_EN)) = '') 
                   AND (Department_EN IS NULL OR LTRIM(RTRIM(Department_EN)) = '') 
                   AND (Section_EN IS NULL OR LTRIM(RTRIM(Section_EN)) = '') 
                  THEN 'Top Management' ELSE CAST(Division_EN AS NVARCHAR(255)) END AS FuncName, 
             CAST(Department_EN AS NVARCHAR(255)) AS DeptName, CAST(Section_EN AS NVARCHAR(255)) AS SecName, 0 AS UserID, 
             (Fname_EN + ' ' + Lname_EN) AS Fullname, (Fname_TH + ' ' + Lname_TH) AS ThaiName, 
             PositionLevel AS Position, 
             CASE WHEN VSF.Email LIKE '%@sapsf.com' THEN NULL ELSE VSF.Email END AS EmailAddress, 
             NULL AS TelePhone, EmpStatus AS UserStatus, EmpID AS UserLogOn, I.telephone AS MobilePhone, 
             ISNULL(P.OrganizeLevel, '9') AS OrganizeLevel, 
             ISNULL(P.OrganizeOrder, '99') AS OrganizeOrder,
             I.internal_no
             FROM VwShowDataSuccessFactor VSF
             LEFT JOIN TbCompany C ON C.ComCode = VSF.ComCode
             LEFT JOIN TbPosition P ON P.FullNameEN = VSF.PositionLevel
             LEFT JOIN TbInternal I ON VSF.Email = I.Email";

        $sql3 = "SELECT C.Company, F.FuncName, D.DeptName, S.SecName, 0 AS UserID, 
             '' AS Fullname, '' AS ThaiName, '' AS Position, 
             NULL AS EmailAddress, NULL AS TelePhone, '1' AS UserStatus, 
             NULL AS UserLogOn, NULL AS MobilePhone, 
             9 AS OrganizeLevel, 99 AS OrganizeOrder, NULL AS internal_no
             FROM TbFunction F
             JOIN TbCompany C ON F.CompanyID = C.CompanyID
             LEFT JOIN TbDepartment D ON F.FuncID = D.FuncID
             LEFT JOIN TbSection S ON D.DeptID = S.DeptID";

        $union = "$sql1 UNION ALL $sql2 UNION ALL $sql3";
        return "($union)";
    }

    // ==========================================
    // DATA RETRIEVAL & SEARCH METHODS
    // ==========================================

    /**
     * ดึงพนักงานทั้งหมดตามชื่อบริษัท
     *
     * @param string $company_name
     * @return array
     */
    public function get_employees_by_company_name($company_name)
    {
        $union_sql = $this->_get_union_sql();

        $sql = "SELECT * FROM $union_sql AS Combined 
                WHERE Company = ? 
                ORDER BY CASE WHEN FuncName = 'Top Management' THEN 0 ELSE 1 END ASC,
                         FuncName ASC, 
                         CASE WHEN ISNULL(TRY_CAST(OrganizeLevel AS INT), 99) <= 1 THEN 0 ELSE 1 END ASC,
                         DeptName ASC,
                         CASE WHEN OrganizeLevel = '2' THEN 0 ELSE 1 END ASC,
                         SecName ASC,
                         CASE WHEN OrganizeLevel = '3' THEN 0 ELSE 1 END ASC,
                         ISNULL(TRY_CAST(OrganizeLevel AS INT), 99) ASC, 
                         ISNULL(TRY_CAST(OrganizeOrder AS INT), 99) ASC, 
                         Fullname ASC";

        return $this->db->query($sql, array($company_name))->result();
    }

    /**
     * ค้นหาและกรองพนักงานตามบริษัท, สายงาน และคำค้นหา
     *
     * @param string $company_name
     * @param string|null $func_name
     * @param string|null $keyword
     * @return array
     */
    public function get_employees_filtered($company_name, $func_name = null, $keyword = null)
    {
        $union_sql = $this->_get_union_sql();

        $sql = "SELECT * FROM $union_sql AS Combined WHERE Company = ?";
        $params = array($company_name);

        if (!empty($func_name)) {
            $safe_func = "N" . $this->db->escape($func_name);
            $sql .= " AND FuncName = $safe_func";
        }

        if (!empty($keyword)) {
            $keyword_lower = strtolower($keyword);
            $safe_eng = "N" . $this->db->escape('%' . $keyword_lower . '%');
            $safe_thai = "N" . $this->db->escape('%' . $keyword . '%');

            $sql .= " AND (LOWER(Fullname) LIKE $safe_eng 
                      OR ThaiName LIKE $safe_thai 
                      OR LOWER(UserLogOn) LIKE $safe_eng 
                      OR LOWER(EmailAddress) LIKE $safe_eng 
                      OR LOWER(MobilePhone) LIKE $safe_eng 
                      OR LOWER(internal_no) LIKE $safe_eng 
                      OR LOWER(TelePhone) LIKE $safe_eng)";
        }

        $sql .= " ORDER BY CASE WHEN FuncName = 'Top Management' THEN 0 ELSE 1 END ASC,
                         FuncName ASC, 
                         CASE WHEN ISNULL(TRY_CAST(OrganizeLevel AS INT), 99) <= 1 THEN 0 ELSE 1 END ASC,
                         DeptName ASC,
                         CASE WHEN OrganizeLevel = '2' THEN 0 ELSE 1 END ASC,
                         SecName ASC,
                         CASE WHEN OrganizeLevel = '3' THEN 0 ELSE 1 END ASC,
                         ISNULL(TRY_CAST(OrganizeLevel AS INT), 99) ASC, 
                         ISNULL(TRY_CAST(OrganizeOrder AS INT), 99) ASC, 
                         Fullname ASC";

        return $this->db->query($sql, $params)->result();
    }

    /**
     * ค้นหาพนักงานสำหรับ Live Search Autocomplete (จำกัด 15 รายการ)
     *
     * @param string $company_name
     * @param string $keyword
     * @return array
     */
    public function search_employees_by_name($company_name, $keyword)
    {
        $union_sql = $this->_get_union_sql();

        $keyword_lower = strtolower($keyword);
        $safe_eng = "N" . $this->db->escape('%' . $keyword_lower . '%');
        $safe_thai = "N" . $this->db->escape('%' . $keyword . '%');

        $sql = "SELECT DISTINCT Fullname, ThaiName, UserLogOn, EmailAddress, MobilePhone, internal_no, TelePhone 
                FROM $union_sql AS Combined 
                WHERE Company = ? 
                AND (LOWER(Fullname) LIKE $safe_eng 
                  OR ThaiName LIKE $safe_thai 
                  OR LOWER(UserLogOn) LIKE $safe_eng 
                  OR LOWER(EmailAddress) LIKE $safe_eng 
                  OR LOWER(MobilePhone) LIKE $safe_eng 
                  OR LOWER(internal_no) LIKE $safe_eng 
                  OR LOWER(TelePhone) LIKE $safe_eng)
                ORDER BY Fullname OFFSET 0 ROWS FETCH NEXT 15 ROWS ONLY";

        return $this->db->query($sql, array($company_name))->result();
    }

    /**
     * ดึงข้อมูลพนักงานคนเดียวตาม Fullname
     *
     * @param string $company_name
     * @param string $fullname
     * @return array
     */
    public function get_employee_by_fullname($company_name, $fullname)
    {
        $union_sql = $this->_get_union_sql();
        $sql = "SELECT * FROM $union_sql AS Combined 
                WHERE Company = ? AND Fullname = ?";
        return $this->db->query($sql, array($company_name, $fullname))->result();
    }

    /**
     * ดึงข้อมูลพนักงานจาก Email (ค้นหาแบบ Like)
     *
     * @param string $username
     * @return object|null
     */
    public function get_employee_by_email_like($username)
    {
        $union_sql = $this->_get_union_sql();
        $safe_username = "N" . $this->db->escape('%' . $username . '%');
        $sql = "SELECT TOP 1 * FROM $union_sql AS Combined 
                WHERE EmailAddress LIKE $safe_username";
        return $this->db->query($sql)->row();
    }

    /**
     * ดึงพนักงานสำหรับหน้า Admin โดย JOIN TbMap โดยตรง เพื่อให้ได้ MapID
     *
     * @param string $company_name
     * @return array
     */
    public function get_employees_admin($company_name)
    {
        $safe = $this->db->escape($company_name);
        $sql = "SELECT
                    tc.UserID, tc.Fullname, tc.ThaiName, tc.TelePhone, tc.EmailAddress, tc.PositionID, tc.UserStatus, tc.UserLogOn, tc.Picture AS picture,
                    m.MapID, m.SecID,
                    s.SecName,
                    d.DeptName,
                    f.FuncName,
                    co.Company, co.CompanyID,
                    p.OrganizeLevel, p.OrganizeOrder, p.FullNameEN AS Position
                FROM TbContactUser tc
                INNER JOIN TbMap m ON tc.UserID = m.UserID
                LEFT JOIN TbSection s ON m.SecID = s.SecID
                LEFT JOIN TbDepartment d ON s.DeptID = d.DeptID
                LEFT JOIN TbFunction f ON d.FuncID = f.FuncID
                LEFT JOIN TbCompany co ON f.CompanyID = co.CompanyID
                LEFT JOIN TbPosition p ON tc.PositionID = p.PositionID
                WHERE co.Company = $safe
                ORDER BY f.FuncName ASC, d.DeptName ASC, s.SecName ASC, tc.Fullname ASC";
        return $this->db->query($sql)->result();
    }

    /**
     * ดึง UserLogOn และ Picture ของพนักงาน (ใช้สำหรับเช็คลบ/เปลี่ยนชื่อรูปภาพ)
     *
     * @param int $user_id
     * @return object|null
     */
    public function get_employee_picture_info($user_id)
    {
        $this->db->select('UserLogOn, Picture AS picture');
        $this->db->where('UserID', $user_id);
        return $this->db->get('TbContactUser')->row();
    }

    /**
     * ดึงรายชื่อพนักงาน Active ทั้งหมด สำหรับ Dropdown ในหน้า Map
     *
     * @return array
     */
    public function get_active_employees_dropdown()
    {
        $this->db->select('UserID, Fullname, ThaiName');
        $this->db->from('TbContactUser');
        $this->db->where("LTRIM(RTRIM(UserStatus)) = '1'", NULL, FALSE);
        $this->db->order_by('Fullname', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูลตำแหน่งทั้งหมดจาก TbPosition
     *
     * @return array
     */
    public function get_all_positions()
    {
        $this->db->select('PositionID, FullNameEN');
        $this->db->from('TbPosition');
        $this->db->order_by('PositionID', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงวันที่อัปเดตข้อมูลล่าสุด (จาก SuccessFactors)
     *
     * @return string
     */
    public function get_last_update_date()
    {
        $this->db->select_max('updated_at', 'last_update');
        $query = $this->db->get('VwShowDataSuccessFactor');
        $result = $query->row();

        if ($result && $result->last_update) {
            if (is_object($result->last_update) && method_exists($result->last_update, 'format')) {
                return $result->last_update->format('d/m/Y');
            } else {
                return date('d/m/Y', strtotime($result->last_update));
            }
        }
        return date('d/m/Y');
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * อัปเดตข้อมูลส่วนตัวของพนักงาน (TbContactUser และ TbInternal)
     *
     * @param int|null $user_id
     * @param string $email
     * @param array $profile_data
     * @param string $internal_no
     * @param string $mobile_phone
     * @return bool
     */
    public function update_employee_profile_and_internal($user_id, $email, array $profile_data, $internal_no, $mobile_phone)
    {
        $this->db->trans_start();

        // 1. อัปเดต TbContactUser ถ้ามี UserID
        if (!empty($user_id)) {
            $this->db->where('UserID', $user_id);
            $this->db->update('TbContactUser', $profile_data);
        }

        // 2. ตรวจสอบและอัปเดต/เพิ่มใน TbInternal
        $this->db->where('Email', $email);
        $q = $this->db->get('TbInternal');

        if ($q->num_rows() > 0) {
            $this->db->where('Email', $email);
            $this->db->update('TbInternal', [
                'internal_no' => $internal_no,
                'telephone'   => $mobile_phone
            ]);
        } else {
            $this->db->insert('TbInternal', [
                'Email'       => $email,
                'internal_no' => $internal_no,
                'telephone'   => $mobile_phone
            ]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * อัปเดตข้อมูลพนักงานใน TbContactUser + TbMap
     * รองรับ Unicode/ภาษาไทย ด้วย N'...' prefix ใน raw SQL
     *
     * @param int $contact_id
     * @param array $data
     * @return bool
     */
    public function update_employee($contact_id, array $data)
    {
        $map_id  = isset($data['MapID']) ? (int)$data['MapID'] : null;
        $sec_id  = isset($data['SecID']) ? $data['SecID']      : false;
        unset($data['MapID'], $data['SecID']);

        $fullname    = isset($data['Fullname'])     ? $data['Fullname']     : null;
        $thainame    = isset($data['ThaiName'])     ? $data['ThaiName']     : null;
        $position_id = isset($data['PositionID'])   ? $data['PositionID']   : null;
        $telephone   = isset($data['TelePhone'])    ? $data['TelePhone']    : null;
        $email       = isset($data['EmailAddress']) ? $data['EmailAddress'] : null;

        $esc = function ($s) {
            return str_replace("'", "''", (string)$s);
        };

        // 1. UPDATE TbContactUser
        $parts = [];
        if ($fullname    !== null) $parts[] = "Fullname     = N'" . $esc($fullname)    . "'";
        if ($thainame    !== null) $parts[] = "ThaiName     = N'" . $esc($thainame)    . "'";
        if ($position_id !== null) $parts[] = "PositionID   = "   . (int)$position_id;
        if ($telephone   !== null) $parts[] = "TelePhone    = N'" . $esc($telephone)   . "'";
        if ($email       !== null) $parts[] = "EmailAddress = N'" . $esc($email)       . "'";
        if (isset($data['UserLogOn']) && $data['UserLogOn'] !== null) {
            $parts[] = "UserLogOn = N'" . $esc($data['UserLogOn']) . "'";
        }
        if (array_key_exists('picture', $data)) {
            if ($data['picture'] === null) {
                $parts[] = "Picture = NULL";
            } else {
                $parts[] = "Picture = N'" . $esc($data['picture']) . "'";
            }
        }
        if (isset($data['UserStatus']) && $data['UserStatus'] !== null && $data['UserStatus'] !== '') {
            $parts[] = "UserStatus = N'" . (int)$data['UserStatus'] . "'";
        }

        $result = true;
        if (!empty($parts)) {
            $sql = "UPDATE TbContactUser SET "
                . implode(', ', $parts)
                . " WHERE UserID = " . (int)$contact_id;

            $db_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            $result = $this->db->query($sql);
            $this->db->db_debug = $db_debug;
        }

        // 2. UPDATE TbMap.SecID
        if ($sec_id !== false && !empty($map_id)) {
            $db_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            $this->db->where('MapID',  $map_id);
            $this->db->where('UserID', $contact_id);
            $this->db->update('TbMap', ['SecID' => $sec_id]);
            $this->db->db_debug = $db_debug;
        }

        return $result;
    }

    /**
     * เพิ่มพนักงานใหม่ลงใน TbContactUser และ TbMap
     *
     * @param array $data
     * @return bool
     */
    public function insert_employee(array $data)
    {
        $esc = function ($s) {
            return str_replace("'", "''", (string)$s);
        };

        $fullname     = !empty($data['Fullname'])     ? "N'" . $esc($data['Fullname'])     . "'" : "N''";
        $thainame     = !empty($data['ThaiName'])     ? "N'" . $esc($data['ThaiName'])     . "'" : "N''";
        $position_id  = !empty($data['PositionID'])   ? (int)$data['PositionID']                 : 0;
        $staff_id     = !empty($data['StaffID'])      ? "N'" . $esc($data['StaffID'])      . "'" : "N''";
        $office       = !empty($data['Office'])       ? "N'" . $esc($data['Office'])       . "'" : "N''";
        $mobile_phone = !empty($data['MobilePhone'])  ? "N'" . $esc($data['MobilePhone'])  . "'" : "N''";
        $telephone    = !empty($data['TelePhone'])    ? "N'" . $esc($data['TelePhone'])    . "'" : "N''";
        $email        = !empty($data['EmailAddress']) ? "N'" . $esc($data['EmailAddress']) . "'" : "N''";
        $user_log_on  = !empty($data['UserLogOn'])    ? "N'" . $esc($data['UserLogOn'])    . "'" : "N''";
        $user_status  = isset($data['UserStatus'])    ? "N'" . (int)$data['UserStatus']    . "'" : "N'1'";
        $sec_id       = !empty($data['SecID'])        ? (int)$data['SecID']                      : 0;

        $sql = "INSERT INTO TbContactUser (
            Fullname, ThaiName, PositionID, StaffID, Office, MobilePhone, TelePhone, EmailAddress, UserLogOn, UserStatus, LastModify, SecID
        ) VALUES (
            $fullname, $thainame, $position_id, $staff_id, $office, $mobile_phone, $telephone, $email, $user_log_on, $user_status, GETDATE(), $sec_id
        )";

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->query($sql);
        $this->db->db_debug = $db_debug;
        if (!$result) return false;

        $user_id = null;
        $q_scope = $this->db->query("SELECT SCOPE_IDENTITY() AS last_id");
        if ($q_scope && $q_scope->num_rows() > 0 && $q_scope->row()->last_id !== null) {
            $user_id = $q_scope->row()->last_id;
        } else {
            $q_ident = $this->db->query("SELECT IDENT_CURRENT('TbContactUser') AS last_id");
            if ($q_ident && $q_ident->num_rows() > 0) {
                $user_id = $q_ident->row()->last_id;
            }
        }

        if ($user_id && !empty($data['SecID'])) {
            $db_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            $this->db->insert('TbMap', [
                'UserID' => $user_id,
                'SecID'  => $data['SecID']
            ]);
            $this->db->db_debug = $db_debug;
        }

        return true;
    }

    /**
     * ลบพนักงาน
     *
     * @param int $user_id
     * @return bool
     */
    public function delete_employee($user_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('UserID', $user_id);
        $result = $this->db->delete('TbContactUser');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
