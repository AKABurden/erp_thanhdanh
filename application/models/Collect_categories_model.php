<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Collect_categories_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_id($id_parent = 0, &$array_costs = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('costs_parent' => $id_parent));
            $current_level = $this->db->get('tblcollect_categories')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_costs, $current_level[$key]);
                    $this->get_by_id($value['id'], $array_costs, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    public function isUsed($id = '')
    {
        $this->db->where('id_costs', $id);
        $tblother_payslips_coupon = $this->db->get('tblother_payslips_coupon')->row();

        $this->db->where('id_costs', $id);
        $vouchers_coupon = $this->db->get('tblvouchers_coupon')->row();
        if (!empty($tblother_payslips_coupon) || !empty($vouchers_coupon)) {
            return true;
        } else {
            return false;
        }
    }

    public function getColcatName($id = '')
    {
        if (empty($id)) {
            return null;
        }
        $colcat = get_table_where('tblcollect_categories', array('id' => $id), '', 'row', '', 'name');
        if (!empty($colcat)) {
            return $colcat->name;
        } else {
            return null;
        }
    }
}
