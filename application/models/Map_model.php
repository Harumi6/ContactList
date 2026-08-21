<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Map_model
 * จัดการข้อมูลการผูกพนักงานเข้ากับส่วนงาน (TbMap)
 *
 * @property CI_DB_query_builder $db
 */
class Map_model extends CI_Model
{
    // ==========================================
    // DATA RETRIEVAL METHODS
    // ==========================================

    /**
     * ดึงข้อมูลการ Map ของพนักงานกับ Section ทั้งหมดในบริษัทที่เลือก
     *
     * @param int $company_id
     * @return array
     */
    public function admin_get_maps_by_company($company_id)
    {
        $this->db->select('TbMap.MapID, TbMap.UserID, TbMap.SecID, TbContactUser.Fullname, TbContactUser.UserStatus, TbSection.SecName, TbSection.SecCode, TbDepartment.DeptName, TbDepartment.DeptID, TbFunction.FuncName, TbFunction.FuncID, TbCompany.Company, TbCompany.CompanyID');
        $this->db->from('TbMap');
        $this->db->join('TbContactUser', 'TbMap.UserID = TbContactUser.UserID', 'inner');
        $this->db->join('TbSection', 'TbMap.SecID = TbSection.SecID', 'left');
        $this->db->join('TbDepartment', 'TbSection.DeptID = TbDepartment.DeptID', 'left');
        $this->db->join('TbFunction', 'TbDepartment.FuncID = TbFunction.FuncID', 'left');
        $this->db->join('TbCompany', 'TbFunction.CompanyID = TbCompany.CompanyID', 'left');
        $this->db->where('TbCompany.CompanyID', $company_id);
        $this->db->order_by('TbFunction.FuncName', 'ASC');
        $this->db->order_by('TbDepartment.DeptName', 'ASC');
        $this->db->order_by('TbSection.SecName', 'ASC');
        $this->db->order_by('TbContactUser.Fullname', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================================
    // DATA MUTATION METHODS (INSERT / UPDATE / DELETE)
    // ==========================================

    /**
     * เพิ่มการ Map พนักงานกับ Section
     *
     * @param array $data ['UserID' => ..., 'SecID' => ...]
     * @return bool
     */
    public function insert_map(array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->trans_start();

        // 1. Insert TbMap
        $this->db->insert('TbMap', $data);

        // 2. Update TbContactUser.SecID ให้ตรงกัน
        $this->db->where('UserID', $data['UserID']);
        $this->db->update('TbContactUser', ['SecID' => $data['SecID']]);

        $this->db->trans_complete();

        $status = $this->db->trans_status();
        $this->db->db_debug = $db_debug;
        return $status;
    }

    /**
     * แก้ไขข้อมูลการ Map
     *
     * @param int $map_id
     * @param array $data
     * @return bool
     */
    public function update_map($map_id, array $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->trans_start();

        // 1. Update TbMap
        $this->db->where('MapID', $map_id);
        $this->db->update('TbMap', $data);

        // 2. Update TbContactUser.SecID
        if (isset($data['UserID']) && isset($data['SecID'])) {
            $this->db->where('UserID', $data['UserID']);
            $this->db->update('TbContactUser', ['SecID' => $data['SecID']]);
        } else {
            // Find UserID from MapID
            $this->db->select('UserID');
            $this->db->where('MapID', $map_id);
            $query = $this->db->get('TbMap');
            if ($query->num_rows() > 0) {
                $user_id = $query->row()->UserID;
                if (isset($data['SecID'])) {
                    $this->db->where('UserID', $user_id);
                    $this->db->update('TbContactUser', ['SecID' => $data['SecID']]);
                }
            }
        }

        $this->db->trans_complete();

        $status = $this->db->trans_status();
        $this->db->db_debug = $db_debug;
        return $status;
    }

    /**
     * ลบข้อมูล Map ตาม MapID
     *
     * @param int $map_id
     * @return bool
     */
    public function delete_map($map_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('MapID', $map_id);
        $result = $this->db->delete('TbMap');
        $this->db->db_debug = $db_debug;
        return $result;
    }

    /**
     * ลบข้อมูล Map ทั้งหมดของ UserID
     *
     * @param int $user_id
     * @return bool
     */
    public function delete_map_by_user($user_id)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('UserID', $user_id);
        $result = $this->db->delete('TbMap');
        $this->db->db_debug = $db_debug;
        return $result;
    }
}
