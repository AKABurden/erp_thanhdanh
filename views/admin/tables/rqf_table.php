    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('RFQ', '', 'delete');
    $hasPermissionEdit = has_permission('RFQ', '', 'edit');

    $aColumns = [
        'tblrfq_ask_price.date_create',
        'tblrfq_ask_price.code',
        'tblrfq_ask_price.suppliers_id',
        'tblpurchases.code',
        'tblrfq_ask_price.staff_create',
        'tblrfq_ask_price.status',
        '3',
        '4',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblrfq_ask_price';
    $where        = [];
    $filter = [];
    $join             = [
    'JOIN tblpurchases ON tblpurchases.id = tblrfq_ask_price.id_purchases',
    ];

    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1 || $this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblrfq_ask_price.status = '.$this->ci->input->post('filterStatus'));
            } else {
                
            }
        }
    }
    if ($this->ci->input->post('search_code')) {
        if(is_numeric($this->ci->input->post('search_code'))) {
            array_push($where, 'AND tblrfq_ask_price.id = '.$this->ci->input->post('search_code'));
        }
    }
    if ($this->ci->input->post('search_staff')) {
        array_push($where, 'AND  tblrfq_ask_price.staff_create IN (' . implode(', ', $this->ci->input->post('search_staff')) . ')');
    }
    if ($this->ci->input->post('search_id_suppliers')) {
        array_push($where, 'AND  tblrfq_ask_price.suppliers_id LIKE "%' . implode(', ', $this->ci->input->post('search_id_suppliers')) . '%"');
    }
    $search_date = $this->ci->input->post('search_date');
    if($search_date)
    {
        $data_start = explode(' - ', $search_date);
        array_push($where, 'AND tblrfq_ask_price.date_create BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
    }
    if(has_permission('RFQ','','view_own')&&!is_admin())
    {
         array_push($where, 'AND  tblrfq_ask_price.staff_create = '.get_staff_user_id());
    }
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['
        tblrfq_ask_price.id,
        tblrfq_ask_price.prefix,
        tblpurchases.prefix as prefixp,
        tblrfq_ask_price.id_purchases,
        tblrfq_ask_price.history_status,
        tblrfq_ask_price.not_new_by_staff,
        tblrfq_ask_price.type_plan as type_plan_rfq_ask_price
    ']);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;

    foreach ($rResult as $aRow) {
        $j++;
        $row = [];

        $not_new_by_staff = explode(' ',$aRow['not_new_by_staff']);
        if(!in_array(get_staff_user_id(), $not_new_by_staff) && $aRow['tblrfq_ask_price.status'] == 1) {
            $row[] = _dt($aRow['tblrfq_ask_price.date_create']).' <br><span class="wap-new">new</span>';
            $row['DT_RowClass'] = 'alert-new';
        }
        else {
            $row[] = _dt($aRow['tblrfq_ask_price.date_create']);
        }

        $purchases  = $aRow['prefix'].'-'.$aRow['tblrfq_ask_price.code'];
        $purchases = '<a href="#" onclick="rdq_modal('.$aRow['id_purchases'].'); return false;" >' . $purchases . '</a>';

        $row[] = $purchases;
        if($aRow['type_plan_rfq_ask_price'] == 1){
        $productions_capacity = '<span class="inline-block label label-success">'._l('ch_productions_capacity').'</span><br>';
        }else
        {
        $productions_capacity ='';   
        }
        $row[] = $aRow['prefixp'].$aRow['tblpurchases.code'].'<br>'.$productions_capacity;
        $suppliers_id = explode(',',$aRow['tblrfq_ask_price.suppliers_id']);
        
        $_data='';
        foreach ($suppliers_id as $k => $v) {
            $suppliers = get_table_where('tblsuppliers',array('id'=>$v),'','row');
            if(!empty($suppliers)){
            $_data.='<a href="#" onclick="int_suppliers_view('.$v.'); return false;" >' . $suppliers->company . '</a>,<br>';
            }
        }
        $row[] = $_data;
        $staff = staff_profile_image($aRow['tblrfq_ask_price.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblrfq_ask_price.staff_create'])
                        )).get_staff_full_name($aRow['tblrfq_ask_price.staff_create']);
        $row[] = ($aRow['tblrfq_ask_price.staff_create'] ? $staff : '');
        
        if($aRow['tblrfq_ask_price.status']==1)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                elseif($aRow['tblrfq_ask_price.status']==2)
                {
                    $type='success';
                    $status='Đã duyệt';
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblrfq_ask_price.status'].'">' . $status.'';
            if(has_permission('RFQ', '', 'approve'))
            {
                if($aRow['tblrfq_ask_price.status']!=2){
                    $status.='<a href="javacript:void(0)" data-loading-text="" onclick="var_status('.$aRow['tblrfq_ask_price.status'].','.$aRow['id'].'); return false;">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>                    
                    ';
                }
                else
                {
                    $status.='<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
                }
            }else
            {
                    $status.='<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip"></i>';
            }
                $status.='
                    </a>
                </span>';
        $_data='';
        $history_status = explode('|',$aRow['history_status']);
        foreach ($history_status as $key => $value) {
            $data=explode(',',$value);
            if(is_numeric($data[0]))
            {
            $_data.=staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($data[1])
                        )).get_staff_full_name($data[0]).'<br>';
            }
        }
        $row[] = $status.'<br>'.$_data;
        $data = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$aRow['id']));
        $count = count($data);
        $_data ='';
        $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.format_status_number_rfq_ch($count).'
                        </button>';
        if(!empty($count))
        {
            foreach ($data as $k => $v) {
            $_data.='<li><a href="#" onclick="view_supplier_quotes('.$v['id'].'); return false;" >' .$v['prefix'].'-'.$v['code'] . '</a></li>';   
            }

            $_outputStatus .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">'.$_data;
            $_outputStatus .= '</ul>';
        }
        $_outputStatus .= '</div>';
        $row[] = $_outputStatus;

      
        $_outputStatus = '<div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">'._l('action').'
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu h_right">';
        if($hasPermissionEdit)
        {
            $_outputStatus .= '<li><a  href="#" onclick="rdq_modal('.$aRow['id_purchases'].'); return false;" ><i class="fa fa-pencil"></i> ' . _l('edit_order') . '</a></li>';
        }
        if ($hasPermissionDelete) {
            $_outputStatus .= '<li><a href="' . admin_url('RFQ/delete/' . $aRow['id']) . '" class="text-danger delete-remind"><i class="fa fa-times"></i> ' . _l('delete_order') . '</a></li>';
        }
        $_outputStatus .= '</ul></div>';
        $row[] = $_outputStatus;
        
        $output['aaData'][] = $row;
    }
