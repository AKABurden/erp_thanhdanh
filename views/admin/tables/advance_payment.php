    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('other_payslips_coupon', '', 'delete');
    $hasPermissionEdit = has_permission('other_payslips_coupon', '', 'edit');

    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tbladvance_payment.id',
        'tbladvance_payment.code',
        'tbladvance_payment.date',
        'tbladvance_payment.staff',
        'c.name as namec',
        'n.name as namen',
        'tblcosts.name',
        'tbladvance_payment.status',
        'tbladvance_payment.total',
        'tbladvance_payment.staff_create',
        'tbladvance_payment.note',
        '1',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tbladvance_payment';
    $where        = [];


    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1) {
                array_push($where, 'AND tbladvance_payment.status = 1');
            } else if($this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tbladvance_payment.status = 0');
            } else if($this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tbladvance_payment.objects = 1');
            } else if($this->ci->input->post('filterStatus') == 4) {
                array_push($where, 'AND tbladvance_payment.objects = 2');
            } else if($this->ci->input->post('filterStatus') == 5) {
                array_push($where, 'AND tbladvance_payment.objects = 3');
            } else if($this->ci->input->post('filterStatus') == 6) {
                array_push($where, 'AND tbladvance_payment.objects = 4');
            }
        }
    }
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tbladvance_payment.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }
    $search_staff = $this->ci->input->post('search_staff');
    if(is_array($search_staff))
    {
        array_push($where, 'AND tbladvance_payment.staff in (' . implode(',', $search_staff) . ')');
    }
    $paymode_c = $this->ci->input->post('paymode_c');
    if(($paymode_c))
    {
        array_push($where, 'AND tbladvance_payment.paymode_c =' . $paymode_c);
    }
    $paymode_n = $this->ci->input->post('paymode_n');
    if(($paymode_n))
    {
        array_push($where, 'AND tbladvance_payment.paymode_n =' . $paymode_n);
    }
    $filter = [];
    $join = [
        'LEFT JOIN tblpayment_modes c ON c.id=tbladvance_payment.paymode_c',
        'LEFT JOIN tblpayment_modes n ON n.id=tbladvance_payment.paymode_n',
        'LEFT JOIN tblcosts ON tblcosts.id=tbladvance_payment.id_costs',
        'LEFT JOIN tblstaff on tblstaff.staffid = tbladvance_payment.staff_create',
    ];
    if(has_permission('advance_payment','','view_own')&&!is_admin())
    {
         array_push($where, 'AND  tbladvance_payment.staff_create = '.get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbladvance_payment.date_create','tbladvance_payment.history_status',

    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
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
        if ($aColumns[$i] == 'tbladvance_payment.id') {
        $_data ='<div class="text-center">'.$j.'</div>';
        }
        if ($aColumns[$i] == 'tbladvance_payment.status') {
            if($aRow['tbladvance_payment.status']==0)
                {
                    $type='warning';
                    $status=_l('Chưa duyệt');
                }
                elseif($aRow['tbladvance_payment.status']==1)
                {
                    $type='info';
                    $status=_l('Đã duyệt');
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tbladvance_payment.status'].'">' . $status.'';
            if(has_permission('other_payslips_coupon', '', 'approve'))
            {
                if($aRow['tbladvance_payment.status']==0) {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['tbladvance_payment.status'] . ',' . $aRow['tbladvance_payment.id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                }
                else
                {
                    $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="update_status_not(' . $aRow['tbladvance_payment.status'] . ',' . $aRow['tbladvance_payment.id'] . '); return false">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
                }
            }
                $status .= '</a>
                        </span><br>';
                $__data='';
                $history_status = explode('|',$aRow['history_status']);

                foreach ($history_status as $key => $value) {
                    $data=explode(',',$value);
                    if(is_numeric($data[0]))
                    {
                    $__data.=staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                                    'data-toggle' => 'tooltip',
                                    'data-title' => ' Vào lúc: '._dt($data[1])
                                )).get_staff_full_name($data[0]).'<br>';
                    }
                }

                $_data = $status.$__data;
        }
        if ($aColumns[$i] == 'tbladvance_payment.code') {
        $payslips=$aRow['tbladvance_payment.code'];
        $payslips .= '<div class="row-options">';
        if($hasPermissionEdit){
        $payslips .= ' <a href="#" onclick="edit_advance_payment('.$aRow['tbladvance_payment.id'].'); return false;" >' . _l('edit') . '</a>';
        }
        if ($hasPermissionDelete) {
            $payslips .= ' | <a href="' . admin_url('advance_payment/delete/' . $aRow['tbladvance_payment.id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
        }   
        $payslips .= '</div>';
        $_data = $payslips;
        }
        if ($aColumns[$i] == 'tbladvance_payment.date') {
        $_data =_d($aRow['tbladvance_payment.date']);
        }
        if ($aColumns[$i] == 'tbladvance_payment.staff') {
                $_data = get_staff_full_name($aRow['tbladvance_payment.staff']);
        }
        if ($aColumns[$i] == 'tbladvance_payment.total') {
            $footer_data['payment']+=$aRow['tbladvance_payment.total']; 
            $_data = number_format($aRow['tbladvance_payment.total']);
        }
        if ($aColumns[$i] == 'tbladvance_payment.staff_create') {
            $_data = staff_profile_image($aRow['tbladvance_payment.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($aRow['date_create'])
                        )).get_staff_full_name($aRow['tbladvance_payment.staff_create']).'<br>';
        }
        if ($aColumns[$i] == '1') {
        $_data = '<div class="dropdown text-center" >
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu h_right">';
        $_data .= '<li><a href="'.admin_url('advance_payment/print_pdf/'.$aRow['tbladvance_payment.id']).'" target="_blank"><i class="fa fa-file-pdf-o width-icon-actions"></i>'._l('print_vote').'</a></li>';
        $_data .= '</ul></div>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
    foreach ($footer_data as $key => $total) {
        $footer_data[$key] = number_format($total);
    }
    $output['sums'] = $footer_data;