<?php

/**
 * @property mixed $db
 */
class AdminLogin_model extends CI_Model
{
    public function load_admin_login()
    {
        $this->db->select('*');
        $this->db->from('TbAdministratorLogin');
        $query = $this->db->get();
        return $query->result();
    }

    public function check_admin_login(array $input)
    {
        $username = $input['username'];
        $password = md5($input['password']);

        $this->db->where('Login', $username);
        $this->db->where('Password', $password);
        $this->db->from('TbAdministratorLogin');
        $query = $this->db->get();
        return $query->result();

    }

    public function get_admin_by_username($username)
    {
        $this->db->where('Login', $username);
        $this->db->from('TbAdministratorLogin');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_all_admins()
    {
        $this->db->select('TbAdministratorLogin.*, TbCompany.Company');
        $this->db->from('TbAdministratorLogin');
        $this->db->join('TbCompany', 'TbAdministratorLogin.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->order_by('TbAdministratorLogin.LoginID', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_admins_by_company($company_id)
    {
        $this->db->select('TbAdministratorLogin.*, TbCompany.Company');
        $this->db->from('TbAdministratorLogin');
        $this->db->join('TbCompany', 'TbAdministratorLogin.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbAdministratorLogin.CompanyID', $company_id);
        $this->db->order_by('TbAdministratorLogin.LoginID', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_admin_by_id($login_id)
    {
        $this->db->where('LoginID', $login_id);
        $this->db->from('TbAdministratorLogin');
        $query = $this->db->get();
        return $query->row();
    }

    public function insert_admin($data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbAdministratorLogin', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    public function update_admin($login_id, $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('LoginID', $login_id);
        $result = $this->db->update('TbAdministratorLogin', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

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
