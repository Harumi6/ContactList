<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Function_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // โหลด Database เผื่อไว้ (ถ้าโหลดใน autoload.php แล้ว สามารถเอาบรรทัดนี้ออกได้ครับ)
        $this->load->database(); 
    }

    /**
     * 1. ดึงข้อมูล Function ทั้งหมด (พร้อมแถมชื่อบริษัทมาให้ด้วย)
     * เอาไว้ใช้แสดงในหน้าตารางจัดการข้อมูล (DataTables)
     */
    public function get_all_functions() {
        $this->db->select('TbFunction.*, TbCompany.CompanyName');
        $this->db->from('TbFunction');
        // JOIN ไปเอาชื่อบริษัทมาแปะ
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbCompany.CompanyID', 'ASC'); // เรียงตามบริษัทก่อน
        $this->db->order_by('TbFunction.FuncName', 'ASC'); // แล้วค่อยเรียงตามชื่อ Function
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * 2. ดึงข้อมูล Function เดียวตาม ID
     * เอาไว้ใช้เวลาจะคลิกเข้าไปดูรายละเอียด หรือคลิกแก้ไขข้อมูล
     */
    public function get_function_by_id($func_id) {
        $this->db->select('TbFunction.*, TbCompany.CompanyName');
        $this->db->from('TbFunction');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbFunction.FuncID', $func_id);
        
        $query = $this->db->get();
        return $query->row(); // คืนค่าเป็น object ก้อนเดียว เพราะหาแค่ ID เดียว
    }

    /**
     * 3. ดึงข้อมูล Function โดยกรองตาม CompanyID (⭐ ฟังก์ชันยอดฮิต)
     * เอาไว้ทำ "Dropdown แบบเชื่อมโยง (AJAX)" เช่น พอผู้ใช้เลือกบริษัท A ให้โชว์แค่ Function ของบริษัท A
     */
    public function get_functions_by_company($company_id) {
        $this->db->where('CompanyID', $company_id);
        $this->db->where('FuncStatus', 1); // ดึงเฉพาะ Function ที่สถานะยัง Active อยู่
        $this->db->order_by('FuncName', 'ASC');
        
        $query = $this->db->get('TbFunction');
        return $query->result();
    }

    /**
     * 3.1 ดึงข้อมูล Function ทั้งหมด (รวมที่ Inactive) เพื่อนำมาแสดงในตารางให้ Admin จัดการ
     */
    public function admin_get_functions_by_company($company_id) {
        $this->db->where('CompanyID', $company_id);
        $this->db->order_by('FuncName', 'ASC');
        
        $query = $this->db->get('TbFunction');
        return $query->result();
    }

    /**
     * 4. เพิ่มข้อมูล Function ใหม่
     */
    public function insert_function($data) {
        // $data คือ Array ข้อมูลที่ส่งมาจาก Controller เช่น array('FuncName' => 'ผลิต', 'CompanyID' => 1)
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbFunction', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * 5. อัปเดต/แก้ไขข้อมูล Function
     */
    public function update_function($func_id, $data) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('FuncID', $func_id);
        $result = $this->db->update('TbFunction', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * 6. ลบข้อมูล Function
     * (ถ้าไม่อยากลบข้อมูลทิ้งถาวร แนะนำให้เปลี่ยนเป็นการใช้ฟังก์ชัน update สถานะ FuncStatus เป็น 0 แทนครับ)
     */
    public function delete_function($func_id) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        
        // ค้นหา Department ที่อยู่ภายใต้ Function นี้เพื่อลบ Section ก่อน
        $this->db->select('DeptID');
        $this->db->where('FuncID', $func_id);
        $departments = $this->db->get('TbDepartment')->result();
        
        if (!empty($departments)) {
            $dept_ids = array();
            foreach ($departments as $d) {
                $dept_ids[] = $d->DeptID;
            }
            
            // ลบ Sections ทิ้งก่อน
            $this->db->where_in('DeptID', $dept_ids);
            $this->db->delete('TbSection');
            
            // ลบ Departments 
            $this->db->where('FuncID', $func_id);
            $this->db->delete('TbDepartment');
        }

        // ลบ Function
        $this->db->where('FuncID', $func_id);
        $result = $this->db->delete('TbFunction');
        
        $this->db->db_debug = $db_debug;
        return $result;
    }
    
}