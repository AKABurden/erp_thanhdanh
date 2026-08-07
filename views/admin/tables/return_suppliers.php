    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('return_suppliers', '', 'delete');
    $hasPermissionEdit = has_permission('return_suppliers', '', 'edit');

    $custom_fields = get_table_custom_fields('return_suppliers');
    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tblreturn_suppliers.code',
        'tblsuppliers.company',
        'tblreturn_suppliers.total',
        'tblreturn_suppliers.staff_create',
        'tblreturn_suppliers.treatment_methods',
        'tblreturn_suppliers.status',
        'tblreturn_suppliers.warehouseman_id',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblreturn_suppliers';
    $where        = [];
    $filter = [];
    $join = [
        'LEFT JOIN tblsuppliers ON tblsuppliers.id=tblreturn_suppliers.suppliers_id',
    ];
    if ($this->ci->input->post('filterStatus')) {
        if (is_numeric($this->ci->input->post('filterStatus'))) {
            if ($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblreturn_suppliers.status = 2');
            } else if ($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblreturn_suppliers.status = 1');
            } else if ($this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblreturn_suppliers.warehouseman_id <> 0');
            } else if ($this->ci->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblreturn_suppliers.warehouseman_id = 0');
            }
        }
    }
    if ($this->ci->input->post('search_code')) {
        if (is_numeric($this->ci->input->post('search_code'))) {
            array_push($where, 'AND tblreturn_suppliers.id = ' . $this->ci->input->post('search_code'));
        }
    }
    if ($this->ci->input->post('search_staff')) {
        array_push($where, 'AND  tblreturn_suppliers.staff_create IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
    }
    if ($this->ci->input->post('search_id_suppliers')) {
        array_push($where, 'AND tblreturn_suppliers.suppliers_id IN (' . implode(', ', $this->ci->input->post('search_id_suppliers')) . ')');
    }
    $search_date = $this->ci->input->post('search_date');
    if ($search_date) {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblreturn_suppliers.date_create BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }

    $suppliers_id = $this->ci->input->post('suppliers_id');
    if (is_numeric($suppliers_id)) {
        array_push($where, 'AND tblreturn_suppliers.suppliers_id = ' . $this->ci->input->post('suppliers_id'));
    }
    if (has_permission('return_suppliers', '', 'view_own') && !is_admin()) {
        array_push($where, 'AND tblreturn_suppliers.staff_create = ' . get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        'tblreturn_suppliers.date',
        'tblreturn_suppliers.prefix',
        'tblreturn_suppliers.history_status',
        'tblreturn_suppliers.not_new_by_staff',
        'tblreturn_suppliers.suppliers_id',
        'tblreturn_suppliers.id as id_returns',
        'tblreturn_suppliers.other_payslips',
    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j = 0;
    $currentPage = $this->_instance->input->post('start');
    $currentall = $output['iTotalRecords'];
    foreach ($rResult as $key => $aRow) {
        //kiểm tra số lượng còn đã xuất / để xác nhận là đã xuất hay chưa
        $test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $aRow['id_returns'], 'quantity_export >' => 0, 'type_export ' => 15), '', 'row');
        $j++;
        $row = [];
        $not_new_by_staff = explode(',', $aRow['not_new_by_staff']);

        if (!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblreturn_suppliers.status'] == 1) {
            $row[] = _d($aRow['date']) . ' <br><span class="wap-new">new</span>';
            $row['DT_RowClass'] = 'alert-new';
        } else {
            $row[] = _d($aRow['date']);
        }

        $return_suppliers  = $aRow['prefix'] . $aRow['tblreturn_suppliers.code'];
        $isPerson = false;
        $return_suppliers = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_returns'] . '); return false;" >' . $return_suppliers . '</a>';
        $row[] = $return_suppliers;

        $row[] = '<a href="#" onclick="int_suppliers_view(' . $aRow['suppliers_id'] . '); return false;">' . $aRow['tblsuppliers.company'] . '</a>';
        $row[] = '<div class="text-right">' . number_format($aRow['tblreturn_suppliers.total']) . '</div>';
        $staff = staff_profile_image($aRow['tblreturn_suppliers.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($aRow['tblreturn_suppliers.staff_create'])
        )) . get_staff_full_name($aRow['tblreturn_suppliers.staff_create']);
        $row[] = ($aRow['tblreturn_suppliers.staff_create'] ? $staff : '');
        if ($aRow['tblreturn_suppliers.treatment_methods'] == 1) {
            $type = 'warning';
            $treatment_methods = _l('ch_pay_down');
            $other_payslips_name = '';
            if (!empty($aRow['other_payslips'])) {
                $other_payslips = get_table_where('tblother_payslips_coupon', array('id' => $aRow['other_payslips']), '', 'row');
                if (!empty($other_payslips_name)) {
                    $other_payslips_name = $other_payslips->prefix . '-' . $other_payslips->code;
                }
            }
        } elseif ($aRow['tblreturn_suppliers.treatment_methods'] == 2) {
            $type = 'info';
            $treatment_methods = _l('ch_debt_deduction');
            $other_payslips_name = '';
        }
        $treatment_methods_ch = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblreturn_suppliers.treatment_methods'] . '">' . $treatment_methods . '</span>';
        $row[] = $treatment_methods_ch . '<br>' . $other_payslips_name;

        if ($aRow['tblreturn_suppliers.status'] == 1) {
            $type = 'warning';
            $status = _l('dont_approve');
        } elseif ($aRow['tblreturn_suppliers.status'] == 2) {
            $type = 'info';
            $status = _l('ch_confirm_22');
        }
        $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblreturn_suppliers.status'] . '">' . $status . '';
        if (has_permission('return_suppliers', '', 'approve')) {
            if ($aRow['tblreturn_suppliers.status'] == 1) {
                $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblreturn_suppliers.status'] . ',' . $aRow['id_returns'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
            } else {
                $status .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
            }
        }
        $status .= '</a>
                </span><br>';
        $_data = '';
        $history_status = explode('|', $aRow['history_status']);

        foreach ($history_status as $key => $value) {
            $data = explode(',', $value);
            if (is_numeric($data[0])) {
                $_data .= staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => ' Vào lúc: ' . _dt($data[1])
                )) . get_staff_full_name($data[0]) . '<br>';
            }
        }

        $row[] = $status . $_data;

        $_data = '';
        if (has_permission('return_suppliers', '', 'approve_warehouse')) {
            if ($aRow['tblreturn_suppliers.status'] == 2 && !empty($aRow['tblreturn_suppliers.staff_create'])) {

                $button = _l('ch_warehouse_nd');
                $title = _l('warehouseman_confirm');
                $type = 'fa-square-o';
                if ($aRow['tblreturn_suppliers.warehouseman_id']) {
                    $button = _l('ch_warehouse_d');
                    $title = _l('warehouseman_confirm_cancel');
                    $type = 'fa-check-square-o';
                    $_data = '<a href="" onclick="confirm_warehous(' . $aRow['id_returns'] . ',' . $aRow['tblreturn_suppliers.warehouseman_id'] . ');return false;" class=" btn btn-info btn-icon "  data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" data-original-title="' . $title . '"><i class="fa  ' . $type . '"></i> ' . $button . '</a>' . ($aRow['tblreturn_suppliers.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tblreturn_suppliers.warehouseman_id']) . '</span>' : '');
                } else {
                    $_data = '<span class="inline-block label label-warning" task-status-table="">Số lượng không đủ</span>';
                    if (test_quantity_return($aRow['id_returns'])) {
                        $_data = '<a href="" onclick="confirm_warehous(' . $aRow['id_returns'] . ',' . $aRow['tblreturn_suppliers.warehouseman_id'] . ');return false;" class=" btn btn-info btn-icon "  data-toggle="tooltip" data-loading-text="' . _l('wait_text') . '" data-original-title="' . $title . '"><i class="fa  ' . $type . '"></i> ' . $button . '</a>' . ($aRow['tblreturn_suppliers.warehouseman_id'] ? '<br>' . _l('warehouseman') . ': <span style="color: red;">' . get_staff_full_name($aRow['tblreturn_suppliers.warehouseman_id']) . '</span>' : '');
                    }
                }
            }
        }
        $row[] = $_data;


        // Custom fields add values
        foreach ($customFieldsColumns as $customFieldColumn) {
            $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
        }

        $_outputStatus = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
        $_outputStatus .= '<li><a href="#" onclick="view_return_suppliers(' . $aRow['id_returns'] . '); return false;" ><i class="fa fa-eye"></i> ' . _l('view_order') . '</a></li>';
        // if ($aRow['tblreturn_suppliers.status'] < 2 && $hasPermissionEdit) {
        //     if (!empty($aRow['tblreturn_suppliers.id_order'])) {
        //         $_outputStatus .= '<li><a href="' . admin_url('return_suppliers/detail_order/' . $aRow['id_returns']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
        //     } else {
        //         $_outputStatus .= '<li><a href="' . admin_url('return_suppliers/detail/' . $aRow['id_returns']) . '" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
        //     }
        // }
        $_outputStatus .= '<li><a href="' . admin_url('return_suppliers/print_pdf/' . $aRow['id_returns']) . '" target="_blank"><i class="fa fa-print"></i> ' . _l('print_vote') . '</a></li>';
        if ($hasPermissionDelete && empty($test_quantity)) {
            $_outputStatus .= '<li><a href="' . admin_url('return_suppliers/delete/' . $aRow['id_returns']) . '" class="text-danger delete-reminds"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
        }
        $_outputStatus .= '</ul></div>';
        $row[] = $_outputStatus;

        // $row['DT_RowClass'] = 'has-row-options';


        $row = hooks()->apply_filters('import_table_row_data', $row, $aRow);

        $output['aaData'][] = $row;
    }
