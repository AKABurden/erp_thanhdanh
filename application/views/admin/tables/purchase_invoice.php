<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('purchase_invoice', '', 'delete');
$hasPermissionPay_slip = has_permission('pay_slip', '', 'create');

$aColumns     = array(
    'tblpurchase_invoice.id',
    'tblpurchase_invoice.date_invoice',
    'tblpurchase_invoice.code_invoice',
    'tblpurchase_invoice.id_import',
    'tblsuppliers.company',
    'total_price_befor_vat',
    'total_price_vat',
    // '12',
    'total_price_affter_vat',
    // 'price_other_expenses',
    // 'tblpurchase_invoice.status',
    'tblpurchase_invoice.staff_create',
    // 'tblpurchase_invoice.link',
    '10'
);
$sIndexColumn = "id";
$sTable       = 'tblpurchase_invoice';
$where        = array();
$suppliers_id = $this->ci->input->post('suppliers_id');
if (is_numeric($suppliers_id)) {
    array_push($where, 'AND tblpurchase_invoice.id_supplier = ' . $this->ci->input->post('suppliers_id'));
}
// sum note

$start_date_search = $this->ci->input->post('start_date_search');
if (!empty($start_date_search)) {
    array_push($where, 'AND tblpurchase_invoice.date_invoice >= "' . to_sql_date($this->ci->input->post('start_date_search')) . '"');
}

$end_date_search = $this->ci->input->post('end_date_search');
if (!empty($end_date_search)) {
    array_push($where, 'AND tblpurchase_invoice.date_invoice <= "' . to_sql_date($this->ci->input->post('end_date_search')) . '"');
}
// ./sum note
$join         = array(
    'LEFT JOIN tblsuppliers ON tblsuppliers.id=tblpurchase_invoice.id_supplier',
);
if (has_permission('purchase_invoice', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblpurchase_invoice.staff_create = ' . get_staff_user_id());
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblpurchase_invoice.id',
    'tblpurchase_invoice.id_supplier',
    'tblpurchase_invoice.id_import',
    'tblpurchase_invoice.id_suggestion',
    'tblpurchase_invoice.date_create',
    'tblpurchase_invoice.type_create',
    'tblpurchase_invoice.status'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
$j = 0;
$footer_data = array(
    'no_vat' => 0,
    'vat' => 0,
    'co_vat' => 0,
    'pay' => 0,
    'km' => 0,
);
foreach ($rResult as $aRow) {
    $km = 0;
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblpurchase_invoice.id') {
            if (is_numeric($suppliers_id) && $aRow['status'] == 0) {
                $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
            } else {
                $_data = '';
            }
        }

        if ($aColumns[$i] == 'tblpurchase_invoice.date_invoice') {
            $_data = _d($aRow['tblpurchase_invoice.date_invoice']);
        }
        if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data = '<a href="#" onclick="int_suppliers_view(' . $aRow['id_supplier'] . ',false); return false;" >' . $aRow['tblsuppliers.company'] . '</a>';
        }
        if ($aColumns[$i] == 'tblpurchase_invoice.link') {
            $_data = '';
            if (!empty($aRow['tblpurchase_invoice.link'])) {
                $_data = '<div class="text-center"><a href="' . $aRow['tblpurchase_invoice.link'] . '" class="btn btn-primary dropdown-toggle" target="_blank" type="button" >' . _l('Link') . '
                    </a></div>';
                // $_data ='<a href="'.$aRow['tblpurchase_invoice.link'].'">' . $aRow['tblpurchase_invoice.link'] . '</a>';
            }
        }
        // if ($aColumns[$i] == '12') {
        //     $_data = '<div class="text-right">' . number_format($km) . '<div>';
        //     $footer_data['km'] += $km;
        // }
        if ($aColumns[$i] == 'tblpurchase_invoice.code_invoice') {
            $_data = $aRow['tblpurchase_invoice.code_invoice'];
        }
        if ($aColumns[$i] == 'tblpurchase_invoice.id_import') {
            $_data = '';
            if ($aRow['type_create'] == 0) {
                $id_import = explode(',', $aRow['tblpurchase_invoice.id_import']);
                $count = count($id_import);
                if ($count == 1) {
                    $import = get_table_where('tblimport', array('id' => $id_import[0]), '', 'row');
                    $km = 0; //$import->promotion_expected;
                    $_data = '<a href="#" onclick="view_import(' . $id_import[0] . '); return false;" >' . $import->prefix . '-' . $import->code . '</a>';
                } else {
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . format_status_number_import_ch($count) . '
                        </button>';
                    $__data = '';
                    foreach ($id_import as $key => $value) {
                        $import = get_table_where('tblimport', array('id' => $value), '', 'row');
                        $km += 0; //$import->promotion_expected;
                        $__data .= '<li><a href="#" onclick="view_import(' . $value . '); return false;" >' . $import->prefix . '-' . $import->code . '</a></li>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                }
            } else {
                $id_import = explode(',', $aRow['id_suggestion']);
                $count = count($id_import);
                if ($count == 1) {
                    $import = get_table_where('tblsuggestion', array('id' => $id_import[0]), '', 'row');
                    $km = 0; //$import->promotion_expected;
                    $_data = '<a data-tnh="modal" style="" class="tnh-modal" href="'.admin_url('suggestion/view_modal/').$id_import[0].'" data-toggle="modal" data-target="#myModal">' . $import->code . '</a>';
                } else {
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . format_status_number_suggestion_ch($count) . '
                        </button>';
                    $__data = '';
                    foreach ($id_import as $key => $value) {
                        $import = get_table_where('tblsuggestion', array('id' => $value), '', 'row');
                        $km += 0; //$import->promotion_expected;
                        $__data .= '<li><a data-tnh="modal" style="" class="tnh-modal" href="'.admin_url('suggestion/view_modal/').$id_import[0].'" data-toggle="modal" data-target="#myModal">' . $import->code . '</a></li>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                }
            }
        }
        if ($aColumns[$i] == 'total_price_vat') {
            $_data = '<div class="text-right">' . number_format($aRow['total_price_vat']) . '<div>';
            $footer_data['vat'] += $aRow['total_price_vat'];
        }
        // if ($aColumns[$i] == 'total_price_befor_vat - price_other_expenses as total_payment') {
        //     $_data = '<div class="text-right">' . number_format($aRow['total_payment']) . '<div>';
        // }
        if ($aColumns[$i] == 'total_price_befor_vat') {
            $_data = '<div class="text-right">' . number_format($aRow['total_price_befor_vat']) . '<div>';
            $footer_data['no_vat'] += $aRow['total_price_befor_vat'];
        }
        if ($aColumns[$i] == 'price_other_expenses') {
            $_data = '<div class="text-right">' . number_format($aRow['price_other_expenses']) . '<div>';
            $footer_data['pay'] += $aRow['price_other_expenses'];
        }
        if ($aColumns[$i] == 'total_price_affter_vat') {
            $_data = '<div class="text-right">' . number_format($aRow['total_price_affter_vat']) . '<div>';
            $footer_data['co_vat'] += $aRow['total_price_affter_vat'];
        }
        // if ($aColumns[$i] == 'tblpurchase_invoice.status') {
        // $_data = '<div class="text-center">' . format_status_pay_slip($aRow['tblpurchase_invoice.status']) . '<div>';
        // }
        if ($aColumns[$i] == 'tblpurchase_invoice.staff_create') {
            $_data = staff_profile_image($aRow['tblpurchase_invoice.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])
            )) . get_staff_full_name($aRow['tblpurchase_invoice.staff_create']) . '<br>';;
        }


        if ($aColumns[$i] == '10') {
            $_outputStatus = '<div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu h_right">';
            // if ($aRow['tblpurchase_invoice.status'] != 2 && $hasPermissionPay_slip) {
            //     $_outputStatus .= '<li><a onclick="payment(' . $aRow['id'] . ')"><i class="fa fa-file width-icon-actions"></i> ' . _l('ch_pay_slip') . '</a></li>';
            // }
            $_outputStatus .= '<li><a onclick="electronic_bill(' . $aRow['id'] . ')"><i class="fa fa-file-image-o width-icon-actions"></i> ' . _l('ch_electronic_bill') . '</a></li>';

            if ($aRow['status'] != 2 && $hasPermissionDelete) {
                $_outputStatus .= '<li><a href="' . admin_url('purchase_invoice/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
            }

            $_outputStatus .= '</ul></div>';
            $row[] = $_outputStatus;
        }
        $row[] = $_data;
    }
    $_data = '';


    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
