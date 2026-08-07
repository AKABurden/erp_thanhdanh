<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = array(
    '1',
    'tblmainstream_goods.id',
    'tblmainstream_goods.id_items',
    'tblmainstream_goods.price',
    '2',
);
$sIndexColumn = "id";
$sTable = 'tblmainstream_goods';
$where = array(
    'AND id_suppliers="' . $id . '"',
);
$join = array();
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblmainstream_goods.type',
));
$output = $result['output'];
$rResult = $result['rResult'];
$j = 0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aRow['type'] == 'items') {
            $name = get_table_where('tblitems', array('id' => $aRow['tblmainstream_goods.id_items']), '', 'row');
            if (empty($name)) {
                $row = array();
                continue;
                break;
            }
        } else {
            $table = get_table_where('tbltype_items', array('type' => $aRow['type']), '', 'row');
            $name = get_table_where($table->table, array('id' => $aRow['tblmainstream_goods.id_items']), '', 'row');
            if (empty($name)) {
                $row = array();
                continue;
                break;
            }
            $name->avatar = $name->images;

        }
        if ($aColumns[$i] == '1') {
            $_data = '<div class="text-center"><img style="border-radius: 50%;width: 4em;height: 4em;" src="' . (!empty($name->avatar)
                    ? (file_exists(
                        $name->avatar
                    )
                        ? base_url($name->avatar)
                        : (file_exists('uploads/materials/' . $name->avatar)
                            ? base_url(
                                'uploads/materials/' . $name->avatar
                            )
                            : (file_exists('uploads/products/' . $name->avatar) ? base_url(
                                'uploads/products/' . $name->avatar
                            ) : base_url('assets/images/preview-not-available.jpg'))))
                    : base_url(
                        'assets/images/preview-not-available.jpg'
                    )) . '"><br>' . format_item_purchases($aRow['type']) . '</div>';
        }
        else if ($aColumns[$i] == '2') {
            $_data = format_item_color($aRow['tblmainstream_goods.id_items'], $aRow['type'], 1);
        }
        else if ($aColumns[$i] == 'tblmainstream_goods.id') {
            $_data = '<a  href="#" onclick="int_items_view(' . $aRow['tblmainstream_goods.id_items'] . '); return false;" data-toggle="modal" data-id="' . $aRow['tblmainstream_goods.id_items'] . '">' . $name->code . '</a>';
        }
        else if ($aColumns[$i] == 'tblmainstream_goods.id_items') {
            $_data = $name->name;
        }
        else if ($aColumns[$i] == 'tblmainstream_goods.price') {
            $_data = '<div class="text-right">' . number_format($aRow['tblmainstream_goods.price']) . '</div>';
        }
        $row[] = $_data;
    }
    if(!empty($row)) {
        $_data = '';
        if (is_admin()) {
            $_data = '<a href="#" class="btn btn-danger btn-icon " delete_combo ="' . $aRow['tblmainstream_goods.id'] . '"  onclick="delete_items(' . $aRow['tblmainstream_goods.id'] . '); return false;"><i class="fa fa-remove"></i></a>';
            $row[] = $_data;
        } else {
            $row[] = '';
        }
        $output['aaData'][] = $row;
    }
}
