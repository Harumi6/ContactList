<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Position_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ดึงข้อมูล Position ทั้งหมด
     */
    public function get_all_positions()
    {
        $this->db->select('*');
        $this->db->from('TbPosition');
        $this->db->order_by('OrganizeLevel', 'ASC');
        $this->db->order_by('OrganizeOrder', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึง OrganizeLevel แบบไม่ซ้ำเพื่อทำ Dropdown
     */
    public function get_organize_levels()
    {
        $this->db->select('OrganizeLevel, LevelNameEN, LevelNameTH');
        $this->db->from('TbPosition');
        $this->db->where('OrganizeLevel IS NOT NULL');
        $this->db->group_by(array('OrganizeLevel', 'LevelNameEN', 'LevelNameTH'));
        $this->db->order_by('OrganizeLevel', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ดึง OrganizeOrder ตาม OrganizeLevel
     */
    public function get_orders_by_level($level)
    {
        $this->db->select('OrganizeOrder');
        $this->db->from('TbPosition');
        $this->db->where('OrganizeLevel', $level);
        $this->db->where('OrganizeOrder IS NOT NULL');
        $this->db->group_by('OrganizeOrder');
        $this->db->order_by('OrganizeOrder', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * ตรวจสอบว่า OrganizeOrder ซ้ำกับที่มีอยู่หรือไม่
     */
    public function check_duplicate_order($level, $order, $exclude_id = null)
    {
        if ($level === null || $order === null || $order === '') {
            return false;
        }
        $this->db->where('OrganizeLevel', $level);
        $this->db->where('OrganizeOrder', $order);
        if ($exclude_id !== null) {
            $this->db->where('PositionID !=', $exclude_id);
        }
        $query = $this->db->get('TbPosition');
        return $query->num_rows() > 0;
    }

    /**
     * เพิ่มข้อมูล Position
     */
    public function insert_position($data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbPosition', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดตข้อมูล Position
     */
    public function update_position($position_id, $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('PositionID', $position_id);
        $result = $this->db->update('TbPosition', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Position
     */
    public function delete_position($position_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('PositionID', $position_id);
        $result = $this->db->delete('TbPosition');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
