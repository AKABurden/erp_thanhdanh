<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Discount_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get($id = '')
    {
        $item = array();
        $purchases = get_table_where('tbldiscount',array('id'=>$id),'','row');
        $items = get_table_where('tbldiscount_datail',array('id_discount'=>$id));
        foreach ($items as $key => $value) {
            $item[$value['id_category']]=$value;
        }
        $purchases->items = $item;
        $items_product = get_table_where('tbldiscount_datail_product',array('id_discount'=>$id));
        foreach ($items_product as $key => $value) {
            $items_product[$value['id_category']]=$value;
        }
        $purchases->items_product = $items_product;
        $this->db->select('
                        userid as id,
                        tblclients.company as text,
                        CONCAT(prefix_client,code_client) as code_clients,
                        tblclients.address as address,
                        tblclients.phonenumber as phonenumber,
                        '
                , false);
        $this->db->join('tblclients','tblclients.userid = tbldiscount_client.id_client','left');
        $this->db->where('tbldiscount_client.id_discount', $id);
        $purchases->clients = $this->db->get('tbldiscount_client')->result_array();;
        return $purchases;
    } 
    public function get_payment($id = '')
    {
        $purchases = get_table_where('tbldiscount',array('id'=>$id),'','row');
        $items = get_table_where('tbldiscount_datail',array('id_discount'=>$id));
        foreach ($items as $key => $value) {
            $item[$value['id_category']]=$value;
        }
        $this->db->select('
                        tbldiscount_datail.id as id,
                        tblpayment_time_level.id as id_payment,
                        tblpayment_time_level.name as name_payment,
                        tbldiscount_datail.discounts as discounts,
                        '
                , false);
        $this->db->join('tblpayment_time_level','tblpayment_time_level.id = tbldiscount_datail.id_category','left');
        $this->db->where('id_discount', $id);
        $purchases->items = $this->db->get('tbldiscount_datail')->result_array();
        $this->db->select('
                        userid as id,
                        tblclients.company as text,
                        CONCAT(prefix_client,code_client) as code_clients,
                        tblclients.address as address,
                        tblclients.phonenumber as phonenumber,
                        '
                , false);
        $this->db->join('tblclients','tblclients.userid = tbldiscount_client.id_client','left');
        $this->db->where('id_discount', $id);
        $purchases->clients = $this->db->get('tbldiscount_client')->result_array();
        return $purchases;
    }   
    public function add_payment($data,$type='')
    {
        if ($data) {
            $items=$data['items'];
            if(!empty($data['client'])){
            $client=$data['client'];
            }
            if(isset($data['note'])) nl2br($data['note']);
            $effective_date = explode('-', $data['effective_date']);
            $inventory=array(
                'code'=>sprintf('%06d', ch_getMaxID('id', 'tbldiscount') + 1),
                'prefix'=>get_option('prefix_discount'),
                'note'=>$data['note'],
                'date_start'=>to_sql_date(trim($effective_date[0])),
                'date_end'=>to_sql_date(trim($effective_date[1])),
                'type_client'=>implode(',', $data['type_client']),
                'apply'=>$data['apply'],
                'name_discount'=>$data['name_discount'],
                'id_client'=>$data['id_client'],
                'date'=>to_sql_date($data['date'],true),
                'staff_create'=>get_staff_user_id(),
                'date_create'=>date('Y:m:d H:i:s'),
                'status'=>0,
                'type'=>$type,
                );
            // echo '<pre>';
            // var_dump($data);die;
            $this->db->insert('tbldiscount',$inventory);
            $id=$this->db->insert_id();
            if ($id) {
                foreach ($items as $key => $item) {
                    $_item['id_discount']=$id;
                    $_item['id_category']=$item['id'];
                    $_item['discounts']=$item['discounts'];
                    $_item['quantity']=0;
                    $_item['readonly']=0;
                    $_item['note']='';
                    $this->db->insert('tbldiscount_datail',$_item);
                }
                if($data['id_client'] == 2){
                foreach ($client as $key => $client) {
                    $_client['id_discount']=$id;
                    $_client['id_client']=$client['id_client'];
                    $_client['date_start']=to_sql_date(trim($effective_date[0]));
                    $_client['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->insert('tbldiscount_client',$_client);
                }
                }
                return $id;
            }   
        }
        return false;
    }
    public function add($data,$type='')
    {
        if ($data) {
            $items=$data['items'];
            $items_product=$data['items_product'];
            unset($data['items']);
            unset($data['items_product']);
            if(!empty($data['client'])){
            $client=$data['client'];
            }
            if(isset($data['note'])) nl2br($data['note']);
            $effective_date = explode('-', $data['effective_date']);
            $inventory=array(
                'code'=>sprintf('%06d', ch_getMaxID('id', 'tbldiscount') + 1),
                'prefix'=>get_option('prefix_discount'),
                'note'=>$data['note'],
                'date_start'=>to_sql_date(trim($effective_date[0])),
                'date_end'=>to_sql_date(trim($effective_date[1])),
                'type_client'=>implode(',', $data['type_client']),
                'apply'=>$data['apply'],
                'name_discount'=>$data['name_discount'],
                'id_client'=>$data['id_client'],
                'date'=>to_sql_date($data['date'],true),
                'staff_create'=>get_staff_user_id(),
                'date_create'=>date('Y:m:d H:i:s'),
                'status'=>0,
                'type'=>$type,
                );
            // echo '<pre>';
            // var_dump($data);die;
            $this->db->insert('tbldiscount',$inventory);
            $id=$this->db->insert_id();
            if ($id) {
                foreach ($items as $key => $item) {
                    $_item['id_discount']=$id;
                    $_item['id_category']=$item['id'];
                    $_item['discounts']=$item['discounts'];
                    $_item['discounts_cash'] = str_replace(",", "", $item['discounts_cash']);
                    if($_item['discounts'] > 0)
                    {
                     $_item['quantity']=$item['quantity'];   
                    }else
                    {
                    $_item['quantity']=0;
                    }
                    $_item['readonly']=$item['readonly'];
                    $_item['note']=$item['note'];
                    $this->db->insert('tbldiscount_datail',$_item);
                }
                foreach ($items_product as $key => $item) {
                    $_item_product['id_discount']=$id;
                    $_item_product['id_category']=$item['id'];
                    $_item_product['discounts']=$item['discounts_product'];
                    if($_item_product['discounts'] > 0)
                    {
                     $_item_product['quantity']=$item['quantity_product'];   
                    }else
                    {
                    $_item_product['quantity']=0;
                    }
                    $_item_product['readonly']=$item['readonly_product'];
                    $_item_product['note']=$item['note'];
                    $this->db->insert('tbldiscount_datail_product',$_item_product);
                }
                if($data['id_client'] == 2){
                foreach ($client as $key => $client) {
                    $_client['id_discount']=$id;
                    $_client['id_client']=$client['id_client'];
                    $_client['date_start']=to_sql_date(trim($effective_date[0]));
                    $_client['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->insert('tbldiscount_client',$_client);
                }
                }
                return $id;
            }   
        }
        return false;
    }
    public function update($data=NULL,$id='')
    {

        if ($data) {
            $items=$data['items'];
            $items_product=$data['items_product'];
            unset($data['items']);
            unset($data['items_product']);
            if(!empty($data['client'])){
            $client=$data['client'];
            }
            if(isset($data['note'])) nl2br($data['note']);
            $effective_date = explode('-', $data['effective_date']);
            $inventory=array(
                'note'=>$data['note'],
                'date_start'=>to_sql_date(trim($effective_date[0])),
                'date_end'=>to_sql_date(trim($effective_date[1])),
                'type_client'=>implode(',', $data['type_client']),
                'apply'=>$data['apply'],
                'name_discount'=>$data['name_discount'],
                'id_client'=>$data['id_client'],
                'date'=>to_sql_date($data['date'],true),
                );
            $this->db->where('id',$id);
            if ($this->db->update('tbldiscount',$inventory)) {
                $affected_id = array();
                foreach ($items as $key => $item) {
                $ktr = get_table_where('tbldiscount_datail',array('id_discount'=>$id,'id_category'=>$item['id']),'','row');
                if(!empty($ktr))
                {
                    $affected_id[] = $ktr->id;
                    $_item['discounts']=$item['discounts'];
                    $_item['discounts_cash'] = str_replace(",", "", $item['discounts_cash']);
                    if($_item['discounts'] > 0)
                    {
                     $_item['quantity']=$item['quantity'];   
                    }else
                    {
                    $_item['quantity']=0;
                    }
                    $_item['readonly']=$item['readonly'];
                    $_item['note']=$item['note'];

                    $this->db->where('id',$ktr->id);  
                    $this->db->update('tbldiscount_datail',$_item);  
                }else
                {
                    $_item['id_discount']=$id;
                    $_item['id_category']=$item['id'];
                    $_item['discounts']=$item['discounts'];
                    $_item['discounts_cash'] = str_replace(",", "", $item['discounts_cash']);
                    if($_item['discounts'] > 0)
                    {
                     $_item['quantity']=$item['quantity'];   
                    }else
                    {
                    $_item['quantity']=0;
                    }
                    $_item['readonly']=$item['readonly'];
                    $_item['note']=$item['note'];
                    $this->db->insert('tbldiscount_datail',$_item);   
                    $id_discount_datail=$this->db->insert_id();
                    $affected_id[] = $id_discount_datail;
                }
                }
                $affected_id_product = array();
                foreach ($items_product as $key => $item) {
                $ktr = get_table_where('tbldiscount_datail_product',array('id_discount'=>$id,'id_category'=>$item['id']),'','row');
                if(!empty($ktr))
                {
                    $affected_id_product[] = $ktr->id;
                    $_item['discounts']=$item['discounts_product'];
                    if($_item['discounts'] > 0)
                    {
                     $_item['quantity']=$item['quantity_product'];   
                    }else
                    {
                    $_item['quantity']=0;
                    }
                    $_item['readonly']=$item['readonly_product'];
                    $_item['note']=$item['note'];

                    $this->db->where('id',$ktr->id);  
                    $this->db->update('tbldiscount_datail_product',$_item);  
                }else
                {
                    $_item['id_discount']=$id;
                    $_item['id_category']=$item['id'];
                    $_item['discounts']=$item['discounts_product'];
                    if($_item['discounts'] > 0)
                    {
                     $_item['quantity']=$item['quantity_product'];   
                    }else
                    {
                    $_item['quantity']=0;
                    }
                    $_item['readonly']=$item['readonly_product'];
                    $_item['note']=$item['note'];
                    $this->db->insert('tbldiscount_datail_product',$_item);   
                    $id_discount_datail=$this->db->insert_id();
                    $affected_id_product[] = $id_discount_datail;
                }
                }
                $id_client_id = array();
                if($data['id_client'] == 2){
                foreach ($client as $key => $client) {
                    $ktr_client = get_table_where('tbldiscount_client',array('id_discount'=>$id,'id_client'=>$client['id_client']),'','row');
                    if(!empty($ktr_client))
                    {
                    $id_client_id[] = $ktr_client->id;
                    }else
                    {
                    $_client['id_discount']=$id;
                    $_client['id_client']=$client['id_client'];
                    $_client['date_start']=to_sql_date(trim($effective_date[0]));
                    $_client['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->insert('tbldiscount_client',$_client);
                    $id_client=$this->db->insert_id();
                    $id_client_id[] = $id_client;
                    }
                }
                }else
                {
                    $this->db->delete('tbldiscount_client',array('id_discount'=>$id));
                }
                if($data['id_client'] == 2){
                    $_clients['date_start']=to_sql_date(trim($effective_date[0]));
                    $_clients['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->where('id_discount',$id);  
                    $this->db->update('tbldiscount_client',$_clients);  
                }
                if(empty($affected_id))
                {
                    $this->db->where('id_discount', $id);
                    $this->db->delete('tbldiscount_datail');
                }
                else
                {
                    $this->db->where('id_discount', $id);
                    $this->db->where_not_in('id', $affected_id);
                    $this->db->delete('tbldiscount_datail');
                }
                if(empty($id_client_id))
                {
                    $this->db->where('id_discount', $id);
                    $this->db->delete('tbldiscount_client');
                }
                else
                {
                    $this->db->where('id_discount', $id);
                    $this->db->where_not_in('id', $id_client_id);
                    $this->db->delete('tbldiscount_client');
                }
                if(empty($affected_id_product))
                {
                    $this->db->where('id_discount', $id);
                    $this->db->delete('tbldiscount_datail_product');
                }
                else
                {
                    $this->db->where('id_discount', $id);
                    $this->db->where_not_in('id', $affected_id_product);
                    $this->db->delete('tbldiscount_datail_product');
                }
            }   
            return $id;
        }
        return false;
    }
    public function update_payment($data=NULL,$id='')
    {

        if ($data) {
            $items=$data['items'];
            if(!empty($data['client'])){
            $client=$data['client'];
            }
            if(isset($data['note'])) nl2br($data['note']);
            $effective_date = explode('-', $data['effective_date']);
            $inventory=array(
                'note'=>$data['note'],
                'date_start'=>to_sql_date(trim($effective_date[0])),
                'date_end'=>to_sql_date(trim($effective_date[1])),
                'type_client'=>implode(',', $data['type_client']),
                'apply'=>$data['apply'],
                'name_discount'=>$data['name_discount'],
                'id_client'=>$data['id_client'],
                'date'=>to_sql_date($data['date'],true),
                );
            $this->db->where('id',$id);
            if ($this->db->update('tbldiscount',$inventory)) {
                $affected_id = array();
                foreach ($items as $key => $item) {
                $ktr = get_table_where('tbldiscount_datail',array('id_discount'=>$id,'id_category'=>$item['id']),'','row');
                if(!empty($ktr))
                {
                    $affected_id[] = $ktr->id;
                    $_item['discounts']=$item['discounts'];
                    $this->db->where('id',$ktr->id);  
                    $this->db->update('tbldiscount_datail',$_item);  
                }else
                {
                    $_item['id_discount']=$id;
                    $_item['id_category']=$item['id'];
                    $_item['discounts']=$item['discounts'];
                    $_item['quantity']=0;
                    $_item['readonly']=0;
                    $_item['note']='';
                    $this->db->insert('tbldiscount_datail',$_item);   
                    $id_discount_datail=$this->db->insert_id();
                    $affected_id[] = $id_discount_datail;
                }
                }
                $id_client_id = array();
                if($data['id_client'] == 2){
                foreach ($client as $key => $client) {
                    $ktr_client = get_table_where('tbldiscount_client',array('id_discount'=>$id,'id_client'=>$client['id_client']),'','row');
                    if(!empty($ktr_client))
                    {
                    $id_client_id[] = $ktr_client->id;
                    }else
                    {
                    $_client['id_discount']=$id;
                    $_client['id_client']=$client['id_client'];
                    $_client['date_start']=to_sql_date(trim($effective_date[0]));
                    $_client['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->insert('tbldiscount_client',$_client);
                    $id_client=$this->db->insert_id();
                    $id_client_id[] = $id_client;
                    }
                }
                }else
                {
                    $this->db->delete('tbldiscount_client',array('id_discount'=>$id));
                }
                if($data['id_client'] == 2){
                    $_clients['date_start']=to_sql_date(trim($effective_date[0]));
                    $_clients['date_end']=to_sql_date(trim($effective_date[1]));
                    $this->db->where('id_discount',$id);  
                    $this->db->update('tbldiscount_client',$_clients);  
                }
                if(empty($affected_id))
                {
                    $this->db->where('id_discount', $id);
                    $this->db->delete('tbldiscount_datail');
                }
                else
                {
                    $this->db->where('id_discount', $id);
                    $this->db->where_not_in('id', $affected_id);
                    $this->db->delete('tbldiscount_datail');
                }
                if(empty($id_client_id))
                {

                    $this->db->where('id_discount', $id);
                    $this->db->delete('tbldiscount_client');
                }
                else
                {
                    $this->db->where('id_discount', $id);
                    $this->db->where_not_in('id', $id_client_id);
                    $this->db->delete('tbldiscount_client');
                }
            }   
            return $id;
        }
        return false;
    }    

    public function get_full_item($id = '',$type = '')
    {
        if($type == 'items')
        {
            $this->db->select('tblitems.price as price')->distinct();
            $this->db->from('tblitems');
            $this->db->order_by('tblitems.id', 'desc');
            if (is_numeric($id)) {
                
                $this->db->where('tblitems.id', $id);
                $item = $this->db->get()->row();
                return $item;
            }  
        } else {
            $table = get_table_where('tbltype_items',array('type'=>$type),'','row')->table;
            $this->db->select($table.'.price_sell as price')->distinct();
            $this->db->from($table);
            $this->db->order_by($table.'.id', 'desc');
            if (is_numeric($id)) {
                
                $this->db->where($table.'.id', $id);
                $item = $this->db->get()->row();
                return $item;
            }
        }
    }
    public function delete($id ='')
    {
        $this->db->where('id', $id);
        $this->db->delete('tbldiscount');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id_discount', $id);
            $this->db->delete('tbldiscount_datail');
            $this->db->where('id_discount', $id);
            $this->db->delete('tbldiscount_client');
            log_activity('Discount Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}