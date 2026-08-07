<?php
defined('BASEPATH') or exit('No direct script access allowed');


$aColumns     = array(
    'code',
    'style_number',
    'color_name',
    'primary_size',
    'upc',
    'quantity',
    'trim_card',
    'Sample',
    'loss',
    'one',
    'slqc',
    'stickers',
    'qc_sample',
    'tc',
    'layoutno',
    'remark',
);
$sIndexColumn = "id";
$sTable       = 'tblcompose_detail';
$where        = array();
if (has_permission('compose', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND tblcompose_detail.staff_create = ' . get_staff_user_id());
}
$join         = array();
if ($this->ci->input->post('search_code')) {
    array_push($where, 'AND tblcompose_detail.code LIKE "%' . $this->ci->input->post('search_code') . '%"');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'id'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();

$footer_data = array(
    'all' => 0
);
$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'quantity') {
            if (is_numeric($aRow['quantity'])) {
                $_data = formatNumber($aRow['quantity']);
                $footer_data['all'] += $aRow['quantity'];
            } else {
                $_data = $aRow['quantity'];
            }
        }
        if ($aColumns[$i] == 'layoutno') {
            $_data = '<textarea style="width: 100%;" class="form-control update_code layout_no" data-id="' . $aRow['id'] . '"  data-name="layoutno" value="' . $aRow['layoutno'] . '" />' . $aRow['layoutno'] . '</textarea>';
        }
        if ($aColumns[$i] == 'one') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="one" class="form-control update_code one" value="' . $aRow['one'] . '"><div class="hide text">' . $aRow['one'] . '</div>';
        }
        if ($aColumns[$i] == 'trim_card') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="trim_card" class="form-control update_code trim_card" value="' . $aRow['trim_card'] . '"><div class="hide text">' . $aRow['trim_card'] . '</div>';
        }
        if ($aColumns[$i] == 'Sample') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="Sample" class="form-control update_code Sample" value="' . $aRow['Sample'] . '"><div class="hide text">' . $aRow['Sample'] . '</div>';
        }
        if ($aColumns[$i] == 'slqc') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="slqc" class="form-control update_code slqc" value="' . $aRow['slqc'] . '"><div class="hide text">' . $aRow['slqc'] . '</div>';
        }
        if ($aColumns[$i] == 'remark') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="remark" class="form-control update_code remark" value="' . $aRow['remark'] . '"><div class="hide text">' . $aRow['remark'] . '</div>';
        }
        if ($aColumns[$i] == 'stickers') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="stickers" class="form-control update_code stickers" value="' . $aRow['stickers'] . '"><div class="hide text">' . $aRow['stickers'] . '</div>';
        }
        if ($aColumns[$i] == 'tc') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="tc" class="form-control update_code tc" value="' . $aRow['tc'] . '"><div class="hide text">' . $aRow['tc'] . '</div>';
        }
        if ($aColumns[$i] == 'qc_sample') {
            $_data = '<input style="width: 100%;" data-id="' . $aRow['id'] . '"  data-name="qc_sample" class="form-control update_code qc_sample" value="' . $aRow['qc_sample'] . '"><div class="hide text">' . $aRow['qc_sample'] . '</div>';
        }
        // if ($aColumns[$i] == 'tblcompose.name') {
        //     $_data = '<a href="#" onclick="view_compose(' . $aRow['id'] . '); return false;" >'.$aRow['tblcompose.name'].'</a>';
        // }
        // if ($aColumns[$i] == 'tblcompose.date_create') {
        //     $_data = _d($aRow['tblcompose.date_create']);
        // }
        // if ($aColumns[$i] == 'tblcompose.staff_create') {
        //     $staff = staff_profile_image($aRow['tblcompose.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
        //         'data-toggle' => 'tooltip',
        //         'data-title' => get_staff_full_name($aRow['tblcompose.staff_create'])
        //     )) . get_staff_full_name($aRow['tblcompose.staff_create']);
        //     $_data = ($aRow['tblcompose.staff_create'] ? $staff : '');
        // }
        $row[] = $_data;
    }
    // $_outputStatus = '<div class="dropdown H_drop " style="">
    // <button class="btn btn-primary dropdown-toggle " type="button" data-toggle="dropdown">' . _l('action') . '
    //     <span class="caret"></span>
    // </button>
    // <ul class="dropdown-menu h_right">';
    // $_outputStatus .= '<li><a href="'.admin_url('compose/detail/' . $aRow['id']).'" return false;" ><i class="fa fa-pencil" aria-hidden="true"></i>' . _l('Sửa phiếu') . '</a></li>';
    // $_outputStatus .= '<li><a href="' . admin_url('compose/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times" aria-hidden="true"></i>' . _l('delete_order') . '</a></li>';
    // $_outputStatus .= '</ul></div>';
    // $row[] = $_outputStatus;
    $row[] = icon_btn('compose/delete/' . $aRow['id'], 'remove', 'btn-danger delete-reminders');

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;
