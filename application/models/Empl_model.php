<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Empl_model extends App_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */

    public function add_empl($data)
    {
        $this->db->insert('tbluv', $data);
        return $this->db->insert_id();
    }
    public function update_employee($data, $id)
    {
        return $this->db->update('tbluv', $data, array('id' => $id));
    }
    public function delete_empl($id)
    {
        return $this->db->delete('tbluv', array('id' => $id));
    }
    public function get_row_empl($id)
    {
        $this->db->where('id', $id);
        $data = $this->db->get('tbluv')->row_array();
        return $data;
    }
}