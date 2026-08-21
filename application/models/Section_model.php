<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Section_model
 * จัดการข้อมูลส่วนงาน (TbSection)
 *
 * @property CI_DB_query_builder $db
 */
class Section_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึงข้อมูล Section ทั้งหมดในบริษัทสำหรับ Dropdown ในหน้า Admin
     *
     * @param int $company_id
     * @return array
     */
    public function get_sections_by_company_id($company_id)
    {
        $this->db->select('TbSection.SecID, TbSection.SecName');
        $this->db->from('TbSection');
        $this->db->join('TbDepartment', 'TbSection.DeptID = TbDepartment.DeptID', 'inner');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'inner');
        $this->db->where('TbFunction.CompanyID', $company_id);
        $this->db->order_by('TbSection.SecName', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Section ทั้งหมด (รวมที่ Inactive) เพื่อนำมาแสดงในตารางให้ Admin จัดการ
     *
     * @param int $company_id
     * @return array
     */
    public function admin_get_sections_by_company($company_id)
    {
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
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Section ตาม DeptID สำหรับ Dropdown เชื่อมโยง
     *
     * @param int $dept_id
     * @return array
     */
    public function get_sections_by_department($dept_id)
    {
        $this->db->select('SecID, SecName');
        $this->db->from('TbSection');
        $this->db->where('DeptID', $dept_id);
        $this->db->where('SecStatus', 1);
        $this->db->order_by('SecName', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มข้อมูล Section ใหม่
     *
     * @param array $data
     * @return bool
     */
    public function insert_section(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbSection', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดต/แก้ไขข้อมูล Section
     *
     * @param int $sec_id
     * @param array $data
     * @return bool
     */
    public function update_section($sec_id, array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('SecID', $sec_id);
        $result = $this->db->update('TbSection', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Section
     *
     * @param int $sec_id
     * @return bool
     */
    public function delete_section($sec_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('SecID', $sec_id);
        $result = $this->db->delete('TbSection');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
