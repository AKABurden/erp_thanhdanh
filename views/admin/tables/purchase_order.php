    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('purchase_order', '', 'delete');
    $hasPermissionEdit = has_permission('purchase_order', '', 'edit');
    $hasPermissionadd = has_permission('purchase_order', '', 'create');
    $hasPermission_import = has_permission('import', '', 'create');

    $custom_fields = get_table_custom_fields('purchase_order');
    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        '4',
        'tblpurchase_order.date',
        'tblsuppliers.company',
        'tblpurchase_order.code',
        // '1',
        'tblpurchase_order.totalAll_suppliers',
        'tblpurchase_order.total_dqd',
        '(tblpurchase_order.price_other_expenses + tblpurchase_order.amount_paid) as total_expenses',
        'tblpurchase_order.red_invoice',
        'tblpurchase_order.status',
        'tblpurchase_order.note',
        // 'tblpurchase_order.status_pay',
        // '2',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpurchase_order';
    $where        = [];
    $filter = [];
    $join         = array(
        'LEFT JOIN tblcurrencies ON tblcurrencies.id=tblpurchase_order.currency',
        'LEFT JOIN tblsuppliers ON tblsuppliers.id=tblpurchase_order.suppliers_id',
        'LEFT JOIN tbltickets_priorities ON tbltickets_priorities.priorityid = tblpurchase_order.id_tickets_priorities'
    );
    foreach ($custom_fields as $key => $field) {
        $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
        array_push($customFieldsColumns, $selectAs);
        array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
        array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON tblpurchase_order.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
    $join = hooks()->apply_filters('purchases_table_sql_join', $join);

    if ($this->ci->input->post('filterStatus')) {
        if (is_numeric($this->ci->input->post('filterStatus'))) {
            if ($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tblpurchase_order.status = 1');
            } else if ($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblpurchase_order.status = 2');
            } else if ($this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblpurchase_order.status = 3');
            } else if ($this->ci->input->post('filterStatus') == 4) {
                array_push($where, 'AND tblpurchase_order.red_invoice <> 0');
            } else if ($this->ci->input->post('filterStatus') == 5) {
                array_push($where, 'AND tblpurchase_order.red_invoice = 0');
            } else if ($this->ci->input->post('filterStatus') == 6) {
                array_push($where, 'AND ((tblpurchase_order.status_pay = 2 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 2))');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice');
            } else if ($this->ci->input->post('filterStatus') == 7) {
                array_push($where, 'AND ((tblpurchase_order.status_pay = 1 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 1))');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice');
            } else if ($this->ci->input->post('filterStatus') == 8) {
                array_push($where, 'AND ((tblpurchase_order.status_pay = 0 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 0))');
                array_push($join, 'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice');
            } else if ($this->ci->input->post('filterStatus') == 9) {
                array_push($where, 'AND tblpurchase_order.id NOT IN (select tblimport.id_order from tblimport)');
            } else if ($this->ci->input->post('filterStatus') == 10) {
                array_push($where, 'AND SUBSTRING(tblpurchase_order.cancel, 1, 5) != "1foso" AND tblpurchase_order.id IN (select tblimport.id_order from tblimport)');
            } else if ($this->ci->input->post('filterStatus') == 11) {
                array_push($where, 'AND SUBSTRING(tblpurchase_order.cancel, 1, 5) = "1foso"');
            }
        }
    }
    $suppliers_id = $this->ci->input->post('suppliers_id');
    $type_suppliert = $this->ci->input->post('type_suppliert');
    $items_search = $this->ci->input->post('custom_item_select');
    $type_items = $this->ci->input->post('type_items');



    if (is_numeric($suppliers_id)) {
        array_push($where, 'AND tblpurchase_order.suppliers_id = ' . $this->ci->input->post('suppliers_id'));
    }
    if (!empty($items_search)) {
        array_push($where, 'AND EXISTS (
            SELECT tblpurchase_order_items.id_purchase_order
            FROM tblpurchase_order_items
            WHERE tblpurchase_order_items.id_purchase_order = tblpurchase_order.id
            AND tblpurchase_order_items.product_id = ' . $items_search . ' AND tblpurchase_order_items.type = "'.$type_items.'"
        )');
    }
    if ($this->ci->input->post('search_code')) {
        if (is_numeric($this->ci->input->post('search_code'))) {
            array_push($where, 'AND tblpurchase_order.id = ' . $this->ci->input->post('search_code'));
        }
    }
    if ($this->ci->input->post('search_staff')) {
        array_push($where, 'AND  tblpurchase_order.staff_create IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
    }
    if ($this->ci->input->post('search_id_suppliers')) {
        array_push($where, 'AND tblpurchase_order.suppliers_id IN (' . implode(', ', $this->ci->input->post('search_id_suppliers')) . ')');
    }
    if ($this->ci->input->post('search_priorities')) {
        array_push($where, 'AND tblpurchase_order.id_tickets_priorities IN (' . implode(', ', $this->ci->input->post('search_priorities')) . ')');
    }
    $search_date = $this->ci->input->post('search_date');
    if ($search_date) {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblpurchase_order.date_create BETWEEN "' . to_sql_date($data_start[0]) . ' 00:00:00" and "' . to_sql_date($data_start[1]) . ' 23:59:59"');
    }
    if (has_permission('purchase_order', '', 'view_own') && !is_admin()) {
        array_push($where, 'AND  tblpurchase_order.staff_create = ' . get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['
        tblpurchase_order.id,
        tbltickets_priorities.name,
        tbltickets_priorities.color,
        tblpurchase_order.id_tickets_priorities,
        tblpurchase_order.prefix,
        tblpurchase_order.status_pay,
        tblpurchase_order.amount_paid,
        tblpurchase_order.price_other_expenses,
        tblpurchase_order.history_status,
        tblpurchase_order.suppliers_id,
        tblpurchase_order.cancel,
        tblpurchase_order.id_purchases,
        tblpurchase_order.check_purchase_all,
        tblpurchase_order.not_new_by_staff,
        tblpurchase_order.type_order,
        tblcurrencies.name as name_curren,
        (SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id = tblpurchase_order.id_internal_proposal LIMIT 1) as internal_proposal,
        tblpurchase_order.type_plan as type_plan_purchase_order
    ']);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $footer_data = array(
        'total' => 0,
        'pay' => 0,
    );
    $j = 0;
    foreach ($rResult as $aRow) {
        // kiểm tra đã tạo phiếu nhập hết hay chưa
        $count_items_import = get_items_import($aRow['id']);
        $import = get_table_where('tblimport', array('id_order' => $aRow['id']), '', 'row');
        $j++;
        $row = [];

        $not_new_by_staff = explode(',', $aRow['not_new_by_staff']);
        if ($type_suppliert == 1) {
            if (($aRow['tblpurchase_order.red_invoice'] == 0) && (is_numeric($suppliers_id) && ($aRow['tblpurchase_order.status'] > 2) && ($aRow['status_pay'] != 2))) {
                $importss = get_table_where('tblimport', array('id_order' => $aRow['id'], 'warehouseman_id !=' => 0), '', 'row');
                if (!empty($importss)) {
                    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
                } else {
                    $row[] = '';
                }
            } else {
                $row[] = '';
            }
        } else {
            if (($aRow['tblpurchase_order.red_invoice'] == 0) && (is_numeric($suppliers_id) && ($aRow['tblpurchase_order.status'] > 2) && ($aRow['status_pay'] != 2) && $aRow['amount_paid'] == 0)) {
                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
            } else {
                $row[] = '';
            }
        }
        $checkDateWarning = true;
        // $row[] = $j;


        if ($aRow['type_plan_purchase_order'] == 1) {
            $productions_capacity = '<span class="inline-block label label-success">' . _l('Kế Hoạch NVL') . '</span><br>';
        } else {
            $productions_capacity = '';
        }
        $purchases  = $aRow['prefix'] . '-' . $aRow['tblpurchase_order.code'];
        $isPerson = false;
        $purchases = '';
        if ($count_items_import == 0) {
            $checkDateWarning = false;
        }
        if (!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblpurchase_order.status'] == 1 && $aRow['cancel'] == 0) {
            // $purchases.= '<br><p class="wap-new">new</p>';
            // $row['DT_RowClass'] = 'alert-new';
            $checkDateWarning = false;
        } else if ($aRow['id_tickets_priorities'] && $checkDateWarning) {
            $purchases .= '<br><p style="background: ' . $aRow['color'] . ';color: #fff;font-weight: 300;border-radius: 10px;padding: 0px 10px;">' . $aRow['name'] . '</p>';
        }

        $checkStatus = true;
        $_outputStatus = '<div class="dropdown H_drop " style="">
            <button class="btn btn-primary dropdown-toggle " type="button" data-toggle="dropdown">' . _l('action') . '
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
        $_outputStatus .= '<li><a href="#" onclick="view_purchase_order(' . $aRow['id'] . '); return false;" ><i class="fa fa-eye" aria-hidden="true"></i>' . _l('view_order') . '</a></li>';

        if ($hasPermissionadd) {
            $_outputStatus .= '<li><a href="' . admin_url('purchase_order/coppy_purchases_detail/' . $aRow['id']) . '" ><i class="fa fa-copy" aria-hidden="true"></i> ' . _l('coppy_purchase_order') . '</a></li>';
        }
        if (empty($import) && $hasPermissionEdit) {
            if (!empty($aRow['id_purchases'])) {
                if ($aRow['check_purchase_all'] == 1) {
                    // $_outputStatus .= '<li><a href="' . admin_url('purchase_order/purchases_detail_all/' . $aRow['id']) . '" ><i class="fa fa-pencil" aria-hidden="true"></i>' . _l('edit_order') . '</a></li>';
                } else {
                    $_outputStatus .= '<li><a href="' . admin_url('purchase_order/purchases_detail/' . $aRow['id']) . '" ><i class="fa fa-pencil" aria-hidden="true"></i>' . _l('edit_order') . '</a></li>';
                }
            } else {
                $_outputStatus .= '<li><a href="' . admin_url('purchase_order/detail/' . $aRow['id']) . '" ><i class="fa fa-pencil" aria-hidden="true"></i>' . _l('edit_order') . '</a></li>';
            }
        }

        if (($aRow['status_pay'] != 2) && ($aRow['tblpurchase_order.red_invoice'] == 0) && ($aRow['tblpurchase_order.status'] > 2) && has_permission('pay_slip', '', 'create')) {
            $importss = get_table_where('tblimport', array('id_order' => $aRow['id'], 'warehouseman_id !=' => 0), '', 'row');
            if (!empty($importss)) {
                $_outputStatus .= '<li><a onclick="payment(' . $aRow['id'] . ')"><i class="fa fa-file width-icon-actions"></i> ' . _l('ch_pay_slip') . '</a></li>';
            }
        }
        // if($aRow['cancel'] != 0 || $aRow['tblpurchase_order.status'] < 3 || $count_items_import <= 0)
        // {
        //     $checkStatus = false;
        // }
        if ($aRow['cancel'] != 0 || $count_items_import <= 0) {
            $checkStatus = false;
        }
        if (isset($checkStatus) && $checkStatus === true && $hasPermission_import) {
            $_outputStatus .= '<li><a href="' . admin_url('import/create_detail/' . $aRow['id']) . '"><i class="fa fa-inbox" aria-hidden="true"></i> ' . _l('ch_importsadd') . '</a></li>';
        }
        $_outputStatus .= '<li><a href="' . admin_url('purchase_order/print_pdf_qc/' . $aRow['id']) . '" target="_blank"><i class="fa fa-print"></i> ' . _l('print_vote') . ' kiểm tra</a></li>';

        $_outputStatus .= '<li><a href="' . admin_url('purchase_order/print_pdf/' . $aRow['id']) . '" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> ' . _l('print_vote') . '</a></li>';
        if ($hasPermissionDelete && ($aRow['tblpurchase_order.red_invoice'] == 0) && ($aRow['status_pay'] == 0)) {
            $_outputStatus .= '<li><a href="' . admin_url('purchase_order/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times" aria-hidden="true"></i>' . _l('delete_order') . '</a></li>';
        }
        $_outputStatus .= '<li><input type="hidden" value="' . $aRow['id'] . '" name="idd" class="idd"></li>';
        $_outputStatus .= '</ul></div>';
        $row[] = _d($aRow['tblpurchase_order.date']) . '<br>' . $_outputStatus . $purchases;
        $purchases = '';
        $internal_proposal_text = '';

        if (!empty($aRow['internal_proposal'])) {
                $internal_proposal = explode('__', $aRow['internal_proposal']);
                $internal_proposal_text .= '<p style="margin-bottom: 2px;"><span class="label label-success mtop5 text-center" style="padding-top: 1px;padding-bottom: 1px;">Phiếu: <a class="c_modal" href="' . (admin_url('internal_proposal/view/' . $internal_proposal[0])) . '">' . $internal_proposal[1] . '</a></span></p>';
            }
        $row[] = '<a onclick="view_purchase_order(' . $aRow['id'] . '); return false;" >' . $aRow['prefix'] . '-' . $aRow['tblpurchase_order.code'] . '</a>' . '<br>' . $productions_capacity  . $internal_proposal_text;
        $type_order = '';
        if ($aRow['type_order'] == 1) {
            $type_order = '<br><span class="inline-block label label-warning">Trong nước</span>';
        } else {
            $type_order = '<br><span class="inline-block label label-info">Nhập khẩu</span>';
        }
        $row[] = $aRow['tblsuppliers.company'] . $purchases . $type_order;

        // $row[] = format_purchase_order($aRow['id']);

        $row[] = formatNumber($aRow['tblpurchase_order.totalAll_suppliers']) . ' ' . $aRow['name_curren'];
        $row[] = formatNumber($aRow['tblpurchase_order.total_dqd']) . ' VNĐ';
        $footer_data['total'] += $aRow['tblpurchase_order.total_dqd'];
        // $row[] = formatNumber($aRow['total_expenses']);
        $status_other = '';
        if ($aRow['total_expenses'] > 0) {
            $title = '';
            if ($aRow['price_other_expenses'] > 0) {
                $title .= _l('ch_other_expenses') . ': ' . formatMoney($aRow['price_other_expenses']) . '<br>';
            }
            if ($aRow['amount_paid'] > 0) {
                $title .= _l('ch_status_pays_slip_import') . ': ' . formatMoney($aRow['amount_paid']);
            }
            if (!empty($aRow['tblpurchase_order.red_invoice'])) {
                $ktr = get_table_where('tblpurchase_invoice', array('id' => $aRow['tblpurchase_order.red_invoice']), '', 'row');
                if (!empty($ktr) && $ktr->status == 2) {
                    $amount_paid = $aRow['tblpurchase_order.totalAll_suppliers'] - $aRow['price_other_expenses'];
                    $title .= _l('ch_status_pays_slip_invoice') . ': ' . formatMoney($amount_paid);
                    $aRow['total_expenses'] = $aRow['tblpurchase_order.totalAll_suppliers'];
                }
            }

            if (($aRow['tblpurchase_order.red_invoice'] == 0)) {
                $status_other_check = $aRow['status_pay'];
                if ($status_other_check > 0) {
                    $status_other = '<div class="text-center">' . format_status_pay_slip_new($aRow['status_pay'], $aRow['total_expenses']) . '<div>';
                } else {
                    $status_other = '<div class="text-center">' . format_status_pay_slip($aRow['status_pay']) . '<div>';
                }
            } else {
                $invoice = get_table_where('tblpurchase_invoice', array('id' => $aRow['tblpurchase_order.red_invoice']), '', 'row');
                $status_other_check = $invoice->status;
                if ($status_other_check > 0) {
                    $status_other = '<div class="text-center">' . format_status_pay_slip_new($invoice->status, $aRow['total_expenses']) . '<div>';
                } else {
                    $status_other = '<div class="text-center">' . format_status_pay_slip($invoice->status) . '<div>';
                }
            }
            $row[] = '<span data-html="true" data-toggle="tooltip" data-title="' . $title . '" class="text-has-action"></span>' . $status_other;
            $footer_data['pay'] += $aRow['total_expenses'];
        } else {
            if (!empty($aRow['tblpurchase_order.red_invoice'])) {
                $ktr = get_table_where('tblpurchase_invoice', array('id' => $aRow['tblpurchase_order.red_invoice']), '', 'row');
                if (!empty($ktr) && $ktr->status == 2) {
                    $aRow['total_expenses'] = $aRow['tblpurchase_order.totalAll_suppliers'];
                }
            }
            if (($aRow['tblpurchase_order.red_invoice'] == 0)) {
                $status_other_check = $aRow['status_pay'];
                if ($status_other_check > 0) {
                    $status_other = '<div class="text-center">' . format_status_pay_slip_new($aRow['status_pay'], $aRow['total_expenses']) . '<div>';
                } else {
                    $status_other = '<div class="text-center">' . format_status_pay_slip($aRow['status_pay']) . '<div>';
                }
            } else {
                $invoice = get_table_where('tblpurchase_invoice', array('id' => $aRow['tblpurchase_order.red_invoice']), '', 'row');
                $status_other_check = $invoice->status;
                if ($status_other_check > 0) {
                    $status_other = '<div class="text-center">' . format_status_pay_slip_new($invoice->status, $aRow['total_expenses']) . '<div>';
                } else {
                    $status_other = '<div class="text-center">' . format_status_pay_slip($invoice->status) . '<div>';
                }
            }

            $row[] = $status_other;
            $footer_data['pay'] += $aRow['total_expenses'];
        }


        $content = str_replace('"', '\'', form_open(admin_url('purchase_invoice/add'), array('id' => 'purchase_invoice-form'))) . '<input name=\'id_import\' class=\'hide\' value=\'' . $aRow['id'] . '\'><input name=\'id_supplier\'class=\'hide\' value=\'' . $aRow['suppliers_id'] . '\'>' . str_replace('"', '\'', render_input('code_invoice', 'ch_code_invoice', '', 'number')) . str_replace('"', '\'', render_date_input('date_invoice', 'ch_date_invoice', _d(date('Y-m-d')))) . str_replace('"', '\'', render_textarea('note', 'ch_note')) . '<div class=\'text-right\'><button type=\'submit\' class=\'btn btn-danger\'>' . _l('submit') . '</button><a class=\'btn btn-default po-close\'>' . _l('close') . '</a></div>' . str_replace('"', '\'', form_close()) . '<script>
                        var opt = {
                            format: \'d/m/Y\',
                            timepicker: false,
                            scrollInput: false,
                            lazyInit: true,
                            dayOfWeekStart: \'hau\',
                        };
                        $(\'#date_invoice\').datetimepicker(opt);
                           _validate_form($(\'#purchase_invoice-form\'),{code_invoice:\'required\',date_invoice:\'required\'},purchase_invoice);

           function purchase_invoice(form) {
               var data = $(form).serialize(),
                   action = form.action;
               return $.post(action, data).done(function(form) {
                   form = JSON.parse(form),
                   alert_float(form.alert_type, form.message);
                                                                                                    
                    $(\'.popover\').popover(\'hide\');
                    $(\'.table-import\').DataTable().ajax.reload();
                    window.open(\'' . admin_url('purchase_invoice') . '\', \'_blank\');   

               }), !1
           }</script>';
        $title = '';
        if ($aRow['tblpurchase_order.red_invoice'] != 0) {
            $color = 1;
            $class = 'class="invoice_button_red"';
            $content = '';
        } else {
            $title = 'Tạo hóa đơn thuế!';
            $color = 0;
            $class = 'class="invoice_button"';
            if (($aRow['status_pay'] != 0) && $aRow['amount_paid'] > 0) {
                $content = '';
            }
            if (!has_permission('purchase_invoice', '', 'create')) {
                $title = 'Bạn không có quyền tạo hóa đơn thuế!';
                $content = 'Bạn không có quyền tạo hóa đơn thuế!</button><a class=\'btn btn-default po-close\'>' . _l('close') . '</a></div>';
            }
        }

        $row[] = '<div class="text-center">
                    <a ' . $class . ' data-container="body" data-id="' . $aRow['id'] . '"  data-html="true" data-toggle="popover" data-placement="left" data-content="' . $content . '">' . format_type_invoice($color) . '</a>
                    </div>';

        if ($aRow['tblpurchase_order.status'] == 1) {
            $type = 'warning';
            $status = 'Chưa xác nhận';
        } elseif ($aRow['tblpurchase_order.status'] == 2) {
            $type = 'info';
            $status = 'Đã xác nhận';
        } else {
            $type = 'success';
            $status = 'Đã duyệt';
        }


        $none_img = staff_profile_image(0, array('staff-profile-image-small'), 'small');
        $history_status = explode('|', $aRow['history_status']);

        // $users = '<div style="min-width: 700px;position: relative;">';
        // $dem_temp = 0; // đếm xem có bao nhiêu đc active
        // foreach ($history_status as $key => $value) {
        //     $data = explode(',',$value);
        //     if($key == 0) {
        //         $users .= '<div class="step-status">
        //                     <div class="active">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                     <div class="wap-title-status success">'._l('create').'</div>
        //                 </div>';
        //         $dem_temp++;
        //     } else if($key == 1) {
        //         $users .= '<div class="step-status">
        //                     <div class="active">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                     <div class="wap-title-status success">'._l('proceed').'</div>
        //                 </div>';
        //         $dem_temp++;
        //     } else if($key == 2) {
        //         $users .= '<div class="step-status">
        //                     <div class="active">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                     <div class="wap-title-status success">'._l('accept').'</div>
        //                 </div>';
        //         $dem_temp++;
        //     } else if($key == 3) {
        //         $users .= '<div class="step-status">
        //                     <div class="active">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                     <div class="wap-title-status success">'._l('add_items').'</div>
        //                 </div>';
        //         $dem_temp++;
        //     }
        // }

        // $no_event = '';
        // $no_click = '';
        // if($aRow['cancel'] != 0) {
        //     $no_event = 'no-drop';
        //     $no_click = 'none-event';
        // }

        // if($aRow['tblpurchase_order.status'] == 1) {
        //     $users .= '<div class="step-status '.$no_event.'">
        //                     <div class="'.$no_click.'" data-loading-text="" onclick="var_status('.$aRow['tblpurchase_order.status'].','.$aRow['tblpurchase_order.id'].'); return false;">'.$none_img.'</div>
        //                     <div class="wap-title-status">'._l('proceed').'</div>
        //                 </div>
        //                 <div class="step-status">
        //                     <div class="no-drop">'.$none_img.'</div>
        //                     <div class="wap-title-status">'._l('accept').'</div>
        //                 </div>
        //                 <div class="step-status">
        //                     <div class="no-drop">'.$none_img.'</div>
        //                     <div class="wap-title-status">'._l('add_items').'</div>
        //                 </div>';
        // } else if($aRow['tblpurchase_order.status'] == 2) {
        //     $users .= '<div class="step-status '.$no_event.'">
        //                     <div class="'.$no_click.'"  data-loading-text="" onclick="var_status('.$aRow['tblpurchase_order.status'].','.$aRow['tblpurchase_order.id'].'); return false;">'.$none_img.'</div>
        //                     <div class="wap-title-status">'._l('accept').'</div>
        //                 </div>
        //                 <div class="step-status">
        //                     <div class="no-drop">'.$none_img.'</div>
        //                     <div class="wap-title-status">'._l('add_items').'</div>
        //                 </div>';
        // } else if($aRow['tblpurchase_order.status'] == 3) {
        //     if(!empty($import))
        //     {
        //     $dem_temp++;
        //     if(file_exists('uploads/company/'.get_option('favicon')))
        //     {
        //     $none_img_ch ='<img src="'.base_url('uploads/company/'.get_option('favicon')).'" class="staff-profile-image-small">';
        //     if($count_items_import == 0)
        //     {
        //     $status_import ='<span class="inline-block label label-warning">' . _l('ch_imports_full') . '</span>'; 
        //     }else
        //     {
        //     $status_import ='<span class="inline-block label label-warning">' . _l('ch_imports_part') . '</span>';     
        //     }
        //     $users .= '<div class="step-status no-drop">
        //                     <div class="none-event no-drop active">'.$none_img_ch.'</div>
        //                     <div class="wap-title-status success">'._l('add_items').'</div>'.$status_import.'
        //                 </div>';   
        //     }else
        //     {
        //     $none_img_ch = $none_img;
        //     $users .= '<div class="step-status no-drop">
        //                     <div class="none-event no-drop active">'.$none_img_ch.'</div>
        //                     <div class="wap-title-status success">'._l('add_items').'</div>'.$status_import.'
        //                 </div>';   
        //     }  
        //     }else
        //     {
        //     $none_img_ch = $none_img;
        //     $users .= '<div class="step-status no-drop">
        //                     <div class="none-event no-drop" >'.$none_img_ch.'</div>
        //                     <div class="wap-title-status">'._l('add_items').'</div>
        //                 </div>';
        //     }

        // }
        // if($aRow['cancel'] == 0) {
        //     if($count_items_import == 0)
        //     {
        //         $no_event_ch = 'class="no-drop none-event"';
        //     } else {
        //         $no_event_ch = 'class="'.$no_event.'"';
        //     }
        //     $users .= '<div class="step-status">
        //                 <div '.$no_event_ch.' onclick="cancel_status('.$aRow['tblpurchase_order.id'].'); return false;">'.$none_img.'</div>
        //                 <div class="wap-title-status red">'._l('ch_cancel').'</div>
        //             </div>';
        // } else {
        //     $data = explode(',',$aRow['cancel']);
        //     if($data[0] == '1foso') {  
        //         $users .= '<div class="step-status">
        //                 <div class="cancel"><img src="'.base_url('uploads/company/'.get_option('favicon')).'" class="staff-profile-image-small" data-toggle="tooltip" data-title="'._l('system').' '._l('ch_time').' '._dt($data[1]).'"></div>
        //                 <div class="wap-title-status red">'._l('ch_cancel').'</div>
        //             </div>';  
        //     } else {
        //         if(is_admin()){
        //         $users .= '<div class="step-status">
        //                 <div class="cancel" onclick="no_cancel_status('.$aRow['tblpurchase_order.id'].'); return false;">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                 <div class="wap-title-status red">'._l('ch_cancel').'</div>
        //             </div>';   
        //         }else
        //         {
        //             $users .= '<div class="step-status">
        //                 <div class="cancel">'.staff_profile_image($data[0], array('staff-profile-image-small'), 'small',array(
        //                     'data-toggle' => 'tooltip',
        //                     'data-title' => get_staff_full_name($data[0]).' '._l('ch_time').' '._dt($data[1]))).'</div>
        //                 <div class="wap-title-status red">'._l('ch_cancel').'</div>
        //             </div>';   
        //         }
        //     }

        // }
        // if($dem_temp == 1) {
        //     $dem_temp = '10';
        // } else if($dem_temp == 2) {
        //     $dem_temp = '30';
        // } else if($dem_temp == 3) {
        //     $dem_temp = '50';
        // } else if($dem_temp == 4) {
        //     $dem_temp = '70';
        // }

        // $users .= '<div class="line line'.$dem_temp.'"></div>';
        // $users .= '<div class="clearfix"></div>';
        // $users .= '</div>';
        // $row[] = $users;
        // $row[] = text_align(process_purchases_order_img($aRow['id']).process_purchases_order($aRow['id']),'center');
        $import_check = get_table_where('tblimport', array('id_order' => $aRow['id']), '', 'row');
        $count_items_import_check = get_items_import($aRow['id']);
        $data_check = '<div class="label label-danger">Chưa nhập</div>';
        if (!empty($import_check)) {
            if ($count_items_import_check == 0) {
                $data_check = '<div class="label label-info">Nhập đủ</div>';
            } else {
                $data_check = '<div class="label label-warning">Nhập 1 phần</div>';
            }
        }
        $row[] = $data_check;

        $row[] = $aRow['tblpurchase_order.note'];

        // if($aRow['cancel'] == 0) {
        //     $row[] = '';
        // }
        // else {
        //     $get_evaluate = get_table_where('tblpurchase_order_evaluate',array('id_purchase_order'=>$aRow['id']),'','row');
        //     if(!$get_evaluate) {
        //         $row[] = '<div class=" H_border">
        //                     <button class="btn btn-info H_action_button" onclick="add_evaluate('.$aRow['id'].'); return false;">'._l('evaluate').'</button>
        //                 </div>';
        //     }
        //     else {
        //         $row[] = '<div class=" H_border">
        //                     <button class="btn btn-info H_action_button" onclick="edit_evaluate('.$aRow['id'].'); return false;">'._l('update_evaluate').'</button>
        //                 </div>';
        //     }
        // }



        // Custom fields add values
        foreach ($customFieldsColumns as $customFieldColumn) {
            $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
        }

        // $row['DT_RowClass'] = 'has-row-options';

        $row = hooks()->apply_filters('purchases_table_row_data', $row, $aRow);

        $output['aaData'][] = $row;
    }
    foreach ($footer_data as $key => $total) {
        $footer_data[$key] = formatNumber($total);
    }
    $output['sums'] = $footer_data;
