<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('transfer', '', 'delete');
$hasPermissionEdit = has_permission('transfer', '', 'edit');
$hasPermissionApprove = has_permission('transfer', '', 'approve');
$hasPermissionApprove_warehouse = has_permission('transfer', '', 'approve_warehouse');

$aColumns = array(
    'tbltransfer_warehouse.date',
    'tbltransfer_warehouse.code',
    '"" as reference_order',
    '"" as date_expected',
    'tbltransfer_warehouse.staff_id',
    'tbltransfer_warehouse.status',
    // 'waid.name as waidname',
    // 'wato.name as watoname',
    'tbltransfer_warehouse.warehouseman_id',
    'tbltransfer_warehouse.status_active_transfer as status_active_transfer',
    'tbltransfer_warehouse.note',
);
$sIndexColumn = "id";
$sTable = 'tbltransfer_warehouse';
$where = array();
if ($this->ci->input->post('filterStatus')) {
    if (is_numeric($this->ci->input->post('filterStatus'))) {
        if ($this->ci->input->post('filterStatus') == 1) {
            array_push($where, 'AND tbltransfer_warehouse.type = 1');
        } elseif ($this->ci->input->post('filterStatus') == 2) {
            array_push($where, 'AND tbltransfer_warehouse.type = 2');
        } elseif ($this->ci->input->post('filterStatus') == 3) {
            array_push($where, 'AND tbltransfer_warehouse.status = 1');
        } elseif ($this->ci->input->post('filterStatus') == 4) {
            array_push($where, 'AND tbltransfer_warehouse.status = 0');
        }
    } elseif($this->ci->input->post('filterStatus') == 'nvl_sx'){
        array_push($where, 'AND tbltransfer_warehouse.productions_capacity_id != 0 AND tbltransfer_warehouse.status_active_transfer = 0');
    } elseif($this->ci->input->post('filterStatus') == 'tp'){
        array_push($where, 'AND tbltransfer_warehouse.order_id_new != 0 AND tbltransfer_warehouse.status_active_transfer = 0');
    } elseif($this->ci->input->post('filterStatus') == 'export_warehouse'){
        array_push($where, 'AND tbltransfer_warehouse.status_active_transfer != 0');
    }
}
if ($this->ci->input->post('purchase_product_search')) {
    array_push($where, 'AND tbltransfer_warehouse.purchase_product_id ='.$this->ci->input->post('purchase_product_search'));
}
if ($this->ci->input->post('tranfer_business_search')) {
    array_push($where, 'AND EXISTS(
        SELECT tbl_tranfer_to_tranfer_business.tranfer_id
        FROM tbl_tranfer_to_tranfer_business
        WHERE tbl_tranfer_to_tranfer_business.tranfer_id = tbltransfer_warehouse.id
        AND tbl_tranfer_to_tranfer_business.tranfer_business_id = '.$this->ci->input->post('tranfer_business_search').'
        )
    ');
}
if ($this->ci->input->post('orders_search')) {
    array_push($where, 'AND tbltransfer_warehouse.order_id_new ='.$this->ci->input->post('orders_search'));
}
if ($this->ci->input->post('transfer_search')) {
    array_push($where, 'AND tbltransfer_warehouse.id ='.$this->ci->input->post('transfer_search'));
}
if ($this->ci->input->post('productions_capacity_id_search')) {
    array_push($where, 'AND tbltransfer_warehouse.productions_capacity_id ='.$this->ci->input->post('productions_capacity_id_search'));
}
if ($this->ci->input->post('date_start')) {
    array_push($where, 'AND DATE_FORMAT(tbltransfer_warehouse.date, "%Y-%m-%d") >= "'.to_sql_date($this->ci->input->post('date_start')) . '"');
}
if ($this->ci->input->post('date_end')) {
    array_push($where, 'AND DATE_FORMAT(tbltransfer_warehouse.date, "%Y-%m-%d") <= "'.to_sql_date($this->ci->input->post('date_end')) . '"');
}
$productions_orders_search = $this->ci->input->post('productions_orders_search');
if (!empty($productions_orders_search)) {
    array_push($where, 'AND exists (
        SELECT tbl_productions_orders_items.id
        FROM tbl_productions_orders_items
        WHERE tbl_productions_orders_items.plan_id = tbl_productions_plan.id AND tbl_productions_orders_items.productions_orders_id = '.$productions_orders_search.'
    )', false, false);
}
$join = array(
    'LEFT JOIN tblwarehouse waid on waid.id = tbltransfer_warehouse.warehouse_id',
    'LEFT JOIN tblwarehouse wato on wato.id = tbltransfer_warehouse.warehouse_to',
    'LEFT JOIN tbl_orders on tbl_orders.id = tbltransfer_warehouse.order_id_new',
    'LEFT JOIN tbl_productions_plan on tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id',
);
if (has_permission('transfer', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tbltransfer_warehouse.staff_id = ' . get_staff_user_id());
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbltransfer_warehouse.id',
    'tbltransfer_warehouse.prefix',
    'date_create',
    'history_status',
    'import_outsource_id',
    'date_active_transfer',
    'tbltransfer_warehouse.productions_capacity_id',
    'date_active_transfer',
    'tbl_orders.reference_no as code_order',
    'tbl_productions_plan.reference_no as code_plan',
    'order_id_new',
    'date_active_transfer',
    'staff_acvive_transfer',
), 'ORDER BY tbltransfer_warehouse.id desc');
$output = $result['output'];
$rResult = $result['rResult'];

$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;

    $order_id = [];
    if ($aRow['order_id_new']) {
        $order_id[] = $aRow['order_id_new'];
    }

    if (!empty($aRow['productions_capacity_id'])) {
        $this->ci->db->select('
            tbl_productions_plan_object.object_id
        ', false);
        $this->ci->db->from('tbl_productions_plan_object');
        $this->ci->db->where('tbl_productions_plan_object.object_type', 'orders');
        $this->ci->db->where('tbl_productions_plan_object.productions_plan_id', $aRow['productions_capacity_id']);
        $object = $this->ci->db->get()->result_array();
        if ($object) {
            $order_id = array_merge($order_id, array_column($object, 'object_id'));
        }
    }

    $orders = null;
    $order_item = null;
    if (!empty($order_id)) {
        $order_id = array_unique($order_id);
        $this->ci->db->select('
            GROUP_CONCAT(tbl_orders.reference_no SEPARATOR "</br>") as reference_order
        ', false);
        $this->ci->db->from('tbl_orders');
        $this->ci->db->where_in('tbl_orders.id', $order_id);
        $orders = $this->ci->db->get()->row_array();

        $this->ci->db->select('
            MIN(tbl_order_item_shippings.date_shipping) as date_shipping
        ', false);
        $this->ci->db->from('tbl_order_items');
        $this->ci->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
        $this->ci->db->where_in('tbl_order_items.order_id', $order_id);
        $order_item = $this->ci->db->get()->row_array();
    }

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == '"" as reference_order') {
            $_data = !empty($orders) ? $orders['reference_order'] : '';
        }

        if ($aColumns[$i] == '"" as date_expected') {
            $_data = !empty($order_item) ? _d($order_item['date_shipping']) : '';
        }

        if ($aColumns[$i] == 'tbltransfer_warehouse.code') {
            $_data = '<div class="text-center">' . $aRow['prefix'] . '-' . $aRow['tbltransfer_warehouse.code'] . '</div>';
            $transfer = $aRow['prefix'] . '-' . $aRow['tbltransfer_warehouse.code'];
            $transfer = '<a href="#" onclick="view_transfer(' . $aRow['id'] . '); return false;">' . $transfer . '</a>';
            if($aRow['order_id_new'] > 0){
                $transfer .='<br><span class="inline-block label label-success" task-status-table="">'.$aRow['code_order'].'</span>';
            }elseif($aRow['productions_capacity_id'] > 0){
                $transfer .='<br><span class="inline-block label label-danger" task-status-table="">'.$aRow['code_plan'].'</span>';
            }
            
            $_data = $transfer;
        }
        if ($aColumns[$i] == 'tbltransfer_warehouse.date') {
            $_data = _d($aRow['tbltransfer_warehouse.date']);
        }
        if ($aColumns[$i] == 'tbltransfer_warehouse.status') {
            if ($aRow['tbltransfer_warehouse.status'] == 1) {
                $type = 'warning';
                $status = _l('dont_approve');
            } elseif ($aRow['tbltransfer_warehouse.status'] == 2) {
                $type = 'info';
                $status = _l('ch_confirm_22');
            }
            $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tbltransfer_warehouse.status'] . '">' . $status . '';
            if ($hasPermissionApprove) {
                if ($aRow['tbltransfer_warehouse.status'] == 1) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tbltransfer_warehouse.status'] . ',' . $aRow['id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                } else {
                    $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                }
            }
            $status .= '</a>
                        </span><br>';
            $__data = '';
            $history_status = explode('|', $aRow['history_status']);

            foreach ($history_status as $key => $value) {
                $data = explode(',', $value);
                if (is_numeric($data[0])) {
                    $__data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: ' . _dt($data[1])
                        )) . get_staff_full_name($data[0]) . '<br>';
                }
            }

            $_data = $status . $__data;
        }
        if ($aColumns[$i] == 'tbltransfer_warehouse.staff_id') {
            $_data = staff_profile_image($aRow['tbltransfer_warehouse.staff_id'],
                    array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])
                    )) . get_staff_full_name($aRow['tbltransfer_warehouse.staff_id']) . '<br>';
        }
        if ($aColumns[$i] == 'tbltransfer_warehouse.warehouseman_id') {
            $_data = '';
            $button = _l('ch_warehouse_nd');
            $title = _l('warehouseman_confirm');
            $type = 'fa-square-o';
            if ($hasPermissionApprove_warehouse) {
                if ($aRow['tbltransfer_warehouse.status'] == 2 && !empty($aRow['tbltransfer_warehouse.staff_id'])) {


                    if ($aRow['tbltransfer_warehouse.warehouseman_id']) {
                        $button = _l('ch_warehouse_d');
                        $title = _l('warehouseman_confirm_cancel');
                        $type = 'fa-check-square-o';
                    }
                    if (empty($aRow['tbltransfer_warehouse.warehouseman_id'])) {
                        $_data = '<span class="inline-block label label-warning" task-status-table="">Số lượng không đủ</span>';
                        if (test_quantity_tranfer($aRow['id'])) {
                            $_data = '<a href="" onclick="confirm_warehous(' . $aRow['id'] . ',' . $aRow['tbltransfer_warehouse.warehouseman_id'] . ');return false;" class=" btn btn-info btn-icon "  data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" data-original-title="' . $title . '"><i class="fa  ' . $type . '"></i> ' . $button . '</a>' . ($aRow['tbltransfer_warehouse.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tbltransfer_warehouse.warehouseman_id']) . '</span>' : '');
                        }
                    } else {
                        $_data = '<span class="inline-block label label-success" task-status-table="">Đã duyệt kho</span>';
                    }
                }
            }
            if (!empty($aRow['tbltransfer_warehouse.warehouseman_id'])) {
                $_data = '<span class="inline-block label label-success" task-status-table="">Đã duyệt kho</span>';
                if ($aRow['tbltransfer_warehouse.warehouseman_id']) {
                    $test_quantity = get_table_where('tblwarehouse_product',
                        array('import_id' => $aRow['id'], 'quantity_export >' => 0, 'type_export ' => 2), '', 'row');
                    if (!empty($test_quantity)) {
                        $_data = '<span class="inline-block label label-danger" task-status-table="">Đã có xuất kho</span>';
                    }
                }
            }
        }

        if ($aColumns[$i] == 'tbltransfer_warehouse.status_active_transfer as status_active_transfer') {
            if ($aRow['status_active_transfer'] == 1) {
                $_data = '<div class="text-center"><span class="inline-block label label-info"  onclick="changeStatusActive(' . $aRow['id'] . ', 0)">Đã Duyệt<a href="javacript:void(0)"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i></a></span>
                    <br/>' . staff_profile_image($aRow['staff_acvive_transfer'],
                        array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: ' . _dt($aRow['date_active_transfer'])
                        )) . get_staff_full_name($aRow['staff_acvive_transfer']) . '</div>';
            } else {
                $_data = '<div class="text-center"><span class="inline-block label label-warning" onclick="changeStatusActive(' . $aRow['id'] . ', 1)">Chưa Duyệt<a href="javacript:void(0)"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i></a></span></div>';
            }
        }

        $row[] = $_data;
    }

    $_outputStatus = '<div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu h_right">';
    $_outputStatus .= '<li><a href="#" onclick="view_transfer(' . $aRow['id'] . '); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
    if (empty($aRow['tbltransfer_warehouse.warehouseman_id']) && $hasPermissionEdit && !$aRow['import_outsource_id']) {
        // $_outputStatus .= '<li><a href="' . admin_url('transfer/detail/' . $aRow['id']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
    }
    if($aRow['productions_capacity_id'] > 0){
        $_outputStatus .= '<li><a target="_blank" href="' . admin_url('transfer/print_transfer_nvl/' . $aRow['id']) . '" ><i class="fa fa-file-pdf-o"></i> ' . _l('In phiếu') . '</a></li>';
    }elseif($aRow['order_id_new'] > 0){
        $_outputStatus .= '<li><a target="_blank" href="' . admin_url('transfer/print_transfer_product/' . $aRow['id']) . '" ><i class="fa fa-file-pdf-o"></i> ' . _l('In phiếu') . '</a></li>';
    }else{
        $_outputStatus .= '<li><a target="_blank" href="' . admin_url('transfer/print_transfer/' . $aRow['id']) . '" ><i class="fa fa-file-pdf-o"></i> ' . _l('In phiếu') . '</a></li>';
    }
    if ($hasPermissionDelete && !$aRow['import_outsource_id']) {
        if ($aRow['tbltransfer_warehouse.warehouseman_id']) {
            $test_quantity = get_table_where('tblwarehouse_product',
                array('import_id' => $aRow['id'], 'quantity_export >' => 0, 'type_export ' => 2), '', 'row');
            if (empty($test_quantity)) {
                $_outputStatus .= '<li><a href="' . admin_url('transfer/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
            }
        } else {
            $_outputStatus .= '<li><a href="' . admin_url('transfer/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
        }
    }
    $_outputStatus .= '</ul></div>';
    $row[] = $_outputStatus;

    $output['aaData'][] = $row;
}
