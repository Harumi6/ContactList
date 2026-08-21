<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Function_model
 * จัดการข้อมูลสายงาน (TbFunction)
 *
 * @property CI_DB_query_builder $db
 */
class Function_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึงข้อมูล Function ทั้งหมด พร้อมชื่อบริษัท
     *
     * @return array
     */
    public function get_all_functions()
    {
        $this->db->select('TbFunction.*, TbCompany.Company');
        $this->db->from('TbFunction');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbCompany.CompanyID', 'ASC');
        $this->db->order_by('TbFunction.FuncName', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Function รายการเดียวตาม FuncID
     *
     * @param int $func_id
     * @return object|null
     */
    public function get_function_by_id($func_id)
    {
        $this->db->select('TbFunction.*, TbCompany.Company');
        $this->db->from('TbFunction');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbFunction.FuncID', $func_id);
        return $this->db->get()->row();
    }

    /**
     * ดึงข้อมูล Function ที่ Active ตาม CompanyID (สำหรับ Dropdown เชื่อมโยง)
     *
     * @param int $company_id
     * @return array
     */
    public function get_functions_by_company($company_id)
    {
        $this->db->where('CompanyID', $company_id);
        $this->db->where('FuncStatus', 1);
        $this->db->order_by('FuncName', 'ASC');
        return $this->db->get('TbFunction')->result();
    }

    /**
     * ดึงข้อมูล Function ทั้งหมด (รวม Inactive) สำหรับหน้า Admin ตาม CompanyID
     *
     * @param int $company_id
     * @return array
     */
    public function admin_get_functions_by_company($company_id)
    {
        $this->db->where('CompanyID', $company_id);
        $this->db->order_by('FuncName', 'ASC');
        return $this->db->get('TbFunction')->result();
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มข้อมูล Function ใหม่
     *
     * @param array $data
     * @return bool
     */
    public function insert_function(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbFunction', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดต/แก้ไขข้อมูล Function
     *
     * @param int $func_id
     * @param array $data
     * @return bool
     */
    public function update_function($func_id, array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('FuncID', $func_id);
        $result = $this->db->update('TbFunction', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Function พร้อมทั้งลบ Department และ Section ที่สังกัดทั้งหมด
     *
     * @param int $func_id
     * @return bool
     */
    public function delete_function($func_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        // 1. ค้นหา Departments ภายใต้ Function นี้ เพื่อลบ Section ก่อน
        $this->db->select('DeptID');
        $this->db->where('FuncID', $func_id);
        $departments = $this->db->get('TbDepartment')->result();

        if (!empty($departments)) {
            $dept_ids = array();
            foreach ($departments as $d) {
                $dept_ids[] = $d->DeptID;
            }

            // ลบ Sections
            $this->db->where_in('DeptID', $dept_ids);
            $this->db->delete('TbSection');

            // ลบ Departments
            $this->db->where('FuncID', $func_id);
            $this->db->delete('TbDepartment');
        }

        // 2. ลบ Function
        $this->db->where('FuncID', $func_id);
        $result = $this->db->delete('TbFunction');

        $this->db->db_debug = $db_debug;
        return $result;
    }
}