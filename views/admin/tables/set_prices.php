<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'tbl_set_prices.id',
    'tbl_set_prices.name',
    '2',
    '3',
    'tbl_set_prices.status',
    'tbl_set_prices.type_customer',
    'tbl_set_prices.id_groups',
    // 'tbl_set_prices.type_item',
    '6',
    '7'
];

$sIndexColumn = 'id';
$sTable       = 'tbl_set_prices';

$join         = array(
);
$where         = array();
if(is_numeric($this->ci->input->post('filterStatus'))){
array_push($where, 'AND FIND_IN_SET('.$this->ci->input->post('filterStatus').', tbl_set_prices.id_groups)');
}
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbl_set_prices.type_item',
    'tbl_set_prices.date_start as date_start',
    'tbl_set_prices.date_end as date_end',
    'tbl_set_prices.id_groups as id_groups',
    'tbl_set_prices.checkbox_date as checkbox_date',
));
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'tbl_set_prices.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        else if ($aColumns[$i] == '2') {
            $_data = count(get_table_where('tbl_set_prices_items',array('id_set_prices'=>$aRow['tbl_set_prices.id']))).' '._l('tnh_items');
        }
        else if ($aColumns[$i] == '3') {
            if($aRow['checkbox_date'] == 1) {
                $_data = '<span class="inline-block label label-success">'._l('no_limit').'</span>';
            }
            else {
                $_data = _d($aRow['date_start']).' - '._d($aRow['date_end']);
            }
        }
        else if ($aColumns[$i] == 'tbl_set_prices.status') {
            $checked = '';
            if ($aRow['tbl_set_prices.status'] == 1) {
                $checked = 'checked';
            }
            $_data = '<div class="onoffswitch">
                        <input type="checkbox" data-switch-url="'.admin_url().'set_prices/change_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['tbl_set_prices.id'].'" data-id="'.$aRow['tbl_set_prices.id'].'" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_'.$aRow['tbl_set_prices.id'].'"></label>
                    </div>';
        }
        else if ($aColumns[$i] == 'tbl_set_prices.type_customer') {
            $_data = '';
            $count = explode(',', $aRow['id_groups']);
            if(!$aRow['id_groups']) {
                $count = array();
            }
            if($aRow['tbl_set_prices.type_customer'] == 1) {
                $_data = '<span class="inline-block label label-warning">'._l('all_customer').'</span>';
            }
            else if($aRow['tbl_set_prices.type_customer'] == 2) {
                if(count($count) > 0) {
                    $CI =& get_instance();
                    $CI->db->select('tblcustomers_groups.*');
                    $CI->db->where_in('id',$count);
                    $get_data_customers_groups = $CI->db->get('tblcustomers_groups')->result_array();
                }
                
                $_data = '<span class="inline-block label label-danger pointer '.(count($count) > 0 ? 'js-menu-status' : '').'">'._l('customer_group').' ('.count($count).')';
                $_data .= '<div class="content-menu hide">';
                if($get_data_customers_groups) {
                    foreach ($get_data_customers_groups as $key => $value) {
                        $_data .= '<div>'.$value['name'].'</div>';
                    }
                }
                $_data .= '</div>';
                $_data .= '</span>';
            }
        }
        else if ($aColumns[$i] == 'tbl_set_prices.id_groups') {
            $_data = '';
            $get_data_customers_groups=array();
            $count = explode(',', $aRow['id_groups']);
            if(!$aRow['id_groups']) {
                $count = array();
            }
            if($aRow['tbl_set_prices.type_customer'] == 1) {
                $_data = '';
            }
            else if($aRow['tbl_set_prices.type_customer'] == 2) {
                if(count($count) > 0) {
                    $CI =& get_instance();
                    $CI->db->select('tblcustomers_groups.*');
                    $CI->db->where_in('id',$count);
                    $get_data_customers_groups = $CI->db->get('tblcustomers_groups')->result_array();
                }
                if($get_data_customers_groups) {
                    foreach ($get_data_customers_groups as $key => $value) {
                        $_data .= '<div>'.$value['name'].'</div>';
                    }
                }
                $_data .= '</div>';
            }
        }
        // else if ($aColumns[$i] == 'tbl_set_prices.type_item') {
        //     if($aRow['tbl_set_prices.type_item'] == 1) {
        //         $_data = '<span class="inline-block label label-warning">'._l('ch_items').'</span>';
        //     }
        //     else if($aRow['tbl_set_prices.type_item'] == 2) {
        //         $_data = '<span class="inline-block label label-danger">'._l('tnh_products').'</span>';
        //     }
        // }
        else if ($aColumns[$i] == '6') {
            $_data = '<button class="btn btn-info apply-set-price" data-id="'.$aRow['tbl_set_prices.id'].'">'._l('apply').' <i class="fa fa-spinner fa-spin wap-load hide"></i></button>';
        }
        else if ($aColumns[$i] == '7') {
            $_data = '';
            $_outputStatus = '<div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu h_right">
                                    <li><a href="'.admin_url('set_prices/detail/'.$aRow['type_item'].'/'.$aRow['tbl_set_prices.id']).'"><i class="fa fa-eye"></i> '._l('tnh_detail').'</a></li>
                                    <li><a onclick="edit_set_prices('.$aRow['tbl_set_prices.id'].');return false;"><i class="fa fa-edit"></i> '._l('edit').'</a></li>
                                    <li><a href="'.admin_url('set_prices/print_pdf/'.$aRow['tbl_set_prices.id']).'" target="_blank"><i class="fa fa-print"></i>  '._l('print_set_prices').'</a></li>
                                    <li><a onclick="delete_set_prices('.$aRow['tbl_set_prices.id'].');return false;" class="delete-remind"><i class="fa fa-remove"></i> '._l('delete').'</a></li>
                                </ul>
                            </div>';
            $_data = $_outputStatus;
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
