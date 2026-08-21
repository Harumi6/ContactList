<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Company_model
 * จัดการข้อมูลบริษัท (TbCompany)
 *
 * @property CI_DB_query_builder $db
 */
class Company_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึงข้อมูลบริษัททั้งหมด
     *
     * @return array
     */
    public function get_all_companies()
    {
        $this->db->select('*');
        $this->db->from('TbCompany');
        $this->db->order_by('CompanyID', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึงข้อมูลบริษัทตาม CompanyID
     *
     * @param int|string $id
     * @return object|null
     */
    public function get_company_by_id($id)
    {
        if (empty($id)) {
            return null;
        }
        $this->db->select('*');
        $this->db->from('TbCompany');
        $this->db->where('CompanyID', (int)$id);
        return $this->db->get()->row();
    }
}
