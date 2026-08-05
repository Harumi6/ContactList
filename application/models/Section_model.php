<?php

/**
 * @property mixed $db
 */
class Section_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ดึงข้อมูล Section ทั้งหมดในบริษัทสำหรับ Dropdown หน้า Admin
     */
    public function get_sections_by_company_id($company_id)
    {
        $this->db->select('TbSection.SecID, TbSection.SecName');
        $this->db->from('TbSection');
        $this->db->join('TbDepartment', 'TbSection.DeptID = TbDepartment.DeptID', 'inner');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'inner');
        $this->db->where('TbFunction.CompanyID', $company_id);
        $this->db->order_by('TbSection.SecName', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึงข้อมูล Section ทั้งหมด (รวมที่ Inactive) เพื่อนำมาแสดงในตารางให้ Admin จัดการ
     * Join กับ TbDepartment, TbFunction, TbCompany
     */
    public function admin_get_sections_by_company($company_id) {
        $this->db->select('TbSection.*, TbDepartment.DeptName, TbDepartment.DeptID, TbFunction.FuncName, TbFunction.FuncID, TbCompany.Company, TbCompany.CompanyID');
        $this->db->from('TbSection');
        $this->db->join('TbDepartment', 'TbSection.DeptID = TbDepartment.DeptID', 'left');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'left');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbCompany.CompanyID', $company_id);
        $this->db->or_where('TbCompany.CompanyID IS NULL', NULL, FALSE);
        $this->db->order_by('TbFunction.FuncName', 'ASC');
        $this->db->order_by('TbDepartment.DeptName', 'ASC');
        $this->db->order_by('TbSection.SecName', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * เพิ่มข้อมูล Section ใหม่
     */
    public function insert_section($data) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbSection', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดต/แก้ไขข้อมูล Section
     */
    public function update_section($sec_id, $data) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('SecID', $sec_id);
        $result = $this->db->update('TbSection', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Section
     */
    public function delete_section($sec_id) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('SecID', $sec_id);
        $result = $this->db->delete('TbSection');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
