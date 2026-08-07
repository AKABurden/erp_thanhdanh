<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('purchases', '', 'delete');

$hasPermission_order = has_permission('purchase_order', '', 'create');
$hasPermission_rfq = has_permission('RFQ', '', 'create');
$hasPermission_quote = has_permission('supplier_quotes', '', 'create');
$custom_fields = get_table_custom_fields('purchases');
$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'tblpurchases.date',
    'tblpurchases.code',
    'tbl_productions_plan.reference_no',
    // 'tblpurchases.name_purchase',
    'tblpurchases.staff_create',
    'tblpurchases.status',
    'tblpurchases.explanation as note',
    'tblpurchases.history_status',
    'tblpurchases.type',
];
$sIndexColumn = 'id';
$sTable = 'tblpurchases';
$where = [];
$filter = [];

$join = ['LEFT JOIN tbl_productions_plan on tbl_productions_plan.id = tblpurchases.id_plan'];
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join,
        'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON tblpurchases.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
$join = hooks()->apply_filters('purchases_table_sql_join', $join);

if ($this->ci->input->post('filterStatus')) {
    if (is_numeric($this->ci->input->post('filterStatus'))) {
        if ($this->ci->input->post('filterStatus') == 1 || $this->ci->input->post('filterStatus') == 2 || $this->ci->input->post('filterStatus') == 3 || $this->ci->input->post('filterStatus') == 4) {
            array_push($where, 'AND tblpurchases.status = ' . $this->ci->input->post('filterStatus'));
        } elseif ($this->ci->input->post('filterStatus') == 5) {
            array_push($where, 'AND tbl_productions_plan.reference_no is not NULL');
        } elseif ($this->ci->input->post('filterStatus') == 7) {
            array_push($where, 'AND tblpurchase_order.id is NULL');
            array_push($join, 'LEFT JOIN tblpurchase_order on tblpurchase_order.id_purchases = tblpurchases.id');
        } elseif ($this->ci->input->post('filterStatus') == 8) {
            array_push($where, 'AND tblpurchase_order.id is not NULL');
            array_push($where, 'AND tblpurchases.status < 4');
            array_push($join, 'LEFT JOIN tblpurchase_order on tblpurchase_order.id_purchases = tblpurchases.id');
        } elseif ($this->ci->input->post('filterStatus') == 9) {
            array_push($where, 'AND tblpurchase_order.id is not NULL');
            array_push($where, 'AND tblpurchases.status = 4');
            array_push($join, 'LEFT JOIN tblpurchase_order on tblpurchase_order.id_purchases = tblpurchases.id');
        }
    }
}
if ($this->ci->input->post('search_code')) {
    if (is_numeric($this->ci->input->post('search_code'))) {
        array_push($where, 'AND tblpurchases.id = ' . $this->ci->input->post('search_code'));
    }
}
if ($this->ci->input->post('search_staff')) {
    array_push($where,
        'AND  tblpurchases.staff_create IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
}

if ($this->ci->input->post('custom_item_select')) {
    array_push($where,
        'AND EXISTS (
            SELECT tblpurchases_items.purchases_id
            FROM tblpurchases_items
            WHERE tblpurchases_items.purchases_id = tblpurchases.id
            AND tblpurchases_items.product_id = '.$this->ci->input->post('custom_item_select').'
        )');
}

$search_date = $this->ci->input->post('search_date');
if ($search_date) {
    $data_start = explode(' - ', $search_date);
    array_push($where,
        'AND tblpurchases.date_create BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
}
if (has_permission('purchases', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblpurchases.staff_create = ' . get_staff_user_id());
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblpurchases.id',
    'tblpurchases.prefix',
    'tblpurchases.process',
    'tblpurchases.note_cancel',
    'tblpurchases.not_new_by_staff',
    'tblpurchases.id_order',
    'tblpurchases.order_id',

], 'GROUP BY tblpurchases.id');
$output = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $aRow) {
    $j++;
    $row = [];
    $not_new_by_staff = explode(',', $aRow['not_new_by_staff']);
    // if(empty($aRow['process'])&&($aRow['tblpurchases.status'] == 3)&&(empty($aRow['id_order'])))
    // {
    // $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['tblpurchases.id'] . '"><label></label></div>';
    // }else
    // {
    // $processas = explode(',', $aRow['process']);
    //     if($processas[0] == 3&&($aRow['tblpurchases.status'] == 3&&(empty($aRow['id_order']))))
    //     {
    //     $purchase = get_items_purchase_new($aRow['tblpurchases.id']);
    //     if($purchase > 0){
    //     $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['tblpurchases.id'] . '"><label></label></div>';
    //     }else
    //     {
    //     $row[] ='';
    //     }
    //     }else
    //     {
    //     $row[] ='';
    //     }
    // }
    if (!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblpurchases.status'] == 2) {
        $row[] = _dt($aRow['tblpurchases.date']) . ' <br><span class="wap-new">new</span>';
        $row['DT_RowClass'] = 'alert-new';
    } else {
        $row[] = _dt($aRow['tblpurchases.date']);
    }

    $purchases = $aRow['prefix'] . $aRow['tblpurchases.code'];
    $isPerson = false;
    $purchases = '<a href="#" onclick="view_purchases(' . $aRow['id'] . '); return false;" >' . $purchases . '</a>';


    $row[] = $purchases;
    if (!empty($aRow['tbl_productions_plan.reference_no'])) {
        $productions_capacity = '<span class="inline-block label label-success">' . _l('productions_plan_acronym') . '</span><br>';
        $row[] = $productions_capacity . $aRow['tbl_productions_plan.reference_no'];
    } else {
        if (!empty($aRow['order_id'])) {
            $get_order = get_table_where('tbl_orders', array('id' => $aRow['order_id']), '', 'row');
            $row[] = '<span class="inline-block label label-warning">' . _l('Đơn đặt hàng bán') . '</span><br>' . $get_order->reference_no;
        } else {
            $row[] = '';
        }
    }


    // $row[] = ($aRow['tblpurchases.name_purchase'] ? $aRow['tblpurchases.name_purchase'] : '');

    $staff = staff_profile_image($aRow['tblpurchases.staff_create'], array('staff-profile-image-small mright5'),
            'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => get_staff_full_name($aRow['tblpurchases.staff_create'])
            )) . get_staff_full_name($aRow['tblpurchases.staff_create']);
    $row[] = ($aRow['tblpurchases.staff_create'] ? $staff : '');

    if ($aRow['tblpurchases.status'] == 1) {
        $type = 'warning';
        $status = _l('dont_confirm');
    } elseif ($aRow['tblpurchases.status'] == 2) {
        $type = 'info';
        $status = _l('dont_approve');
    } elseif ($aRow['tblpurchases.status'] == 3) {
        $type = 'success';
        $status = _l('ch_confirm_22');
    } else {
        $type = 'danger';
        $status = _l('ch_cancel');
    }
    $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblpurchases.status'] . '">' . $status . '';
    if (has_permission('purchases', '', 'approve')) {
        if ($aRow['tblpurchases.status'] != 3) {
            if ($aRow['tblpurchases.status'] == 4) {

                $status .= '<a class="delete-remind" data-title="Bấm để bỏ hủy" href="' . admin_url('purchases/no_note_cancel/' . $aRow['id']) . '">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
            } else {
                $status .= '<a href="javacript:void(0)" data-loading-text="" onclick="var_status(' . $aRow['tblpurchases.status'] . ',' . $aRow['id'] . '); return false;">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>
                    ';
            }
        } else {
            $status .= '<a class="add_contact_person" data-toggle="tooltip" title="" data-type="service" data-placement="top"  data-id="' . $aRow['id'] . '">
                    <i class="fa fa-check task-icon task-finished-icon" data-title="" data-toggle="tooltip"></i>';
        }
    } else {
        $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
    }
    $status .= '</a>
                </span><br>';
    $_data = '';
    $history_status = explode('|', $aRow['tblpurchases.history_status']);

    foreach ($history_status as $key => $value) {
        $data = explode(',', $value);
        if (is_numeric($data[0])) {
            if ($key == 1) {
                $name_status = _l('ch_confirm') . ': ';
            } elseif ($key == 2) {
                $name_status = _l('ch_confirm_2') . ': ';
            } elseif ($key == 3) {
                $name_status = _l('ch_cancel') . ': ';
            }
            $_data .= $name_status . staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small',
                    array(
                        'data-toggle' => 'tooltip',
                        'data-title' => _l('ch_time') . ': ' . _dt($data[1])
                    )) . get_staff_full_name($data[0]) . '<br>';
        }
        $note_cancel = false;
        if ($key == 3 && ($aRow['note_cancel'] != '')) {
            $note_cancel = true;
            $_data .= '<b style="color:red">' . _l('ch_note_cancel') . ': ' . $aRow['note_cancel'] . '</b><br>';
        }
    }

    $row[] = $status . $_data;
    $row[] = $aRow['note'];

    $ktrrfq = get_table_where("tblrfq_ask_price", array('id_purchases' => $aRow['id']), '', 'row');
    $purchase_order = get_table_where('tblpurchase_order', array('id_purchases' => $aRow['id']));
    // if (empty($ktrrfq)) {
    //     $ktrrfq = get_table_where('tblsupplier_quotes', array('id_purchases' => $aRow['id']), '', 'row');
    //     $supplier_quote = $ktrrfq;
    // }
    // if (empty($ktrrfq)) {

    //     $ktrrfq = get_table_where('tblpurchase_order', array('id_purchases' => $aRow['id']), '', 'row');
    // }
    // $toggleActive = '<div class="onoffswitch" data-toggle="tooltip">
    //     <input type="checkbox"' . (!empty($ktrrfq) ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'purchases/change_purchases_type" name="onoffswitch"  class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['tblpurchases.type'] == 1 ? 'checked' : '') . '>
    //     <label class="onoffswitch-label change_type" for="' . $aRow['id'] . '"></label>
    //     </div>';
    // $toggleActive .= '<span class="hide">' . ($aRow['tblpurchases.type'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
    // if (empty($purchase_order)) {
    //     $row[] = $toggleActive;
    // } else {
    //     $row[] = '';
    // }

    $dataRow = '';
    $data = '<a href="' . admin_url('purchase_order/create_detail/' . $aRow['id']) . '" type="button"  class="btn btn-success">' . _l('ch_add_purchase_order') . '</a>';
    if (!empty($aRow['process'])) {
        $process = explode('|', $aRow['process']);
        if ($process[0] == 1) {
            $ktr = get_table_where('tblrfq_ask_price', array('id_purchases' => $aRow['id']), '', 'row');
            $dataRow = '<span class="inline-block label label-warning">' . $ktr->prefix . '-' . $ktr->code . '</span>';
        } elseif ($process[0] == 2) {
            $supplier_quotes = $supplier_quote->prefix . '-' . $supplier_quote->code;
            $dataRow = '<a href="#" onclick="view_supplier_quotes(' . $supplier_quote->id . '); return false;" >' . purchase_quote($supplier_quotes) . '</a>';
        } elseif ($process[0] == 3) {
            $purchase = get_items_purchase_new($aRow['id']);
            $count = count($purchase_order);
            $_data = '';
            foreach ($purchase_order as $k => $v) {
                $_data .= '<li><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                            </button>
                            <ul class="dropdown-menu right50">';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';
            if ($purchase > 0) {
                // $row[] = text_align(count_number_PO_ch($count).'<br>'.$data.'<br>'.$_data,'center');
                $dataRow = $_outputStatus;
            } else {
                $dataRow = $_outputStatus;
            }
        }
    } else {
        $dataRow = '';
    }
    // $row[] = text_align(process_purchases_img($aRow['id']) . process_purchases($aRow['id']), 'center');

    $purchase_check = get_items_purchase_new($aRow['id']);
    $purchase_order_check = get_items_purchase_check($aRow['id']);
    $this->ci->db->where('tblpurchases_items.purchases_id', $aRow['id']);
    $this->ci->db->from('tblpurchases_items');
    $count_items = $this->ci->db->count_all_results();
    $data_check = '';
    $_outputStatus_check = '';
    $count_check = 0;
    $_data_check_count = '';
    $order_check = get_table_where('tblpurchase_order', array('id_purchases' => $aRow['id'],'check_purchase_all'=>0), '', 'row');

    if (!empty($order_check)) {
        $purchase_order_check_count = get_table_where('tblpurchase_order',
            array('id_purchases' => $order_check->id_purchases));
        $count_check = count($purchase_order_check_count);
        foreach ($purchase_order_check_count as $k => $v) {
            $_data_check_count .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
        }
    }
    $order_check_all = get_table_where('tblpurchase_to_order', ['id_purchases' => $aRow['id']], '', 'result_array');

    if (!empty($order_check_all)) {
        $count_check += count($order_check_all);
        foreach ($order_check_all as $k => $v) {
            $purchase_order_check_count_all = get_table_where('tblpurchase_order',
                array('id' => $v['id_purchases_order']));
            foreach ($purchase_order_check_count_all as $kk => $vv) {
                $_data_check_count .= '<li class="hoang"><a onclick="view_purchase_order(' . $vv['id'] . '); return false;" >' . $vv['prefix'] . '-' . $vv['code'] . '</a></li>';
            }
        }
    }

    // $_outputStatus_check = '<div class="dropdown" style="text-align: center;margin-top:10px">
    //     <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count_check).'
    //     </button>
    //     <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
    // $_outputStatus_check .= $_data_check_count;
    // $_outputStatus_check .= '</ul></div>';

    if ($purchase_order_check == $count_items) {
        $data_check = '<div class="label label-danger">Chưa đặt</div>';
    } else {
        if ($purchase_check <= 0) {
            $_outputStatus_check = '<div class="dropdown" style="text-align: center;margin-top:10px">
                <button class="dropdown-toggle no_background label label-info" type="button" data-toggle="dropdown">Đã đặt (' . count_number_PO_ch($count_check) . ')
                    </button>
                    <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus_check .= $_data_check_count;
            $_outputStatus_check .= '</ul></div>';
            $data_check = $_outputStatus_check;
            // $data_check = '<div class="label label-info">Đã tạo đơn hàng </div>'.$_outputStatus_check;
        } elseif ($purchase_check > 0) {
            $_outputStatus_check = '<div class="dropdown" style="text-align: center;margin-top:10px">
                <button class="dropdown-toggle no_background label label-warning" type="button" data-toggle="dropdown">Đặt 1 phần (' . count_number_PO_ch($count_check) . ')
                    </button>
                    <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus_check .= $_data_check_count;
            $_outputStatus_check .= '</ul></div>';
            $data_check = $_outputStatus_check;
            // $data_check = '<div class="label label-warning">Tạo 1 phần đơn hàng</div>'.$_outputStatus_check;
        }
    }
    $row[] = $data_check;


    $_outputStatus = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="view_purchases(' . $aRow['id'] . '); return false;" ><i class="fa fa-eye"></i>  ' . _l('view_order') . '</a></li>';
    if (has_permission('purchases', '', 'edit')) {
        if ($aRow['tblpurchases.status'] < 3) {
            $_outputStatus .= '<li><a href="' . admin_url('purchases/detail/' . $aRow['id']) . '" ><i class="fa fa-pencil"></i>  ' . _l('edit_order') . '</a></li>';
        }
    }
    if (!empty($aRow['process']) || !empty($aRow['id_order'])) {
        $process = explode('|', $aRow['process']);
        if ($process[0] == 1) {
        } elseif ($process[0] == 2) {
            $_outputStatus .= '';
        } else {
            $order = get_table_where('tblpurchase_order', array('id_purchases' => $aRow['id']), '', 'row');
            if (!empty($order)) {
                $purchase = get_items_purchase_new($aRow['id']);
                if (($purchase > 0) && ($aRow['tblpurchases.status'] != 4)) {
                    if ($hasPermission_order) {
                        $_outputStatus .= '<li><a href="' . admin_url('purchase_order/create_detail/' . $aRow['id']) . '"><i class="lnr lnr-cog"></i>  ' . _l('ch_add_purchase_order') . '</a></li>';
                    }
                }
            } else {
                $purchase = get_items_purchase_new($aRow['id']);
                if (($purchase > 0) && ($aRow['tblpurchases.status'] != 4) && $hasPermission_order) {
                    $_outputStatus .= '<li><a href="' . admin_url('purchase_order/create_detail/' . $aRow['id']) . '"><i class="lnr lnr-cog"></i>  ' . _l('ch_add_purchase_order') . '</a></li>';
                } else {
                    $_outputStatus .= '';
                }
            }
        }
    } else {
        if ($aRow['tblpurchases.status'] == 3) {
            if ($aRow['tblpurchases.type'] == 1) {

                if ($hasPermission_order) {
                    $_outputStatus .= '<li><a href="' . admin_url('purchase_order/create_detail/' . $aRow['id']) . '"><i class="lnr lnr-cog"></i>  ' . _l('ch_add_purchase_order') . '</a></li>';
                }
            } else {
                if ($hasPermission_quote) {
                    $_outputStatus .= '<li><a href="' . admin_url('supplier_quotes/detail_create/' . $aRow['id'] . '/0/1') . '"><i class="lnr lnr-cog"></i>' . _l('ch_add_quote') . '</a></li>';
                }
                if ($hasPermission_order) {
                    $_outputStatus .= '<li><a href="' . admin_url('purchase_order/create_detail/' . $aRow['id']) . '"><i class="lnr lnr-cog"></i>  ' . _l('ch_add_purchase_order') . '</a></li>';
                }
            }
        }
    }
    $content = str_replace('"', '\'', render_textarea('note_cancel',
            'ch_note_finish')) . '<div class=\'text-right\'><button onclick=\'save_contact_person(' . $aRow['id'] . ')\' class=\'btn btn-danger \'>' . _l('submit') . '</button><a class=\'btn btn-default po-close\'>' . _l('close') . '</a></div>';
    if (!$note_cancel) {
        $_outputStatus .= '<li class="not-outside"><a type="button" data-placement="left" data-container="body" data-html="true" data-toggle="popover"  data-content="' . $content . '"><i class="fa fa-remove"></i>  ' . _l('ch_finish') . '</a></li>';
    }
    $_outputStatus .= '<li><a href="' . admin_url('purchases/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-print"></i>  ' . _l('print_vote') . '</a></li>';
    if ($hasPermissionDelete) {
        $_outputStatus .= '<li><a href="' . admin_url('purchases/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i>  ' . _l('delete_order') . '</a></li>';
    }
    $_outputStatus .= '</ul></div>';


    $row[] = $_outputStatus;

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn,
            'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    // $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('purchases_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
