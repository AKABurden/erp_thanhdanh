<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hasPermissionDelete = has_permission('warehouse', '', 'delete');
$hasPermissionEdit = has_permission('warehouse', '', 'edit');
$aColumns     = array(
    'id',
    'warehouse',
    'code',
    'name',
    'status',
    'date_create',
    '1',
);
$sIndexColumn = "id";
$sTable       = 'tbllocaltion_warehouses';
$where        = array();
$join         = array();

array_push($where, 'AND (id_parent is null or id_parent=0)');
array_push($where, 'AND tbllocaltion_warehouses.pod_id = 0 AND tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.productions_plan_id = 0 AND tbllocaltion_warehouses.stage_id = 0');
//array_push($where, 'AND child=0');
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'code','child'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    $string=button_child_localtion_warehouses($aRow['id'],'<i class="fa fa-caret-right" aria-hidden="true"></i><i class="fa fa-caret-right" aria-hidden="true"></i>'.$aRow['name'].'<i class="fa fa-caret-right" aria-hidden="true"></i>');
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name')
        {
            $_data='<p '.($aRow['child']==1?'class="text-danger"':'').'>'.$aRow['name'].'
                        
                    </p>';
            $_data.=$string['name'];
        }
        if ($aColumns[$i] == 'warehouse')
        {
            $warehouse=get_table_where('tblwarehouse',array('id'=>$aRow['warehouse']),'','row');
            if (!empty($warehouse)) {
                $_data = $warehouse->name;
            } else {
                $_data = '';
            }
        }
        if ($aColumns[$i] == 'date_create')
        {
            $_data='<p>'._dt($aRow['date_create']).'</p>';
            $_data.=$string['date_create'];
        }
        if ($aColumns[$i] == 'status')
        {

            $_data='<p class="onoffswitch '.($aRow['status'] == 0 ? 'onoffswitch_ch' : 'onoffswitch_chc' ).'" data-toggle="tooltip" data-switch-url="' . admin_url() . 'warehouse/change_warehouse_localtion_status/'.$aRow['id'].'/'.$aRow['status'].'" data-title="' . _l('') . '">
                <input type="checkbox"' . (!has_permission('warehouse_localtion', '', 'edit') ? ' disabled="disabled"' : '') . ' data-switch-url="' . admin_url() . 'warehouse/change_warehouse_localtion_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['status'] == 0 ? 'checked' : '') . '>
                <label style="height: 23px;" class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
                </p>';
            $_data.=$string['status'];
        }
        if ($aColumns[$i] == '1')
        {
            $_data='';
            if ($aRow['id'] != LOCATIONS_DEFAULT_MANUFACTURES) {
                if(has_permission('warehouse_localtion', '', 'delete'))
                {
                    $_data.='<p>'.(($aRow['child'] == 1 )? '<a onclick="delete_localtion_warehouses('.$aRow['id'].')" class="btn btn-danger  btn-icon pull-right" data-toggle="tooltip" data-placement="left"  title="Xóa vị trí kho thì phải chuyển dời tất cả các mặc hàng trên kệ">
                                <i class="fa fa-remove"></i>
                            </a>' : '' );
                }
                if(has_permission('warehouse_localtion', '', 'edit'))
                {
                    $_data.='
                            <a class="btn btn-default btn-icon pull-right" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Đang tải..." onclick="new_localtion_warehouse('.$aRow['id'].',this)">
                                <i class="fa fa-pencil-square-o"></i>
                        </a></p>';
                }
            }
            $_data.=$string['delete'];
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
