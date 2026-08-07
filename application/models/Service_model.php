<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Service_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function show_detail_service($id){
        $this->db->select('*');
        $this->db->from('tbl_services_items');
        $this->db->join('tbl_services ', 'tbl_services.id=tbl_services_items.id_services', 'left');
        $this->db->order_by('tbl_services_items.id','desc');
        $this->db->where("tbl_services_items.id_services=$id") ;
        $query = $this->db->get();
        if($query->num_rows() != 0)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    public function show_service($id){
        $this->db->select('*');
        $this->db->from('tbl_services');
        $this->db->join('tblcosts ', 'tblcosts.id=tbl_services.type_service', 'left');
        $this->db->where("tbl_services.id=$id") ;
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
}
?>