<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AdminLogin_model
 * จัดการข้อมูลผู้ดูแลระบบและสิทธิ์การเข้าสู่ระบบ (TbAdministratorLogin)
 *
 * @property CI_DB_query_builder $db
 */
class AdminLogin_model extends CI_Model
{
    // ==========================================
    // AUTHENTICATION & QUERY METHODS
    // ==========================================

    /**
     * ดึงข้อมูลผู้ดูแลระบบทั้งหมด
     *
     * @return array
     */
    public function load_admin_login()
    {
        $this->db->select('*');
        $this->db->from('TbAdministratorLogin');
        return $this->db->get()->result();
    }

    /**
     * ตรวจสอบ Username และ Password ของ Admin
     *
     * @param array $input ['username' => '...', 'password' => '...']
     * @return array
     */
    public function check_admin_login(array $input)
    {
        $username = $input['username'];
        $password = md5($input['password']);

        $this->db->where('Login', $username);
        $this->db->where('Password', $password);
        $this->db->from('TbAdministratorLogin');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Admin ตาม Username (Login)
     *
     * @param string $username
     * @return array
     */
    public function get_admin_by_username($username)
    {
        $this->db->where('Login', $username);
        $this->db->from('TbAdministratorLogin');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูล Admin รายคนตาม LoginID
     *
     * @param int $login_id
     * @return object|null
     */
    public function get_admin_by_id($login_id)
    {
        $this->db->where('LoginID', $login_id);
        $this->db->from('TbAdministratorLogin');
        return $this->db->get()->row();
    }

    /**
     * ดึงรายชื่อ Admin ทั้งหมด พร้อมชื่อบริษัท (สำหรับ Master Admin)
     *
     * @return array
     */
    public function get_all_admins()
    {
        $this->db->select('TbAdministratorLogin.*, TbCompany.Company');
        $this->db->from('TbAdministratorLogin');
        $this->db->join('TbCompany', 'TbAdministratorLogin.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbAdministratorLogin.LoginID', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงรายชื่อ Admin เฉพาะบริษัทที่กำหนด (สำหรับ Company Admin)
     *
     * @param int $company_id
     * @return array
     */
    public function get_admins_by_company($company_id)
    {
        $this->db->select('TbAdministratorLogin.*, TbCompany.Company');
        $this->db->from('TbAdministratorLogin');
        $this->db->join('TbCompany', 'TbAdministratorLogin.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbAdministratorLogin.CompanyID', $company_id);
        $this->db->order_by('TbAdministratorLogin.LoginID', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มข้อมูล Admin ใหม่
     *
     * @param array $data
     * @return bool
     */
    public function insert_admin(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbAdministratorLogin', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * แก้ไขข้อมูล Admin
     *
     * @param int $login_id
     * @param array $data
     * @return bool
     */
    public function update_admin($login_id, array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('LoginID', $login_id);
        $result = $this->db->update('TbAdministratorLogin', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Admin
     *
     * @param int $login_id
     * @return bool
     */
    public function delete_admin($login_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('LoginID', $login_id);
        $result = $this->db->delete('TbAdministratorLogin');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
