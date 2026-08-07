    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('RFQ', '', 'delete');

    $aColumns = [
        'tblrfq_ask_price.id',
        'tblrfq_ask_price.code',
        'tblrfq_ask_price.staff_create',
        'tblrfq_ask_price.date_create',
        'tblrfq_ask_price.status',
        '1',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblrfq_ask_price';
    $where        = [];
    $filter = [];
    $join             = [
        'LEFT JOIN tblrfq_ask_price_items  ON tblrfq_ask_price_items.id_rfq_ask_price=tblrfq_ask_price.id'
    ];
    $where        = array(
    'AND tblrfq_ask_price_items.suppliers_id="' . $id . '"',
    );
    $order_by = 'GROUP BY tblrfq_ask_price.id';
    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1 || $this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblrfq_ask_price.status = '.$this->ci->input->post('filterStatus'));
            } else {
                
            }
        }
    }

    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
     'tblrfq_ask_price.prefix,tblrfq_ask_price.id_purchases,tblrfq_ask_price.history_status'
    ],$order_by);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;

    foreach ($rResult as $aRow) {
        $j++;
        $row = [];

        $row[] = $j;

        $purchases  = $aRow['prefix'].'-'.$aRow['tblrfq_ask_price.code'];
        $purchases = '<a >' . $purchases . '</a>';

        $row[] = $purchases;
        $staff = staff_profile_image($aRow['tblrfq_ask_price.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblrfq_ask_price.staff_create'])
                        )).get_staff_full_name($aRow['tblrfq_ask_price.staff_create']);
        $row[] = ($aRow['tblrfq_ask_price.staff_create'] ? $staff : '');

        $row[] = _dt($aRow['tblrfq_ask_price.date_create']);
        
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

        $this->ci->db->select('tblrfq_ask_price_items.*');
        $this->ci->db->from('tblrfq_ask_price_items');
        $this->ci->db->where('id_rfq_ask_price',$aRow['tblrfq_ask_price.id']);
        $items = $this->ci->db->get()->result_array();
        $_data = '';

        foreach ($items as $k => $v) {
            $get_items = get_items($v['product_id'],$v['type']);
            $_data .= $get_items->name.' ('.$v['quantity'].')<br>';
        }

        $row[] = $_data;
        $output['aaData'][] = $row;
    }
