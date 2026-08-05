<?php

/**
 * @property mixed $db
 */
class Map_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ดึงข้อมูลการ Map ของพนักงานกับ Section ทั้งหมดในบริษัทที่เลือก
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
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * เพิ่มการ Map
     */
    public function insert_map($data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->trans_start();

        // Insert TbMap
        $this->db->insert('TbMap', $data);

        // Update TbContactUser.SecID
        $this->db->where('UserID', $data['UserID']);
        $this->db->update('TbContactUser', ['SecID' => $data['SecID']]);

        $this->db->trans_complete();

        $status = $this->db->trans_status();
        $this->db->db_debug = $db_debug;
        return $status;
    }

    /**
     * แก้ไขข้อมูลการ Map
     */
    public function update_map($map_id, $data)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->trans_start();

        // Update TbMap
        $this->db->where('MapID', $map_id);
        $this->db->update('TbMap', $data);

        // Update TbContactUser.SecID
        if (isset($data['UserID']) && isset($data['SecID'])) {
            $this->db->where('UserID', $data['UserID']);
            $this->db->update('TbContactUser', ['SecID' => $data['SecID']]);
        } else {
            // Find UserID from MapID to update
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
     * ลบข้อมูล Map
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
     * ลบข้อมูล Map โดย UserID
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
