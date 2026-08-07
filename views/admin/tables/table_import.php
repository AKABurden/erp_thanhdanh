    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('import', '', 'delete');
    $hasPermissionEdit = has_permission('import', '', 'edit');

    $custom_fields = get_table_custom_fields('imports');
    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tblimport.code',
        'tblsuppliers.company',
        'tblimport.id_order',
        'tblimport.total',
        'tblimport.status_qc',
        'tblimport.warehouseman_id',
        'tblimport.invoice_id',
        'tblimport.status_pay',
        'tblimport.staff_create',
        'tblimport.status',
        // 'tblimport.red_invoice',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblimport';
    $where        = [];
    $filter = [];
    $join = [
        'LEFT JOIN tblsuppliers ON tblsuppliers.id=tblimport.suppliers_id',
    ];
    foreach ($custom_fields as $key => $field) {
        $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
        array_push($customFieldsColumns, $selectAs);
        array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
        array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON tblimport.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
    $join = hooks()->apply_filters('import_table_sql_join', $join);

    if ($this->ci->input->post('filterStatus')) {
        if (is_numeric($this->ci->input->post('filterStatus'))) {
            if ($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblimport.red_invoice <> 0');
            } else if ($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblimport.red_invoice = 0');
            } else if ($this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblimport.status_qc = 2');
            } else if ($this->ci->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblimport.status_qc = 1');
            } else if ($this->ci->input->post('filterStatus') == 5) {
                array_push($where, 'AND tblimport.warehouseman_id <> 0');
            } else if ($this->ci->input->post('filterStatus') == 6) {
                array_push($where, 'AND tblimport.warehouseman_id = 0');
            } else if ($this->ci->input->post('filterStatus') == 7) {
                array_push($where, 'AND (tblimport.status_pay = 2 or tblpurchase_invoice.status = 2)');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblimport.red_invoice');
            } else if ($this->ci->input->post('filterStatus') == 8) {
                array_push($where, 'AND (tblimport.status_pay = 1 or tblpurchase_invoice.status = 1)');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblimport.red_invoice');
            } else if ($this->ci->input->post('filterStatus') == 9) {
                array_push($where, 'AND (tblimport.status_pay = 0 or tblpurchase_invoice.status = 0)');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblimport.red_invoice');
            }
        }
    }
    if ($this->ci->input->post('search_code')) {
        if (is_numeric($this->ci->input->post('search_code'))) {
            array_push($where, 'AND tblimport.id = ' . $this->ci->input->post('search_code'));
        }
    }
    if ($this->ci->input->post('search_staff')) {
        array_push($where, 'AND  tblimport.staff_create IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
    }
    if ($this->ci->input->post('search_id_suppliers')) {
        array_push($where, 'AND tblimport.suppliers_id IN (' . implode(', ', $this->ci->input->post('search_id_suppliers')) . ')');
    }
    $items_search = $this->ci->input->post('custom_item_select');
    $type_items = $this->ci->input->post('type_items');
    if (!empty($items_search)) {
        array_push($where, 'AND EXISTS (
            SELECT tblimport_items.id_import
            FROM tblimport_items
            WHERE tblimport_items.id_import = tblimport.id
            AND tblimport_items.product_id = ' . $items_search . ' AND tblimport_items.type = "'.$type_items.'"
        )');
    }

    $search_date = $this->ci->input->post('search_date');
    if ($search_date) {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblimport.date_create BETWEEN "' . to_sql_date($data_start[0]) . ' 00:00:00" and "' . to_sql_date($data_start[1]) . ' 23:59:59"');
    }

    $suppliers_id = $this->ci->input->post('suppliers_id');
    if (is_numeric($suppliers_id)) {
        array_push($where, 'AND tblimport.suppliers_id = ' . $this->ci->input->post('suppliers_id'));
    }
    if (has_permission('import', '', 'view_own') && !is_admin()) {
        array_push($where, 'AND tblimport.staff_create = ' . get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        'tblimport.date',
        'tblimport.prefix',
        'tblimport.history_status',
        'tblimport.not_new_by_staff',
        'tblimport.suppliers_id',
        'tblimport.status_pay',
        'tblimport.price_other_expenses',
        'tblimport.amount_paid',
        'tblimport.id as id_imports',
        'tblimport.type_plan as type_plan_import',
        'tblimport.date_qc as date_qc',
    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j = 0;
    $currentPage = $this->_instance->input->post('start');
    $currentall = $output['iTotalRecords'];
    $footer_data = array(
        'all' => 0,
        'total' => 0,
    );
    foreach ($rResult as $key => $aRow) {
        //kiểm tra số lượng còn đã xuất / để xác nhận là đã xuất hay chưa
        $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $aRow['id_imports'], 'quantity_export >' => 0, 'type_export ' => 1), '', 'row');
        $j++;
        $row = [];
        $not_new_by_staff = explode(',', $aRow['not_new_by_staff']);

        if (!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblimport.status'] == 1) {
            $row[] = _d($aRow['date']) . ' <br><span class="wap-new">new</span>';
            $row['DT_RowClass'] = 'alert-new';
        } else {
            $row[] = _d($aRow['date']);
        }

        if ($aRow['type_plan_import'] == 1) {
            $productions_capacity = '<span class="inline-block label label-success">' . _l('ch_productions_capacity') . '</span><br>';
        } else {
            $productions_capacity = '';
        }
        $import  = $aRow['prefix'] . '-' . $aRow['tblimport.code'] . '<br>' . $productions_capacity;
        $isPerson = false;
        $import = '<a href="#" onclick="view_import(' . $aRow['id_imports'] . '); return false;" >' . $import . '</a>';
        $row[] = $import;

        $row[] = '<a href="#" onclick="int_suppliers_view(' . $aRow['suppliers_id'] . '); return false;">' . $aRow['tblsuppliers.company'] . '</a>';

        if (!empty($aRow['tblimport.id_order'])) {
            $order = get_table_where('tblpurchase_order', array('id' => $aRow['tblimport.id_order']), '', 'row');
            $_order = '<a href="#" onclick="view_purchase_order(' . $aRow['tblimport.id_order'] . '); return false;" >' . $order->prefix . '-' . $order->code . '</a>';
            $purchases = '';
            if (!empty($order->id_purchase_proce)) {
                $purchase_proce = get_table_where('tblpurchases', array('id' => $order->id_purchase_proce), '', 'row');
                $purchases = '<a  onclick="view_purchases(' . $purchase_proce->id . '); return false;" ><br><span class="inline-block label label-info">' . $purchase_proce->prefix . $purchase_proce->code . '</span>';
            }
            if (!empty($order->id_purchases)) {
                $purchases = format_purchase_order_v2($aRow['tblimport.id_order']);
            }

            $row[] = $_order . $purchases;
        } else {
            $row[] = '';
        }

        $row[] = '<div class="text-right">' . formatNumber($aRow['tblimport.total']) . '</div>';
        $footer_data['total'] += $aRow['tblimport.total'];

        $staff = staff_profile_image($aRow['tblimport.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($aRow['tblimport.staff_create'])
        )) . get_staff_full_name($aRow['tblimport.staff_create']);
        $row[] = ($aRow['tblimport.staff_create'] ? $staff : '');


        // if ($aRow['tblimport.status'] == 1) {
        //     $type = 'warning';
        //     $status = _l('dont_approve');
        // } elseif ($aRow['tblimport.status'] == 2) {
        //     $type = 'info';
        //     $status = _l('ch_confirm_22');
        // }
        // $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblimport.status'] . '">' . $status . '';
        // if (has_permission('import', '', 'approve')) {
        //     if ($aRow['tblimport.status'] == 1) {
        //         $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblimport.status'] . ',' . $aRow['id_imports'] . '); return false">
        //             <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
        //     } else {
        //         $status .= '<a href="javacript:void(0)">
        //             <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
        //     }
        // }
        // $status .= '</a>
        //         </span><br>';
        // $_data = '';
        // $history_status = explode('|', $aRow['history_status']);

        // foreach ($history_status as $key => $value) {
        //     $data = explode(',', $value);
        //     if (is_numeric($data[0])) {
        //         $_data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
        //             'data-toggle' => 'tooltip',
        //             'data-title' => ' Vào lúc: ' . _dt($data[1])
        //         )) . get_staff_full_name($data[0]) . '<br>';
        //     }
        // }

        // $row[] = $status . $_data;

        if ($aRow['tblimport.status_qc'] == 0) {
            $type = 'warning';
            $status_qc = _l('dont_approve_qc');
        } elseif ($aRow['tblimport.status_qc'] > 0) {
            $type = 'info';
            $status_qc = _l('ch_confirm__qc');
        }
        $status_qc = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblimport.status_qc'] . '">' . $status_qc . '';
        // if (has_permission('import', '', 'approve_qc')) {
        //     if ($aRow['tblimport.status_qc'] == 0) {
        //         $status_qc .= '
        //         <a href="' . admin_url('hand_over/handling_delivery_records/0/') . $aRow['id_imports'] . '" class="tnh-modal">
        //             <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
        //     } else {
        //         $status_qc .= '<a href="javacript:void(0)">
        //             <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
        //     }
        // }
        if (has_permission('import', '', 'approve_qc')) {
            if ($aRow['tblimport.status_qc'] == 0) {
                $status_qc .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblimport.status_qc'] . ',' . $aRow['id_imports'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
            } else {
                $status_qc .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
            }
        }
        $status_qc .= '</a>
                </span><br>';
        $_data = '';
        if ($aRow['tblimport.status_qc'] > 0) {
            $_data = staff_profile_image($aRow['tblimport.status_qc'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => ' Vào lúc: ' . _dt($aRow['date_qc'])
            )) . get_staff_full_name($aRow['tblimport.status_qc']) . '<br>';
        }
        $row[] = $status_qc . $_data;
        $_data = '';
        if (has_permission('import', '', 'approve_warehouse')) {
            if ($aRow['tblimport.status'] == 2 && !empty($aRow['tblimport.staff_create']) && $aRow['tblimport.status_qc'] > 0) {

                $button = _l('ch_warehouse_nd');
                $title = _l('warehouseman_confirm');
                $type = 'fa-square-o';
                if ($aRow['tblimport.warehouseman_id']) {
                    $button = _l('ch_warehouse_d');
                    $title = _l('warehouseman_confirm_cancel');
                    $type = 'fa-check-square-o';
                }
                $_data = '<a href="" onclick="confirm_warehous(' . $aRow['id_imports'] . ',' . $aRow['tblimport.warehouseman_id'] . ');return false;" class=" btn btn-info btn-icon "  data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" data-original-title="' . $title . '"><i class="fa  ' . $type . '"></i> ' . $button . '</a>' . ($aRow['tblimport.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tblimport.warehouseman_id']) . '</span>' : '');
            }
        }
        if (!empty($test_quantity)) {
            $button = _l('ch_exsit_export');
            $title = _l('ch_exsit_export');
            $_data = '<a class=" btn btn-warning btn-icon no-drop"  data-toggle="tooltip"  data-original-title="' . $title . '">' . $button . '</a>' . ($aRow['tblimport.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tblimport.warehouseman_id']) . '</span>' : '');
        }
        $row[] = $_data;


        // Custom fields add values
        foreach ($customFieldsColumns as $customFieldColumn) {
            $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
        }
        
        $arrInvoice_id = explode(',', $aRow['tblimport.invoice_id']);
        $tagInvoice = '';
        foreach ($arrInvoice_id as $invoice_id) {
            $invoice = get_table_where('tblpurchase_invoice', ['id' => $invoice_id], '', 'row', '', 'code_invoice');
            if (!empty($invoice)) {
                $tagInvoice .= '<a href="#" onclick="view_invoice(' . $invoice_id . '); return false;" >' . $invoice->code_invoice . '</a><br>';
            }
        }
        $row[] = $tagInvoice;

        $_outputStatus = '<div class="dropdown" style="width: 160px;">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
        $_outputStatus .= '<li><a href="#" onclick="view_import(' . $aRow['id_imports'] . '); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
        // if ($aRow['tblimport.status_qc'] > 0) {
        // }
        $_outputStatus .= '<li><a href="' . admin_url('import/print_pdf/' . $aRow['id_imports']) . '" target="_blank"><i class="fa fa-print"></i> ' . _l('print_vote') . '</a></li>';
        if ($aRow['tblimport.status'] < 2 && $hasPermissionEdit) {
            if (!empty($aRow['tblimport.id_order'])) {
                $_outputStatus .= '<li><a href="' . admin_url('import/detail_order/' . $aRow['id_imports']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
            } else {
                $_outputStatus .= '<li><a href="' . admin_url('import/detail/' . $aRow['id_imports']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
            }
        }
        if ($hasPermissionDelete && empty($test_quantity)) {
            $_outputStatus .= '<li><a href="' . admin_url('import/delete/' . $aRow['id_imports']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete') . '</a></li>';
        }
        $_outputStatus .= '</ul></div>';
        $row[] = $_outputStatus;


        // $row['DT_RowClass'] = 'has-row-options';


        $row = hooks()->apply_filters('import_table_row_data', $row, $aRow);

        $output['aaData'][] = $row;
    }
    foreach ($footer_data as $key => $total) {
        $footer_data[$key] = formatNumber($total);
    }
    $output['sums'] = $footer_data;
