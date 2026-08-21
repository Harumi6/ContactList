<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Department_model
 * จัดการข้อมูลแผนก (TbDepartment) และโครงสร้างองค์กร
 *
 * @property CI_DB_query_builder $db
 */
class Department_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึง Department ทั้งหมด พร้อม JOIN ผ่าน TbFunction → TbCompany
     *
     * @return array
     */
    public function get_department_by_company()
    {
        $this->db->select('TbDepartment.*, TbFunction.FuncName, TbCompany.Company');
        $this->db->from('TbDepartment');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'left');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbDepartment.DeptName', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึง FuncName (สายงาน) ตามชื่อบริษัท
     * ใช้สำหรับ Dropdown "Function" บนหน้าเว็บหลัก
     *
     * @param string $company_name
     * @return array
     */
    public function get_department_by_company_name($company_name)
    {
        $sql = "SELECT FuncName FROM (
                    SELECT F.FuncName 
                    FROM TbFunction F
                    JOIN TbCompany C ON F.CompanyID = C.CompanyID
                    WHERE C.Company = ?
                    
                    UNION ALL
                    
                    SELECT 
                         CASE WHEN (VSF.Division_EN IS NULL OR LTRIM(RTRIM(VSF.Division_EN)) = '') 
                               AND (VSF.Department_EN IS NULL OR LTRIM(RTRIM(VSF.Department_EN)) = '') 
                               AND (VSF.Section_EN IS NULL OR LTRIM(RTRIM(VSF.Section_EN)) = '') 
                              THEN 'Top Management' ELSE CAST(VSF.Division_EN AS NVARCHAR(255)) END AS FuncName 
                    FROM VwShowDataSuccessFactor VSF
                    LEFT JOIN TbCompany C ON C.ComCode = VSF.ComCode
                    WHERE CAST(ISNULL(C.Company, VSF.ComCode) AS NCHAR(50)) = ?
                ) AS Combined
                WHERE FuncName IS NOT NULL AND LTRIM(RTRIM(FuncName)) <> ''
                GROUP BY FuncName
                ORDER BY CASE WHEN FuncName = 'Top Management' THEN 0 ELSE 1 END ASC, FuncName ASC";

        return $this->db->query($sql, array($company_name, $company_name))->result();
    }

    /**
     * ดึงโครงสร้างองค์กร Function → Department → Section ตามชื่อบริษัท
     * ใช้แสดงโครงสร้างกรณีไม่มีข้อมูลพนักงาน
     *
     * @param string $company_name
     * @return array
     */
    public function get_org_structure_by_company_name($company_name)
    {
        $sql1 = "SELECT Company, 
                 CASE WHEN (FuncName IS NULL OR LTRIM(RTRIM(FuncName)) = '') 
                       AND (DeptName IS NULL OR LTRIM(RTRIM(DeptName)) = '') 
                       AND (SecName IS NULL OR LTRIM(RTRIM(SecName)) = '') 
                      THEN 'Top Management' ELSE FuncName END AS FuncName, 
                 DeptName, SecName 
                 FROM VwShowData 
                 WHERE Company = ?";

        $sql2 = "SELECT 
                 CAST(ISNULL(C.Company, VSF.ComCode) AS NCHAR(50)) AS Company, 
                 CASE WHEN (Division_EN IS NULL OR LTRIM(RTRIM(Division_EN)) = '') 
                       AND (Department_EN IS NULL OR LTRIM(RTRIM(Department_EN)) = '') 
                       AND (Section_EN IS NULL OR LTRIM(RTRIM(Section_EN)) = '') 
                      THEN 'Top Management' ELSE CAST(Division_EN AS NVARCHAR(255)) END AS FuncName, 
                 CAST(Department_EN AS NVARCHAR(255)) AS DeptName, CAST(Section_EN AS NVARCHAR(255)) AS SecName 
                 FROM VwShowDataSuccessFactor VSF
                 LEFT JOIN TbCompany C ON C.ComCode = VSF.ComCode
                 WHERE CAST(ISNULL(C.Company, VSF.ComCode) AS NCHAR(50)) = ?";

        $sql3 = "SELECT C.Company, F.FuncName, D.DeptName, S.SecName 
                 FROM TbFunction F
                 JOIN TbCompany C ON F.CompanyID = C.CompanyID
                 LEFT JOIN TbDepartment D ON F.FuncID = D.FuncID
                 LEFT JOIN TbSection S ON D.DeptID = S.DeptID
                 WHERE C.Company = ?";

        $sql = "SELECT Company, FuncName, DeptName, SecName 
                FROM ($sql1 UNION $sql2 UNION $sql3) AS Combined 
                WHERE Company = ?
                ORDER BY CASE WHEN FuncName = 'Top Management' THEN 0 ELSE 1 END ASC, FuncName ASC, DeptName ASC, SecName ASC";

        return $this->db->query($sql, array($company_name, $company_name, $company_name, $company_name))->result();
    }

    /**
     * ดึงข้อมูล Department สำหรับหน้า Admin ตามบริษัท
     *
     * @param int $company_id
     * @return array
     */
    public function admin_get_departments_by_company($company_id)
    {
        $this->db->select('TbDepartment.*, TbFunction.FuncName, TbCompany.Company, TbCompany.CompanyID');
        $this->db->from('TbDepartment');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'left');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbCompany.CompanyID', $company_id);
        $this->db->or_where('TbCompany.CompanyID IS NULL', NULL, FALSE);
        $this->db->order_by('TbFunction.FuncName', 'ASC');
        $this->db->order_by('TbDepartment.DeptName', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DROPDOWN & HELPER METHODS
    // ==========================================

    /**
     * ดึงข้อมูล Department ตาม FuncID สำหรับทำเชื่อมโยง Dropdown
     *
     * @param int $func_id
     * @return array
     */
    public function get_departments_by_function($func_id)
    {
        $this->db->select('DeptID, DeptName');
        $this->db->from('TbDepartment');
        $this->db->where('FuncID', $func_id);
        $this->db->where('DeptStatus', 1);
        $this->db->order_by('DeptName', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มข้อมูล Department ใหม่
     *
     * @param array $data
     * @return bool
     */
    public function insert_department(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbDepartment', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดต/แก้ไขข้อมูล Department
     *
     * @param int $dept_id
     * @param array $data
     * @return bool
     */
    public function update_department($dept_id, array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('DeptID', $dept_id);
        $result = $this->db->update('TbDepartment', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Department พร้อมทั้งลบ Sections ที่สังกัด
     *
     * @param int $dept_id
     * @return bool
     */
    public function delete_department($dept_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        // 1. ลบ Sections ภายใต้ Department นี้ก่อน
        $this->db->where('DeptID', $dept_id);
        $this->db->delete('TbSection');

        // 2. ลบ Department
        $this->db->where('DeptID', $dept_id);
        $result = $this->db->delete('TbDepartment');

        $this->db->db_debug = $db_debug;
        return $result;
    }
}
