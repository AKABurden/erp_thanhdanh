    <?php

    defined('BASEPATH') or exit('No direct script access allowed');
    $custom_fields = get_table_custom_fields('supplier_quotes');
    $hasPermissionDelete = has_permission('RFQ', '', 'delete');

    $aColumns = [
        'tblsupplier_quotes.id',
        'tblsupplier_quotes.code',
        'tblsupplier_quotes.staff_create',
        'tblsupplier_quotes.date',
        'tblsupplier_quotes.status',
        '1',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblsupplier_quotes';
    $where        = [];
    array_push($where, 'AND tblsupplier_quotes.suppliers_id = '.$id);
    $filter = [];
    $join             = [
    ];
    foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $key . ' ON tblsupplier_quotes.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
    }

    if ($this->ci->input->post('filterStatus')) {
        if(is_numeric($this->ci->input->post('filterStatus'))) {
            if($this->ci->input->post('filterStatus') == 1 || $this->ci->input->post('filterStatus') == 2) {
                array_push($where, 'AND tblsupplier_quotes.status = '.$this->ci->input->post('filterStatus'));
            } else {

            }
        }
    }

    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
     'tblsupplier_quotes.prefix,tblsupplier_quotes.suppliers_id,tblsupplier_quotes.history_status'
    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
    foreach ($rResult as $aRow) {
        $j++;
        $row = [];

        $row[] = $j;
        $purchase_order = get_table_where('tblpurchase_order',array('id_quotes'=>$aRow['tblsupplier_quotes.id']),'','row');
        $supplier_quotes  = $aRow['prefix'].'-'.$aRow['tblsupplier_quotes.code'];
        $supplier_quotes = '<a>' . $supplier_quotes . '</a>';

        $row[] = $supplier_quotes;

        $staff = staff_profile_image($aRow['tblsupplier_quotes.staff_create'], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($aRow['tblsupplier_quotes.staff_create'])
                        )).get_staff_full_name($aRow['tblsupplier_quotes.staff_create']);
        $row[] = ($aRow['tblsupplier_quotes.staff_create'] ? $staff : '');


        $row[] = _d($aRow['tblsupplier_quotes.date']);
        
        if($aRow['tblsupplier_quotes.status']==1)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                else if($aRow['tblsupplier_quotes.status']==2)
                {
                    $type='success';
                    $status='Đã duyệt';
                }
            $status='<span class="inline-block label label-'.$type.'" task-status-table="'.$aRow['tblsupplier_quotes.status'].'">' . $status.'';

                $status.='
                    </a>
                </span>';
        $_data='';
        $history_status = explode('|',$aRow['history_status']);
        foreach ($history_status as $key => $value) {
            $data = explode(',',$value);
            if(is_numeric($data[0])) {
                $_data.=staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => ' Vào lúc: '._dt($data[1])
                        )).get_staff_full_name($data[0]).'<br>';
            }
        }
        $row[] = $status.'<br>'.$_data;
        $this->ci->db->select('tblsupplier_quote_items.*')->distinct();
        $this->ci->db->from('tblsupplier_quote_items');
        $this->ci->db->where('id_supplier_quotes',$aRow['tblsupplier_quotes.id']);
        $items = $this->ci->db->get()->result_array();
        $_data = '';
        foreach ($items as $k => $v) {
            $get_items = get_items($v['product_id'],$v['type']);
            $_data .= $get_items->name.' ('.$v['quantity'].')<br>';
        }

        $row[] = $_data;
        foreach ($customFieldsColumns as $customFieldColumn) {
            $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
        }
          // text_align('<a href="#" onclick="rdq_modal('.$aRow['id_purchases'].'); return false;" type="button"  class="btn btn-warning">'._l('ch_edit_rfq').'</a>','center'); 
                $row['DT_RowClass'] = 'has-row-options';
        $output['aaData'][] = $row;
    }
