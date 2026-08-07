<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Option_pdf extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data['title']          = _l('option_pdf');
        $data['dataMainPurchases'] = get_table_where('tbl_field_pdf',array('parent_field'=>'purchases'),'','row');
        $data['dataMainSupplier_quotes'] = get_table_where('tbl_field_pdf',array('parent_field'=>'supplier_quotes'),'','row');
        $data['dataMainPurchase_order'] = get_table_where('tbl_field_pdf',array('parent_field'=>'purchase_order'),'','row');
        $data['dataMainImport'] = get_table_where('tbl_field_pdf',array('parent_field'=>'import'),'','row');
        $data['dataMainRetrun'] = get_table_where('tbl_field_pdf',array('parent_field'=>'return_suppliers'),'','row');

        $this->load->view('admin/option_pdf/manager', $data);
    }
    public function add_field()
    {
    	$data = $this->input->post();
    	$getData = get_table_where('tbl_field_pdf',array('parent_field'=>$data['parent']),'','row');
    	if(!$getData) {
    		$this->db->insert('tbl_field_pdf',['parent_field'=>$data['parent']]);

    		$this->db->where('parent_field',$data['parent']);
    		$this->db->update('tbl_field_pdf',['parent_field'=>$data['parent'],'arr_field'=>$data['field']]);
    	}
    	else {
    		$old = $getData->arr_field;

    		if(!$old || $old == '') {
    			$new = $data['field'];
    		}
    		else {
    			$new = $old.','.$data['field'];
    		}
    		$this->db->where('parent_field',$data['parent']);
    		$this->db->update('tbl_field_pdf',['parent_field'=>$data['parent'],'arr_field'=>$new]);
    	}
    }
    public function remove_field()
    {
    	$data = $this->input->post();
    	$getData = get_table_where('tbl_field_pdf',array('parent_field'=>$data['parent']),'','row');
    	$old = explode(',', $getData->arr_field);
    	$arr = array();
    	foreach ($old as $key => $value) {
    		if($value != $data['field']) {
    			$arr[] = $value;
    		}
    	}
    	$new = implode(',', $arr);
    	$this->db->where('parent_field',$data['parent']);
    	$this->db->update('tbl_field_pdf',['parent_field'=>$data['parent'],'arr_field'=>$new]);
    }
}