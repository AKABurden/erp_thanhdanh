<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hasPermissionDelete = has_permission('pay_slip', '', 'delete');
$tbOrders = "(
    SELECT
        tbl_invoice_items.invoice_id as invoice_id,
        SUM(tbl_deliveries.grand_total_items) as grand_total_items,
        0 as cost_delivery,
        SUM(tbl_deliveries.total_tax) as total_tax,
        SUM(tbl_deliveries.grand_total) as grand_total,
        GROUP_CONCAT(tbl_deliveries.reference_no SEPARATOR '<br>') as reference_orders
    FROM tbl_invoice_items
    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_invoice_items.object_id
    GROUP BY tbl_invoice_items.invoice_id
) tb_orders";
$aColumns     = array(
    'tbl_invoices.id',
    'tbl_invoices.reference_no',
    'tb_orders.grand_total as grand_total',
    '(COALESCE(tbl_invoices.total_payment,0))  as total_payment_order',
    '1'
);
$sIndexColumn = "id";
$sTable       = 'tbl_invoices';
$where        = array(
  
);
$vouchers_coupon = get_table_where('tblvouchers_coupon',array('id'=>$id),'','row');
if(!empty($vouchers_coupon))
{  
$id_order = explode(',', $vouchers_coupon->arr_code_orders);
$order = array();
$total_order = array();
foreach ($id_order as $key => $value) {
    $v = explode('|', $value);
    $order[] = $v[0];
    $total_order[$v[0]]=$v[1];
}
array_push($where, 'AND tbl_invoices.id IN('.implode(',', $order).')');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,array('INNER JOIN ' . $tbOrders . ' ON tb_orders.invoice_id = tbl_invoices.id',), $where, array(
));
$views =array();
$view = $this->ci->input->post('view');
if(!empty($view)) {
    $views = explode(',',$view);
}
$output       = $result['output'];
$rResult      = $result['rResult'];
$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = '';
        }
        // if(in_array($aRow['tbl_invoices.id'], $views))
        // {
        // $style = '<span onclick="no_view('.$aRow['tbl_invoices.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_close.png').'"></i></span>';    
        // }else
        // {
        // $style = '<span onclick="view('.$aRow['tbl_invoices.id'].'); return false;" style="padding: 1px 5px 0px 7px;cursor:pointer;" ><img src="'.base_url('assets/images/details_open.png').'"></span>';
        // }
        if ($aColumns[$i] == '1') {
        $_data ='<div class="text-right">'.number_format($total_order[$aRow['tbl_invoices.id']]).'</div>';
        }
        if ($aColumns[$i] == 'tbl_invoices.id') {
        $_data ='<div class="text-center">'.$j.'</div>';
        }
        if ($aColumns[$i] == '(COALESCE(tbl_invoices.total_payment,0))  as total_payment_order') {
        $_data ='<div class="text-right">'.number_format($aRow['total_payment_order'] - $total_order[$aRow['tbl_invoices.id']]).'</div>';
        }
        if ($aColumns[$i] == 'tb_orders.grand_total as grand_total') {
        $_data ='<div class="text-right">'.number_format($aRow['grand_total']).'</div>';
        }
        if ($aColumns[$i] == 'tbl_invoices.reference_no') {
        $imports  = $aRow['tbl_invoices.reference_no'];
        $_data=$imports;
        }

        $row[] = $_data;
    }
    $_data='';
    $output['aaData'][] = $row;
    // if(in_array($aRow['tbl_invoices.id'], $views))
    //     {
    //         $this->ci->load->model('orders_model');
    //         $import = $this->ci->orders_model->getOrderItemsByOrderId($aRow['tbl_invoices.id']);
    //         foreach ($import as $key => $value) {
    //             $row=[];
    //             $row[]='<div class="text-center">'.($key+1).'</div>';
    //             $row[]='<div class="type-item">'.(($value['type_item'] == "products") ? '<span class="label label-success">'.lang($value['type_item']).'</span>' : '<span class="label label-primary">'.lang('ch_items').'</span>').'</div>';
    //             $row[]=$value['item_name'].'('.$value['item_code'].')';
    //             $row[]='<div class="text-center">'.number_format($value['quantity']).'</div>';
    //             $row[]='<div class="text-right">'.number_format($value['price']).'</div>';
    //             $row[]='<div class="text-right">'.number_format(($value['tax_rate_item']/100)*$value['quantity']*$value['price']).'</div>';
    //             $row[]='<div class="text-right">'.number_format($value['discount_percent_amount_item']).'</div>';
    //             $row[]='<div class="text-right">'.number_format($value['total_amount']).'</div>';
    //             $output['aaData'][] = $row;
    //         }
    //     }
}
