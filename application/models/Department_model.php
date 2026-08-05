<?php

/**
 * @property mixed $db
 */
class Department_model extends CI_Model
{
    /**
     * ดึง Department ทั้งหมด พร้อม JOIN ผ่าน TbFunction → TbCompany
     * เรียงตาม DeptName, SecName
     */
    public function get_department_by_company()
    {
        $this->db->select('TbDepartment.*, TbFunction.FuncName, TbCompany.Company');
        $this->db->from('TbDepartment');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'left');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbDepartment.DeptName', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึง FuncName (สายงาน) ตามชื่อบริษัท
     * ใช้สำหรับ dropdown "Department" ซึ่งจริงๆ แสดง Function names
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
                
        $query = $this->db->query($sql, array($company_name, $company_name));
        return $query->result();
    }

    /**
     * ดึงโครงสร้างองค์กร Function → Department → Section ตามชื่อบริษัท
     * ใช้แสดงโครงสร้างแม้ไม่มีพนักงาน
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
                
        $query = $this->db->query($sql, array($company_name, $company_name, $company_name, $company_name));
        return $query->result();
    }


    /**
     * ดึงข้อมูล Department ทั้งหมด (รวมที่ Inactive) เพื่อนำมาแสดงในตารางให้ Admin จัดการ
     * Join กับ TbFunction และ TbCompany เพื่อดึงชื่อ
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

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * เพิ่มข้อมูล Department ใหม่
     */
    public function insert_department($data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbDepartment', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดต/แก้ไขข้อมูล Department
     */
    public function update_department($dept_id, $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('DeptID', $dept_id);
        $result = $this->db->update('TbDepartment', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Department
     */
    public function delete_department($dept_id) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        // ลบ Sections ทิ้งก่อน
        $this->db->where('DeptID', $dept_id);
        $this->db->delete('TbSection');

        $this->db->where('DeptID', $dept_id);
        $result = $this->db->delete('TbDepartment');
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ดึงข้อมูล Department ตาม Function สำหรับ Dropdown
     */
    public function get_departments_by_function($func_id) {
        $this->db->where('FuncID', $func_id);
        $this->db->where('DeptStatus', 1);
        $this->db->order_by('DeptName', 'ASC');
        
        $query = $this->db->get('TbDepartment');
        return $query->result();
    }
}
