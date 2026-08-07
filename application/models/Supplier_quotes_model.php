<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Supplier_quotes_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_items_ch()
    {
    	$this->db->select('tblitems.*')->distinct();
        $this->db->from('tblitems');
        $this->db->order_by('tblitems.id', 'desc');
        return $this->db->get()->result_array();
    }
    public function get_full_edit($id)
    {
        $this->db->select('tblsupplier_quotes.*')->distinct();
        $this->db->from('tblsupplier_quotes');
        $this->db->where('id',$id);
        $supplier_quotes = $this->db->get()->row();
        $supplier_quotes->items = $this->get_items($id);
        return $supplier_quotes;

    }   
    public function get_items_rfq_order($id,$id_supplier)
    {
       
            $result['result'] = array();
            $table = get_table_where('tbltype_items');
            $temp = '';
            $dem_temp = 0;
            foreach ($table as $key => $value) {    
                $result['result'][$dem_temp] = array('id'=>'h','name'=>$value['name'],'type_items'=>$value['type']);
                $dem_temp++;
                if(!empty($id))
                {
                $this->db->select($value['table'].'.code as code_name,'.$value['table'].'.id,'.$value['table'].'.name,'.$value['id_type'].' as type,tblrfq_ask_price_items.quantity');
                $this->db->from('tblrfq_ask_price_items');
                }
                $this->db->group_by($value['table'].'.id');
                $this->db->order_by($value['table'].'.id', 'ASC');
                if(!empty($id))
                {
                $this->db->join($value['table'],$value['table'].'.id = tblrfq_ask_price_items.product_id');   
                $this->db->where('tblrfq_ask_price_items.id_rfq_ask_price',$id); 
                $this->db->where('tblrfq_ask_price_items.type',$value['type']);
                $this->db->where('tblrfq_ask_price_items.suppliers_id',$id_supplier);
                }
                $data = $this->db->get()->result_array();


                foreach ($data as $key_data => $value_data) {
                    $value_data=array_merge($value_data,array('type_items'=>$value['type']));
                    $result['result'][$dem_temp] = $value_data; 
                    $dem_temp++;
                        
                }
            }
        return $result['result'];
    } 
    public function get_items_quotes_combobox($id_purchases)
    {
       
            $result['result'] = array();
            $table = get_table_where('tbltype_items');
            $temp = '';
            $dem_temp = 0;
            foreach ($table as $key => $value) {    
                $result['result'][$dem_temp] = array('id'=>'h','name'=>$value['name'],'type_items'=>$value['type']);
                $dem_temp++;
                if(!empty($id_purchases))
                {
                $this->db->select($value['table'].'.code as code_name,'.$value['table'].'.id,'.$value['table'].'.name,'.$value['id_type'].' as type,tblsupplier_quote_items.quantity');
                $this->db->from('tblsupplier_quote_items');
                }
                $this->db->group_by($value['table'].'.id');
                $this->db->order_by($value['table'].'.id', 'ASC');
                if(!empty($id_purchases))
                {
                $this->db->join($value['table'],$value['table'].'.id = tblsupplier_quote_items.product_id');   
                $this->db->where('tblsupplier_quote_items.id_supplier_quotes',$id_purchases); 
                $this->db->where('tblsupplier_quote_items.type',$value['type']);
                }
                $data = $this->db->get()->result_array();
                foreach ($data as $key_data => $value_data) {
                    $value_data=array_merge($value_data,array('type_items'=>$value['type']));
                            $whereJoin=array();
                            $whereJoin['where']=array(
                              'id_items ' =>$value_data['id'],
                              'type_items ' =>$value_data['type_items'],
                            );
                            $whereJoin['join']=array();
                            $whereJoin['field']='product_quantity';
                            $quantity_warehoue=sum_from_table_join('tblwarehouse_items',$whereJoin);
                        $value_data['quantity_warehoue'] = $quantity_warehoue;
                    $result['result'][$dem_temp] = $value_data; 
                    $dem_temp++;
                }
            }
        return $result['result'];
    }
    public function get_items_quotes_order($id_purchases)
    {
       
            $result['result'] = array();
            $table = get_table_where('tbltype_items');
            $temp = '';
            $dem_temp = 0;
            foreach ($table as $key => $value) {    
                $result['result'][$dem_temp] = array('id'=>'h','name'=>$value['name'],'type_items'=>$value['type']);
                $dem_temp++;
                if(!empty($id_purchases))
                {
                $this->db->select($value['table'].'.code as code_name,'.$value['table'].'.id,'.$value['table'].'.name,'.$value['id_type'].' as type,tblpurchases_items.quantity_net');
                $this->db->from('tblpurchases_items');
                }
                $this->db->group_by($value['table'].'.id');
                $this->db->order_by($value['table'].'.id', 'ASC');
                if(!empty($id_purchases))
                {
                $this->db->join($value['table'],$value['table'].'.id = tblpurchases_items.product_id');   
                $this->db->where('tblpurchases_items.purchases_id',$id_purchases); 
                $this->db->where('tblpurchases_items.type',$value['type']);
                }
                $data = $this->db->get()->result_array();
                foreach ($data as $key_data => $value_data) {
                    $value_data=array_merge($value_data,array('type_items'=>$value['type']));
                    $result['result'][$dem_temp] = $value_data; 
                    $dem_temp++;
                }
            }
        return $result['result'];
    } 
    public function get_items_order($id,$type,$id_quote)
    {
        if($type == 'items')
        {
            $this->db->select('tblitems.code,tblitems.name,tblitems.avatar,tblunits.unit as unit_name,tblsupplier_quote_items.*,tblitems.price as price')->distinct();
            $this->db->from('tblsupplier_quote_items');
            
            $this->db->order_by('tblitems.id', 'desc');
            if(!empty($id_quote))
                {
                $this->db->join('tblitems','tblitems.id = tblsupplier_quote_items.product_id');  
                $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left'); 
                $this->db->where('tblsupplier_quote_items.id_supplier_quotes',$id_quote); 
                $this->db->where('tblsupplier_quote_items.type',$type);
                }
            if (is_numeric($id)) {
                
                $this->db->where('tblitems.id', $id);
            }  
            $item = $this->db->get()->row();
            $item->color = format_item_color($id,$type);
            return $item;

        } else {
            $table = get_table_where('tbltype_items',array('type'=>$type),'','row')->table;
            $this->db->select($table.'.price_sell as price,'.$table.'.code,'.$table.'.name,'.$table.'.images as avatar,tblunits.unit as unit_name,tblsupplier_quote_items.*')->distinct();
            $this->db->from('tblsupplier_quote_items');
            $this->db->order_by($table.'.id', 'desc');
            if(!empty($id_quote))
                {
                $this->db->join($table,$table.'.id = tblsupplier_quote_items.product_id ');  
                $this->db->join('tblunits','tblunits.unitid='.$table.'.unit_id','left');
                $this->db->where('tblsupplier_quote_items.id_supplier_quotes',$id_quote); 
                $this->db->where('tblsupplier_quote_items.type',$type);
                }
            if (is_numeric($id)) {
                
                $this->db->where($table.'.id', $id);
                $item = $this->db->get()->row();
                $item->color = format_item_color($id,$type);
                return $item;
            }
        }
    } 
    public function get_items($id='')
    {
        // $this->db->select('tblsupplier_quote_items.*,tblitems.name as name_item,tblitems.code as code_item,tblunits.unit as unit')->distinct();
        // $this->db->from('tblsupplier_quote_items');
        // $this->db->join('tblitems','tblitems.id=tblsupplier_quote_items.product_id','left');
        // $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        // $this->db->where('id_supplier_quotes',$id);
        // return $this->db->get()->result_array();
        $items = get_table_where('tblsupplier_quote_items',array('id_supplier_quotes'=>$id));
        foreach ($items as $key => $value) {
            if($value['type'] == 'items')
            {
                $this->db->select('tblitems.name as name_item,tblitems.code as code_item,tblunits.unit as unit,,tblitems.avatar')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
                $this->db->where('tblitems.id',$value['product_id']);
                $items[$key] = array_merge($items[$key],$this->db->get()->row_array());

            }else
            {
                $table = get_table_where('tbltype_items',array('type'=>$value['type']),'','row')->table;
                $this->db->select($table.'.name as name_item,'.$table.'.code as code_item,tblunits.unit as unit,'.$table.'.images as avatar')->distinct();
                $this->db->from( $table);
                $this->db->join('tblunits','tblunits.unitid='.$table.'.unit_id','left');
                $this->db->where($table.'.id',$value['product_id']);
                $items[$key] = array_merge($items[$key],$this->db->get()->row_array());
            }
        }
        return $items;
    } 
    public function add($data=array())
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $data['discount_percent'] = str_replace(',', '', $data['discount_percent']);
        $quotes = array(
            'code'=>sprintf('%06d', ch_getMaxID('id', 'tblsupplier_quotes') + 1),
            'prefix'=>get_option('prefix_supplier_quotes'),
            'note'=>$data['note'],
            'staff_create'=>get_staff_user_id(),
            'date'=>to_sql_date($data['date']),
            'date_create'=>date('Y:m:d H:i:s'),
            'suppliers_id'=>$data['suppliers_id'],
            'status'=>1,
            'discount_percent'=>$data['discount_percent'],
            'valtype_check'=>$data['valtype_check'],
            'tax_all'=>$data['tax_all'],
        );
        if(!empty($data['id_ask_price']))
        {
          $id_ask_price = get_table_where('tblrfq_ask_price',array('id'=>$data['id_ask_price']),'','row');
          $quotes['type_plan']= $id_ask_price->type_plan;
          $quotes['id_purchase_proce']= $id_ask_price->id_purchases; 
          $quotes['id_ask_price']= $data['id_ask_price'];
        }
        if(!empty($data['id_purchases']))
        {
          $id_purchase = get_table_where('tblpurchases',array('id'=>$data['id_purchases']),'','row');
          $quotes['type_plan']= $id_purchase->type_plan;
          $quotes['id_purchase_proce']= $data['id_purchases'];
          $quotes['id_purchases']= $data['id_purchases'];
        }
        
        if($this->db->insert('tblsupplier_quotes',$quotes))
        {
            $id=$this->db->insert_id();
            if(!empty($data['id_purchases']))
            {
              $this->db->update('tblpurchases',array('process'=>('2|'.$id)),array('id'=>$data['id_purchases']));
            }
            if(!empty($data['id_ask_price']))
            {
              $this->db->update('tblrfq_ask_price',array('process'=>('2|'.$id)),array('id'=>$data['id_ask_price']));
            }
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                    unset($custom_fields);
                    $custom_fields['supplier_quotes']                = $_custom_fields['supplier_quotes'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Supplier quotes Insert [ID: ' . $id . ']');
            $count=1;
        }
        $items=$data['items'];
        if($id)
        {
            $total = 0;
            $total_novat = 0;
            foreach ($items as $key => $item) {
                if(!empty($item['id']))
                {
                $item['unit_cost'] = str_replace(',', '', $item['unit_cost']);
                $item['quantity'] = str_replace(',', '', $item['quantity']);
                $subtotalitem = $item['quantity']*$item['unit_cost']*(1+($item['tax_rate']/100));
                $subtotal_novat = $item['quantity']*$item['unit_cost'];
                $items=array(
                    'id_supplier_quotes'=>$id,
                    'product_id'=>$item['id'],
                    'type'=>$item['type'],
                    'quantity'=>$item['quantity'],
                    'tax_id'=>$item['tax_id'],
                    'tax_rate'=>$item['tax_rate'],
                    'unit_cost'=>$item['unit_cost'],
                    'subtotal'=>$subtotalitem,
                );
                $total +=$subtotalitem;
                $total_novat += $subtotal_novat;
                if($this->db->insert('tblsupplier_quote_items',$items))
                {

                    log_activity('Supplier quotes insert [ID Supplier quotes: ' . $id . ', ID Product: ' . $item['product_id'] . ']');
                    $count++;
                }
                else {
                    exit("error");
                }
                }
            }
            $discount=0;
            if($data['valtype_check'] == 1)
            {
                $discount = $total_novat*$data['discount_percent']/100;
            }else
            {
                $discount = $data['discount_percent'];
            }
            $subtotal = $total - $discount;
            $_items =  array(
                'subtotal_novat'=>$total_novat,
                'subtotal'=>$subtotal,
                'discount'=>$discount,
            );
            $this->db->update('tblsupplier_quotes',$_items,array('id'=>$id));
        }
        if($count>0)
        {
            if(!empty($data['id_purchases']))
            {
                create_purchase_to_quotes($id);
            }
            return $id;
        }
        return false;
    }
    public function update($data=array(),$id = '')
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $data['discount_percent'] = str_replace(',', '', $data['discount_percent']);
        $quotes = array(
            'note'=>$data['note'],
            'suppliers_id'=>$data['suppliers_id'],
            'discount_percent'=>$data['discount_percent'],
            'date'=>to_sql_date($data['date']),
            'valtype_check'=>$data['valtype_check'],
            'tax_all'=>$data['tax_all'],
        );
        if($this->db->update('tblsupplier_quotes',$quotes,array('id'=>$id)))
        {
            log_activity('Supplier quotes updateted [ID: ' . $id . ']');
            $count=1;
        }
        $items=$data['items'];
        if($id)
        {
            if (isset($custom_fields)) {
            $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['supplier_quotes']                = $_custom_fields['supplier_quotes'];
            handle_custom_fields_post($id, $custom_fields);
            }
            $array=array();
            $total = 0;
            $total_novat = 0;
            foreach ($items as $key => $item) {
                if(!empty($item['id']))
                {
                $item['unit_cost'] = str_replace(',', '', $item['unit_cost']);
                $item['quantity'] = str_replace(',', '', $item['quantity']);
                $subtotalitem = $item['quantity']*$item['unit_cost']*(1+($item['tax_rate']/100));
                $subtotal_novat = $item['quantity']*$item['unit_cost'];
                $items=array(
                    'id_supplier_quotes'=>$id,
                    'product_id'=>$item['id'],
                    'type'=>$item['type'],
                    'quantity'=>$item['quantity'],
                    'tax_id'=>$item['tax_id'],
                    'tax_rate'=>$item['tax_rate'],
                    'unit_cost'=>$item['unit_cost'],
                    'subtotal'=>$subtotalitem,
                );
                $total +=$subtotalitem;
                $total_novat += $subtotal_novat;
                $ktr = get_table_where('tblsupplier_quote_items',array('id_supplier_quotes'=>$id,'product_id'=>$item['id'],'type'=>$item['type']),'','row');
                if($ktr)
                {
                   $_item=array(
                    'quantity'=>$item['quantity'],
                    'tax_id'=>$item['tax_id'],
                    'tax_rate'=>$item['tax_rate'],
                    'unit_cost'=>$item['unit_cost'],
                    'subtotal'=>$subtotalitem,
                    ); 
                   $this->db->update('tblsupplier_quote_items',$_item,array('id'=>$ktr->id));
                   $array[] = $ktr->id;
                }else
                {
                    $this->db->insert('tblsupplier_quote_items',$items);
                    $array[] = $this->db->insert_id();
                }

                log_activity('Supplier quotes updateted [ID Supplier quotes: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                    $count++;
                }
            }
            $discount=0;
            if($data['valtype_check'] == 1)
            {
                $discount = $total_novat*$data['discount_percent']/100;
            }else
            {
                $discount = $data['discount_percent'];
            }
            $subtotal = $total - $discount;
            $_items =  array(
                'subtotal_novat'=>$total_novat,
                'subtotal'=>$subtotal,
                'discount'=>$discount,
            );
            $this->db->update('tblsupplier_quotes',$_items,array('id'=>$id));
            if(!empty($array))
                {
                    $this->db->where('id_supplier_quotes',$id);
                    $this->db->where_not_in('id',$array);
                    $this->db->delete('tblsupplier_quote_items');
                }
        }
        if($count>0)
        {
            return $id;
        }
        return false;
    }
    public function delete_supplier_quotes($id ='')
    {
        $supplier_quotes = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
        $this->db->where('id', $id);
        $this->db->delete('tblsupplier_quotes');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id_supplier_quotes', $id);
            $this->db->delete('tblsupplier_quote_items');
            log_activity('Supplier quotes Deleted [ID:' . $id . ']');
        if(!empty($supplier_quotes->id_ask_price))
        {
        $this->db->where('id_rfq_ask_price', $supplier_quotes->id_ask_price);
        $this->db->where('suppliers_id', $supplier_quotes->suppliers_id);
        $this->db->delete('tblevaluate_suppliers');    
        
        $this->db->update('tblpurchases',array('process'=>''),array('id'=>$supplier_quotes->id_ask_price)); 
        }
        if(!empty($supplier_quotes->id_purchases))
        {
        $this->db->update('tblpurchases',array('process'=>''),array('id'=>$supplier_quotes->id_purchases)); 
        }
            return true;
        }

        return false;
    }
    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblsupplier_quotes',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function add_targert($data='',$id,$idquote)
    {
    if($data['targert'])
        {
            $array=array();
            foreach ($data['targert'] as $key => $item) {
                foreach ($item as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                    $targerts=array(
                    'id_rfq_ask_price'=>$id,
                    'id_evaluation_criteria'=>$k,
                    'id_evaluation_criteria_children'=>$k1,
                    'suppliers_id'=>$key,
                    'point'=>$v1['point'],    
                    );
                $ktr = get_table_where('tblevaluate_suppliers',array('id_rfq_ask_price'=>$id,'id_evaluation_criteria'=>$targerts['id_evaluation_criteria'],'id_evaluation_criteria_children'=>$targerts['id_evaluation_criteria_children'],'suppliers_id'=>$targerts['suppliers_id']),'','row');
                if(!$ktr)
                {
                if($this->db->insert('tblevaluate_suppliers',$targerts))
                {
                    $array[] = $this->db->insert_id();
                    log_activity('Targerts suppliers updateted [ID Supplier quotes: ' . $idquote . ', ID Targerts: ' . $targerts['id_evaluation_criteria_children'] . ']');
                }
                else {
                    exit("error");
                }
                }else
                {
                    $targertss = array(
                        'point'=>$v1['point'],
                    );
                 $this->db->update('tblevaluate_suppliers',$targertss,array('id'=>$ktr->id));
                   $array[] = $ktr->id;
                }
                }

                if(!empty($array))
                {
                    $this->db->where('id_rfq_ask_price',$id);
                    $this->db->where('suppliers_id',$key);
                    $this->db->where_not_in('id',$array);
                    $this->db->delete('tblevaluate_suppliers');
                }
                }
            }
            return true;
        }else
        {
            return false;
        }

    }
}
