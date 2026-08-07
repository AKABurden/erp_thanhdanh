<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Measurement_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isUsed($id = '')
    {
        // $this->db->where('type_print', $id);
        // $tbl_products = $this->db->get('tbl_products')->row();

        // if (!empty($tbl_products)) {
        //     return true;
        // } else {
        //     return false;
        // }
        return false;
    }

    public function insert($data)
    {
        $type = array(1, 2, 3);
        if (empty($data['type']) || 
            !isset($data['value']) || 
            !is_numeric($data['value']) || 
            !in_array($data['type'], $type)) {
            return 0;
        }
        if ($data['value'] < 0) {
            $data['value'] = $data['value'] * -1;
        }

        $this->db->insert('tblmeasurement', $data);
        $rs = $this->db->insert_id();
        return $rs;
    }
}