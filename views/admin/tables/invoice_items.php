<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('invoice_items', '', 'delete')) {
    $aColumns[] = '1';
}
$custom_fields = get_table_custom_fields('items');
$this->ci->db->query("SET sql_mode = ''");
$aColumns = array_merge($aColumns, [
    'tblitems.id',
    'tblcategories.category',
    'tblitems.code',
    'tblitems.name',
    'tblunits.unit',
    'COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity',
    'tbl_colors.color',
    'tbl_packaging.name',
    'tblitems.price',
    'tblitems.product_features',
    'tblitems.staff_id',
    'tblitems_groups.name',
    'tblitems.minimum_quantity',
    'tblitems.active',
    'tblitems.calculated_on_sales',
]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'items';
$where=array();
$join = [
    'LEFT JOIN tblunits ON tblunits.unitid=tblitems.unit',
    'LEFT JOIN tblitems_groups ON tblitems_groups.id=tblitems.group_id',
    'LEFT JOIN tblcategories ON tblcategories.id=tblitems.category_id',
    'LEFT JOIN tbl_colors ON tbl_colors.id=tblitems.color_id',
    'LEFT JOIN tbl_packaging ON tbl_packaging.id=tblitems.packaging_id',
    'LEFT JOIN tblwarehouse_items ON tblwarehouse_items.id_items=tblitems.id AND  tblwarehouse_items.type_items = "items"',
];
$additionalSelect = [
    'tblitems.id',
    'tblitems.type',
    'tblitems.avatar',
    'tbl_colors.name as name_color',
];
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'items.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="items" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
$join = hooks()->apply_filters('items_table_sql_join', $join);
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
    if(has_permission('invoice_items','','view_own')&&!is_admin())
    {
         array_push($where, 'AND  tblitems.staff_id = '.get_staff_user_id());
    }
$groupBy = 'GROUP BY tblitems.id';
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect,$groupBy);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == '1') {
            $_data= '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        }
        if($aColumns[$i] == 'tbl_colors.color') {
            $_data ='';
            if(!empty($aRow['tbl_colors.color']))
            {
            $_data = '<span class="label" style="border: 1px solid '.$aRow['tbl_colors.color'].';color:'.$aRow['tbl_colors.color'].'">' . $aRow['name_color'] . '</span>';
            }
        }
        if($aColumns[$i] == 'tblitems.id') {
            $_data='<div  class="preview_image text-center" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img-'.$aRow['id'].'">
                            <div style="width:100px">
                                <a href="'.(file_exists($aRow['avatar']) ? base_url($aRow['avatar']) : base_url('assets/images/preview-not-available.jpg')).'" data-lightbox="customer-profile" class="display-block mbot5">
                                        <img src="'.(file_exists($aRow['avatar']) ? base_url($aRow['avatar']) : base_url('assets/images/preview-not-available.jpg')).'" style="border-radius: 50%;width: 45%;height: 45px;" />
                                </a>
                            </div>
                        </div>
                    </div>';
        }else
        if ($aColumns[$i] == 'tblitems.code') {
            if($aRow['type'] == 1)
            {
            $__data='<span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">' ._l('ch_standard'). '</span>';
            }else
            {
            $__data='<span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #3409de">' ._l('COMBO'). '</span>';   
            }
            $_data = '<a href="#" onclick="int_items_view(' . $aRow['id'] . '); return false;" data-id="' . $aRow['id'] . '">' . $_data . '</a><br>'.$__data;
            $_data .= '<div class="row-options">';
            // if (has_permission('items', '', 'view')||($aRow['tblitem.staff_id'] == get_staff_user_id())) {
            $_data .= '<a  href="#" onclick="int_items_view(' . $aRow['id'] . '); return false;" data-toggle="modal" data-id="' . $aRow['id'] . '">' . _l('view') . '</a>';
            // }
            if (has_permission('invoice_items', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url() . 'invoice_items/item/'.$aRow['id'].'" data-toggle="modal" data-id="' . $aRow['id'] . '">' . _l('edit') . '</a>';
            }

            if (has_permission('invoice_items', '', 'delete')) {
                $_data .= ' | <a href="#" onclick="delete_items('.$aRow['id'].')" class="text-danger delete-remind">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        }else
        if ($aColumns[$i] == 'tblitems.price') {
            $_data = number_format($_data);
        }elseif ($aColumns[$i] == 'tblitems.staff_id') {
            $_data = get_staff_full_name($_data);
        }else if ($aColumns[$i] == 'COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity') {
            if($aRow['product_quantity'] > 0)
            {
            $this->ci->db->select('SUM(tblwarehouse_items.product_quantity) as product_quantity,tblwarehouse.name as name_ware');
            $this->ci->db->where('type_items',"items");
            $this->ci->db->where('id_items',$aRow['id']);
            $this->ci->db->group_by('tblwarehouse_items.warehouse_id');
            $this->ci->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id', 'left');
            $get_quanti = $this->ci->db->get('tblwarehouse_items')->result_array();
            $title='';
            foreach ($get_quanti as $key => $value) {
                $title .= $value['name_ware'].': '.formatNumber($value['product_quantity']).'<br>';    
            }
            $_data =  '<div class="== text-center"><span style="color:red;font-weight: 500;font-size: 20px;" data-html="true" data-toggle="tooltip" data-title="' .$title. '" class="text-has-action ch_ch_hover">'.formatNumber($aRow['product_quantity']).'</span></div>';
            }else
            {
            $_data ='<span style="color:red;font-weight: 500;font-size: 20px;">'.formatNumber($aRow['product_quantity']).'</span>';    
            }
        }elseif ($aColumns[$i] == 'tblitems.active') {
        $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
        <input type="checkbox"' . (!    is_admin() ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'invoice_items/change_items_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['tblitems.active'] == 1 ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
        </div>';

        $toggleActive .= '<span class="hide">' . ($aRow['id'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

        $_data = $toggleActive;
        }
        else if($aColumns[$i] == 'tblitems.calculated_on_sales') {
            $toggleActive = '<div class="onoffswitch" style="left: calc(50% - 25px);">
                                <input type="checkbox"' . (!    is_admin() ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'invoice_items/change_items_calculated" name="onoffswitch" class="onoffswitch-checkbox" id="c' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['tblitems.calculated_on_sales'] == 1 ? 'checked' : '') . '>
                                <label class="onoffswitch-label" for="c' . $aRow['id'] . '"></label>
                            </div>';
            $toggleActive .= '<span class="hide">' . ($aRow['id'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
            $_data = $toggleActive;
        }
        else {
            if (startsWith($aColumns[$i], 'ctable_') && is_date($_data)) {
                $_data = _d($_data);
            }
            if (startsWith($aColumns[$i], 'ctable_') && is_numeric($_data)) {
                $_data = number_format($_data);
            }
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }


    $output['aaData'][] = $row;
}
