<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers_price_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function show_List($id){
    	$this->db->select('*, a.id');
	    $this->db->from('tblsuppliers_price a');
	    $this->db->join('tblsuppliers b', 'b.id=a.supplier_id', 'left');
	    $this->db->join('tblcurrencies c', 'c.id=b.default_currency', 'left');
	    $this->db->order_by('a.id','desc');  
	    $this->db->where("a.id=$id") ;      
	    $query = $this->db->get(); 
	    if($query->num_rows() != 0)
	    {
	        return $query->row();
	    }
	    else
	    {
	        return false;
	    }
    }
	public function show_list_detail($id){
    	$this->db->where('supplier_price_id',$id);
		$this->db->order_by('tblsuppliers_price_detail.id', 'desc');
    	$q=$this->db->get('tblsuppliers_price_detail');
		$qu=[];
		$join=[];
		return $q->result();
	}
	public function get_full_item($id = '',$type = '')
	{
		if($type == 'items')
		{
			$this->db->select('tblitems.*,tblunits.unit as unit_name')->distinct();
			$this->db->from('tblitems');
			$this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
			$this->db->order_by('tblitems.id', 'desc');
			if (is_numeric($id)) {

				$this->db->where('tblitems.id', $id);
				$item = $this->db->get()->row();
				$item->color = format_item_color($id,$type);
				$item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/'.$item->avatar) ? base_url('uploads/materials/'.$item->avatar) : (file_exists('uploads/products/'.$item->avatar) ? base_url('uploads/products/'.$item->avatar) : base_url('assets/images/preview-not-available.jpg')))):base_url('assets/images/preview-not-available.jpg'));
				return $item;
			}
		} else {
			$table = get_table_where('tbltype_items',array('type'=>$type),'','row')->table;
			$this->db->select($table.'.*,'.$table.'.images as avatar,tblunits.unit as unit_name')->distinct();
			$this->db->from($table);
			$this->db->join('tblunits','tblunits.unitid='.$table.'.unit_id','left');
			$this->db->order_by($table.'.id', 'desc');
			if (is_numeric($id)) {

				$this->db->where($table.'.id', $id);
				$item = $this->db->get()->row();
				$item->color = format_item_color($id,$type);
				$item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/'.$item->avatar) ? base_url('uploads/materials/'.$item->avatar) : (file_exists('uploads/products/'.$item->avatar) ? base_url('uploads/products/'.$item->avatar) : (file_exists('uploads/tools_supplies/'.$item->avatar) ? base_url('uploads/tools_supplies/'.$item->avatar) : base_url('assets/images/preview-not-available.jpg'))))):base_url('assets/images/preview-not-available.jpg'));
				return $item;
			}
		}
	}
	public function delete_import($id)
	{
		$this->db->where("id", $id);
		$query=$this->db->delete('tblsuppliers_price');
		if($query){
			return true;
		}else{
			return false;
		}
	}
	public function delete_import_detail($id)
	{
		$this->db->where("id", $id);
		$this->db->delete('tblsuppliers_price_detail');
	}
}
?>    