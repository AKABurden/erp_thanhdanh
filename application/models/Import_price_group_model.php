<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import_price_group_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function show_List($id) {
		$this->db->select('tblgroup_price.*, tblclients.company as company');
		$this->db->from('tblgroup_price');
		$this->db->join('tblclients', 'tblclients.userid = tblgroup_price.client', 'left');
		$this->db->order_by('tblgroup_price.id', 'desc');
		$this->db->where("tblgroup_price.id", $id);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			return $query->row();
		} else {
			return false;
		}
	}

	public function show_list_detail($id)
	{
		$this->db->where('group_price_id', $id);
		$this->db->order_by('id', 'desc');
		return $this->db->get('tblgroup_price_detail')->result();
	}
	public function get_full_item_export_price($id = '', $type = '')
	{
		if ($type == 'items') {
			$this->db->select('tblitems.*,tblunits.unit as unit_name')->distinct();
			$this->db->from('tblitems');
			$this->db->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left');
			$this->db->order_by('tblitems.id', 'desc');
			if (is_numeric($id)) {
				$this->db->where('tblitems.id', $id);
				$item = $this->db->get()->row();
				return $item;
			}
		} else {
			$table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
			$this->db->select($table . '.*,' . $table . '.images as avatar')->distinct();
			$this->db->from($table);
			$this->db->order_by($table . '.id', 'desc');
			if (is_numeric($id)) {
				$this->db->where($table . '.id', $id);
				$item = $this->db->get()->row();
				return $item;
			}
		}
	}
	public function get_full_item($id = '', $type = '')
	{
		if ($type == 'items') {
			$this->db->select('tblitems.*,tblunits.unit as unit_name')->distinct();
			$this->db->from('tblitems');
			$this->db->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left');
			$this->db->order_by('tblitems.id', 'desc');
			if (is_numeric($id)) {
				$this->db->where('tblitems.id', $id);
				$item = $this->db->get()->row();
				$item->color = format_item_color($id, $type);
				$item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
				return $item;
			}
		} else {
			$table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
			$this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name')->distinct();
			$this->db->from($table);
			$this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
			$this->db->order_by($table . '.id', 'desc');
			if (is_numeric($id)) {
				$this->db->where($table . '.id', $id);
				$item = $this->db->get()->row();
				$item->color = format_item_color($id, $type);
				$item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
				return $item;
			}
		}
	}

	public function delete_import($id)
	{
		$this->db->where("id", $id);
		$query = $this->db->delete('tblgroup_price');
		if ($query) {
			$this->db->where("group_price_id", $id);
			$this->db->delete('tblgroup_price_discount');
			return true;
		} else {
			return false;
		}
	}

	public function delete_import_detail($id)
	{
		$this->db->where("id", $id);
		$this->db->delete('tblgroup_price_detail');
	}
}

?>