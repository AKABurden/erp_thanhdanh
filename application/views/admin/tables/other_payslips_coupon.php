    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('other_payslips_coupon', '', 'delete');
    $hasPermissionEdit = has_permission('other_payslips_coupon', '', 'edit');

    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tblother_payslips_coupon.code',
        'tblother_payslips_coupon.date',
        'tblother_payslips_coupon.objects',
        'tblother_payslips_coupon.objects_id',
        'tblother_payslips_coupon.type_vouchers',
        'tblother_payslips_coupon.vouchers_id',
        'tblcollect_categories.name',
        'tblpayment_modes.name',
        'tblother_payslips_coupon.total',
        'tblother_payslips_coupon.staff_id',
        'tblother_payslips_coupon.note',
        '1',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblother_payslips_coupon';
    $where        = [];
    if ($this->ci->input->post('filterStatus')) {
        if (is_numeric($this->ci->input->post('filterStatus'))) {
            if ($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblother_payslips_coupon.status = 1');
            } else if ($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblother_payslips_coupon.status = 0');
            } else if ($this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblother_payslips_coupon.objects = 1');
            } else if ($this->ci->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblother_payslips_coupon.objects = 2');
            } else if ($this->ci->input->post('filterStatus') == 5) {
                array_push($where, 'AND tblother_payslips_coupon.objects = 3');
            } else if ($this->ci->input->post('filterStatus') == 6) {
                array_push($where, 'AND tblother_payslips_coupon.objects = 4');
            }
        }
    }
    if ($this->ci->input->post('objects_idd')) {
        if (is_numeric($this->ci->input->post('objects_idd'))) {
            array_push($where, 'AND tblother_payslips_coupon.objects = ' . $this->ci->input->post('objects_idd'));
        }
    }
    if ($this->ci->input->post('objects_ids')) {
        if (is_numeric($this->ci->input->post('objects_ids'))) {
            array_push($where, 'AND tblother_payslips_coupon.objects_id = ' . $this->ci->input->post('objects_ids'));
        }
    }
    if ($this->ci->input->post('objects_texts')) {
        if ($this->ci->input->post('objects_texts')) {
            array_push($where, 'AND tblother_payslips_coupon.objects_text LIKE "%' . $this->ci->input->post('objects_texts') . '%"');
        }
    }
    $search_date = $this->ci->input->post('search_date');
    if ($search_date) {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblother_payslips_coupon.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }
    $filter = [];
    $join = [
        'LEFT JOIN tblpayment_modes ON tblpayment_modes.id=tblother_payslips_coupon.payment_modes',
        'LEFT JOIN tblcosts ON tblcosts.id=tblother_payslips_coupon.id_costs',
        'LEFT JOIN tblcollect_categories ON tblcollect_categories.id = tblother_payslips_coupon.id_costs',
    ];
    if (has_permission('other_payslips_coupon', '', 'view_own') && !is_admin()) {
        array_push($where, 'AND  tblother_payslips_coupon.staff_id = ' . get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        'tblother_payslips_coupon.id',
        'tblother_payslips_coupon.prefix',
        'tblother_payslips_coupon.objects_id',
        'tblother_payslips_coupon.date_create',
        'tblother_payslips_coupon.objects_text',
        'tblother_payslips_coupon.history_status',

    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j = 0;
    $footer_data = array(
        'all' => 0,
        'payment' => 0,
    );
    foreach ($rResult as $aRow) {
        $row = array();
        $j++;
        $footer_data['all']++;
        for ($i = 0; $i < count($aColumns); $i++) {
            if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                $_data = $aRow[strafter($aColumns[$i], 'as ')];
            } else {
                $_data = $aRow[$aColumns[$i]];
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.status') {
                if ($aRow['tblother_payslips_coupon.status'] == 0) {
                    $type = 'warning';
                    $status = _l('Chưa thu');
                } elseif ($aRow['tblother_payslips_coupon.status'] == 1) {
                    $type = 'info';
                    $status = _l('Đã thu');
                }
                $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblother_payslips_coupon.status'] . '">' . $status . '';
                if (has_permission('other_payslips_coupon', '', 'approve')) {
                    if ($aRow['tblother_payslips_coupon.status'] == 0) {
                        $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tblother_payslips_coupon.status'] . ',' . $aRow['id'] . '); return false">
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
            if ($aColumns[$i] == 'tblother_payslips_coupon.type_vouchers') {
                $_data = '';
                $type_vouchers[5]['name'] = 'Đơn đặt hàng bán';
                $type_vouchers[2]['name'] = 'Xuất kho khác';
                $type_vouchers[8]['name'] = 'Trả hàng';
                $type_vouchers[12]['name'] = 'Tạm ứng';
                if (!empty($aRow['tblother_payslips_coupon.type_vouchers'])) {
                    $_data = $type_vouchers[$aRow['tblother_payslips_coupon.type_vouchers']]['name'];
                }
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.code') {
                $payslips = $aRow['prefix'] . '-' . $aRow['tblother_payslips_coupon.code'];
                $_data = $payslips;
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.date') {
                $_data = _d($aRow['tblother_payslips_coupon.date']);
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.objects') {
                if ($aRow['tblother_payslips_coupon.objects'] == 1) {
                    $text = 'KHÁCH HÀNG';
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 2) {
                    $text = 'NHÀ CUNG CẤP';
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 3) {
                    $text = 'NHÂN VIÊN';
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 4) {
                    $text = 'KHÁC';
                }
                $_data = $text;
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.objects_id') {
                $_data = '';
                if ($aRow['tblother_payslips_coupon.objects'] == 2) {
                    $supplier = get_table_where('tblsuppliers', array('id' => $aRow['tblother_payslips_coupon.objects_id']), '', 'row');
                    $_data = '<a href="#" onclick="int_suppliers_view(' . $supplier->id . '); return false;" >' . $supplier->company . '</a>';
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 1) {
                    $client = get_table_where('tblclients', array('userid' => $aRow['tblother_payslips_coupon.objects_id']), '', 'row');
                    $_data = !empty($client->company) ? $client->company : '';
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 3) {
                    $_data = get_staff_full_name($aRow['tblother_payslips_coupon.objects_id']);
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 4) {
                    $_data = $aRow['objects_text'];
                }
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.total') {
                $footer_data['payment'] += $aRow['tblother_payslips_coupon.total'];
                $_data = formatNumber($aRow['tblother_payslips_coupon.total'],0);
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.vouchers_id') {
                $_data = '';

                if ($aRow['tblother_payslips_coupon.objects'] == 1) {
                    if ($aRow['tblother_payslips_coupon.type_vouchers'] == 5) {
                        if (!empty($aRow['tblother_payslips_coupon.objects_id']) && ($aRow['tblother_payslips_coupon.vouchers_id'] != 0)) {
                            $import = get_table_where('tbl_orders', array('id' => $aRow['tblother_payslips_coupon.vouchers_id']), '', 'row');
                            $_data = '<a href="' . admin_url('orders/view_order/' . $import->id) . '" data-toggle="modal" data-target="#myModal">' . $import->reference_no . '</a>';
                        }
                    }
                }
                if ($aRow['tblother_payslips_coupon.objects'] == 3) {
                    if ($aRow['tblother_payslips_coupon.type_vouchers'] == 12) {
                        if (!empty($aRow['tblother_payslips_coupon.objects_id']) && ($aRow['tblother_payslips_coupon.vouchers_id'] != 0)) {
                            $import = get_table_where('tblother_payslips', array('id' => $aRow['tblother_payslips_coupon.vouchers_id']), '', 'row');
                            $_data = $import->prefix . '-' . $import->code;
                        }
                    }
                }
            }
            if ($aColumns[$i] == 'tblother_payslips_coupon.staff_id') {
                $_data = staff_profile_image($aRow['tblother_payslips_coupon.staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => ' Vào lúc: ' . _dt($aRow['date_create'])
                )) . get_staff_full_name($aRow['tblother_payslips_coupon.staff_id']) . '<br>';
            }


            if ($aColumns[$i] == '1') {
                $_data = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
                if ($hasPermissionEdit && ($aRow['tblother_payslips_coupon.type_vouchers'] != 12)) {
                    $_data .= '<li><a href="#" onclick="edit_other_payslips_coupon(' . $aRow['id'] . '); return false;" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
                }
                $_data .= '<li><a href="' . admin_url('other_payslips_coupon/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i> ' . _l('print_vote') . '</a></li>';

                $invoice = '';
                if ($aRow['tblother_payslips_coupon.objects'] == 2) {
                    if (!empty($aRow['tblother_payslips_coupon.type_vouchers'])) {
                        if ($aRow['tblother_payslips_coupon.type_vouchers'] == 1) {
                            if (!empty($aRow['tblother_payslips_coupon.vouchers_id'])) {

                                $ktr_import = get_table_where('tblpurchase_order', array('id' => $aRow['tblother_payslips_coupon.vouchers_id']), '', 'row');
                                $invoice = $ktr_import->red_invoice;
                            }
                        }
                    }
                }
                if ($hasPermissionDelete && empty($invoice)) {
                    $_data .= '<li><a href="' . admin_url('other_payslips_coupon/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
                }
                $_data .= '</ul></div>';
            }
            $row[] = $_data;
        }
        $output['aaData'][] = $row;
    }
    foreach ($footer_data as $key => $total) {
        $footer_data[$key] = formatNumber($total,0);
    }
    $output['sums'] = $footer_data;
