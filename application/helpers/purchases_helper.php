<?php

defined('BASEPATH') or exit('No direct script access allowed');

function format_purchase_status($status, $classes = '', $label = true)
{
    $id          = $status;
    
    if($status==0)
    {
        $label_class = 'warning';
    }
    else
    {
        $label_class = 'success';
    }
    if ($status == 4) {
        $status = _l('ch_cancel');
    }
    else if ($status == 3) {
        $status = _l('ch_confirm_22');
    } else if ($status == 1) {
        $status = _l('dont_confirm');
    }
    else if ($status == 2) {
        $status = _l('ch_confirm_d');
    }
    else if ($status == -1) {
        $status = _l('ch_purchases_new');
    }
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
function format_import_status($status, $classes = '', $label = true)
{
    $id          = $status;
    
    if($status==0)
    {
        $label_class = 'warning';
    }
    else
    {
        $label_class = 'success';
    }
    if ($status == 1) {
        $status = _l('dont_approve');
    }
    else if ($status == 2) {
        $status = _l('ch_confirm_22');
    }
    else if ($status == -1) {
        $status = _l('ch_purchases_new');
    }
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
function format_supplers_status($status, $classes = '', $label = true)
{
    $id          = $status;
    if($status==0)
    {
        $label_class = 'warning';
    }
    else
    {
        $label_class = 'success';
    }
    if ($status == 2) {
        $status = _l('ch_confirm_22');
    } else if ($status == 1) {
        $status = _l('dont_approve');
    }
    else if ($status == -1) {
        $status = _l('ch_purchases_new');
    }
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
function get_status_label_purchases($id)
{
    $label = 'default';

    if ($id == 2) {
        $label = 'light-green';
    } else if ($id == 3) {
        $label = 'default';
    } else if ($id == 4) {
        $label = 'info';
    } else if ($id == 5) {
        $label = 'success';
    } else if ($id == 6) {
        $label = 'warning';
    }

    return $label;
}
function format_status_pay_slip_s($id, $classes = '', $label = true)
{
    if($id==0)
    {
        $label_class = 'warning';
    }
    else
    {
        $label_class = 'success';
    }
    if ($id == 1) {
        $status = _l('ch_received_total');
    } else if ($id == 0) {
        $status = _l('ch_not_received');
    }
    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
    } else {
        return $status;
    }
}
function format_type_invoice($id)
{
            if ($id == 1) {
                $label = 'danger';
                $status_name='✔ '._l('ch_invoice_tax');
            } else if ($id == 0) {
                $label = 'success';
                $status_name='✔ '._l('ch_retail_invoice');
            }
            $class = 'label label-' . $label;
            return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
function format_status_inventory($status, $classes = '', $label = true)
{

        $id          = $status;
    
            if($status==0)
            {
                $label_class = 'warning';
            }
            else
            {
                $label_class = 'success';
            }
            if ($status == 0) {
                $status = _l('dont_approve');
            }
            else if ($status == 1) {
                $status = _l('ch_confirm_22');
            }
            else if ($status == -1) {
                $status = _l('ch_purchases_new');
            }
            if ($label == true) {
                return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status invoice-status-' . $id . '">' . $status . '</span>';
            } else {
                return $status;
            }
}
function format_status_purchases($id)
{

        $label = get_status_label_purchases($id);
        if ($id == 2) {
            $label = 'light-green';
            $status_name=_l('ch_confirm_22');
        }
        else if ($id == 1) {
                $label = 'info';
                $status_name=_l('dont_approve');
            } else if ($id == 0) {
                $label = 'warning';
                $status_name=_l('dont_approve');
            }
            $class = 'label label-' . $label;
            return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
function format_item_purchases($id)
{
        if($id == 'products')
        {
           $id = 'product';
        }
        if($id == 'semi_products')
        {
           $id = 'product';
        }
        if($id == 'materials')
        {
           $id = 'nvl';
        }
        if($id == 'tools_supplies')
        {
           $id = 'tools'; 
        }
        $name = get_table_where('tbltype_items',array('type'=>$id),'','row');
        $label = get_status_label_purchases($name->id);
        if ($id == 2) {
            $label = 'light-green';
            $item_name=$name;
        }
        else if ($id == 1) {
                $label = 'info';
                $item_name=$name;
            } else if ($id == 0) {
                $label = 'warning';
                $item_name=$name->name;
            }
            $class = 'label label-' . $label;
            return '<span class="inline-block ' . $class . '">' . $name->name . '</span>';
}
function format_item_color_v22($id,$type,$hide=0)
{
    $label = 'success';
    $text_hide = '';
    if($hide == 0)
    {
        $text_hide ='';
    }
    if($type =='items')
    {
        $items = get_table_where('tblitems',array('id'=>$id),'','row');
        if(!empty($items->color_id)){
        $color = get_table_where('tbl_colors',array('id'=>$items->color_id),'','row');
        if(!empty($color))
        {
        
        $item_name=$color->name;
        return $text_hide.'<span class="label" style="border: 1px solid '.$color->color.';color:'.$color->color.'">' . $item_name . '</span>';    
        }else
        {
        $item_name='NONE'; 
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';
        }
        }else
        {
        $item_name='NONE'; 
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';
        }
        
    }
    if($type =='product')
    {   
        $html = '';
        $items = get_table_where('tbl_products_colors',array('product_id'=>$id));
        if(!empty($items))
        {
        foreach ($items as $key => $value) {
        if(!empty($value['color_id']))
        {
        $color = get_table_where('tbl_colors',array('id'=>$value['color_id']),'','row');
        if(!empty($color))
        {
        $item_name=$color->name;
        $html.= $text_hide.'<span class="label" style="border: 1px solid '.$color->color.';color:'.$color->color.'">' . $item_name . '</span>';
        }
        }
        }
        return $html;
        }else
        {
        $item_name='NONE';   
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';  
        }
    }else
    {
        return '';
    }
}
function format_item_color($id,$type,$hide=0)
{
    $label = 'success';
    $text_hide = '';
    if($hide == 0)
    {
        $text_hide =_l('ch_color').': ';
    }
    if($type =='items')
    {
        $items = get_table_where('tblitems',array('id'=>$id),'','row');
        if(!empty($items->color_id)){
        $color = get_table_where('tbl_colors',array('id'=>$items->color_id),'','row');
        if(!empty($color))
        {
        
        $item_name=$color->name;
        return $text_hide.'<span class="label" style="border: 1px solid '.$color->color.';color:'.$color->color.'">' . $item_name . '</span>';    
        }else
        {
        $item_name='NONE'; 
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';
        }
        }else
        {
        $item_name='NONE'; 
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';
        }
        
    }
    if($type =='product')
    {   
        $html = '';
        $items = get_table_where('tbl_products_colors',array('product_id'=>$id));
        if(!empty($items))
        {
        foreach ($items as $key => $value) {
        if(!empty($value['color_id']))
        {
        $color = get_table_where('tbl_colors',array('id'=>$value['color_id']),'','row');
        if(!empty($color))
        {
        $item_name=$color->name;
        $html.= $text_hide.'<span class="label" style="border: 1px solid '.$color->color.';color:'.$color->color.'">' . $item_name . '</span>';
        }
        }
        }
        return $html;
        }else
        {
        $item_name='NONE';   
        return $text_hide.'<span class="label" style="border: 1px solid rgba(188,183,189,0.92);color:rgba(188,183,189,0.92)">' . $item_name . '</span>';  
        }
    }else
    {
        return '';
    }
}
/**
 * Load lead language
 * Used in public GDPR form
 * @param  string $lead_id
 * @return string return loaded language
 */