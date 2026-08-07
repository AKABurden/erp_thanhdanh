<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Compose_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function delete($id = '')
    {
        $this->db->where('id', $id);
        if ($this->db->delete('tblcompose_detail')) {
            return true;
        }
        return false;
    }
    public function add($data = '')
    {
        $items = $data['items'];
        foreach ($items as $key => $item) {
            $itemss = $item;
            $itemss['id_compose'] = 0;
            $itemss['staff_create'] = get_staff_user_id();
            $itemss['date_create'] = date('Y-m-d H:i:s');
            $this->db->insert('tblcompose_detail', $itemss);
            $count = 1;
        }
        if ($count > 0) {
            return true;
        }
        return false;
    }
    public function update($data = '', $id = '')
    {
        $compose = array(
            'name' => $data['name'],
            'note' => $data['reason'],
            'date' => to_sql_date($data['date'], true),
        );
        $this->db->where('id', $id);
        if ($this->db->update('tblcompose', $compose)) {
            $this->db->where('id_compose', $id);
            $this->db->delete('tblcompose_detail');
            $items = $data['items'];
            foreach ($items as $key => $item) {
                $itemss = $item;
                $itemss['id_compose'] = $id;
                $this->db->insert('tblcompose_detail', $itemss);
            }
            $count = 1;
        }
        if ($count > 0) {
            return $id;
        }
        return false;
    }
}
