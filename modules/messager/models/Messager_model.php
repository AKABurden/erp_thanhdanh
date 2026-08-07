<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Messager_model extends App_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function find_user_sales($id = '')
    {
        if(!empty($id))
        {
            $this->db->where('customer_id', $id);
            $sale = $this->db->get('tblsales')->row();
            if(!empty($sale))
            {
                return $sale->id;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }
    public function find_user_facebook($id)
    {
        if(!empty($id))
        {
        	$this->db->where('id_facebook', $id);
        	$user = $this->db->get('tblclients')->row();
        	if($user)
        	{
        		return $user;
        	}
        	else
        	{
        		return false;
        	}

        }
        else
        {
        	return false;
        }
    }
    public function find_name_phone($data)
    {
        
        $this->db->where('company', $data['search_customer']);
        $this->db->or_where('phonenumber',  $data['search_customer']);
        $user = $this->db->get('tblclients')->row();

        if(!empty($user))
        {
            return $user;
        }
        else
        {
            return false;
        }
    }    
    public function update_profile($data = '', $client_id = '')
    {
    	$_data['phonenumber']  = $data['phone_number_client'];
    	$_data['address']      = $data['address_client'];
    	$_data['email']        = $data['email_client'];
    	$_data['note']         = $data['note_profile'];
    	$_data['id_facebook']  = $data['id_facebook'];
    	if($data['sex'] == 1)
    	{
    	    $_data['title']	= 'Anh';
    	}
    	elseif($data['sex'] == 2)
    	{
    	    $_data['title']	= 'Chị';
    	}
    	else
    	{
    	    $_data['title']	= '';
    	}
    	$this->db->update('tblclients',$_data,array('userid'=>$client_id));
    	return true;
    	
    }
    public function get_code($prefix = 'prefix_clients')
    {
        $this->db->select_max('userid');
        $id_max     = $this->db->get('tblclients')->row();
        $last_id    = strlen(($id_max->userid) + 1);
        $max_code   = 6;
        $n = $max_code - $last_id;
        $_code = "";
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $_code .= 0;
            }
        }
        return $last_code = get_option($prefix) . $_code . ($id_max->userid + 1);
    }
    public function add_profile($data='')
    {
   
        $_data['code']          =   $this->get_code();
        $_data['company']       =   $data['name'];
    	$_data['phonenumber']   =   $data['phone_number_client'];
    	$_data['address']       =   $data['address_client'];
    	$_data['email']         =   $data['email_client'];
    	$_data['note']          =   $data['note_profile'];
    	$_data['id_facebook']   =   $data['id_facebook'];

    	if($data['sex'] == 1)
    	{
    	    $_data['title']	= 'Anh';
    	}
    	elseif($data['sex'] == 2)
    	{
    	    $_data['title']	= 'Chị';
    	}
    	else
    	{
    	    $_data['title']	= NULL;
    	}

    	$this->db->insert('tblclients', $_data);
    	$id = $this->db->insert_id();
    	if (!empty($id))
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    public function get_sale_client($userid = '')
    {   
        $sales_arr      = array();
        $data           = false;
        $this->db->select('id, prefix, code, total,date');
        $this->db->where('customer_id', $userid);
        $sales = $this->db->get('tblsales')->result_array();
        if(!empty($sales))
        {
            foreach ($sales as $key => $value) {
                $sales[$key];
                $sales_arr[] = $value['id'];
            }
            $this->db->select('tblitems.name, sum(tblsale_items.quantity) as quantity');
            $this->db->where_in('sale_id', $sales_arr);
            $this->db->join('tblitems', 'tblsale_items.product_id = tblitems.id');
            $this->db->group_by('product_id');
            $items = $this->db->get('tblsale_items')->result_array();
        }
        $data['sales'] = $sales;
        $data['items'] = $items;
        return $data;
    }
}