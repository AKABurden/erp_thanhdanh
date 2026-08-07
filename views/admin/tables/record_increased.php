<?php
$hasPermissionDelete = has_permission('record_increased', '', 'delete');
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns         = [
    'tblrecord_increased.id',
    'tblrecord_increased.code_vouchers',
    'tblrecord_increased.date_of_recording_increases',
    'tblrecord_increased.property_code',
    'tblrecord_increased.asset_name',
    'tbltype_record_increased.name',
    'tbldepartments.name',
    'tblrecord_increased.original_price',
    'tblrecord_increased.value_of_depreciation',
    'tblrecord_increased.accumulated_depreciation',
    'tblrecord_increased.residual_value',
    'tblrecord_increased.number_used_time',
    'tblrecord_increased.monthly_depreciation_value',
    'tblrecord_increased.date_depreciation',
    'tblrecord_increased.record_reduce'
];
$sIndexColumn     = 'id';
$sTable           = 'tblrecord_increased';
$additionalSelect = [
    'tblrecord_increased.used_time'
];
$join             = [
    'JOIN tbltype_record_increased ON tbltype_record_increased.id = tblrecord_increased.type_record_increased',
    'JOIN tbldepartments ON tbldepartments.departmentid = tblrecord_increased.units_used',
];

$where    = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = $aRow[ $aColumns[$i] ];
        if ($aColumns[$i] == 'tblrecord_increased.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        if ($aColumns[$i] == 'tblrecord_increased.code_vouchers') {
            $ktr = get_table_where('tbldepreciation_detail',array('id_increased'=>$aRow['tblrecord_increased.id']),'','row');
            $record_increase  = $aRow['tblrecord_increased.code_vouchers'];
            $isPerson = false;
            $record_increase = '<a href="#" onclick="view_record_increase('.$aRow['tblrecord_increased.id'].'); return false;" >' . $record_increase . '</a>';
            $record_increase .= '<div class="row-options">';
            $record_increase .= '<a href="#" onclick="view_record_increase('.$aRow['tblrecord_increased.id'].'); return false;" >' . _l('view') . '</a>';
            if(has_permission('record_increased','','edit')&&empty($ktr)){
                $record_increase .= ' | <a href="#" onclick="edit_record_increase('.$aRow['tblrecord_increased.id'].'); return false;">' . _l('edit') . '</a>';
            }
            if ($hasPermissionDelete&&empty($ktr)) {
                $record_increase .= ' | <a href="' . admin_url('record_increased/delete/' . $aRow['tblrecord_increased.id']) . '" class="text-danger delete-remind">' . _l('delete') . '</a>';
            }   
            $record_increase .= '</div>';

            $_data = $record_increase;
        }
        else if($aColumns[$i] == 'tblrecord_increased.original_price') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }else if($aColumns[$i] == 'tblrecord_increased.value_of_depreciation') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }else if($aColumns[$i] == 'tblrecord_increased.accumulated_depreciation') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }else if($aColumns[$i] == 'tblrecord_increased.residual_value') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tblrecord_increased.monthly_depreciation_value') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tblrecord_increased.number_used_time') {
            $_data = number_format($aRow[$aColumns[$i]]);
            if($aRow['used_time'] == 1)
            {
            $_data = number_format($aRow[$aColumns[$i]]*12);    
            }
        }
        else if($aColumns[$i] == 'tblrecord_increased.date_of_recording_increases') {
            $_data = _d($aRow[$aColumns[$i]]);
        }else if($aColumns[$i] == 'tblrecord_increased.date_depreciation') {
            $_data = _d($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tblrecord_increased.customer_id') {
            $customer = get_table_where('tblclients',array('userid'=>$aRow[$aColumns[$i]]),'','row');
            $_data = (!empty($customer->company) ? $customer->company : '');
        }
        else if($aColumns[$i] == 'tblrecord_increased.grand_total') {
            $_data = number_format($aRow[$aColumns[$i]]);
        }
        else if($aColumns[$i] == 'tblrecord_increased.record_reduce') {
            if($aRow['tblrecord_increased.record_reduce'] == 0)
            {
                $_data= 'Đang sử dụng';
            }else
            {
                $_data= 'Ngưng sử dụng';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
