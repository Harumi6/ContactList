<?php

/**
 * @property mixed $db
 */
class Company_model extends CI_Model
{
    public function get_all_companies()
    {
        $this->db->select('*');
        $this->db->from('TbCompany');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_company_by_id(int $id)
    {
        $this->db->select('*');
        $this->db->from('TbCompany');
        $this->db->where('CompanyID', $id);
        $query = $this->db->get();
        return $query->row();
    }
}
