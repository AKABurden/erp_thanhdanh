<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Misc extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('misc_model');
	}
	// hau
	/* Check if client email exists/  ajax */
	public function suppliers_email_exists()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$userid = $this->input->post('userid');
				if ($userid != '') {
					$this->db->where('id', $userid);
					$_current_email = $this->db->get(db_prefix() . 'suppliers')->row();
					if ($_current_email->email == $this->input->post('email')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('email', $this->input->post('email'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'suppliers');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function client_code_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('code') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('userid', $id);
					$_current_email = $this->db->get(db_prefix() . 'clients')->row();
					if ($_current_email->zcode == $this->input->post('code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('zcode', $this->input->post('code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'clients');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function lead_code_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('code') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('id', $id);
					$_current_email = $this->db->get(db_prefix() . 'leads')->row();
					if ($_current_email->zcode == $this->input->post('code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('zcode', $this->input->post('code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'leads');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function items_code_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('code') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('id', $id);
					$_current_email = $this->db->get(db_prefix() . 'items')->row();
					if ($_current_email->code == $this->input->post('code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('code', $this->input->post('code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'items');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function suppliers_code_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('supplier_code') != '') {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$userid = $this->input->post('userid');
				if ($userid != '') {
					$this->db->where('id', $userid);
					$_current_email = $this->db->get(db_prefix() . 'suppliers')->row();
					if ($_current_email->code == $this->input->post('supplier_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('code', $this->input->post('supplier_code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'suppliers');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function suppliers_company_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('company') != '') {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$userid = $this->input->post('userid');
				if ($userid != '') {
					$this->db->where('id', $userid);
					$_current_email = $this->db->get(db_prefix() . 'suppliers')->row();
					if ($_current_email->company == $this->input->post('company')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('company', $this->input->post('company'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'suppliers');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function suppliers_vat_exists()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$userid = $this->input->post('userid');
				if ($userid != '') {
					$this->db->where('id', $userid);
					$_current_vat = $this->db->get(db_prefix() . 'suppliers')->row();
					if ($_current_vat->vat == $this->input->post('vat')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('vat', $this->input->post('vat'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'suppliers');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function changeRowNew_ch($table = '', $id = '')
	{
		if ($table != '' && $id != '') {
			$checkExists = get_table_where($table, array('id' => $id), '', 'row');
			$arrID = explode(',', $checkExists->not_new_by_staff);
			$checkArr = in_array(get_staff_user_id(), $arrID);
			if (!$checkArr) {
				if ($checkExists->not_new_by_staff) {
					$data = $checkExists->not_new_by_staff . ',' . get_staff_user_id();
				} else {
					$data = get_staff_user_id();
				}
				$this->db->set('not_new_by_staff', $data);
				$this->db->where('id', $id);
				$this->db->update($table);
			}
		}
	}

	public function fetch_address_info_gmaps()
	{
		include_once(APPPATH . 'third_party/JD_Geocoder_Request.php');

		$data = $this->input->post();
		$address = '';

		$address .= $data['address'];
		if (!empty($data['city'])) {
			$address .= ', ' . $data['city'];
		}

		if (!empty($data['country'])) {
			$address .= ', ' . $data['country'];
		}

		$apiKey = get_option('google_api_key');
		if (empty($apiKey)) {
			echo json_encode([
				'response' => [
					'status' => 'MISSING_API_KEY',
					'error_message' => 'Add Google API Key in Setup->Settings->Google',
				],
			]);
			die;
		}

		$georequest = new JD_Geocoder_Request($apiKey);
		$georequest->forwardSearch($address);
		echo json_encode($georequest);
	}

	public function get_currency($id)
	{
		echo json_encode(get_currency($id));
	}

	public function get_taxes_dropdown_template()
	{
		$name = $this->input->post('name');
		$taxname = $this->input->post('taxname');
		echo $this->misc_model->get_taxes_dropdown_template($name, $taxname);
	}

	public function dismiss_cron_setup_message()
	{
		update_option('hide_cron_is_required_message', 1);
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function dismiss_timesheets_notice_admins()
	{
		update_option('show_timesheets_overview_all_members_notice_admins', 0);
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function dismiss_cloudflare_notice()
	{
		update_option('show_cloudflare_notice', 0);
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function clear_system_popup()
	{
		$this->session->unset_userdata('system-popup');
	}

	public function tinymce_file_browser()
	{
		$data['connector'] = admin_url() . '/utilities/media_connector';
		$data['mediaLocale'] = get_media_locale();
		$this->app_css->add('app-css', base_url($this->app_css->core_file('assets/css', 'style.css')) . '?v=' . $this->app_css->core_version(), 'editor-media');
		$this->load->view('admin/includes/elfinder_tinymce', $data);
	}

	public function get_relation_data_hau()
	{
		if ($this->input->post()) {
			$type = $this->input->post('type');
			$type_items = $this->input->post('type_items');
			$id_order = $this->input->post('type_items');
			$data = get_relation_data_hau($type, '', $type_items, $id_order);
			if ($this->input->post('rel_id')) {
				$rel_id = $this->input->post('rel_id');
			} else {
				$rel_id = '';
			}

			$relOptions = init_relation_options($data, $type, $rel_id);
			echo json_encode($relOptions);
			die;
		}
	}

	public function get_relation_data()
	{
		if ($this->input->post()) {
			$type = $this->input->post('type');
			$type_items = $this->input->post('type_items');
			$id_order = $this->input->post('id_order');
			if (!empty($id_order)) {
				$data = get_relation_data_hau($type, '', $type_items, $id_order);
			} else {
				$data = get_relation_data($type, '', $type_items);
			}



			if ($this->input->post('rel_id')) {
				$rel_id = $this->input->post('rel_id');
			} else {
				$rel_id = '';
			}

			$relOptions = init_relation_options($data, $type, $rel_id);

			// print_arrays($relOptions);
			echo json_encode($relOptions);
			die;
		}
	}

	public function get_relation_data_order_quote()
	{
		if ($this->input->post()) {
			$type = $this->input->post('type');
			$type_items = $this->input->post('type_items');
			$id_quotes = $this->input->post('id_quotes');
			if (!empty($id_quotes)) {
				$data = get_relation_data_order_quote($type, '', $type_items, $id_quotes);
			} else {
				$data = get_relation_data($type, '', $type_items);
			}
			if ($this->input->post('rel_id')) {
				$rel_id = $this->input->post('rel_id');
			} else {
				$rel_id = '';
			}

			$relOptions = init_relation_options($data, $type, $rel_id);
			echo json_encode($relOptions);
			die;
		}
	}

	public function get_relation_data_order_purchases()
	{
		if ($this->input->post()) {
			$type = $this->input->post('type');
			$type_items = $this->input->post('type_items');
			$id_purchases = $this->input->post('id_purchases');
			if (!empty($id_purchases)) {
				$data = get_relation_data_order_purchases($type, '', $type_items, $id_purchases);
			} else {
				$data = get_relation_data($type, '', $type_items);
			}
			if ($this->input->post('rel_id')) {
				$rel_id = $this->input->post('rel_id');
			} else {
				$rel_id = '';
			}

			$relOptions = init_relation_options($data, $type, $rel_id);
			echo json_encode($relOptions);
			die;
		}
	}

	public function delete_sale_activity($id)
	{
		if (is_admin()) {
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'sales_activity');
		}
	}

	public function upload_sales_file()
	{
		handle_sales_attachments($this->input->post('rel_id'), $this->input->post('type'));
	}

	public function add_sales_external_attachment()
	{
		if ($this->input->post()) {
			$file = $this->input->post('files');
			$this->misc_model->add_attachment_to_database($this->input->post('rel_id'), $this->input->post('type'), $file, $this->input->post('external'));
		}
	}

	public function toggle_file_visibility($id)
	{
		$this->db->where('id', $id);
		$row = $this->db->get(db_prefix() . 'files')->row();
		if ($row->visible_to_customer == 1) {
			$v = 0;
		} else {
			$v = 1;
		}

		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'files', [
			'visible_to_customer' => $v,
		]);
		echo $v;
	}

	public function format_date()
	{
		if ($this->input->post()) {
			$date = $this->input->post('date');
			$date = strtotime(current(explode('(', $date)));
			echo _d(date('Y-m-d', $date));
		}
	}

	public function send_file()
	{
		if ($this->input->post('send_file_email')) {
			if ($this->input->post('file_path')) {
				$this->load->model('emails_model');
				$this->emails_model->add_attachment([
					'attachment' => $this->input->post('file_path'),
					'filename' => $this->input->post('file_name'),
					'type' => $this->input->post('filetype'),
					'read' => true,
				]);
				$message = $this->input->post('send_file_message');
				$message = nl2br($message);
				$success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);
				if ($success) {
					set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
				} else {
					set_alert('warning', _l('custom_file_fail_send'));
				}
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function update_ei_items_order($type)
	{
		$data = $this->input->post();
		foreach ($data['data'] as $order) {
			$this->db->where('id', $order[0]);
			$this->db->update(db_prefix() . 'itemable', [
				'item_order' => $order[1],
			]);
		}
	}

	/* Since version 1.0.2 add client reminder */
	public function add_reminder($rel_id_id, $rel_type)
	{
		$message = '';
		$alert_type = 'warning';
		if ($this->input->post()) {
			$success = $this->misc_model->add_reminder($this->input->post(), $rel_id_id);
			if ($success) {
				$alert_type = 'success';
				$message = _l('reminder_added_successfully');
			}
		}
		echo json_encode([
			'alert_type' => $alert_type,
			'message' => $message,
		]);
	}

	public function get_reminders($id, $rel_type)
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data('reminders', [
				'id' => $id,
				'rel_type' => $rel_type,
			]);
		}
	}

	public function my_reminders()
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data('staff_reminders');
		}
	}

	public function reminders()
	{
		$this->load->model('staff_model');
		$data['members'] = $this->staff_model->get('', ['active' => 1]);
		$data['title'] = _l('reminders');
		$data['bodyclass'] = 'all-reminders';
		$this->load->view('admin/utilities/all_reminders', $data);
	}

	public function reminders_table()
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data('all_reminders');
		}
	}

	/* Since version 1.0.2 delete client reminder */
	public function delete_reminder($rel_id, $id, $rel_type)
	{
		if (!$id && !$rel_id) {
			die('No reminder found');
		}
		$success = $this->misc_model->delete_reminder($id);
		$alert_type = 'warning';
		$message = _l('reminder_failed_to_delete');
		if ($success) {
			$alert_type = 'success';
			$message = _l('reminder_deleted');
		}
		echo json_encode([
			'alert_type' => $alert_type,
			'message' => $message,
		]);
	}

	public function get_reminder($id)
	{
		$reminder = $this->misc_model->get_reminders($id);
		if ($reminder) {
			if ($reminder->creator == get_staff_user_id() || is_admin()) {
				$reminder->date = _dt($reminder->date);
				$reminder->description = clear_textarea_breaks($reminder->description);
				echo json_encode($reminder);
			}
		}
	}

	public function edit_reminder($id)
	{
		$reminder = $this->misc_model->get_reminders($id);
		if ($reminder && ($reminder->creator == get_staff_user_id() || is_admin()) && $reminder->isnotified == 0) {
			$success = $this->misc_model->edit_reminder($this->input->post(), $id);
			echo json_encode([
				'alert_type' => 'success',
				'message' => ($success ? _l('updated_successfully', _l('reminder')) : ''),
			]);
		}
	}

	public function run_cron_manually()
	{
		if (is_admin()) {
			$this->load->model('cron_model');
			$this->cron_model->run(true);
			redirect(admin_url('settings?group=cronjob'));
		}
	}

	/* Since Version 1.0.1 - General search */
	public function search()
	{
		$q = $this->input->post('q');

		$recentSearches = array_reverse(get_staff_recent_search_history());

		$recentSearches[] = $q;

		$recentSearches = update_staff_recent_search_history($recentSearches);

		$data['result'] = $this->misc_model->perform_search($q);
		echo json_encode([
			'results' => $this->load->view('admin/search', $data, true),
			'history' => $recentSearches,
		]);
	}

	public function remove_recent_search($index)
	{
		$recentSearches = get_staff_recent_search_history();
		unset($recentSearches[$index]);
		update_staff_recent_search_history(array_reverse($recentSearches));
	}

	public function add_note($rel_id, $rel_type)
	{
		if ($this->input->post()) {
			$success = $this->misc_model->add_note($this->input->post(), $rel_type, $rel_id);
			if ($success) {
				set_alert('success', _l('added_successfully', _l('note')));
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function edit_note($id)
	{
		if ($this->input->post()) {
			$success = $this->misc_model->edit_note($this->input->post(), $id);
			echo json_encode([
				'success' => $success,
				'message' => _l('note_updated_successfully'),
			]);
		}
	}

	public function delete_note($id)
	{
		$success = $this->misc_model->delete_note($id);

		if (!$this->input->is_ajax_request()) {
			if ($success) {
				set_alert('success', _l('deleted', _l('note')));
			}
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			echo json_encode(['success' => $success]);
		}
	}

	/* Remove customizer open from database */
	public function set_setup_menu_closed()
	{
		if ($this->input->is_ajax_request()) {
			$this->session->set_userdata([
				'setup-menu-open' => '',
			]);
		}
	}

	/* Set session that user clicked on setup_menu menu link to stay open */
	public function set_setup_menu_open()
	{
		if ($this->input->is_ajax_request()) {
			$this->session->set_userdata([
				'setup-menu-open' => true,
			]);
		}
	}

	/* User dismiss announcement */
	public function dismiss_announcement($id)
	{
		$this->misc_model->dismiss_announcement($id);
		redirect($_SERVER['HTTP_REFERER']);
	}

	/* Set notifications to read */
	public function set_notifications_read()
	{
		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => $this->misc_model->set_notifications_read(),
			]);
		}
	}

	public function set_notification_read_inline($id)
	{
		$this->misc_model->set_notification_read_inline($id);
	}

	public function set_desktop_notification_read($id)
	{
		$this->misc_model->set_desktop_notification_read($id);
	}

	public function mark_all_notifications_as_read_inline()
	{
		$this->misc_model->mark_all_notifications_as_read_inline();
	}

	public function notifications_check()
	{
		$notificationsIds = [];
		if (get_option('desktop_notifications') == '1') {
			$notifications = $this->misc_model->get_user_notifications();

			$notificationsPluck = array_filter($notifications, function ($n) {
				return $n['isread'] == 0;
			});

			$notificationsIds = array_pluck($notificationsPluck, 'id');
		}

		echo json_encode([
			'html' => $this->load->view('admin/includes/notifications', [], true),
			'notificationsIds' => $notificationsIds,
		]);
	}

	/* Check if staff email exists / ajax */
	public function staff_email_exists()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$member_id = $this->input->post('memberid');
				if ($member_id != '') {
					$this->db->where('staffid', $member_id);
					$_current_email = $this->db->get(db_prefix() . 'staff')->row();
					if ($_current_email->email == $this->input->post('email')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('email', $this->input->post('email'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'staff');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/* Check if client email exists/  ajax */
	public function contact_email_exists()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$userid = $this->input->post('userid');
				if ($userid != '') {
					$this->db->where('id', $userid);
					$_current_email = $this->db->get(db_prefix() . 'contacts')->row();
					if ($_current_email->email == $this->input->post('email')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('email', $this->input->post('email'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'contacts');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/* Goes blank page but with messagae access danied / message set from session flashdata */
	public function access_denied()
	{
		$this->load->view('admin/blank_page');
	}

	/* Goes to blank page with message page not found / message set from session flashdata */
	public function not_found()
	{
		$this->load->view('admin/blank_page');
	}

	public function change_maximum_number_of_digits_to_decimal_fields($digits)
	{
		if (is_admin()) {
			hooks()->do_action('before_change_maximum_number_of_digits_to_decimal_fields');
			$tables = $this->db->query("SELECT *
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='" . APP_DB_NAME . "'")->result_array();
			foreach ($tables as $table_data) {
				$table = $table_data['TABLE_NAME'];
				$fields = $this->db->list_fields($table);

				foreach ($fields as $field) {
					$field_info = $this->db->query('SHOW FIELDS
                        FROM ' . $table . " where Field ='" . $field . "'")->result_array();
					$field_type = strtolower($field_info[0]['Type']);
					if (strpos($field_type, 'decimal') !== false) {
						$field_null = strtoupper($field_info[0]['Null']);
						if ($field_null == 'YES') {
							$field_is_null = 'NULL';
						} else {
							$field_is_null = 'NOT NULL';
						}
						$total_decimals = strafter($field_info[0]['Type'], ',');
						$total_decimals = strbefore($total_decimals, ')');

						if ($field_info[0]['Default'] == null) {
							$field_default_value = '';
						} else {
							$field_default_value = ' DEFAULT 0.' . str_repeat(0, $total_decimals);
						}

						$this->db->query("ALTER TABLE $table CHANGE $field $field DECIMAL($digits,$total_decimals) $field_is_null$field_default_value;");
					}
				}
			}
		} else {
			echo 'You need to be logged in as administrator to perform this action.';
		}
	}

	public function change_decimal_places($total_decimals)
	{
		$notChangableFields = ['estimated_hours'];

		if (is_admin()) {
			hooks()->do_action('before_change_decimal_places');

			$tables = $this->db->query("SELECT *
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='" . APP_DB_NAME . "'")->result_array();

			foreach ($tables as $table_data) {
				$table = $table_data['TABLE_NAME'];
				$fields = $this->db->list_fields($table);

				foreach ($fields as $field) {
					if (!in_array($field, $notChangableFields)) {
						$field_info = $this->db->query('SHOW FIELDS
                            FROM ' . $table . " where Field ='" . $field . "'")->result_array();
						$field_type = strtolower($field_info[0]['Type']);
						if (strpos($field_type, 'decimal') !== false) {
							$field_null = strtoupper($field_info[0]['Null']);
							if ($field_null == 'YES') {
								$field_is_null = 'NULL';
							} else {
								$field_is_null = 'NOT NULL';
							}
							if ($field_info[0]['Default'] == null) {
								$field_default_value = '';
							} else {
								$field_default_value = ' DEFAULT 0.' . str_repeat(0, $total_decimals);
							}
							$this->db->query("ALTER TABLE $table CHANGE $field $field DECIMAL(15,$total_decimals) $field_is_null$field_default_value;");
						}
					}
				}
			}
			echo '<p><strong>Table columns with decimal places updated successfully.</strong></p>';
		} else {
			echo 'You need to be logged in as administrator to perform this action.';
		}
	}

	public function convert_tables_to_innodb_engine()
	{
		if (is_admin()) {
			$databaseName = APP_DB_NAME;
			$tables = $this->db->query("SELECT TABLE_NAME,
                             ENGINE
                            FROM information_schema.TABLES
                            WHERE TABLE_SCHEMA = '$databaseName' and ENGINE = 'myISAM'")->result_array();

			foreach ($tables as $table) {
				$tableName = $table['TABLE_NAME'];
				$this->db->query("ALTER TABLE $tableName ENGINE=InnoDB;");
			}
			echo 'Table engines successfully changed to InnoDB';
		} else {
			echo 'You need to be logged in as administrator to perform this action.';
		}
	}

	/**
	 * The upgrade script for 232 does not perform the queries below for backward compatibility
	 * Mostly it changes the varchar maximum length because of InnoDB index
	 */
	public function upgrade_232_database()
	{
		$charset = $this->db->char_set;
		$collat = $this->db->dbcollat;

		if (!is_admin()) {
			die('You must be logged in as administrator to perform this action');
		}

		if (get_option('_232_upgrade_db_queries_performed') === '1') {
			die('This action is already processed');
		}

		$this->db->query('ALTER TABLE `' . db_prefix() . 'contacts` CHANGE `lastname` `lastname` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'contacts` CHANGE `firstname` `firstname` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'clients` CHANGE `company` `company` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'customers_groups` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'options` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'invoicepaymentrecords` CHANGE `paymentmethod` `paymentmethod` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'leads` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'leads` CHANGE `company` `company` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'projects` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');

		$this->db->query('ALTER TABLE `' . db_prefix() . 'contacts` CHANGE `title` `title` VARCHAR(100) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');

		$this->db->query('ALTER TABLE `' . db_prefix() . 'web_to_lead` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'vault` CHANGE `username` `username` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'vault` CHANGE `server_address` `server_address` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tracked_mails` CHANGE `subject` `subject` MEDIUMTEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets_predefined_replies` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets_pipe_log` CHANGE `email_to` `email_to` VARCHAR(100) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets_pipe_log` CHANGE `email` `email` VARCHAR(100) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets_pipe_log` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'subscriptions` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'staff` CHANGE `media_path_slug` `media_path_slug` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'proposals` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'proposals` CHANGE `proposal_to` `proposal_to` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'projectdiscussions` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'projectdiscussioncomments` CHANGE `fullname` `fullname` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'project_files` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'project_activity` CHANGE `description_key` `description_key` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . " NOT NULL COMMENT 'Language file key';");
		$this->db->query('ALTER TABLE `' . db_prefix() . 'notifications` CHANGE `additional_data` `additional_data` TEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'lead_activity_log` CHANGE `additional_data` `additional_data` TEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'knowledge_base_groups` CHANGE `group_slug` `group_slug` TEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'knowledge_base_groups` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');

		$this->db->query('ALTER TABLE `' . db_prefix() . 'files` CHANGE `file_name` `file_name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'expenses_categories` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'expenses` CHANGE `expense_name` `expense_name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'contracts` CHANGE `subject` `subject` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'contacts` CHANGE `profile_image` `profile_image` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'staff` CHANGE `profile_image` `profile_image` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');

		$this->db->query('ALTER TABLE `' . db_prefix() . 'clients` CHANGE `longitude` `longitude` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'clients` CHANGE `latitude` `latitude` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'announcements` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'projectdiscussioncomments` CHANGE `file_name` `file_name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'gdpr_requests` CHANGE `request_type` `request_type` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'user_meta` CHANGE `meta_key` `meta_key` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'tickets_pipe_log` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'mail_queue` CHANGE `email` `email` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'mail_queue` CHANGE `cc` `cc` TEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'mail_queue` CHANGE `bcc` `bcc` TEXT CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'project_files` CHANGE `file_name` `file_name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'ticket_attachments` CHANGE `file_name` `file_name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'milestones` CHANGE `name` `name` VARCHAR(191) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NOT NULL;');
		$this->db->query('ALTER TABLE `' . db_prefix() . 'leads` CHANGE `email` `email` VARCHAR(100) CHARACTER SET ' . $charset . ' COLLATE ' . $collat . ' NULL DEFAULT NULL;');
		add_option('_232_upgrade_db_queries_performed', '1', 0);
	}

	public function changeRowNew($table = '', $id = '')
	{
		if ($table != '' && $id != '') {
			$checkExists = get_table_where($table, array('id' => $id), '', 'row');
			if ($checkExists->status == 1) {
				$arrID = explode(',', $checkExists->not_new_by_staff);
				$checkArr = in_array(get_staff_user_id(), $arrID);
				if (!$checkArr) {
					if ($checkExists->not_new_by_staff) {
						$data = $checkExists->not_new_by_staff . ',' . get_staff_user_id();
					} else {
						$data = get_staff_user_id();
					}
					$this->db->set('not_new_by_staff', $data);
					$this->db->where('id', $id);
					$this->db->update($table);
				}
			}
		}
	}

	public function load_more_log()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$start = $data['dem_more'] * 10;
			$this->db->select('tblactivity_log_v2.*');
			$this->db->where('tblactivity_log_v2.type_parent_obj', $data['value']);
			$this->db->order_by('tblactivity_log_v2.id DESC');
			$this->db->limit(10, $start);
			$dataLog = $this->db->get('tblactivity_log_v2')->result_array();

			$dataResult = array();
			foreach ($dataLog as $key => $value) {
				$dataResult['dataMain'][$key]['id'] = $value['id'];
				$dataResult['dataMain'][$key]['img'] = staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small');
				$dataResult['dataMain'][$key]['name'] = get_staff_full_name($value['staff_id']);
				$dataResult['dataMain'][$key]['date'] = time_ago($value['date']);
				$dataResult['dataMain'][$key]['module'] = _l($value['table_obj']);
				$dataResult['dataMain'][$key]['content'] = $value['content'];
			}
			$dataResult['hide'] = false;
			if (count($dataLog) < 10) {
				$dataResult['hide'] = true;
			}
			echo json_encode($dataResult);
		}
	}

	public function load_more_log_by_search()
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if (isset($data['date']) && !empty($data['date'])) {
				$date = explode(' - ', $data['date']);
			}
			$this->db->select('tblactivity_log_v2.*');
			$this->db->where('tblactivity_log_v2.type_parent_obj', $data['module']);
			if (isset($data['date']) && !empty($data['date'])) {
				$this->db->where('tblactivity_log_v2.date >=', to_sql_date($date[0]));
				$this->db->where('tblactivity_log_v2.date <=', to_sql_date($date[1]));
			}
			if (isset($data['staff']) && !empty($data['staff'])) {
				$this->db->where('tblactivity_log_v2.staff_id =', $data['staff']);
			}
			$this->db->order_by('tblactivity_log_v2.id DESC');
			$dataLog = $this->db->get('tblactivity_log_v2')->result_array();

			$dataResult = array();
			foreach ($dataLog as $key => $value) {
				$dataResult['dataMain'][$key]['id'] = $value['id'];
				$dataResult['dataMain'][$key]['img'] = staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small');
				$dataResult['dataMain'][$key]['name'] = get_staff_full_name($value['staff_id']);
				$dataResult['dataMain'][$key]['date'] = time_ago($value['date']);
				$dataResult['dataMain'][$key]['module'] = _l($value['table_obj']);
				$dataResult['dataMain'][$key]['content'] = $value['content'];
			}
			echo json_encode($dataResult);
		}
	}


	/* Công bổ sung*/
	public function contact_email_toclient_exists()
	{
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				$contacts = $this->input->post('contacts');
				foreach ($contacts as $key => $value) {
					if ($value['email'] != '') {

						if (!empty($value['id'])) {
							$this->db->where('id', $value['id']);
							$_current_email = $this->db->get(db_prefix() . 'contacts')->row();
							if ($_current_email->email == $value['email']) {
								echo json_encode(true);
								die();
							}
						}
						$this->db->where('email', $value['email']);
						$total_rows = $this->db->count_all_results(db_prefix() . 'contacts');
						if ($total_rows > 0) {
							echo json_encode(false);
							die();
						}
					}
				}
				echo json_encode(true);
				die();
			}
		}
	}

	// hoàng crm bổ xung thông báo hêt hạn
	public function checkShowModal()
	{
		
		$current_url = explode('admin/', $_SERVER['HTTP_REFERER']);
		$name_url2 = explode('/', $current_url[1]);
		$name_url = array_pop($current_url);

		if (!get_option('notification_after_software') || !get_option('notification_before_software') || $name_url == 'software_extension' || $name_url2[0] == 'software_extension') {
			echo json_encode(0);
			die;
		} else {
			$getDateCheck = to_sql_date(get_option('expire_software'));
			if (!get_option('notification_before_software') || get_option('notification_before_software') == '') {
				$dateStart = date("Y-m-d");
			} else {
				$dateStart = date("Y-m-d", strtotime($getDateCheck . ' -' . get_option('notification_before_software') . ' day'));
			}

			if (!get_option('notification_after_software') || get_option('notification_after_software') == '') {
				$dateEnd = date("Y-m-d");
			} else {
				$dateEnd = date("Y-m-d", strtotime($getDateCheck . ' +' . get_option('notification_after_software') . ' day'));
			}
			//thông báo
			if (strtotime(date('Y-m-d')) >= strtotime($dateStart) && strtotime(date('Y-m-d')) < strtotime($dateEnd)) {
				$row = get_table_where('tblnotification_software', array(), '', 'row');
				if ($row) {
					$rowDate = get_table_where('tblnotification_software', array('date' => date('Y-m-d')), '', 'row');
					if ($rowDate) {
						$oldStr = $row->staff_seen;
						$arrStr = explode(",", $oldStr);
						if (in_array(get_staff_user_id(), $arrStr)) {
							echo json_encode(0);
							die;
						} else {
							echo json_encode(true);
							die;
						}
					} else {
						echo json_encode(true);
						die;
					}
				} else {
					echo json_encode(true);
					die;
				}
			} //bắt buột
			else if (strtotime(date('Y-m-d')) >= strtotime($dateEnd)) {
				echo json_encode(false);
				die;
			}
			echo json_encode(0);
			die;
		}
	}

	public function closeModal()
	{
		$row = get_table_where('tblnotification_software', array(), '', 'row');
		if (!$row) {
			$in = array(
				'date' => date('Y-m-d'),
				'staff_seen' => get_staff_user_id()
			);
			$this->db->insert('tblnotification_software', $in);
		} else {
			if (strtotime(date('Y-m-d')) == strtotime($row->date)) {
				$oldStr = $row->staff_seen;
				$arrStr = explode(",", $oldStr);
				if (!in_array(get_staff_user_id(), $arrStr)) {
					$oldStr .= ',' . get_staff_user_id();
					$this->db->set('staff_seen', $oldStr);
					$this->db->update('tblnotification_software');
				}
			} else {
				$this->db->set('date', date('Y-m-d'));
				$this->db->set('staff_seen', get_staff_user_id());
				$this->db->update('tblnotification_software');
			}
		}
	}
	// end


	//công bổ sung
	public function client_company_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('company') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('userid', $id);
					$_clients = $this->db->get('tblclients')->row();
					if ($_clients->company == $this->input->post('company')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('company', $this->input->post('company'));
				$total_rows = $this->db->count_all_results('tblclients');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function client_vat_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('vat') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('userid', $id);
					$_clients = $this->db->get('tblclients')->row();
					if ($_clients->vat == $this->input->post('vat')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('vat', $this->input->post('vat'));
				$total_rows = $this->db->count_all_results('tblclients');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function client_email_client_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('email_client') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('userid', $id);
					$_clients = $this->db->get('tblclients')->row();
					if ($_clients->email_client == $this->input->post('email_client')) {
						echo json_encode(true);
						die();
					}
				}

				$this->db->where('email_client', $this->input->post('email_client'));
				$total_rows = $this->db->count_all_results('tblclients');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function client_phonenumber_exists()
	{
		if ($this->input->is_ajax_request() && $this->input->post('phonenumber') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				if ($id != '') {
					$this->db->where('userid', $id);
					$_clients = $this->db->get('tblclients')->row();
					if ($_clients->phonenumber == $this->input->post('phonenumber')) {
						echo json_encode(true);
						die();
					}
				}

				$this->db->where('phonenumber', $this->input->post('phonenumber'));
				$total_rows = $this->db->count_all_results('tblclients');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	public function view_kpi_evaluation()
    {
        $filter_month = $this->input->post('filter_month');

        $data['filter'] = ['month'=>$filter_month];
        $this->load->view('admin/general/view_kpi_evaluation', $data);
    }
	//end công bổ sung
}
