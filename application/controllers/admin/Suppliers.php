<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Suppliers extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('suppliers_model');
			$this->load->model('currencies_model');
			$this->load->model('dashboard_model');
			$this->load->model('purchases_model');
		}
		
		public function int_suppliers_add($edit = '', $id = '', $type = 2)
		{
			$data['count'] = 0;
			$data['debt'] = 0;
			$data['payment'] = 0;
			$data['left'] = 0;
			if (!empty($id)) {
				$data['suppliers'] = $this->suppliers_model->get_suppliers($id);
				$data['title'] = $data['suppliers']->company;
				if ($edit != 'true') {
					$data['suppliers']->default_currency = $this->currencies_model->get($data['suppliers']->default_currency);
					$data['count'] = count(get_table_where('tblimport', array('suppliers_id' => $id)));
					$data['debt'] = get_table_where_sum('tblimport', array('suppliers_id' => $id), 'total');
					$debt_2 = get_table_where_sum('tblimport', array('suppliers_id' => $id), 'price_other_expenses');
					$data['payment'] = get_table_where_sum('tblpay_slip', array('id_supplierss' => $id), 'payment') + $debt_2;
					$data['left'] = $data['debt'] - $data['payment'];
				} else {
					$data['title'] = _l('ch_suppliers_edit_heading');
				}
				$data['suppliers']->contacts = $this->suppliers_model->get_suppliers_contacts($id);
				$data['id'] = $id;
				$data['province'] = get_table_where('province', array('countries' => $data['suppliers']->country));
				$data['district'] = get_table_where('district', array('provinceid' => $data['suppliers']->city));
				$data['ward'] = get_table_where('ward', array('districtid' => $data['suppliers']->district));
			} else {
				$data['title'] = _l('ch_suppliers_add_heading');
			}
			$data['info_suppliers'] = get_table_where('tblsuppliers_info_group');
			$data['currencies'] = $this->currencies_model->get();
			$data['groups'] = $this->suppliers_model->get_groups();
			$data['openEdit'] = $edit;
			$data['type'] = $type;
			$data['items'] = get_table_where('tblitems');
			$this->load->view('admin/suppliers/add_suppliert', $data);
		}
		
		public function import_suppliers()
		{
			$data['title'] = _l('Nhập dữ liệu nhà cung cấp');
			$data['colum_suppliers'] = $this->db->list_fields(db_prefix() . 'suppliers');
			$data['colum_suppliers'] = array_merge($data['colum_suppliers'], ['fullname_contact', 'phone_contact', 'email_contact']);
			$data['colum_suppliers'] = array_diff($data['colum_suppliers'], [
				'default_language',
				'default_currency',
			]);
			// print_arrays($data['colum_suppliers']);
			$data['colum_info_suppliers'] = $this->db->get(db_prefix() . 'suppliers_info_detail')->result_array();
			$data['columsExcel'] = [
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
				'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
				'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
				'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
			];
			$data['country'] = get_table_where(db_prefix() . 'countries');
			$this->load->view('admin/import_excel/import_suppliers', $data);
		}
		
		public function index()
		{
			if (!has_permission('suppliers', '', 'view') && !has_permission('suppliers', '', 'view_own')) {
				access_denied('suppliers');
			}
			$data['title'] = _l('ch_suppliers');
			$whereContactsLoggedIn = '';
			// if (!has_permission('customers', '', 'view')) {
			//     $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
			// }
			$this->load->view('admin/suppliers/manage', $data);
		}
		
		public function add_suppliers_v2($value = '')
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				$data['debt_limit'] = str_replace(',', '', $data['debt_limit']);
				unset($data['items_combo']);
				unset($data['DataTables_Table_2_length']);
				if ($data['id'] == '') {
					if (!has_permission('suppliers', '', 'create')) {
						echo json_encode([
							'success' => true,
							'message' => _l('ch_pqsuppliert_add'),
						]);
					}
					$id = $this->suppliers_model->add_suppliers($data);
					$message = $id ? _l('added_successfully') : '';
					echo json_encode([
						'success' => $id ? true : false,
						'message' => $message,
						'id' => $id,
						'name' => $data['company'],
					]);
				} else {
					if (!has_permission('suppliers', '', 'edit')) {
						echo json_encode([
							'success' => true,
							'message' => _l('ch_pqsuppliert_edit'),
						]);
					}
					$success = $this->suppliers_model->edit_suppliers($data);
					$message = '';
					if ($success == true) {
						$message = _l('updated_successfully');
					}
					echo json_encode([
						'id' => $data['id'],
						'success' => $success,
						'message' => $message,
					]);
				}
			}
		}
		
		public function get_debt($id = '')
		{
			$debt = get_table_where_sum('tblimport', array('suppliers_id' => $id), 'total');
			$debt_2 = get_table_where_sum('tblimport', array('suppliers_id' => $id), 'price_other_expenses');
			$payment = get_table_where_sum('tblpay_slip', array('id_supplierss' => $id), 'payment');
			$debt_suppliers = get_table_where('tblsuppliers', array('id' => $id), '', 'row');
			$ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $id));
			$table_price = array();
			$this->db->where('tblsuppliers_price.supplier_id', $id);
			// $this->db->where('tblsuppliers_price.year',date('Y'));
			$this->db->limit(1);
			$this->db->order_by('tblsuppliers_price.id DESC');
			$suppliers_price = $this->db->get('tblsuppliers_price')->row();
			if (!empty($suppliers_price)) {
				$this->db->where('tblsuppliers_price_detail.supplier_price_id', $suppliers_price->id);
				$table_price = $this->db->get('tblsuppliers_price_detail')->result_array();
			}
			$data['table_price'] = $table_price;
			$data['ktr_supp'] = $ktr_supp;
			$data['success'] = false;
			if ($debt_suppliers->debt_limit > 0) {
				$total = $debt - $payment - $debt_2;
				if ($total > $debt_suppliers->debt_limit) {
					$data['success'] = true;
					$data['total'] = number_format($total);
				}
			}
			$data['default_currency'] = $debt_suppliers->default_currency;
			echo json_encode($data);
			die;
		}
		
		public function get_classify($id = '')
		{
			$data['result_warning'] = '';
			$suppliers = get_table_where('tblsuppliers', array('id' => $id), '', 'row');
			$classify = get_table_where('tblsupplier_classify', array('id' => $suppliers->id_supplier_classify), '', 'row');
			if ($classify) {
				$data['result_warning'] = $classify->result_warning;
				$data['classify_title'] = $classify->name;
			}
			echo json_encode($data);
			die;
		}
		
		public function items_supplier()
		{
			if (!has_permission('suppliers', '', 'view') && !has_permission('suppliers', '', 'view_own')) {
				access_denied('suppliers');
			}
			$data['title'] = _l('mainstream_items');
			$this->load->view('admin/suppliers/mainstream_items', $data);
		}
		
		public function info_suppliers()
		{
			if (!is_admin()) {
				access_denied('suppliers');
			}
			$data['type_form'] = [
				['name' => 'input'],
				['name' => 'date'],
				['name' => 'datetime'],
				['name' => 'checkbox'],
				['name' => 'select'],
				['name' => 'select multiple'],
				['name' => 'radio'],
				// ['name' => 'password']
			];
			$data['title'] = _l('ch_group_information_suppliers');
			$this->load->view('admin/suppliers/info_suppliers/manage', $data);
		}
		
		public function table_info_suppliers()
		{
			$this->app->get_table_data('table_info_suppliers');
		}
		
		public function get_info_suppliers($id = '')
		{
			echo json_encode(get_table_where('tblsuppliers_info_group', array('id' => $id), '', 'row'));
		}
		
		public function get_info_suppliers_datail($id = '')
		{
			echo json_encode(get_table_where('tblsuppliers_info_detail', array('id' => $id), '', 'row'));
		}
		
		public function get_suppliers_info_detail_value($id = '')
		{
			echo json_encode(get_table_where('tblsuppliers_info_detail_value', array('id' => $id), '', 'row'));
		}
		
		public function add_info_suppliers()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['id'])) {
					$_data['name'] = $data['name'];
					$_data['color'] = $data['color'];
					$this->db->insert('tblsuppliers_info_group', $_data);
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
				} else {
					$_data['name'] = $data['name'];
					$_data['color'] = $data['color'];
					$this->db->update('tblsuppliers_info_group', $_data, array('id' => $data['id']));
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
				}
			} else {
				$alert_type = 'warning';
				$message = _l('ch_no_updated_successfuly');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function add_suppliers_info_detail_value()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['id_suppliers_info_detail_value'])) {
					$_data['name'] = $data['name_detail_value'];
					$_data['id_info_detail'] = $data['id_info_suppliers_datails'];
					$this->db->insert('tblsuppliers_info_detail_value', $_data);
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
				} else {
					$_data['name'] = $data['name_detail_value'];
					$this->db->update('tblsuppliers_info_detail_value', $_data, array('id' => $data['id_suppliers_info_detail_value']));
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
				}
			} else {
				$alert_type = 'warning';
				$message = _l('ch_no_updated_successfuly');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function add_info_suppliers_datail()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['id_info_suppliers_datail'])) {
					if (empty($data['is_required'])) {
						$data['is_required'] = 0;
					}
					$_data['name'] = $data['name_detail'];
					$_data['type_form'] = $data['type_form'];
					$_data['is_required'] = $data['is_required'];
					$_data['id_suppliers_info'] = $data['id_info_suppliers'];
					$this->db->insert('tblsuppliers_info_detail', $_data);
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
				} else {
					$_data['name'] = $data['name_detail'];
					$_data['type_form'] = $data['type_form'];
					if (empty($data['is_required'])) {
						$data['is_required'] = 0;
					}
					$_data['is_required'] = $data['is_required'];
					$this->db->update('tblsuppliers_info_detail', $_data, array('id' => $data['id_info_suppliers_datail']));
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
				}
			} else {
				$alert_type = 'warning';
				$message = _l('ch_no_updated_successfuly');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function all_contact_suppliers()
		{
			if (!has_permission('suppliers', '', 'view') && !has_permission('suppliers', '', 'view_own')) {
				access_denied('suppliers');
			}
			$data['title'] = _l('ch_all_contact_suppliers');
			$this->load->view('admin/suppliers/all_contact_supp', $data);
		}
		
		public function add_suppliers($value = '')
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				$data['debt_limit'] = str_replace(',', '', $data['debt_limit']);
				unset($data['items_combo']);
				unset($data['DataTables_Table_2_length']);
				$dataPostBranch = $data['branch'];
				unset($data['branch']);
				if ($data['id'] == '') {
					if (!has_permission('suppliers', '', 'create')) {
						echo json_encode([
							'success' => true,
							'message' => 'Bạn không có quyền sửa nhà cung cấp',
						]);
					}

					if (!empty($data['renewal_date'])) {
						$data['renewal_date'] = to_sql_date($data['renewal_date']);
					}

					if (!empty($data['date_begin'])) {
						$data['date_begin'] = to_sql_date($data['date_begin']);
					}

                    $data['deadline_contract'] = !empty($data['deadline_contract']) ? to_sql_date($data['deadline_contract']) : NULL;
					$id = $this->suppliers_model->add_suppliers($data);
					$message = $id ? _l('added_successfully', _l('ch_groups_suppliers')) : '';
					if ($id) {
						$arrBranch = [];
						if (!empty($dataPostBranch)) {
							foreach ($dataPostBranch as $key => $value) {
								$arrBranch[] = [
									'branch_id' => $value,
									'suppliers_id' => $id,
								];
							}
						}
						if (!empty($arrBranch)) {
							$this->db->insert_batch('tbl_suppliers_branch', $arrBranch);
						}
					}
                    insertActivityLog([
                        'type_parent_obj' => 'suppliers',
                        'table_obj' => 'tblsuppliers',
                        'id_obj' => $id,
                        'name_obj' => $data['company'] ?? null,
                        'content' => lang('Thêm nhà cung cấp') . ' [' . ($data['company'] ?? null) . ']',
                        'actions' => 'add'
                    ]);
					echo json_encode([
						'success' => $id ? true : false,
						'message' => $message,
						'id' => $id,
						'name' => $data['company'],
					]);
				} else {
					if (!has_permission('suppliers', '', 'edit')) {
						echo json_encode([
							'success' => true,
							'message' => 'Bạn không có quyền sửa nhà cung cấp',
						]);
					}
					if (!empty($data['renewal_date'])) {
						$data['renewal_date'] = to_sql_date($data['renewal_date']);
					}

					if (!empty($data['date_begin'])) {
						$data['date_begin'] = to_sql_date($data['date_begin']);
					}

                    $data['deadline_contract'] = !empty($data['deadline_contract']) ? to_sql_date($data['deadline_contract']) : NULL;
					$success = $this->suppliers_model->edit_suppliers($data);
					$message = '';
					if ($success == true) {
						$this->db->where('suppliers_id', $data['id']);
						$this->db->delete('tbl_suppliers_branch');
						$arrBranch = [];
						if (!empty($dataPostBranch)) {
							foreach ($dataPostBranch as $key => $value) {
								$arrBranch[] = [
									'branch_id' => $value,
									'suppliers_id' => $data['id'],
								];
							}
						}
						if (!empty($arrBranch)) {
							$this->db->insert_batch('tbl_suppliers_branch', $arrBranch);
						}

                        insertActivityLog([
                            'type_parent_obj' => 'suppliers',
                            'table_obj' => 'tblsuppliers',
                            'id_obj' => $data['id'],
                            'name_obj' => $data['company'] ?? null,
                            'content' => lang('Sửa nhà cung cấp') . ' [' . ($data['company'] ?? null) . ']',
                            'actions' => 'edit'
                        ]);

						$message = _l('updated_successfully', _l('ch_groups_suppliers'));
					}
					echo json_encode([
						'id' => $data['id'],
						'success' => $success,
						'message' => $message,
					]);
				}
			}
		}
		
		public function get_suppliers($id = '')
		{
			echo json_encode($this->suppliers_model->get_suppliers($id));
		}
		
		public function table()
		{
			$this->app->get_table_data('suppliers');
		}
		
		public function table_contacts()
		{
			$this->app->get_table_data('table_contacts');
		}
		
		public function change_suppliers_status($id, $status)
		{
			if ($this->input->is_ajax_request()) {
				$this->suppliers_model->change_suppliers_status($id, $status);
			}
		}
		
		public function groups()
		{
			if ($this->input->is_ajax_request()) {
				$this->app->get_table_data('suppliers_groups');
			}
			$data['title'] = _l('ch_groups_suppliers');
			$this->load->view('admin/suppliers/groups_manage', $data);
		}
		
		public function group()
		{
			if ($this->input->is_ajax_request()) {
				$data = $this->input->post();
				unset($data['inline']);
				// var_dump($data);die;
				if (empty($data['id'])) {
					if (!has_permission('suppliers_group', '', 'create')) {
						echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền tạo nhóm nhà cung cấp')));
						die;
					}
					$id = $this->suppliers_model->add_group($data);
					$message = $id ? _l('added_successfully', _l('ch_groups_suppliers')) : '';
					echo json_encode([
						'success' => $id ? true : false,
						'message' => $message,
						'id' => $id,
						'name' => $data['name'],
					]);
				} else {
					if (!has_permission('suppliers_group', '', 'edit')) {
						echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền sửa nhóm nhà cung cấp')));
						die;
					}
					$success = $this->suppliers_model->edit_group($data);
					$message = '';
					if ($success == true) {
						$message = _l('updated_successfully', _l('ch_groups_suppliers'));
					}
					echo json_encode([
						'success' => $success,
						'message' => $message,
					]);
				}
			}
		}
		
		public function delete_group($id)
		{
			if (!has_permission('suppliers_group', '', 'delete')) {
				echo json_encode(array('success' => true, 'alert_type' => 'warning', 'message' => _l('Bạn không có quyền xóa nhóm nhà cung cấp')));
				die;
			}
			if (!$id) {
				redirect(admin_url('suppliers/groups'));
			}
			$response = $this->suppliers_model->delete_group($id);
			$alert_type = 'warning';
			$message = _l('Không thể xóa dữ liệu');
			if ($response) {
				$alert_type = 'success';
				$message = _l('Xóa dữ liệu thành công');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function int_suppliers_view($edit = '', $id = '', $type = 2)
		{
			$data['count'] = 0;
			$data['debt'] = 0;
			$data['payment'] = 0;
			$data['left'] = 0;
			if (!empty($id)) {
				$data['suppliers'] = $this->suppliers_model->get_suppliers($id);
				$data['title'] = $data['suppliers']->company;
				if ($edit != 'true') {
					if ($data['suppliers']->type != 0) {
						$data['count'] = count(get_table_where('tblpurchase_order', array('suppliers_id' => $id)));
						$data['debt'] = get_table_where_sum('tblpurchase_order', array('suppliers_id' => $id), 'totalAll_suppliers');
						$debt_2 = get_table_where_sum('tblpurchase_order', array('suppliers_id' => $id), 'price_other_expenses');
						$data['payment'] = get_table_where_sum('tblpay_slip', array('id_supplierss' => $id), 'payment') + $debt_2;
						$data['left'] = $data['debt'] - $data['payment'];
					} else {
						$data['count'] = count(get_table_where('tblpurchase_order', array('suppliers_id' => $id)));
						$data['debt'] = get_table_where_sum('tblpurchase_order', array('suppliers_id' => $id), 'totalAll_suppliers');
						$debt_2 = get_table_where_sum('tblpurchase_order', array('suppliers_id' => $id), 'price_other_expenses');
						$data['payment'] = (get_table_where_sum('tblpay_slip', array('id_supplierss' => $id), 'payment') + $debt_2);
						$data['left'] = $data['debt'] - $data['payment'];
					}
				} else {
					$data['title'] = _l('ch_suppliers_edit_heading');
				}
				$data['suppliers']->contacts = $this->suppliers_model->get_suppliers_contacts($id);
				$data['id'] = $id;
				$default_currency = $this->currencies_model->get_hau($data['suppliers']->default_currency);
				$data['suppliers']->default_currency = (!empty($default_currency) ? $default_currency->name : '');
				$data['province'] = get_table_where('province', array('countries' => $data['suppliers']->country));
				$data['district'] = get_table_where('district', array('provinceid' => $data['suppliers']->city));
				$data['ward'] = get_table_where('ward', array('districtid' => $data['suppliers']->district));
				$data['branch_suppliers'] = get_table_where('tbl_suppliers_branch', array('suppliers_id' => $id));
				$this->db->select('tblbranch.name as name_branch');
				$this->db->from('tbl_suppliers_branch');
				$this->db->join('tblbranch', 'tblbranch.id = tbl_suppliers_branch.branch_id');
				$this->db->where('suppliers_id', $id);
				$branch_suppliers_name = $this->db->get()->result_array();
				$branch_suppliers_name_html = '';
				if (!empty($branch_suppliers_name)) {
					foreach ($branch_suppliers_name as $key => $value) {
						$branch_suppliers_name_html .= $value['name_branch'] . ', ';
					}
				}
				$branch_suppliers_name_html = trim($branch_suppliers_name_html, ', ');
				$data['branch_suppliers_name_html'] = $branch_suppliers_name_html;
			} else {
				$data['title'] = _l('ch_suppliers_add_heading');
			}

			$data['costs'] = get_table_where('tblcosts');
			$data['info_suppliers'] = get_table_where('tblsuppliers_info_group');
			$data['currencies'] = $this->currencies_model->get();
			$data['groups'] = $this->suppliers_model->get_groups();
			$data['openEdit'] = $edit;
			$data['type'] = $type;
			$data['items'] = get_table_where('tblitems');
			$data['branch'] = get_table_where('tblbranch');
			$data['supplierClassify'] = get_table_where('tblsupplier_classify');
			$data['listDiscount'] = $this->site_model->getDiscount(3);
			$this->load->view('admin/suppliers/suppliers_modal', $data);
		}
		
		public function int_suppliers_view_data($edit = '', $id = '', $type = 2)
		{
			if (!empty($id)) {
				$data['supplierss'] = $this->suppliers_model->get_suppliers($id);
				$data['title'] = $data['supplierss']->company;
				if ($edit != 'true') {
					$default_currency = $this->currencies_model->get_hau($data['supplierss']->default_currency);
					$data['supplierss']->default_currency = (!empty($default_currency) ? $default_currency->name : '');
					$data['province'] = get_table_where('province', array('countries' => $data['supplierss']->country));
					$data['district'] = get_table_where('district', array('provinceid' => $data['supplierss']->city));
					$data['ward'] = get_table_where('ward', array('districtid' => $data['supplierss']->district));
				} else {
					$data['title'] = _l('ch_suppliers_edit_heading');
				}
				$data['supplierss']->contacts = $this->suppliers_model->get_suppliers_contacts($id);
				$data['id'] = $id;
			} else {
				$data['title'] = _l('ch_suppliers_add_heading');
			}
			$data['info_suppliers'] = get_table_where('tblsuppliers_info_group');
			$data['currencies'] = $this->currencies_model->get();
			$data['groups'] = $this->suppliers_model->get_groups();
			$data['openEdit'] = $edit;
			$data['type'] = $type;
			$data['items'] = get_table_where('tblitems');
			$this->load->view('admin/suppliers/suppliers_modal_data', $data);
		}
		
		public function table_mainstream_items($id)
		{
			$this->app->get_table_data('table_mainstream_items', array('id' => $id));
		}
		
		public function table_history_ask_suppliert($id)
		{
			$this->app->get_table_data('table_history_ask_suppliert', array('id' => $id));
		}
		
		public function table_history_quotes($id)
		{
			$this->app->get_table_data('table_history_quotes', array('id' => $id));
		}
		
		public function table_history_order($id)
		{
			$this->app->get_table_data('table_history_order', array('id' => $id));
		}
		
		public function table_mainstream_items_all()
		{
			$this->app->get_table_data('table_mainstream_items_all');
		}
		
		public function mainstream_items($id = '')
		{
			$result = array(
				'alert_type' => 'danger',
				'message' => '',
				'success' => false
			);
			if (is_numeric($id)) {
				$data = $this->input->post();
				$this->db->select('id');
				$this->db->where('id_suppliers', $id);
				$this->db->where('id_items', $data['id_items']);
				$items = $this->db->get('tblmainstream_goods')->row();
				if ($items == NULL) {
					$table = get_table_where('tbltype_items', array('type' => $data['type']), '', 'row');
					if ($data['type'] == 'items') {
						$this->db->select('price');
						$this->db->where('id', $data['id_items']);
						$pruduct = $this->db->get('tblitems')->row();
					} else {
						$this->db->select('price_sell as price');
						$this->db->where('id', $data['id_items']);
						$pruduct = $this->db->get($table->table)->row();
					}
					if ($pruduct) {
						$_data['id_suppliers'] = $id;
						$_data['id_items'] = $data['id_items'];
						$_data['price'] = $pruduct->price;
						$_data['type'] = $data['type'];
						$response = $this->db->insert('tblmainstream_goods', $_data);
					}
					if ($response) {
						$result = array(
							'alert_type' => 'success',
							'message' => _l('Thêm sản phẩm thành công'),
							'success' => true
						);
					}
				} else {
					$result = array(
						'alert_type' => 'success',
						'message' => _l('Sản phẩm đã tồn tại'),
						'success' => true
					);
				}
			}
			echo json_encode($result);
		}
		
		public function delete_items($id)
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				if ($this->db->delete('tblmainstream_goods')) {
					echo json_encode(array(
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa dữ liệu thành công'
					));
					die();
				} else {
					echo json_encode(array(
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Xóa dữ liệu không thành công'
					));
					die();
				}
			}
			echo json_encode(array(
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Xóa dữ liệu không thành công'
			));
			die();
		}
		
		public function delete($id)
		{
			if (!has_permission('suppliers', '', 'delete')) {
				echo json_encode(array(
					'success' => 'warning',
					'message' => _l('ch_no_delete')
				));
				die;
			}
            $dtData = get_table_where('tblsuppliers', array('id' => $id), '', 'row_array');
			$ktrimport = get_table_where('tblimport', array('suppliers_id' => $id), '', 'row');
			$ktrorder = get_table_where('tblpurchase_order', array('suppliers_id' => $id), '', 'row');
			$ktrask = get_table_where('tblrfq_ask_price_items', array('suppliers_id' => $id), '', 'row');
			$ktrquotes = get_table_where('tblsupplier_quotes', array('suppliers_id' => $id), '', 'row');
			if (!empty($ktrimport) || !empty($ktrorder) || !empty($ktrask) || !empty($ktrask) || !empty($ktrquotes) || !empty($ktrquotes)) {
				echo json_encode(array(
					'alert_type' => 'warning',
					'message' => _l('ch_exsit_suppliers_delete'),
				));
				die;
			}
			$response = $this->suppliers_model->delete_suppliers($id);
			$alert_type = 'warning';
			$message = _l('ch_no_delete');
			if ($response) {
				$this->db->where('suppliers_id', $id);
				$this->db->delete('tbl_suppliers_branch');
				$alert_type = 'success';
				$message = _l('ch_delete');

                insertActivityLog([
                    'type_parent_obj' => 'suppliers',
                    'table_obj' => 'tblsuppliers',
                    'id_obj' => $dtData['id'],
                    'name_obj' => $dtData['company'] ?? null,
                    'content' => lang('Xóa nhà cung cấp') . ' [' . ($dtData['company'] ?? null) . ']',
                    'actions' => 'delete'
                ]);
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function delete_contact($id)
		{
			if (!has_permission('suppliers', '', 'delete')) {
				echo json_encode(array(
					'success' => 'warning',
					'message' => _l('ch_no_delete')
				));
				die;
			}
			$response = $this->suppliers_model->delete_contact($id);
			$alert_type = 'warning';
			$message = _l('Không thể xóa dữ liệu');
			if ($response) {
				$alert_type = 'success';
				$message = _l('Xóa dữ liệu thành công');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function table_contacts_all()
		{
			$this->app->get_table_data('table_contacts_all_suppliers');
		}
		
		public function evaluation_criteria($value = '')
		{
			if (!is_admin()) {
				access_denied('suppliers');
			}
			$data['title'] = _l('ch_evaluation_criteria');
			$this->load->view('admin/suppliers/evaluation_criteria', $data);
		}
		
		public function add_evaluation_criteria()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['id'])) {
					unset($data['id']);
					$_data['code_evaluation_criteria'] = $data['code_evaluation_criteria'];
					
					$this->db->where('code_evaluation_criteria', $data['code_evaluation_criteria']);
					$ktCode = $this->db->get('tblevaluation_criteria')->row();
					if(!empty($ktCode)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					$this->db->where('code_children', $data['code_evaluation_criteria']);
					$ktCodeChild = $this->db->get('tblevaluation_criteria_children')->row();
					if(!empty($ktCodeChild)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					
					$_data['name'] = $data['name'];
					$_data['color'] = $data['color'];
					$this->db->insert('tblevaluation_criteria', $_data);
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
				} else {
					
					$this->db->where('code_evaluation_criteria', $data['code_evaluation_criteria']);
					$this->db->where('id != "'.$data['id'].'"', false, false);
					$ktCode = $this->db->get('tblevaluation_criteria')->row();
					if(!empty($ktCode)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					$this->db->where('code_children', $data['code_evaluation_criteria']);
					$ktCodeChild = $this->db->get('tblevaluation_criteria_children')->row();
					if(!empty($ktCodeChild)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					$_data['code_evaluation_criteria'] = $data['code_evaluation_criteria'];
					$_data['name'] = $data['name'];
					
					$_data['color'] = $data['color'];
					$this->db->update('tblevaluation_criteria', $_data, array('id' => $data['id']));
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
				}
			} else {
				$alert_type = 'warning';
				$message = _l('ch_no_updated_successfuly');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function add_evaluation_criteria_children()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['id_evaluation_children'])) {
					unset($data['id_evaluation_children']);
					$code = $_data['code_children'] = trim($data['code_children']);
					
					$this->db->where('code_children', $code);
					$ktCodeChild = $this->db->get('tblevaluation_criteria_children')->row();
					if(!empty($ktCodeChild)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					$this->db->where('code_evaluation_criteria', $code);
					$ktCode = $this->db->get('tblevaluation_criteria')->row();
					if(!empty($ktCode)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					$_data['name_children'] = $data['name_children'];
					$_data['id_evaluation'] = $data['id_evaluation'];
					$this->db->insert('tblevaluation_criteria_children', $_data);
					$alert_type = 'success';
					$message = _l('ch_added_successfuly');
				} else {
					$code = $_data['code_children'] = trim($data['code_children']);
					
					$this->db->where('code_children', $code);
					$this->db->where('id != "'.$data['id_evaluation_children'].'"', false, false);
					$ktCodeChild = $this->db->get('tblevaluation_criteria_children')->row();
					if(!empty($ktCodeChild)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					$this->db->where('code_evaluation_criteria', $code);
					$ktCode = $this->db->get('tblevaluation_criteria')->row();
					if(!empty($ktCode)) {
						echo json_encode(array(
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Mã tiêu chí NCC đã tồn tại'
						));die();
					}
					
					
					$_data['name_children'] = $data['name_children'];
					$this->db->update('tblevaluation_criteria_children', $_data, array('id' => $data['id_evaluation_children']));
					$alert_type = 'success';
					$message = _l('ch_updated_successfuly');
				}
			} else {
				$alert_type = 'warning';
				$message = _l('ch_no_updated_successfuly');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function delete_evaluation_criteria($id = '')
		{
			if (!is_admin()) {
				access_denied('Delete Suppliers Contact');
			}
			$this->db->where('id', $id);
			$response = $this->db->delete('tblevaluation_criteria');
			$alert_type = 'warning';
			$message = _l('ch_no_delete');
			if ($response) {
				$alert_type = 'success';
				$message = _l('ch_delete');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function delete_evaluation_criteria_children($id = '')
		{
			if (!is_admin()) {
				access_denied('Delete Suppliers Contact');
			}
			$this->db->where('id', $id);
			$response = $this->db->delete('tblevaluation_criteria_children');
			$alert_type = 'warning';
			$message = _l('ch_no_delete');
			if ($response) {
				$alert_type = 'success';
				$message = _l('ch_delete');
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function get_evaluation_criteria($id = '')
		{
			echo json_encode(get_table_where('tblevaluation_criteria', array('id' => $id), '', 'row'));
		}
		
		public function get_evaluation_criteria_children($id = '')
		{
			echo json_encode(get_table_where('tblevaluation_criteria_children', array('id' => $id), '', 'row'));
		}
		
		public function table_evaluation_criteria($value = '')
		{
			$this->app->get_table_data('table_evaluation_criteria');
		}
		
		//Hoàng CRM bổ xung dashboard
		public function dashboard_suppliers()
		{
			$data['debt_total'] = $this->debt_suppliersss();
			// $data['debt_total'] = 0;
			$data['suppliers'] = get_table_where('tblsuppliers');
			$data['title'] = _l('dashboard_suppliers');
			$tickets_awaiting_reply_by_status = $this->dashboard_model->tickets_awaiting_reply_by_status();
			$tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();
			$data['tickets_reply_by_status'] = json_encode($tickets_awaiting_reply_by_status);
			$data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);
			$data['tickets_reply_by_status_no_json'] = $tickets_awaiting_reply_by_status;
			$data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;
			$data['client_time_stats'] = json_encode($this->dashboard_model->client_time_status());
			$data['leads_time_stats'] = json_encode($this->dashboard_model->lead_time_status());
			$this->load->view('admin/suppliers/dashboard_suppliers', $data);
		}
		
		public function delete_info_suppliers($id = '')
		{
			if ($id != '') {
				$response = $this->db->delete('tblsuppliers_info_group', array('id' => $id));
				$alert_type = 'warning';
				$message = _l('ch_no_delete');
				if ($response) {
					$alert_type = 'success';
					$message = _l('ch_delete');
				}
				echo json_encode(array(
					'alert_type' => $alert_type,
					'message' => $message
				));
			}
		}
		
		public function delete_info_suppliers_detail($id = '')
		{
			$ktr = NULL;
			$alert_type = 'warning';
			$message = _l('ch_no_delete');
			$ktr = get_table_where('tblsuppliers_value', array('id_detail' => $id), '', 'row');
			if (empty($ktr)) {
				$response = $this->db->delete('tblsuppliers_info_detail', array('id' => $id));
				$alert_type = 'warning';
				$message = _l('ch_no_delete');
				if ($response) {
					$this->db->delete('tblsuppliers_info_detail_value', array('id_info_detail' => $id));
					$alert_type = 'success';
					$message = _l('ch_delete');
				}
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function delete_info_suppliers_detail_value($id = '', $id_detail = '')
		{
			$ktr = NULL;
			$alert_type = 'warning';
			$message = _l('ch_no_delete');
			$ktr = get_table_where('tblsuppliers_value', array('id_detail' => $id_detail, 'value' => $id), '', 'row');
			if (empty($ktr)) {
				$response = $this->db->delete('tblsuppliers_info_detail_value', array('id' => $id));
				$alert_type = 'warning';
				$message = _l('ch_no_delete');
				if ($response) {
					$alert_type = 'success';
					$message = _l('ch_delete');
				}
			}
			echo json_encode(array(
				'alert_type' => $alert_type,
				'message' => $message
			));
		}
		
		public function get_html_evaluate($id = '', $stt = '')
		{
			if ($id != '') {
				$data['id'] = $id;
				$data['stt'] = $stt;
				$dem_temp = 0;
				$main = get_table_where('tblevaluation_criteria');
				foreach ($main as $key => $value) {
					$data['dataMain'][$dem_temp]['id_main'] = $value['id'];
					$data['dataMain'][$dem_temp]['main'] = $value['name'];
					$sub = get_table_where('tblevaluation_criteria_children', array('id_evaluation' => $value['id']));
					$dem_temp_sub = 0;
					foreach ($sub as $keySub => $valueSub) {
						$data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['id_sub'] = $valueSub['id'];
						$data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['name'] = $valueSub['name_children'];
						$dem_temp_sub++;
					}
					$dem_temp++;
				}
				echo json_encode([
					'data' => $this->load->view('admin/suppliers/init_evaluate', $data, true),
					'data_left' => $this->load->view('admin/suppliers/init_evaluate_left', $data, true),
				]);
			}
		}
		
		//end
		public function add_transporters()
		{
			if ($this->input->post()) {
				$data = $this->input->post();
				if (empty($data['code'])) {
					$_data['code'] = sprintf('%06d', ch_getMaxID('id', db_prefix() . 'suppliers') + 1);
				}
				$_data['datecreated'] = date('Y-m-d H:i:s');
				$_data['prefix'] = get_option('prefix_supplier');
				$_data['addedfrom'] = get_staff_user_id();
				$_data['company'] = $data['company'];
				$_data['type'] = 1;
				$_data['active'] = 1;
				if (!empty($data['phone'])) {
					$_data['phone'] = $data['phone'];
				}
				if (!empty($data['email'])) {
					$_data['email'] = $data['email'];
				}
				if (!empty($data['address'])) {
					$_data['address'] = $data['address'];
				}
				$this->db->insert('tblsuppliers', $_data);
				$id = $this->db->insert_id();
				$alert_type = 'success';
				$message = _l('ch_added_successfuly');
				if (empty($id)) {
					$alert_type = 'warning';
					$message = _l('cong_add_false');
				}
				echo json_encode([
					'id' => $id,
					'alert_type' => $alert_type,
					'message' => $message,
				]);
			} else {
				$alert_type = 'warning';
				$message = _l('cong_add_false');
				echo json_encode([
					'id' => '',
					'success' => $success,
					'message' => $message,
				]);
			}
		}
		
		public function add_flash_transporters()
		{
			$this->load->view('admin/suppliers/add_flash_transporters');
		}
		
		public function SearchGroup($id = '')
		{
			$data = [];
			$search = $this->input->get('term');
			$type = $this->input->get('type');
			$limit_one = 15;
			$limit_two = 15;
			$limit_all = 50;
			$this->db->select(
				'
            tblsuppliers_groups.id as id,
            tblsuppliers_groups.name as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tblsuppliers_groups.name', $search);
				$this->db->group_end();
			} else {
				if ($id > 0) {
					$this->db->group_start();
					$this->db->where('tblsuppliers_groups.id', $id);
					$this->db->group_end();
				}
			}
			$this->db->order_by('tblsuppliers_groups.name', 'DESC');
			$this->db->limit($limit_one);
			$items = $this->db->get('tblsuppliers_groups')->result_array();
			if (!empty($items)) {
				$data['results'][] =
					[
						'children' => $items
					];
			}
			echo json_encode($data);
			die();
		}
		
		public function add_suppliert_items($id = '')
		{
			$data = $this->input->post();
			$_data['price'] = number_unformat($data['price_items']);
			$_data['id_suppliers'] = $data['suppliers'];
			$_data['id_items'] = $id;
			$_data['type'] = "items";
			if (is_numeric($id)) {
				$ktr = get_table_where('tblmainstream_goods', array('id_items' => $_data['id_items'], 'type' => $_data['type'], 'id_suppliers' => $_data['id_suppliers']), '', 'row');
				if (!empty($ktr)) {
					$this->db->update('tblmainstream_goods', array('price' => $_data['price']), array('id' => $id));
					echo json_encode(array(
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Cập nhật thành công'
					));
					die();
				} else {
					$this->db->insert('tblmainstream_goods', $_data);
					echo json_encode(array(
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Thêm thành công thành công'
					));
					die();
				}
			}
			echo json_encode(array(
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Thêm không thành công'
			));
			die();
		}
		
		public function SearchSupplierss($id = '')
		{
			$data = [];
			$search = $this->input->get('term');
			$type = $this->input->get('type');
			$limit_one = 15;
			$limit_two = 15;
			$limit_all = 50;
			$this->db->select(
				'
            tblsuppliers.id as id,
            tblsuppliers.company as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tblsuppliers.company', $search);
				$this->db->group_end();
			} else {
				if ($id > 0) {
					$this->db->group_start();
					$this->db->where('tblsuppliers.id', $id);
					$this->db->group_end();
				}
			}
			$this->db->where('tblsuppliers.type', 0);
			$this->db->order_by('tblsuppliers.company', 'DESC');
			$this->db->limit($limit_one);
			$items = $this->db->get('tblsuppliers')->result_array();
			if (!empty($items)) {
				$data['results'][] =
					[
						'children' => $items
					];
			}
			echo json_encode($data);
			die();
		}
		
		public function SearchSupplier($id = '')
		{
			$data = [];
			$search = $this->input->get('term');
			$type = $this->input->get('type');
			$limit_one = 15;
			$limit_two = 15;
			$limit_all = 50;
			$this->db->select(
				'
            tblsuppliers.id as id,
            tblsuppliers.company as text',
				false
			);
			if (!empty($search)) {
				$this->db->group_start();
				$this->db->like('tblsuppliers.company', $search);
				$this->db->group_end();
			} else {
				if ($id > 0) {
					$this->db->group_start();
					$this->db->where('tblsuppliers.id', $id);
					$this->db->group_end();
				}
			}
			$this->db->order_by('tblsuppliers.company', 'DESC');
			$this->db->limit($limit_one);
			$items = $this->db->get('tblsuppliers')->result_array();
			if (!empty($items)) {
				$data['results'][] =
					[
						'children' => $items
					];
			}
			echo json_encode($data);
			die();
		}
		
		// sum note
		public function searchSuppliers($id = false)
		{
			$data = [];
			$term = $this->input->get('term');
			$limit = 50;
			$data['results'] = $this->suppliers_model->searchSuppliers($term, $limit);
			if ($id) {
			}
			echo json_encode($data);
		}
		
		// ./sum note
		public function debt_suppliersss()
		{
			$aColumns = [
				'tblsuppliers.id',
				'tblsuppliers.company',
				'(COALESCE(SUM(tblpurchase_order.totalAll_suppliers),0)+COALESCE((select sum(tbl_outsource.grand_total) from tbl_outsource where tbl_outsource.supplier_id=tblsuppliers.id AND tbl_outsource.status_pay < 2 ),0)) as total_import',
				'(COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0)+COALESCE((select sum(tbl_outsource.amount_paid) from tbl_outsource where tbl_outsource.supplier_id=tblsuppliers.id AND tbl_outsource.status_pay < 2 ),0))  as amount_paid_import',
				'SUM(tblpurchase_order.price_other_expenses) as price_other_expenses_import',
				'7',
			];
			$sIndexColumn = 'id';
			$sTable = 'tblsuppliers';
			$where = [];
			$having = 'HAVING (total_import - amount_paid_import) > 0';
			array_push($where, 'AND ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
			$filter = [];
			$join = [
				'LEFT JOIN tblpurchase_order ON tblpurchase_order.suppliers_id=tblsuppliers.id',
				'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice',
			];
			array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
			array_push($where, 'AND tblsuppliers.type = 0');
			$group_by = 'GROUP BY tblsuppliers.id';
			$result = data_tables_init_having_v2($aColumns, $sIndexColumn, $sTable, $join, $where, [
				'tblsuppliers.type',
			], '', $group_by, $having);
			$output = $result['output'];
			$rResult = $result['rResult'];
			$aColumns_order = [
				'tblsuppliers.id',
				'tblsuppliers.company',
				'SUM(tbl_orders.cost_delivery) as total_import',
				'0 as amount_paid_import',
				'SUM(tbl_orders.price_other_expenses_delivery) as price_other_expenses_import',
				'7',
			];
			$sIndexColumn_order = 'id';
			$sTable_order = 'tblsuppliers';
			$where_order = [];
			$having_order = 'HAVING (total_import - price_other_expenses_import) > 0';
			$filter_order = [];
			$join_order = [
				'LEFT JOIN tbl_orders ON tbl_orders.transporter_id=tblsuppliers.id',
			];
			array_push($where_order, 'AND tbl_orders.status = "approved"');
			array_push($where_order, 'AND tblsuppliers.type = 1');
			$group_by_order = 'GROUP BY tblsuppliers.id';
			$result_order = data_tables_init_having_v2($aColumns_order, $sIndexColumn_order, $sTable_order, $join_order, $where_order, [
				'tblsuppliers.type',
			], '', $group_by_order, $having_order);
			$output_order = $result_order['output'];
			$rResult_order = $result_order['rResult'];
			$aColumnsoutsource = [
				'tblsuppliers.id',
				'tblsuppliers.company',
				'SUM(tbl_outsource.grand_total) as total_import',
				'(COALESCE(SUM(tbl_outsource.amount_paid),0)) as amount_paid_import',
				'0 as price_other_expenses_import',
				'7',
			];
			$sIndexColumnoutsource = 'id';
			$sTableoutsource = 'tblsuppliers';
			$whereoutsource = [];
			$havingoutsource = 'HAVING (total_import - amount_paid_import) > 0';
			$filteroutsource = [];
			$joinoutsource = [
				'LEFT JOIN tbl_outsource ON tbl_outsource.supplier_id=tblsuppliers.id',
			];
			array_push($whereoutsource, 'AND tbl_outsource.status = "approved"');
			array_push($whereoutsource, 'AND tblsuppliers.type = 0');
			$group_byoutsource = 'GROUP BY tblsuppliers.id';
			$resultoutsource = data_tables_init_having_v2($aColumnsoutsource, $sIndexColumnoutsource, $sTableoutsource, $joinoutsource, $whereoutsource, [
				'tblsuppliers.type',
			], '', $group_byoutsource, $havingoutsource);
			$outputoutsource = $resultoutsource['output'];
			$rResultoutsource = $resultoutsource['rResult'];
			$debt_total = 0;
			if (empty($rResult)) {
				$rResult = array_merge($rResult, $rResultoutsource);
			}
			$rResult = array_merge($rResult, $rResult_order);
			$output['iTotalRecords'] = $output['iTotalRecords'] + $output_order['iTotalRecords'] + $outputoutsource['iTotalRecords'];
			$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'] + $output_order['iTotalDisplayRecords'] + $outputoutsource['iTotalDisplayRecords'];
			foreach ($rResult as $key => $aRow) {
				for ($i = 0; $i < count($aColumns); $i++) {
					if ($aColumns[$i] == '7') {
						if ($aRow['type'] == 0) {
							$debt_total += $aRow['total_import'] - $aRow['amount_paid_import'];
						} else {
							$debt_total += $aRow['total_import'] - $aRow['price_other_expenses_import'];
						}
					}
				}
			}
			return $debt_total;
		}
		
		public function deleteitems_supplier()
		{
			$mainstream_goods = get_table_where('tblmainstream_goods');
			foreach ($mainstream_goods as $key => $value) {
				if ($value['type'] == 'items') {
					$name = get_table_where('tblitems', array('id' => $value['id_items']), '', 'row');
				} else {
					$table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row');
					$name = get_table_where($table->table, array('id' => $value['id_items']), '', 'row');
				}
				if (empty($name)) {
					$this->db->delete('tblmainstream_goods', array('id' => $value['id']));
				}
			}
		}
		
		public function exportExcelSuppliers()
		{
			$columsExcel = [
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
				'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
				'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
				'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
			];
			if ($this->input->post('export_excel')) {
				ini_set('memory_limit', '3500M');
				require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
				$this->load->library('PHPExcel');
				$inputFileName = 'uploads/import_ch/Thong_tin_NCC.xlsx';
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
				}
				$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestRow = $objWorksheet->getHighestRow();
				$check_key = array_search($highestColumn, $columsExcel);
				$start_date_search = $this->input->post('start_date_search');
				$end_date_search = $this->input->post('end_date_search');
				$groups_ch = $this->input->post('groups_ch');
				$row = 4;
				$this->db->select('
            tblsuppliers.id as id,
            tblsuppliers_groups.code as code_group,
            tblsuppliers_groups.name as groups,
            tblsuppliers.code as code,
            company,
            tblsupplier_classify.name as name_classify,
            vat,
            code_nxk,
            tax,
            bank_account,
            name_account,
            address_bank,
            tm_ck,
            tblcurrencies.name as name_curenci,
            IF(tblsuppliers.default_currency > 0,CONCAT("1 ",tblcurrencies.name," - ",tblcurrencies.amount_to_vnd," VND"),"") as amount_to_vnd,
            time_payment,
            contract_number,
            renewal_date,
            tblsuppliers.address as address,
            tblcontacts_suppliers.name as name_contract,
            tblcontacts_suppliers.phone as phone_contract,
            tblcontacts_suppliers.address as address_contract,
            tblsuppliers.active,
            tblsuppliers.datecreated as datecreated
            ');
				$this->db->from('tblsuppliers');
				$this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');
				$this->db->join('tblcurrencies', 'tblcurrencies.id = tblsuppliers.default_currency', 'left');
				$this->db->join('tblcontacts_suppliers', 'tblcontacts_suppliers.id_supplers = tblsuppliers.id', 'left');
				$this->db->join('tblsupplier_classify', 'tblsupplier_classify.id = tblsuppliers.type_suppliers', 'left');
				if (!empty($start_date_search)) {
					$this->db->where('tblsuppliers.datecreated >= "' . to_sql_date($start_date_search) . ' 00:00:00"');
				}
				if (!empty($end_date_search)) {
					$this->db->where('tblsuppliers.datecreated <= "' . to_sql_date($end_date_search) . ' 23:59:59"');
				}
				if (!empty($groups_ch)) {
					$this->db->where('tblsuppliers.groups_in', $groups_ch);
				}
				$this->db->order_by('tblsuppliers.id asc');
				$supplier = $this->db->get()->result_array();
				$dem = 0;
				$this->load->library('ciqrcode');
				foreach ($supplier as $key => $value) {
					$row++;
					$dem++;
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['code_group'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['groups'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, $value['code'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['company'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['name_classify'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, $value['vat']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, $value['code_nxk'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, $value['tax'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, $value['bank_account'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, $value['name_account'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, $value['address_bank'], PHPExcel_Cell_DataType::TYPE_STRING);
					$tm_ck = '';
					if ($value['tm_ck'] == 1) {
						$tm_ck = 'TM';
					} elseif ($value['tm_ck'] == 2) {
						$tm_ck = 'CK';
					}
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, $tm_ck, PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, $value['name_curenci'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, $value['amount_to_vnd'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, $value['time_payment'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, $value['contract_number'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, _d($value['renewal_date']));
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, $value['address'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, $value['name_contract'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, $value['phone_contract'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[21] . $row, $value['address_contract'], PHPExcel_Cell_DataType::TYPE_STRING);
					if (!empty($value['barcode'])) {
						$code = $value['barcode'];
					} else {
						$code = 'suppliers||' . $value['id'];
						$this->db->where('id', $value['id']);
						$this->db->update('tblsuppliers', ['barcode' => $code]);
					}
					$qr = vn_to_str(str_replace('||', '__', $code));
					$folder = FCPATH . 'uploads/suppliers/';
					if (!file_exists($folder)) {
						mkdir($folder);
						fopen($folder . 'index.html', 'w');
					}
					if (!file_exists($folder . 'qrcode' . '/')) {
						mkdir($folder . 'qrcode' . '/');
						fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
					}
					$params['data'] = $code;
					$params['level'] = 'H';
					$params['size'] = 40;
					$params['savename'] = $folder . 'qrcode/' . $qr . '.png';
					$this->ciqrcode->generate($params);
					$img = ($folder . 'qrcode/' . $qr . '.png');
					if (!empty($img)) {
						$objDrawing1 = new PHPExcel_Worksheet_Drawing();
						$objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
						$objDrawing1->setPath($img);
						$objDrawing1->setWidth(80);
						$objDrawing1->setHeight(53);
						$objDrawing1->setOffsetX(3);
						$objDrawing1->setOffsetY(2);
						$objDrawing1->setCoordinates($columsExcel[22] . $row);
					}
					$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(42);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, '')->getStyle($columsExcel[22] . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A5:W' . $row)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A5:W' . $row)->applyFromArray([
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
				]);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(60);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(40);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(10);
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
				$cacheSettings = array(' memoryCacheSize ' => '8MB');
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$filename = lang('thong_tin_ncc') . '.xls';
				ob_start();
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="$filename"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
				$xlsData = ob_get_contents();
				ob_end_clean();
				$response = array(
					'result' => 1,
					'filename' => $filename,
					'message' => lang('success'),
					'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
				);
				die(json_encode($response));
			}
		}
	}
