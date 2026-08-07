    <?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $hasPermissionDelete = has_permission('purchases', '', 'delete');

    $custom_fields = get_table_custom_fields('purchases');
    $this->ci->db->query("SET sql_mode = ''");

    $aColumns = [
        'tblpurchase_order.id',
        'tblpurchase_order.code',
        'tblpurchase_order.date',
        'tblpurchase_order.staff_create',
        'tblpurchase_order.totalAll_suppliers',
        'tblpurchase_order.status',
        '1',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpurchase_order';
    $where        = [];
    array_push($where, 'AND tblpurchase_order.suppliers_id = '.$id);
    $filter = [];
    $join         = array(
    );
    foreach ($custom_fields as $key => $field) {
        $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
        array_push($customFieldsColumns, $selectAs);
        array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
        array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON tblpurchase_order.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }
    $join = hooks()->apply_filters('purchases_table_sql_join', $join);

    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1 || $this->ci->input->post('filterStatus') == 2 || $this->ci->input->post('filterStatus') == 3) {
                array_push($where, 'AND tblpurchase_order.status = '.$this->ci->input->post('filterStatus'));
            } else {

            }
        }
    }
    
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
     'tblpurchase_order.prefix,tblpurchase_order.history_status,tblpurchase_order.suppliers_id'
    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
    foreach ($rResult as $aRow) {
        $j++;
        $row = [];

        $row[] = $j;

        $purchases  = $aRow['prefix'].'-'.$aRow['tblpurchase_order.code'];
        $isPerson = false;
        $purchases = '<a  >' . $purchases . '</a>';
        $purchases .= '</div>';

        $row[] = $purchases;
        $row[] = _d($aRow['tblpurchase_order.date']);
        
        $staff = staff_profile_image($aRow['tblpurchase_order.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblpurchase_order.staff_create'])
                        )).get_staff_full_name($aRow['tblpurchase_order.staff_create']);
        $row[] = ($aRow['tblpurchase_order.staff_create'] ? $staff : '');

        $row[] = number_format($aRow['tblpurchase_order.totalAll_suppliers']);
        
            if($aRow['tblpurchase_order.status']==1)
                {
                    $type='warning';
                    $status='Chưa xác nhận';
                }
                elseif($aRow['tblpurchase_order.status']==2)
                {
                    $type='info';
                    $status='Đã xác nhận';
                }
                else
                {
                    $type='success';
                    $status='Đã duyệt';
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblpurchase_order.status'].'">' . $status.'';

                $status.='
                    </a>
                </span>';
        $_data='';
        $history_status = explode('|',$aRow['history_status']);
        $name_status = '';
        foreach ($history_status as $key => $value) {
            $data=explode(',',$value);
            if(is_numeric($data[0]))
            {
            if($key == 1)
            {
                $name_status = 'Xác nhận: ';
            }elseif($key == 2)
            {
                $name_status = 'Duyệt:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $_data.=$name_status.staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($data[1])
                        )).get_staff_full_name($data[0]).'<br>';
            }
        }
        
        $row[] = $status.'<br>'.$_data;
        $this->ci->db->select('tblpurchase_order_items.*')->distinct();
        $this->ci->db->from('tblpurchase_order_items');
        $this->ci->db->where('id_purchase_order',$aRow['tblpurchase_order.id']);
        $items = $this->ci->db->get()->result_array();
        $_data = '';
        foreach ($items as $k => $v) {
            $get_items = get_items($v['product_id'],$v['type']);
            $_data .= $get_items->name.' ('.$v['quantity'].')<br>';
        }

        $row[] = $_data;
       
        // Custom fields add values
        foreach ($customFieldsColumns as $customFieldColumn) {
            $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
        }

        $row['DT_RowClass'] = 'has-row-options';


        $row = hooks()->apply_filters('purchases_table_row_data', $row, $aRow);

        $output['aaData'][] = $row;
    }
