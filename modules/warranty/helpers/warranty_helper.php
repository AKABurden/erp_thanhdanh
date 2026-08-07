<?php

defined('BASEPATH') or exit('No direct script access allowed');

function process_purchases_by_warranty($id='')
{

    $data[0] = _l('ch_data_pr');
    $data[1] = _l('ch_data_rfq');
    $data[2] = _l('ch_data_quotes');
    $data[3] = _l('ch_data_order');
    $data[4] = _l('ch_data_cancel');
    	$purchases = get_table_where('tblpurchases',array('id'=>$id),'','row');
        $string_Row = '<ul class="progressbar" style="display: flex;flex-direction: row;justify-content: center;">';
        $string_Row .= '<li class="active">';
        $string_Row .= '    <a class="pointer #ff6f00" status-procedure="1" >';
        $string_Row .=          mb_convert_case($data[0], MB_CASE_TITLE, "UTF-8");
        $string_Row .= '    </a>';
        $string_Row .= '    <br><br>';
        $string_Row .= '    <span>';
        $string_Row .=          $purchases->prefix . $purchases->code;
        $string_Row .= '    </span>';
        $string_Row .='</li>';
        
        if(!empty($purchases->process))
        {
        $process = explode('|', $purchases->process);
        if($process[0] == 1)
        {
            $ask_price = get_table_where('tblrfq_ask_price',array('id'=>$process[1]),'','row');
            $dataRow = '<span>'.$ask_price->prefix.'-'.$ask_price->code.'</span>';
            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00" status-procedure="1" >';
            $string_Row .=      mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
            $string_Row .=      '</a><br><br>'.$dataRow;
            $count_items_quote_rfq = count_items_quote_rfq($id,$ask_price->id);
            $string_Row .= '</li>';
            $idquotes = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$ask_price->id),'','row');
            if(!empty($idquotes))
            {
              $process = explode('|', $ask_price->process);
              if(!empty($idquotes))
                  {
                      $id_quotes= array();
                      $supplier_quotes = get_table_where('tblsupplier_quotes',array('id'=>$idquotes->id),'','row');
                      $quotes = get_table_where('tblsupplier_quotes',array('id_ask_price'=>$ask_price->id));
                                    $count = count($quotes);
                                    $_data = '';
                                    foreach ($quotes as $k => $v) {
                                        $order = get_table_where('tblpurchase_order',array('id_quotes'=>$v['id']),'','row');
                                            if(!empty($order)){
                                            $id_quotes[] = $v['id'];
                                            }
                                        $_data.= '<li class="hoang"><a>' . $v['prefix'].'-'.$v['code'] . '</a></li>';
                                    }
                                    $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.$count.' '._l('ch_quote_count').'
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                                    $_outputStatus .= $_data;
                                    $_outputStatus .= '</ul></div>';



                      $string_Row .= '<li class="active">';
                      $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                      $string_Row .=      mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                      $string_Row .=      '</a><br><br>'.$_outputStatus;
                      $string_Row .='</li>';
                      if(!empty($id_quotes))
                      {
                        if(!empty($id_quotes))
                        {
                            // hauhauhau
                            $count=0;
                            $_data = '';
                            foreach ($id_quotes as $keyorder => $valueorder) {
                                    $purchase_order = get_table_where('tblpurchase_order',array('id_quotes'=>$valueorder));
                                    $count+= count($purchase_order);
                                    foreach ($purchase_order as $k => $v) {
                                        $_data.= '<li class="hoang"><a>' . $v['prefix'].'-'.$v['code'] . '</a></li>';
                                    }
                            }
                                    $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count).'
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                                    $_outputStatus .= $_data;
                                    $_outputStatus .= '</ul></div>';
                            $string_Row .= '<li class="active">';
                            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                            $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                            $string_Row .=      '</a><br><br>'.$_outputStatus;
                            $string_Row .='</li>';
                        }
                      }else
                      {
	                       	$string_Row .= '<li class="">';
	                        $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
	                        $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
	                        $string_Row .=      '</a>';
	                        $string_Row .='</li>';
                      }

                  }else
                  if($process[0] == 3)
                  {
                      $string_Row .= '<li class="active">';
                      $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                      $string_Row .=      mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                      $string_Row .=      '</a>';
                      $string_Row .='</li>';

                  }
            }else
            {
	            $string_Row .= '<li class="">';
	            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
	            $string_Row .=      mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
	            $string_Row .=      '</a>';
	            $string_Row .='</li>';
	            $string_Row .= '<li class="">';
	            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
	            $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
	            $string_Row .=      '</a>';
	            $string_Row .='</li>';
            }

        }else
        if($process[0] == 2)
        {
            $supplier_quotes = get_table_where('tblsupplier_quotes',array('id'=>$process[1]),'','row');
            $quotes  = $supplier_quotes->prefix.'-'.$supplier_quotes->code;
            $dataRow = '<a href="#" onclick="view_supplier_quotes('.$supplier_quotes->id.'); return false;" >' . purchase_quote($quotes) . '</a>';
            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
            $string_Row .=      mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
            $string_Row .=      '</a><br><br>'.$dataRow;
            $string_Row .='</li>';
            if(!empty($supplier_quotes->process))
            {
              $process = explode('|', $supplier_quotes->process);
              if($process[0] == 3)
              {
                                    $order = get_table_where('tblpurchase_order',array('id'=>$process[1]),'','row');
                                    $purchase_order = get_table_where('tblpurchase_order',array('id_quotes'=>$order->id_quotes));
                                    $count = count($purchase_order);
                                    $_data = '';
                                    foreach ($purchase_order as $k => $v) {
                                        $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$v['id'].'); return false;" >' . $v['prefix'].'-'.$v['code'] . '</a></li>';
                                    }
                                    $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count).'
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                                    $_outputStatus .= $_data;
                                    $_outputStatus .= '</ul></div>';
                  $string_Row .= '<li class="active">';
                  $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                  $string_Row .=      mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                  $string_Row .=      '</a><br><br>'.$_outputStatus;
                  $string_Row .='</li>';
              }
            }else
            {
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
            }

        }else
        {
            $order = get_table_where('tblpurchase_order',array('id_purchases'=>$id),'','row');
            if(!empty($order)){
            $purchase_order = get_table_where('tblpurchase_order',array('id_purchases'=>$order->id_purchases));

            $count = count($purchase_order);
            $_data = '';
            foreach ($purchase_order as $k => $v) {
                $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$v['id'].'); return false;" >' . $v['prefix'].'-'.$v['code'] . '</a></li>';
            }

            $order_all = get_table_where('tblpurchases',array('id'=>$id),'','row');

            if(!empty($order_all->id_order))
            {
            $orders = get_table_where('tblpurchase_order',array('id'=>$order_all->id_order),'','row');
            $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$orders->id.'); return false;" >' . $orders->prefix.'-'.$orders->code . '</a></li>';
            $count=$count+1;
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count).'
                        </button>
                        <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';

            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
            $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
            $string_Row .=      '</a><br><br>'.$_outputStatus;
            $string_Row .='</li>';
            }else
            {
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
                $string_Row .=      mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                $string_Row .=      mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
            }
        }
        }
        else
        {
            // hau

            $order = get_table_where('tblpurchase_order',array('id_purchases'=>$id),'','row');
            $order_all = get_table_where('tblpurchases',array('id'=>$id),'','row');
            if(!empty($order)){
            $purchase_order = get_table_where('tblpurchase_order',array('id_purchases'=>$order->id_purchases));

            $count = count($purchase_order);
            $_data = '';
            foreach ($purchase_order as $k => $v) {
                $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$v['id'].'); return false;" >' . $v['prefix'].'-'.$v['code'] . '</a></li>';
            }


            if(!empty($order_all->id_order))
            {
            $orders = get_table_where('tblpurchase_order',array('id'=>$order_all->id_order),'','row');
            $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$orders->id.'); return false;" >' . $orders->prefix.'-'.$orders->code . '</a></li>';
            $count=$count+1;
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count).'
                        </button>
                        <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';

            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
            $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
            $string_Row .=      '</a><br><br>'.$_outputStatus;
            $string_Row .='</li>';
            }elseif(!empty($order_all->id_order))
            {

                $_data='';
                $count1=0;
                $orders = get_table_where('tblpurchase_order',array('id'=>$order_all->id_order),'','row');
                $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$orders->id.'); return false;" >' . $orders->prefix.'-'.$orders->code . '</a></li>';
                $order = get_table_where('tblpurchase_order',array('id_purchases'=>$id),'','row');
                if(!empty($order)){
                $purchase_order = get_table_where('tblpurchase_order',array('id_purchases'=>$order->id_purchases));
                $count1 = count($purchase_order);
                $_data = '';
                foreach ($purchase_order as $k => $v) {
                    $_data.= '<li class="hoang"><a onclick="view_purchase_order('.$v['id'].'); return false;" >' . $v['prefix'].'-'.$v['code'] . '</a></li>';
                }
                }
                $count=1+$count1;
                $_outputStatus = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">'.count_number_PO_ch($count).'
                            </button>
                            <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                $_outputStatus .= $_data;
                $_outputStatus .= '</ul></div>';

                $string_Row .= '<li class="active">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a><br><br>'.$_outputStatus;
                $string_Row .='</li>';
            }
            else
            {
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
                $string_Row .=      mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                $string_Row .=      mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .=      mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .=      '</a>';
                $string_Row .='</li>';
            }

        }
    $string_Row.='<div class="clearfix"></div></ul>';
    return $string_Row;
}

function print_pdf_warranty($data)
{
    $CI =& get_instance();
    $CI->load->library('pdf');
    $font_name      = get_option('pdf_font');
    $font_size      = get_option('pdf_font_size');
    $formatArray = get_pdf_format('pdf_format_invoice');
    $pdf         = new Pdf($formatArray['orientation'], 'mm', $formatArray['format'], true, 'UTF-8', false,false,'data');

    $font_name = get_option('pdf_font');
    $font_size = get_option('pdf_font_size');
    if ($font_size == '') {
        $font_size = 10;
    }
    if(!empty($data->code_vouchers)) {
        $pdf->SetTitle($data->code_vouchers);
    }
    $pdf->SetMargins(10, 45, -1);
    // $pdf->SetAutoPageBreak(TRUE, 0);
    // $pdf->setPrintFooter(false);
    // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setPrintFooter(false);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAuthor(get_option('company'));
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->AddPage($formatArray['orientation'], $formatArray['format']);
    if ($CI->input->get('print') == 'true') {
        $js = 'print(true);';
        $pdf->IncludeJS($js);
    }

    # Dont remove these lines - important for the PDF layout
    // Add <br /> tag and wrap over div element every image to prevent overlaping over text
    $data->content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<div>$1</div>', $data->content);
    // Add cellpadding to all tables inside the html
    $data->content = preg_replace('/(<table\b[^><]*)>/i', '$1 cellpadding="5">', $data->content);
    // Remove white spaces cased by the html editor ex. <td>  item</td>
    $data->content = preg_replace('/[\t\n\r\0\x0B]/', '', $data->content);
    $data->content = preg_replace('/([\s])\1+/', '', $data->content);

    // Tcpdf does not support float css we need to adjust this here
    $data->content = str_replace('float: right', 'text-align: center', $data->content);
    $data->content = str_replace('float: left', 'text-align: center', $data->content);
    // Image center
    $data->content = str_replace('margin-left: auto; margin-right: auto;', 'text-align:center;', $data->content);

    $pdf->writeHTML(''.$data->content, true, 0, true, true);
    return $pdf;
}


function decreaseWarehuseQuantity_bh($warehouse_id,$localtion,$product_id,$quantity,$type_items,$series)
{
    $CI = &get_instance();
    if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity)&& is_numeric($localtion)) {
        
        $product=$CI->db->get_where('tblwarehouse_items',array('id_items'=>$product_id,'warehouse_id'=>$warehouse_id,'localtion'=>$localtion,'type_items'=>$type_items,'series'=>$series))->row(); 
        if($product)
        {
            $total_quantity=$product->product_quantity-$quantity;
            $CI->db->update('tblwarehouse_items',array('product_quantity'=>$total_quantity),array('id'=>$product->id));
        }
        if($CI->db->affected_rows()>0) 
            return true;
    }
    return false;
}

function increaseProductQuantity_bh($warehouse_id,$id_import,$date_warehouse,$date_import,$product_id,$quantity,$localtion,$type_items,$pirce,$series)
{     
    $CI = &get_instance();
    if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity)&& is_numeric($id_import)) {
            $data=array(
                'product_id'=>$product_id,
                'warehouse_id'=>$warehouse_id,
                'quantity'=>$quantity,
                'localtion'=>$localtion,
                'import_id'=>$id_import,
                'type_items'=>$type_items,
                'date_import'=>$date_import,
                'date_warehouse'=>$date_warehouse,
                'quantity_left'=>$quantity,
                'quantity_export'=>0,
                'type_export'=>35,
                'price'=>0,
                'series'=>$series,
                );
            $CI->db->insert('tblwarehouse_product',$data);
        }
        if($CI->db->affected_rows()>0) 
        {
            return true;
        }
    return false;
}

function increaseWarehuseQuantity_bh($warehouse_id,$localtion,$product_id,$quantity,$type_items,$series)
{
    $CI = &get_instance();
    if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity)&& is_numeric($localtion)) {
        $product=$CI->db->get_where('tblwarehouse_items',array('id_items'=>$product_id,'warehouse_id'=>$warehouse_id,'localtion'=>$localtion,'type_items'=>$type_items, 'series'=>$series))->row(); 
        if($product) {
            $total_quantity=$quantity+$product->product_quantity;
            $CI->db->update('tblwarehouse_items',array('product_quantity'=>$total_quantity),array('id'=>$product->id));
        }
        else {
            $data=array(
                'id_items'=>$product_id,
                'warehouse_id'=>$warehouse_id,
                'product_quantity'=>$quantity,
                'localtion'=>$localtion,
                'type_items'=>$type_items,
                'series'=>$series,
                'type_series'=>1
            );
            $CI->db->insert('tblwarehouse_items',$data);
        }

        if($CI->db->affected_rows()>0) 
        return true;
    }
    return false;
}