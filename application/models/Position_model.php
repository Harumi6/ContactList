<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Position_model
 * จัดการข้อมูลตำแหน่ง (TbPosition) และระดับ OrganizeLevel / OrganizeOrder
 *
 * @property CI_DB_query_builder $db
 */
class Position_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึงข้อมูล Position ทั้งหมด
     *
     * @return array
     */
    public function get_all_positions()
    {
        $this->db->select('*');
        $this->db->from('TbPosition');
        $this->db->order_by('OrganizeLevel', 'ASC');
        $this->db->order_by('OrganizeOrder', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึง OrganizeLevel แบบไม่ซ้ำเพื่อทำ Dropdown
     *
     * @return array
     */
    public function get_organize_levels()
    {
        $this->db->select('OrganizeLevel, LevelNameEN, LevelNameTH');
        $this->db->from('TbPosition');
        $this->db->where('OrganizeLevel IS NOT NULL');
        $this->db->group_by(array('OrganizeLevel', 'LevelNameEN', 'LevelNameTH'));
        $this->db->order_by('OrganizeLevel', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ดึง OrganizeOrder ตาม OrganizeLevel
     *
     * @param string|int $level
     * @return array
     */
    public function get_orders_by_level($level)
    {
        $this->db->select('OrganizeOrder');
        $this->db->from('TbPosition');
        $this->db->where('OrganizeLevel', $level);
        $this->db->where('OrganizeOrder IS NOT NULL');
        $this->db->group_by('OrganizeOrder');
        $this->db->order_by('OrganizeOrder', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * ตรวจสอบว่า OrganizeOrder ซ้ำกับที่มีอยู่หรือไม่
     *
     * @param string|int $level
     * @param string|int $order
     * @param int|null $exclude_id
     * @return bool
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
        return $this->db->get('TbPosition')->num_rows() > 0;
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มข้อมูล Position ใหม่
     *
     * @param array $data
     * @return bool
     */
    public function insert_position(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $result = $this->db->insert('TbPosition', $data);
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * อัปเดตข้อมูล Position
     *
     * @param int $position_id
     * @param array $data
     * @return bool
     */
    public function update_position($position_id, array $data)
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
     *
     * @param int $position_id
     * @return bool
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
