<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Costs_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_full_costs($id = '')
    {
        $costs = array();
        if ($id == '') {
            $this->db->where('costs_parent', '0');
            $costs = $this->db->get('tblcosts')->result_array();
        } else {
            $this->db->where('costs_parent', $id);
            $costs = $this->db->get('tblcosts')->result_array();
        }
        return $costs;
    }
    public function get_by_ids($id_parent = 0, &$array_costs = [], $level = 0, $text = '')
    {
        if (is_numeric($level)) {
            $this->db->where(array('costs_parent' => $id_parent));
            $current_level = $this->db->get('tblcosts')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $texts = '';
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    if ($text == '') {
                        $texts = $value['id'];
                    } else {
                        $texts = $text . ',' . $value['id'];
                    }
                    $current_level[$key]['idd'] = $texts;
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_costs, $current_level[$key]);
                    $this->get_by_ids($value['id'], $array_costs, $level + 1, $texts);
                }
            } else {
                return;
            }
        }
    }
    public function get_by_id_new($id = 0, &$array_costs = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('id' => $id));
            $current_level = $this->db->get('tblcosts')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_costs, $current_level[$key]);
                    $dtCostDefault = get_table_where('tblcosts',['code' => 'CPR-LUONG-06'],'','row_array');
                    $this->get_by_id_one($dtCostDefault['id'], $array_costs, $level + 1);
                }
            } else {
                return;
            }
        }
    }
    public function get_by_id_one($id = 0, &$array_costs = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('id' => $id));
            $current_level = $this->db->get('tblcosts')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_costs, $current_level[$key]);
                }
            }
        }
    }
    public function get_by_id($id_parent = 0, &$array_costs = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('costs_parent' => $id_parent));
            $this->db->where(array('active' => 1));
            $current_level = $this->db->get('tblcosts')->result_array();
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
    public function get_costs_parent($id_parent = 0, &$array_costs = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('costs_parent' => $id_parent, 'tblcosts.id >' => 0));
            $current_level = $this->db->get('tblcosts')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    if ($level == 1) {
                        $sub .= "&rarr;";
                    } elseif ($level == 2) {
                        $sub .= "&#8649;";
                    } elseif ($level >= 3) {
                        $sub .= "&#8667;";
                    }
                    $current_level[$key]['code'] = $sub . " " . $current_level[$key]['code'];
                    array_push($array_costs, $current_level[$key]);
                    $this->get_costs_parent($value['id'], $array_costs, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    // yct start
    public function checkCodeExistCosts($code)
    {
        $this->db->from('tblcosts');
        $this->db->where('tblcosts.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function insertCosts($data)
    {
        // var_dump($data);
        // return 0;
        $checkCode = $this->checkCodeExistCosts($data['code']);
        if (!empty($checkCode)) {
            return 0;
        }

        if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
            $data['lever'] = 1;
        } else {
            $lever = 1;
            $parent = $data['costs_parent'];
            while (!empty($parent)) {
                $ktr = get_table_where('tblcosts', array('id' => $parent), '', 'row');
                $parent = $ktr->costs_parent;
                $lever++;
            }
            $data['lever'] = $lever;
        }

        $this->db->insert('tblcosts', $data);
        $rs = $this->db->insert_id();
        return $rs;
    }
    // yct end
}
