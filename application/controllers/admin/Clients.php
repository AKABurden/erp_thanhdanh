<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Clients extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('leads_model');
        $this->load->model('products_model');
    }

    public function import_client()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `tbltemplate_import` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `setup_colums` text  NULL,
          `setup_contact` text  NULL,
          `create_by` int(11)  NOT NULL,
          `date_create` DATETIME NOT NULL,
          `type` varchar(250) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
        $data['title'] = _l('cong_import_data_client');
        $data['columsExcel'] = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        $data['country'] = get_table_where(db_prefix() . 'countries');
        $this->load->view('admin/import_excel/import_client', $data);
    }

//	public function count_all_client()
//	{
//		$checkTrue = true;
//		if (!has_permission('customers', '', 'view')) {
//			$checkTrue = false;
//		}
//		$data['client_status'] = get_table_where('tblstatus_client');
//		$this->db->select('COUNT(*) as count');
//		$data['all_clients'] = $this->db->get('tblclients')->row()->count;
//		foreach ($data['client_status'] as $key => $value) {
//			$this->db->select('COUNT(*) as count');
//			$this->db->where('client_status', $value['id']);
//			$data['client_status'][$key]['sum'] = $this->db->get('tblclients')->row()->count;
//		}
//		echo json_encode($data);
//	}

    public function count_all_client()
    {
        $arrBranch = get_branch_staff();
        $date_start_search = $this->input->post('date_start_search') ? to_sql_date($this->input->post('date_start_search')) . ' 00:00:00' : '';
        $end_start_search = $this->input->post('end_start_search') ? to_sql_date($this->input->post('date_start_search')) . ' 23:59:59' : '';

        $this->db->select('COUNT(DISTINCT (userid)) as count');
        if (!is_admin()) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('(tblclients.userid IN (
                        SELECT tbl_client_branch.client_id FROM tbl_client_branch WHERE tbl_client_branch.branch_id IN (' . $coverStrBranch . ')
                    )  
                )');
            } else {
                $this->db->where('tblclients.userid = 0');
            }
        }

        if (!empty($date_start_search)) {
            $this->db->where('tblclients.datecreated >=', $date_start_search);
        }

        if (!empty($end_start_search)) {
            $this->db->where('tblclients.datecreated <=', $end_start_search);
        }

        $data['sum_all'] = $this->db->get('tblclients')->row()->count;

        $data['groups'] = $this->db->get('tblcustomers_groups')->result_array();

        foreach ($data['groups'] as $key => $value) {
            $this->db->select('COUNT(DISTINCT (userid)) as count');
            $this->db->where('groupid', $value['id']);
            if (!is_admin()) {
                ;
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('(tblclients.userid IN (
                        SELECT tbl_client_branch.client_id FROM tbl_client_branch WHERE tbl_client_branch.branch_id IN (' . $coverStrBranch . ')
                    )  
                )');
                } else {
                    $this->db->where('tblclients.userid = 0');
                }
            }

            if (!empty($date_start_search)) {
                $this->db->where('tblclients.datecreated >=', $date_start_search);
            }

            if (!empty($end_start_search)) {
                $this->db->where('tblclients.datecreated <=', $end_start_search);
            }

            $this->db->join('tblclients', 'tblclients.userid = tblcustomer_groups.customer_id');
            $data['groups'][$key]['sum'] = $this->db->get('tblcustomer_groups')->row()->count;
        }


        $where_add = 'WHERE id > 0';
        $Group_customer = "(
                SELECT 
                    tblcustomer_groups.customer_id as customer_id
                FROM tblcustomer_groups
                $where_add
                ORDER BY id DESC
            )";
        $this->db->select('COUNT(userid) as count');
        $this->db->where('tblclients.userid NOT IN ' . $Group_customer);
        if (!is_admin()) {
            ;
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                $this->db->where('(tblclients.userid IN (
                        SELECT tbl_client_branch.client_id FROM tbl_client_branch WHERE tbl_client_branch.branch_id IN (' . $coverStrBranch . ')
                    )  
                )');
            } else {
                $this->db->where('tblclients.userid = 0');
            }
        }

        if (!empty($date_start_search)) {
            $this->db->where('tblclients.datecreated >=', $date_start_search);
        }

        if (!empty($end_start_search)) {
            $this->db->where('tblclients.datecreated <=', $end_start_search);
        }
        $data['not_all'] = $this->db->get('tblclients')->row()->count;
        echo json_encode($data);
    }

    public function index()
    {
        if (!has_permission('customers', '', 'view') && !has_permission('customers', '', 'view_own')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups'] = $this->clients_model->get_groups();
        $data['title'] = _l('clients');
        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();
        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();
        $data['customer_admins'] = $this->clients_model->get_customers_admin_unique_ids();
        $whereContactsLoggedIn = '';
        if (!has_permission('customers', '', 'view')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
        }
        $data['contacts_logged_in_today'] = $this->clients_model->get_contacts('',
            'last_login LIKE "' . date('Y-m-d') . '%"' . $whereContactsLoggedIn);
        $data['countries'] = $this->clients_model->get_clients_distinct_countries();
        $data['staff'] = get_table_where('tblstaff');
        //Hoàng CRM bổ xung
        $data['hidden_colum'] = get_table_where('tblhidden_colum_client', array('id_user' => get_staff_user_id()), '',
            'row');
        //end
        //Cong bố sung
//      $data['info_group'] = $this->clients_model->getInfoGroup();
        $this->db->order_by('id', 'desc');
        $data['info_group'] = $this->db->get('tblclient_info_detail')->result_array();
        //End công
        // Sum tab
        $data['status_client'] = get_table_where('tblstatus_client');
        // end sum
        $this->load->view('admin/clients/manage', $data);
    }

    public function table()
    {
        if (!has_permission('customers', '', 'view') && !has_permission('customers', '', 'view_own')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }
        $this->app->get_table_data('clients');
    }

    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('all_contacts');
        }
        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['title'] = _l('customer_contacts');
        $this->load->view('admin/clients/all_contacts', $data);
    }

    public function all_shipping()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('client_shipping');
        }
        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['title'] = _l('Địa chỉ giao hàng');
        $this->load->view('admin/clients/all_shipping', $data);
    }

    /* Edit client or add new client*/
    public function client($id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('customers', '', 'create')) {
                    access_denied('customers');
                }
                $data = $this->input->post();
                //format số
                $data['debt_limit'] = str_replace(',', '', $data['debt_limit']);
                $data['discount'] = str_replace(',', '', $data['discount']);
                $data['debt_limit_day'] = str_replace(',', '', $data['debt_limit_day']);
                $data['debt_begin'] = str_replace(',', '', $data['debt_begin']);
                $data['time_payment'] = number_unformat($data['time_payment']);
                if (!empty($data['date_renewal'])) {
                    $data['date_renewal'] = to_sql_date($data['date_renewal']);
                } else {
                    $data['date_renewal'] = null;
                }
                //end
                if (isset($data['VoE'])) {
                    unset($data['VoE']);
                }
                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $data['note'] = $this->input->post('note', false);
                $data['deadline_contract'] = !empty($data['deadline_contract']) ? to_sql_date($data['deadline_contract']) : null;
                $dataPostBranch = $data['branch'];
                unset($data['branch']);

                $data['allowed_vat'] = !empty($data['allowed_vat']) ? 1 : 0;

                $id = $this->clients_model->add($data);
                if (!has_permission('customers', '', 'view')) {
                    $assign['customer_admins'] = [];
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->clients_model->assign_admins($assign, $id);
                }
                if ($id) {
                    image_client_upload($id);
                    $get_code = get_table_where('tblclients', array('userid' => $id), '', 'row');
//					activity_log_v2('client', 'tblclients', $id, $get_code->company, 'Thêm mới khách hàng [' . $get_code->company . ']');
                    set_alert('success', _l('added_successfully', _l('client')));

                    $arrBranch = [];
                    if (!empty($dataPostBranch)) {
                        foreach ($dataPostBranch as $key => $value) {
                            $arrBranch [] = [
                                'branch_id' => $value,
                                'client_id' => $id,
                                'created_by' => get_staff_user_id(),
                                'date_created' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                    if (!empty($arrBranch)) {
                        $this->db->insert_batch('tbl_client_branch', $arrBranch);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'clients',
                        'table_obj' => 'tblclients',
                        'id_obj' => $id,
                        'name_obj' => $get_code->company,
                        'content' => lang('Thêm mới khách hàng') . ' [' . $get_code->company . ']',
                        'actions' => 'add'
                    ]);

                    if ($save_and_add_contact == false) {
                        redirect(admin_url('clients/client/' . $id . '?view'));
                    } else {
                        redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));
                    }
                }
            } else {
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }
                $data = $this->input->post();
                $data['debt_limit'] = str_replace(',', '', $data['debt_limit']);
                $data['discount'] = str_replace(',', '', $data['discount']);
                $data['debt_limit_day'] = str_replace(',', '', $data['debt_limit_day']);
                $data['debt_begin'] = str_replace(',', '', $data['debt_begin']);
                $data['time_payment'] = number_unformat($data['time_payment']);
                if (!empty($data['date_renewal'])) {
                    $data['date_renewal'] = to_sql_date($data['date_renewal']);
                } else {
                    $data['date_renewal'] = null;
                }
                if (isset($data['VoE'])) {
                    $VoE = $data['VoE'];
                    unset($data['VoE']);
                }
                $data['note'] = $this->input->post('note', false);
                $data['deadline_contract'] = !empty($data['deadline_contract']) ? to_sql_date($data['deadline_contract']) : null;
                $dataPostBranch = $data['branch'];
                unset($data['branch']);
                $data['allowed_vat'] = !empty($data['allowed_vat']) ? 1 : 0;
                $success = $this->clients_model->update($data, $id);
//                print_r('a');die();
                if ($success == true) {
                    image_client_upload($id);
                    $get_code = get_table_where('tblclients', array('userid' => $id), '', 'row');
//					activity_log_v2('client', 'tblclients', $id, $get_code->company, 'Cập nhật khách hàng [' . $get_code->company . ']');

                    $this->db->where('client_id', $id);
                    $this->db->delete('tbl_client_branch');
                    $arrBranch = [];
                    if (!empty($dataPostBranch)) {
                        foreach ($dataPostBranch as $key => $value) {
                            $arrBranch [] = [
                                'branch_id' => $value,
                                'client_id' => $id,
                                'created_by' => get_staff_user_id(),
                                'date_created' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                    if (!empty($arrBranch)) {
                        $this->db->insert_batch('tbl_client_branch', $arrBranch);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'clients',
                        'table_obj' => 'tblclients',
                        'id_obj' => $id,
                        'name_obj' => $get_code->company,
                        'content' => lang('Sửa khách hàng') . ' [' . $get_code->company . ']',
                        'actions' => 'edit'
                    ]);

                    set_alert('success', _l('updated_successfully', _l('client')));
                }
                if (isset($VoE)) {
                    redirect(admin_url('clients/client/' . $id . '?view'));
                } else {
                    redirect(admin_url('clients/client/' . $id));
                }
            }
        }
        $group = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;
        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
            redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));
        }
        // Customer groups
        $data['groups'] = $this->clients_model->get_groups();
        $data['type_client'] = get_table_where('tbltype_client');
        if ($id == '') {
            $title = _l('add_new', _l('client_lowercase'));
            //Công bổ sung
            $data['list_client'] = get_table_where('tblclients');
            $default_country = get_option('customer_default_country');
            $data['province'] = get_table_where('province', array('countries' => $default_country));
            $data['district'] = array();
            $data['ward'] = array();
            //end Công bổ sung
        } else {
            //Công bổ sung
            $data['list_client'] = get_table_where('tblclients', array('userid!=' => $id));
            //end Công bổ sung
            $client = $this->clients_model->get($id);
            if ($group == 'advisory_lead') {
                $data['log_advisory_lead'] = get_table_where(db_prefix() . 'log_advisory_lead', [
                    'id_lead' => $client->leadid,
                ], 'date_create desc');
            }
            if ($group == 'care_of_clients') {
                $data['log_care_of_client'] = get_table_where(db_prefix() . 'log_care_of', [
                    'id_client' => $client->userid,
                ], 'date_create desc');
            }
            $data['customer_tabs'] = get_customer_profile_tabs();
            if (!$client) {
                show_404();
            }
            $data['contacts'] = $this->clients_model->get_contacts($id);
            $data['tab'] = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;
            if (!$data['tab']) {
                show_404();
            }
            // Fetch data based on groups
            if ($group == 'profile') {
                $data['customer_groups'] = $this->clients_model->get_customer_groups($id);
                $data['customer_admins'] = $this->clients_model->get_admins($id);
            } elseif ($group == 'attachments') {
                $data['attachments'] = get_all_customer_attachments($id);
            } elseif ($group == 'vault') {
                $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility',
                    $this->clients_model->get_vault_entries($id));
                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = [];
                }
            } elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'invoices') {
                $this->load->model('invoices_model');
                $data['invoice_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($group == 'credit_notes') {
                $this->load->model('credit_notes_model');
                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } elseif ($group == 'statement') {
                if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
                    set_alert('danger', _l('access_denied'));
                    redirect(admin_url('clients/client/' . $id));
                }
                $data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));
            } elseif ($group == 'map') {
                if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {
                    $this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js',
                            'map.js')) . '?v=' . $this->app_css->core_version());
                    $this->app_scripts->add('google-maps-api-js', [
                        'path' => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',
                        'attributes' => [
                            'async',
                            'defer',
                            'latitude' => "$client->latitude",
                            'longitude' => "$client->longitude",
                            'mapMarkerTitle' => "$client->company",
                        ],
                    ]);
                }
            }
            $data['staff'] = $this->staff_model->get('', ['active' => 1]);
            $data['client'] = $client;
            $title = $client->company;
            //Công bổ sung
            //Lấy tỉnh thành phố
            $data['province'] = get_table_where('province', array('countries' => $client->country));
            $data['district'] = get_table_where('district', array('provinceid' => $client->city));
            $data['ward'] = get_table_where('ward', array('districtid' => $client->district));
            //End công bổ sung
            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            $data['branch_client'] = get_table_where('tbl_client_branch', array('client_id' => $client->userid));
            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['dtVat'] = get_table_where('tbltaxes');
        if ($id != '') {
            $customer_currency = $data['client']->default_currency;
            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;
                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;
                        break;
                    }
                }
            }
            if (is_array($customer_currency)) {
                $customer_currency = (object)$customer_currency;
            }
            $data['customer_currency'] = $customer_currency;
            $slug_zip_folder = (
            $client->company != ''
                ? $client->company
                : get_contact_full_name(get_primary_contact_user_id($client->userid))
            );
            $data['zip_in_folder'] = slug_it($slug_zip_folder);
        }
        $data['view'] = false;
        if ($this->input->get()) {
            $id_customer = $id;
            $this->db->select('tblclients.*, tbltype_client.name as nameType_client, tblcombobox_client.name as nameMarriage, tblcountries.short_name as short_name_countries, tblprovince.name as name_province, tbldistrict.name as name_district, tblward.name as name_ward, tblleads_sources.name as name_sources, tblcurrencies.name as name_currencies');
            $this->db->from('tblclients');
            $this->db->join('tbltype_client', 'tbltype_client.id = tblclients.type_client', 'left');
            $this->db->join('tblcombobox_client', 'tblcombobox_client.id = tblclients.marriage', 'left');
            $this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
            $this->db->join('tblprovince', 'tblprovince.provinceid = tblclients.city', 'left');
            $this->db->join('tbldistrict', 'tbldistrict.districtid = tblclients.district', 'left');
            $this->db->join('tblward', 'tblward.wardid = tblclients.ward', 'left');
            $this->db->join('tblleads_sources', 'tblleads_sources.id = tblclients.sources', 'left');
            $this->db->join('tblcurrencies', 'tblcurrencies.id = tblclients.default_currency', 'left');
            $this->db->where('userid', $id_customer);
            $data['dataView'] = $this->db->get()->row();
            $this->db->select('tblcustomer_groups.*, tblcustomers_groups.name as name_groups');
            $this->db->from('tblcustomer_groups');
            $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblcustomer_groups.groupid', 'left');
            $this->db->where('tblcustomer_groups.customer_id', $id_customer);
            $data['dataGroup'] = $this->db->get()->result_array();
            $data['view'] = true;
        }
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title'] = $title;
        //Công bổ sung
        $data['sources'] = get_table_where('tblleads_sources');
        $data['info_group'] = $this->clients_model->getInfoGroup($id);
        $data['dt'] = get_table_where(db_prefix() . 'combobox_client', ['type' => 'dt']);
        $data['kt'] = get_table_where(db_prefix() . 'combobox_client', ['type' => 'kt']);
        $data['marriage'] = get_table_where(db_prefix() . 'combobox_client', ['type' => 'marriage']);
        $data['religion'] = get_table_where(db_prefix() . 'combobox_client', ['type' => 'religion']);
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblclients', 'id_obj' => $id),
            'id DESC');
        $data['table_price_id'] = get_table_where('tbl_set_prices', array('status' => 1));
        $data['listDiscount'] = $this->site_model->getDiscount();
        //end Công bổ sung
        $data['branch'] = get_table_where('tblbranch');
        $this->load->view('admin/clients/client', $data);
    }

    public function export($contact_id)
    {
        if (is_admin()) {
            $this->load->library('gdpr/gdpr_contact');
            $this->gdpr_contact->export($contact_id);
        }
    }

    public function upstatus_mark_as($status = '', $userid = '')
    {
        if (!empty($userid)) {
            $this->db->where('userid', $userid);
            if ($this->db->update('tblclients', array('status_clients' => $status))) {
                echo json_encode([
                    'success' => true,
                    'message' => _l('cap nhat thanh cong'),
                ]);
                die;
            }
        }
        echo json_encode([
            'success' => true,
            'message' => _l('cap nhat ko thanh cong'),
        ]);
        die;
    }

    public function status_client()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('status_client');
        }
        $data['title'] = _l('sum_status_customer');
        $this->load->view('admin/clients/status_client', $data);
    }

    public function delete_status_client($id = '')
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            if ($this->db->delete('tblstatus_client')) {
                echo json_encode([
                    'success' => true,
                    'message' => _l('sum_delete_susscess'),
                ]);
                die;
            }
        }
        echo json_encode([
            'success' => true,
            'message' => _l('sum_delete_fail'),
        ]);
        die;
    }

    public function add_status_client()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['id'] == "") {
                $_data['code'] = $data['code'];
                //Kiểm tra code tồn tại chưa
                $check_code = get_table_where('tblstatus_client', array('code' => $data['code']), '', 'row');
                if (!empty($check_code)) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('Mã phân loại đã tồn tại'),
                    ]);
                    die;
                }
                //end

                $_data['name'] = $data['name'];
                $_data['color'] = $data['color'];
                $this->db->insert('tblstatus_client', $_data);
                $id = $this->db->insert_id();
                if (!empty($id)) {
                    echo json_encode([
                        'success' => true,
                        'message' => _l('them thanh cong'),
                    ]);
                    die;
                }
            } else {
                $_data['code'] = $data['code'];
                //Kiểm tra code tồn tại chưa
                $check_code = get_table_where('tblstatus_client', array('code' => $data['code'], 'id !=' => $data['id']), '', 'row');
                if (!empty($check_code)) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('Mã phân loại đã tồn tại'),
                    ]);
                    die;
                }
                //end

                $_data['name'] = $data['name'];
                $_data['color'] = $data['color'];
                $this->db->where('id', $data['id']);
                if ($this->db->update('tblstatus_client', $_data)) {
                    echo json_encode([
                        'success' => true,
                        'message' => _l('sua thanh cong'),
                    ]);
                    die;
                }
            }
        }
    }

    // Used to give a tip to the user if the company exists when new company is created
    public function check_duplicate_customer_name()
    {
        if (has_permission('customers', '', 'create')) {
            $companyName = trim($this->input->post('company'));
            $response = [
                'exists' => (bool)total_rows(db_prefix() . 'clients', ['company' => $companyName]) > 0,
                'message' => _l('company_exists_info', '<b>' . $companyName . '</b>'),
            ];
            echo json_encode($response);
        }
    }

    public function save_longitude_and_latitude($client_id)
    {
        if (!has_permission('customers', '', 'edit')) {
            if (!is_customer_admin($client_id)) {
                ajax_access_denied();
            }
        }
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', [
            'longitude' => $this->input->post('longitude'),
            'latitude' => $this->input->post('latitude'),
        ]);
        if ($this->db->affected_rows() > 0) {
            echo 'success';
        } else {
            echo 'false';
        }
    }

    public function form_contact($customer_id, $contact_id = '')
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid'] = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['password'] = $this->input->post('password', false);
            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('customers', '', 'create')) {
                    if (!is_customer_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                        die;
                    }
                }
                $id = $this->clients_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                    handle_contact_profile_image_upload($id);
                    $get_code = get_table_where('tblclients', array('userid' => $customer_id), '', 'row');
                    activity_log_v2('client', 'tblclients', $customer_id, $get_code->company,
                        'Cập nhật người liên hệ khách hàng [' . $get_code->company . ']');
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts',
                        ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                    'is_individual' => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'contacts',
                            ['userid' => $customer_id]) == 1,
                ]);
                die;
            }
            if (!has_permission('customers', '', 'edit')) {
                if (!is_customer_admin($customer_id)) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
            }
            $original_contact = $this->clients_model->get_contact($contact_id);
            $success = $this->clients_model->update_contact($data, $contact_id);
            $message = '';
            $proposal_warning = false;
            $original_email = '';
            $updated = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }
            if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                $message = _l('updated_successfully', _l('contact'));
                $success = true;
            }
            if ($updated == true) {
                $get_code = get_table_where('tblclients', array('userid' => $customer_id), '', 'row');
                activity_log_v2('client', 'tblclients', $customer_id, $get_code->company,
                    'Cập nhật người liên hệ khách hàng [' . $get_code->company . ']');
                $contact = $this->clients_model->get_contact($contact_id);
                if (total_rows(db_prefix() . 'proposals', [
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email,
                    ]) > 0 && ($original_contact->email != $contact->email)) {
                    $proposal_warning = true;
                    $original_email = $original_contact->email;
                }
            }
            echo json_encode([
                'success' => $success,
                'proposal_warning' => $proposal_warning,
                'message' => $message,
                'original_email' => $original_email,
                'has_primary_contact' => (total_rows(db_prefix() . 'contacts',
                    ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
            ]);
            die;
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->clients_model->get_contact($contact_id);
            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }
        $data['customer_permissions'] = get_contact_permissions();
        $data['title'] = $title;
        $this->load->view('admin/clients/modals/contact', $data);
    }

    public function confirm_registration($client_id)
    {
        if (!is_admin()) {
            access_denied('Customer Confirm Registration, ID: ' . $client_id);
        }
        $this->clients_model->confirm_registration($client_id);
        set_alert('success', _l('customer_registration_successfully_confirmed'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_file_share_visibility()
    {
        if ($this->input->post()) {
            $file_id = $this->input->post('file_id');
            $share_contacts_id = [];
            if ($this->input->post('share_contacts_id')) {
                $share_contacts_id = $this->input->post('share_contacts_id');
            }
            $this->db->where('file_id', $file_id);
            $this->db->delete(db_prefix() . 'shared_customer_files');
            foreach ($share_contacts_id as $share_contact_id) {
                $this->db->insert(db_prefix() . 'shared_customer_files', [
                    'file_id' => $file_id,
                    'contact_id' => $share_contact_id,
                ]);
            }
        }
    }

    public function delete_contact_profile_image($contact_id)
    {
        hooks()->do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . $contact_id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $contact_id);
        }
        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', [
            'profile_image' => null,
        ]);
    }

    public function mark_as_active($id)
    {
        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', [
            'active' => 1,
        ]);
        redirect(admin_url('clients/client/' . $id));
    }

    public function consents($id)
    {
        if (!has_permission('customers', '', 'view')) {
            if (!is_customer_admin(get_user_id_by_contact_id($id))) {
                echo _l('access_denied');
                die;
            }
        }
        $this->load->model('gdpr_model');
        $data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'contact');
        $data['consents'] = $this->gdpr_model->get_consents(['contact_id' => $id]);
        $data['contact_id'] = $id;
        $this->load->view('admin/gdpr/contact_consent', $data);
    }

    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {
        $success = false;
        $email = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');
            $this->db->select('email,userid');
            $this->db->where('id', $contact_id);
            $contact = $this->db->get(db_prefix() . 'contacts')->row();
            $proposals = $this->proposals_model->get('', [
                'rel_type' => 'customer',
                'rel_id' => $contact->userid,
                'email' => $this->input->post('original_email'),
            ]);
            $affected_rows = 0;
            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update(db_prefix() . 'proposals', [
                    'email' => $contact->email,
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }
            if ($affected_rows > 0) {
                $success = true;
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => _l('proposals_emails_updated', [
                _l('contact_lowercase'),
                $contact->email,
            ]),
        ]);
    }

    public function assign_admins($id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }
        $success = $this->clients_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
            $get_code = get_table_where('tblclients', array('userid' => $id), '', 'row');
            activity_log_v2('client', 'tblclients', $id, $get_code->company,
                'Cập nhật người quản lý khách hàng [' . $get_code->company . ']');
            set_alert('success', _l('updated_successfully', _l('client')));
        }
//        redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));
        redirect(admin_url('clients/client/' . $id . '?view'));
    }

    public function delete_customer_admin($customer_id, $staff_id)
    {
        if (!has_permission('customers', '', 'create') && !has_permission('customers', '', 'edit')) {
            access_denied('customers');
        }
        $get_code = get_table_where('tblclients', array('userid' => $customer_id), '', 'row');
        activity_log_v2('client', 'tblclients', $customer_id, $get_code->company,
            'Xóa người quản lý khách hàng [' . $get_code->company . ']');
        $this->db->where('customer_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'customer_admins');
//        redirect(admin_url('clients/client/' . $customer_id) . '?tab=customer_admins');
        redirect(admin_url('clients/client/' . $customer_id) . '?view');
    }

    public function delete_contact($customer_id, $id)
    {
        if (!has_permission('customers', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('customers');
            }
        }
        $contact = $this->clients_model->get_contact($id);
        $hasProposals = false;
        if ($contact && is_gdpr()) {
            if (total_rows(db_prefix() . 'proposals', ['email' => $contact->email]) > 0) {
                $hasProposals = true;
            }
        }
        $get_code = get_table_where('tblclients', array('userid' => $customer_id), '', 'row');
        activity_log_v2('client', 'tblclients', $customer_id, $get_code->company,
            'Xóa người liên hệ khách hàng [' . $get_code->company . ']');
        $this->clients_model->delete_contact($id);
        if ($hasProposals) {
            $this->session->set_flashdata('gdpr_delete_warning', true);
        }
        redirect(admin_url('clients/client/' . $customer_id . '?group=contacts'));
    }

    public function contacts($client_id)
    {
        $this->app->get_table_data('contacts', [
            'client_id' => $client_id,
        ]);
    }

    public function upload_attachment($id)
    {
        handle_client_attachments_upload($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer',
                $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('customers', '', 'delete') || is_customer_admin($customer_id)) {
            $this->clients_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('customers', '', 'delete')) {
            acction_delete_ajax(1);
        }
        if (!$id) {
            redirect(admin_url('clients'));
        }
        $dtData = get_table_where('tblclients', array('userid' => $id), '', 'row_array');
		$message = [];
		$orders = $this->db->get_where('tbl_orders', ['customer_id' => $id])->num_rows();
		if (!empty($orders)) {
            $message[] = _l('cong_isset_list') . ' Đơn hàng';
        }
		$quotes = $this->db->get_where('tbl_quotes', ['customer_id' => $id])->num_rows();
		if (!empty($quotes)) {
            $message[] = _l('cong_isset_list') . ' Báo giá';
        }
		$deliveries = $this->db->get_where('tbl_deliveries', ['customer_id' => $id])->num_rows();
		if (!empty($deliveries)) {
            $message[] = _l('cong_isset_list') . ' Giao hàng';
        }
		$export_different = $this->db->get_where('tblexport_different',
            ['object' => 1, 'id_object' => $id])->num_rows();
		if (!empty($export_different)) {
            $message[] = _l('cong_isset_list') . ' Xuất khi khác';
        }
		$other_payslips_coupon = $this->db->get_where('tblother_payslips_coupon',
            ['objects' => 1, 'objects_id' => $id])->num_rows();
		if (!empty($other_payslips_coupon)) {
            $message[] = _l('cong_isset_list') . ' Thu khác';
        }
		$other_payslips = $this->db->get_where('tblother_payslips', ['objects' => 1, 'objects_id' => $id])->num_rows();
		if (!empty($other_payslips)) {
            $message[] = _l('cong_isset_list') . ' Chi khác';
        }
		if (!empty($message)) {
            echo json_encode([
                'alert_type' => 'danger',
                'message' => implode(',', $message) . ' Nên không thể xóa',
                'success' => false
            ]);
            die();
        }
		$Kt_Connect = getConnectClient($id);
		if (empty($Kt_Connect)) {
            $response = $this->clients_model->delete($id);
            if ($response) {
                $this->db->where('client_id', $id);
                $this->db->delete('tbl_client_branch');

                insertActivityLog([
                    'type_parent_obj' => 'clients',
                    'table_obj' => 'tblclients',
                    'id_obj' => $id,
                    'name_obj' => $dtData['company'],
                    'content' => lang('Xóa khách hàng') . ' [' . $dtData['company']. ']',
                    'actions' => 'delete'
                ]);
                echo json_encode([
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true'),
                    'success' => true
                ]);
                die();
            }
            echo json_encode([
                'alert_type' => 'danger',
                'message' => _l('cong_delete_false'),
                'success' => false
            ]);
            die();
        } else {
            echo json_encode([
                'alert_type' => 'danger',
                'message' => _l('cong_delete_false'),
                'ktConnect' => $Kt_Connect,
                'success' => false
            ]);
            die();
        }
	}

    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            login_as_client($id);
        }
        hooks()->do_action('after_contact_login');
        redirect(site_url());
    }

    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('customers', '', 'edit') || is_customer_admin(get_user_id_by_contact_id($id))) {
            if ($this->input->is_ajax_request()) {
                $this->clients_model->change_contact_status($id, $status);
            }
        }
    }

    /* Change client status / active / inactive */
    public function change_client_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->clients_model->change_client_status($id, $status);
        }
    }

    /* Zip function for credit notes */
    public function zip_credit_notes($id)
    {
        $has_permission_view = has_permission('credit_notes', '', 'view');
        if (!$has_permission_view && !has_permission('credit_notes', '', 'view_own')) {
            access_denied('Zip Customer Credit Notes');
        }
        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type' => 'credit_notes',
                'status' => $this->input->post('credit_note_zip_status'),
                'date_from' => $this->input->post('zip-from'),
                'date_to' => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=credit_notes'),
            ]);
            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_invoices($id)
    {
        $has_permission_view = has_permission('invoices', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Invoices');
        }
        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type' => 'invoices',
                'status' => $this->input->post('invoice_zip_status'),
                'date_from' => $this->input->post('zip-from'),
                'date_to' => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=invoices'),
            ]);
            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    /* Since version 1.0.2 zip client estimates */
    public function zip_estimates($id)
    {
        $has_permission_view = has_permission('estimates', '', 'view');
        if (!$has_permission_view && !has_permission('estimates', '', 'view_own')
            && get_option('allow_staff_view_estimates_assigned') == '0') {
            access_denied('Zip Customer Estimates');
        }
        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type' => 'estimates',
                'status' => $this->input->post('estimate_zip_status'),
                'date_from' => $this->input->post('zip-from'),
                'date_to' => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=estimates'),
            ]);
            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_payments($id)
    {
        $has_permission_view = has_permission('payments', '', 'view');
        if (!$has_permission_view && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Payments');
        }
        $this->load->library('app_bulk_pdf_export', [
            'export_type' => 'payments',
            'payment_mode' => $this->input->post('paymentmode'),
            'date_from' => $this->input->post('zip-from'),
            'date_to' => $this->input->post('zip-to'),
            'redirect_on_error' => admin_url('clients/client/' . $id . '?group=payments'),
        ]);
        $this->app_bulk_pdf_export->set_client_id($id);
        $this->app_bulk_pdf_export->set_client_id_column(db_prefix() . 'clients.userid');
        $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
        $this->app_bulk_pdf_export->export();
    }

    public function import()
    {
        if (!has_permission('customers', '', 'create')) {
            access_denied('customers');
        }
        $dbFields = $this->db->list_fields(db_prefix() . 'contacts');
        foreach ($dbFields as $key => $contactField) {
            if ($contactField == 'phonenumber') {
                $dbFields[$key] = 'contact_phonenumber';
            }
        }
        $dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix() . 'clients'));
        $this->load->library('import/import_customers', [], 'import');
        $this->import->setDatabaseFields($dbFields)
            ->setCustomFields(get_custom_fields('customers'));
        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }
        if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();
            $data['total_rows_post'] = $this->import->totalRows();
            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }
        $data['groups'] = $this->clients_model->get_groups();
        $data['title'] = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/import', $data);
    }

    public function groups()
    {
        if (!is_admin()) {
            access_denied('Customer Groups');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customers_groups');
        }
        $this->app_scripts->theme('colorpicker-js',
            'assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js');
        $data['colorpicker-js'] = true;
        $data['title'] = _l('customer_groups');

        $data['parent_group'] = $this->db->get_where('tblcustomers_groups', ['id_parent' => 0])->result_array();
        $this->load->view('admin/clients/groups_manage', $data);
    }

    public function group()
    {
        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {
            access_denied('Customer Groups');
        }
        $data = $this->input->post();
        if (empty($data['id_parent'])) {
            $data['id_parent'] = 0;
        }
        if (empty($data['id'])) {
            $id = $this->clients_model->add_group($data);
            $get_code = get_table_where('tblcustomers_groups', array('id' => $id), '', 'row');
            activity_log_v2('client', 'tblcustomers_groups', $id, $get_code->name,
                'Thêm mới nhóm khách hàng [' . $get_code->name . ']');
            $message = $id ? _l('added_successfully', _l('customer_group')) : '';
            $data_group = [];
            if (empty($data['id_parent'])) {
                $data_group = $this->db->get_where('tblcustomers_groups', ['id' => $id])->row();
            }
            echo json_encode([
                'success' => $id ? true : false,
                'message' => $message,
                'id' => $id,
                'name' => $data['name'],
                'data_group' => $data_group,
            ]);
            die();
        } else {
            $success = $this->clients_model->edit_group($data);
            $message = '';
            if ($success == true) {
                $get_code = get_table_where('tblcustomers_groups', array('id' => $data['id']), '', 'row');
                activity_log_v2('client', 'tblcustomers_groups', $data['id'], $get_code->name,
                    'Cập nhật nhóm khách hàng [' . $get_code->name . ']');
                $message = _l('updated_successfully', _l('customer_group'));
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
        }
    }

    public function delete_group($id)
    {
        if (!is_admin()) {
            access_denied('Delete Customer Group');
        }
        if (!$id) {
            redirect(admin_url('clients/groups'));
        }
        $get_code = get_table_where('tblcustomers_groups', array('id' => $id), '', 'row');
        activity_log_v2('client', 'tblcustomers_groups', $id, $get_code->name,
            'Xóa nhóm khách hàng [' . $get_code->name . ']');
        $response = $this->clients_model->delete_group($id);
        if ($response == true) {
            $this->db->where('id_parent', $id);
            $this->db->delete('tblcustomers_groups');
            set_alert('success', _l('deleted', _l('customer_group')));
        } else {
            set_alert('warning', _l('cong_delete_false'));
        }
        redirect(admin_url('clients/groups'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $groups = $this->input->post('groups');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->clients_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {
                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        $this->client_groups_model->sync_customer_groups($id, $groups);
                    }
                }
            }
        }
        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }

    public function vault_entry_create($customer_id)
    {
        $data = $this->input->post();
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }
        unset($data['id']);
        $data['creator'] = get_staff_user_id();
        $data['creator_name'] = get_staff_full_name($data['creator']);
        $data['description'] = nl2br($data['description']);
        $data['password'] = $this->encryption->encrypt($this->input->post('password', false));
        if (empty($data['port'])) {
            unset($data['port']);
        }
        $this->clients_model->vault_entry_create($data, $customer_id);
        set_alert('success', _l('added_successfully', _l('vault_entry')));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_update($entry_id)
    {
        $entry = $this->clients_model->get_vault_entry($entry_id);
        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $data = $this->input->post();
            if (isset($data['fakeusernameremembered'])) {
                unset($data['fakeusernameremembered']);
            }
            if (isset($data['fakepasswordremembered'])) {
                unset($data['fakepasswordremembered']);
            }
            $data['last_updated_from'] = get_staff_full_name(get_staff_user_id());
            $data['description'] = nl2br($data['description']);
            if (!empty($data['password'])) {
                $data['password'] = $this->encryption->encrypt($this->input->post('password', false));
            } else {
                unset($data['password']);
            }
            if (empty($data['port'])) {
                unset($data['port']);
            }
            $this->clients_model->vault_entry_update($entry_id, $data);
            set_alert('success', _l('updated_successfully', _l('vault_entry')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_delete($id)
    {
        $entry = $this->clients_model->get_vault_entry($id);
        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $this->clients_model->vault_entry_delete($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function vault_encrypt_password()
    {
        $id = $this->input->post('id');
        $user_password = $this->input->post('user_password', false);
        $user = $this->staff_model->get(get_staff_user_id());
        if (!app_hasher()->CheckPassword($user_password, $user->password)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error_msg' => _l('vault_password_user_not_correct')]);
            die;
        }
        $vault = $this->clients_model->get_vault_entry($id);
        $password = $this->encryption->decrypt($vault->password);
        $password = html_escape($password);
        // Failed to decrypt
        if (!$password) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(['error_msg' => _l('failed_to_decrypt_password')]);
            die;
        }
        echo json_encode(['password' => $password]);
    }

    public function get_vault_entry($id)
    {
        $entry = $this->clients_model->get_vault_entry($id);
        unset($entry->password);
        $entry->description = clear_textarea_breaks($entry->description);
        echo json_encode($entry);
    }

    public function statement_pdf()
    {
        $customer_id = $this->input->get('customer_id');
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/' . $customer_id));
        }
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        try {
            $pdf = statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }
        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it(_l('customer_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);
    }

    public function send_statement()
    {
        $customer_id = $this->input->get('customer_id');
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/' . $customer_id));
        }
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $send_to = $this->input->post('send_to');
        $cc = $this->input->post('cc');
        $success = $this->clients_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('statement_sent_to_client_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_client_fail'));
        }
        redirect(admin_url('clients/client/' . $customer_id . '?group=statement'));
    }

    public function statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        $data['from'] = $from;
        $data['to'] = $to;
        $viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);
        echo json_encode($viewData);
    }

    // công bổ sung
    public function type_client()
    {
        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {
            access_denied('Customer Type Client');
        }
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $create_by = get_staff_user_id();
                $this->db->insert('tbltype_client',
                    array('name' => $data['name'], 'create_by' => $create_by, 'date_create' => date('Y-m-d H:i:s')));
                $id = $this->db->insert_id();
                $message = $id ? _l('added_successfully', _l('cong_type_client')) : '';
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $message,
                    'id' => $id,
                    'name' => $data['name'],
                ]);
                die();
            } else {
                $this->db->where('id', $data['id']);
                $success = $this->db->update('tbltype_client', array('name' => $data['name']));
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfully', _l('cong_type_client'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
                die();
            }
        }
    }

    public function table_shipping($userid = "")
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('client_shipping', array('userid' => $userid));
        }
    }

    //Lấy tỉnh thành/ thành phố từ quốc gia
    public function get_province()
    {
        if ($this->input->post()) {
            $id_country = $this->input->post('id_country');
            if (!empty($id_country)) {
                $this->db->where('countries', $id_country);
                $province = $this->db->get('tblprovince')->result_array();
                echo json_encode($province);
                die();
            }
        }
        echo json_encode(array());
        die();
    }

    public function get_district()
    {
        if ($this->input->post()) {
            $id_province = $this->input->post('id_province');
            if (!empty($id_province)) {
                $this->db->where('provinceid', $id_province);
                $district = $this->db->get('tbldistrict')->result_array();
                echo json_encode($district);
                die();
            }
        }
        echo json_encode(array());
        die();
    }

    public function get_ward()
    {
        if ($this->input->post()) {
            $id_district = $this->input->post('id_district');
            if (!empty($id_district)) {
                $this->db->where('districtid', $id_district);
                $ward = $this->db->get(db_prefix() . 'ward')->result_array();
                echo json_encode($ward);
                die();
            }
        }
        echo json_encode(array());
        die();
    }

    public function GetShippingClient($id = "")
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            $shipping_client = $this->db->get(db_prefix() . 'shipping_client')->row();
            echo json_encode($shipping_client);
            die();
        }
        echo json_encode(array());
        die();
    }

    public function shipping_client()
    {
        $data = $this->input->post();
        if (!empty($data)) {
            if (!empty($data['id'])) {
                $id = $data['id'];
                unset($data['id']);
                unset($data['client']);
                if (empty($data['address_primary'])) {
                    $data['address_primary'] = 4;
                }
                $this->db->where('id', $id);
                if ($this->db->update('tblshipping_client', $data)) {
                    if (!empty($data['client'])) {
                        $get_code = get_table_where('tblclients', array('userid' => $data['client']), '', 'row');
                        activity_log_v2('client', 'tblclients', $data['client'], $get_code->company,
                            'Cập nhật thông tin giao hàng khách hàng [' . $get_code->company . ']');
                    }
                    echo json_encode(array(
                        'alert_type' => 'success',
                        'success' => true,
                        'message' => _l('true_update_shipping')
                    ));
                    die();
                }
                echo json_encode(array(
                    'alert_type' => 'danger',
                    'success' => false,
                    'message' => _l('false_update_shipping')
                ));
                die();
            } else {
                unset($data['id']);
                $data['create_by'] = get_staff_user_id();
                $data['date_create'] = date('Y-m-d H:i:s');
                if ($this->db->insert('tblshipping_client', $data)) {
                    $id = $this->db->insert_id();
                    $shipping = get_table_where('tblshipping_client', ['id' => $id], '', 'row');
                    $get_code = get_table_where('tblclients', array('userid' => $data['client']), '', 'row');
                    activity_log_v2('client', 'tblclients', $data['client'], $get_code->company,
                        'Thêm mới thông tin giao hàng khách hàng [' . $get_code->company . ']');
                    echo json_encode(array(
                        'alert_type' => 'success',
                        'success' => true,
                        'message' => _l('true_add_shipping'),
                        'shipping' => $shipping
                    ));
                    die();
                }
                echo json_encode(array(
                    'alert_type' => 'danger',
                    'success' => false,
                    'message' => _l('false_add_shipping')
                ));
                die();
            }
        }
        echo json_encode(array('alert_type' => 'danger', 'success' => false, 'message' => _l('false_data_shipping')));
        die();
    }

    /*
     *
     * Chăm sóc khách hàng
     *
     */
    public function care_ofs()
    {
        $data['title'] = 'Sinh nhật khách hàng';
        $data['template'] = get_table_where('tblemailtemplates', array('type' => 'care_ofs'));
        $this->load->view('admin/clients/care_ofs/manage', $data);
    }

    public function table_care_ofs()
    {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                ajax_access_denied();
            }
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('care_ofs');
        }
    }

    public function send_toast_email()
    {
        if (!empty($this->input->post())) {
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            if (!empty($data['id'])) {
                $list_field = [
                    'lastname' => 'company',
                    'birtday' => 'birtday',
                    'email' => 'email',
                    'phonenumber' => 'phonenumber'
                ];
                $this->db->where('id', $data['id']);
                $contacts = $this->db->get(db_prefix() . 'contacts')->row();
                if (!empty($contacts)) {
                    foreach ($list_field as $key => $value) {
                        $data['content'] = str_replace('{' . $value . '}', $contacts->{$key}, $data['content']);
                    }
                }
            }
            if (!empty($data['userid'])) {
                $list_field = [
                    'company' => 'company',
                    'birtday' => 'birtday',
                    'email_client' => 'email',
                    'phonenumber' => 'phonenumber'
                ];
                $this->db->where('userid', $data['userid']);
                $client = $this->db->get(db_prefix() . 'clients')->row();
                if (!empty($client)) {
                    foreach ($list_field as $key => $value) {
                        $data['content'] = str_replace('{' . $value . '}', $client->{$key}, $data['content']);
                    }
                }
            }
            $this->load->config('email');
            $this->email->initialize();
            if (get_option('mail_engine') == 'phpmailer') {
                $this->email->set_debug_output(function ($err) {
                    if (!isset($GLOBALS['debug'])) {
                        $GLOBALS['debug'] = '';
                    }
                    $GLOBALS['debug'] .= $err . '<br />';
                    echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => $err));
                    die();
                });
                $this->email->set_smtp_debug(3);
            }
            $company = get_option('companyname');
            $this->email->set_newline(config_item('newline'));
            $this->email->set_crlf(config_item('crlf'));
            $this->email->from(get_option('smtp_email'), $company);
            $this->email->to($data['email']);
            if (!empty($data['email_cc'])) {
                $this->email->cc($data['email_cc']);
            }
            $systemBCC = get_option('bcc_emails');
            if ($systemBCC != '') {
                $this->email->bcc($systemBCC);
            }
            $this->email->subject($data['subject']);
            $this->email->message($data['content']);
            if ($this->email->send(true)) {
                $create_by = get_staff_user_id();
                $this->db->insert(db_prefix() . 'log_send_email', array(
                    'email' => $data['email'],
                    'cc' => $data['email_cc'],
                    'bcc' => $systemBCC,
                    'subject' => $data['subject'],
                    'mesage' => $data['content'],
                    'create_by' => $create_by,
                    'type' => 'care_ofs',
                    'date_create' => date('Y-m-d H:i:s')
                ));
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('send_true_email')
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => $this->email->print_debugger()
                ));
                die();
            }
        }
        echo json_encode(array('success' => false, 'alert_type' => 'danger', 'message' => _l('send_false_email')));
        die();
    }

    public function get_template($id = "")
    {
        if (!empty($id)) {
            $this->db->where('emailtemplateid', $id);
            $templates = $this->db->get('tblemailtemplates')->row();
            if (!empty($templates)) {
                echo json_encode($templates);
                die();
            }
        }
        echo json_encode(array('message' => '', 'subject' => ''));
        die();
    }

    public function send_list_toast_email()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $assSendTrue = 0;
            $allSendTrue = 0;
            if (!empty($data['list_contact'])) {
                $list_field = [
                    'lastname' => 'company',
                    'birtday' => 'birtday',
                    'email' => 'email',
                    'phonenumber' => 'phonenumber'
                ];
                $data['list_contact'] = explode(',', $data['list_contact']);
                $this->db->where_in('id', $data['list_contact']);
                $contact = $this->db->get('tblcontacts')->result_array();
                foreach ($contact as $key => $value) {
                    if (!empty($value['email'])) {
                        $content = $data['content_list_email'];
                        foreach ($list_field as $k => $v) {
                            $content = str_replace('{' . $v . '}', $value[$k], $content);
                        }
                        $this->load->config('email');
                        $this->email->clear(true);
                        $this->email->initialize();
                        if (get_option('mail_engine') == 'phpmailer') {
                            $this->email->set_debug_output(function ($err) {
                                if (!isset($GLOBALS['debug'])) {
                                    $GLOBALS['debug'] = '';
                                }
                                $GLOBALS['debug'] .= $err . '<br />';
                                echo json_encode(array(
                                    'success' => false,
                                    'alert_type' => 'danger',
                                    'message' => $err
                                ));
                                die();
                            });
                            $this->email->set_smtp_debug(3);
                        }
                        $company = get_option('companyname');
                        $this->email->set_newline(config_item('newline'));
                        $this->email->set_crlf(config_item('crlf'));
                        //Send email
                        $this->email->from(get_option('smtp_email'), $company);
                        $this->email->to($value['email']);
                        if (!empty($data['email_cc'])) {
                            $this->email->cc($data['email_cc']);
                        }
                        $systemBCC = get_option('bcc_emails');
                        if ($systemBCC != '') {
                            $this->email->bcc($systemBCC);
                        }
                        $this->email->subject($data['subject']);
                        $this->email->message($content);
                        ++$allSendTrue;
                        if ($this->email->send(true)) {
                            ++$assSendTrue;
                            $create_by = get_staff_user_id();
                            $this->db->insert(db_prefix() . 'log_send_email', array(
                                'email' => $value['email'],
                                'cc' => !empty($data['email_cc']) ? $data['email_cc'] : '',
                                'bcc' => $systemBCC,
                                'subject' => $data['subject'],
                                'mesage' => $content,
                                'create_by' => $create_by,
                                'type' => 'care_ofs',
                                'date_create' => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                    //Send email
                }
            }
            if (!empty($data['list_client'])) {
                $data['list_client'] = explode(',', $data['list_client']);
                $list_field = [
                    'company' => 'company',
                    'birtday' => 'birtday',
                    'email' => 'email',
                    'phonenumber' => 'phonenumber'
                ];
                $this->db->select('email_client as email, userid, birtday, phonenumber, company');
                $this->db->where_in('userid', $data['list_client']);
                $client = $this->db->get('tblclients')->result_array();
                foreach ($client as $key => $value) {
                    if (!empty($value['email'])) {
                        $content = $data['content_list_email'];
                        foreach ($list_field as $k => $v) {
                            $content = str_replace('{' . $v . '}', $value[$k], $content);
                        }
                        $this->load->config('email');
                        $this->email->clear(true);
                        $this->email->initialize();
                        if (get_option('mail_engine') == 'phpmailer') {
                            $this->email->set_debug_output(function ($err) {
                                if (!isset($GLOBALS['debug'])) {
                                    $GLOBALS['debug'] = '';
                                }
                                $GLOBALS['debug'] .= $err . '<br />';
                                echo json_encode(array(
                                    'success' => false,
                                    'alert_type' => 'danger',
                                    'message' => $err
                                ));
                                die();
                            });
                            $this->email->set_smtp_debug(3);
                        }
                        $company = get_option('companyname');
                        $this->email->set_newline(config_item('newline'));
                        $this->email->set_crlf(config_item('crlf'));
                        //Send email
                        $this->email->from(get_option('smtp_email'), $company);
                        $this->email->to($value['email']);
                        if (!empty($data['email_cc'])) {
                            $this->email->cc($data['email_cc']);
                        }
                        $systemBCC = get_option('bcc_emails');
                        if ($systemBCC != '') {
                            $this->email->bcc($systemBCC);
                        }
                        $this->email->subject($data['subject']);
                        $this->email->message($content);
                        $allSendTrue++;
                        if ($this->email->send(true)) {
                            ++$assSendTrue;
                            $create_by = get_staff_user_id();
                            $this->db->insert(db_prefix() . 'log_send_email', array(
                                'email' => $value['email'],
                                'cc' => !empty($data['email_cc']) ? $data['email_cc'] : '',
                                'bcc' => $systemBCC,
                                'subject' => $data['subject'],
                                'mesage' => $content,
                                'create_by' => $create_by,
                                'type' => 'care_ofs',
                                'date_create' => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                }
            }
            echo json_encode(array(
                'success' => true,
                'alert_type' => 'success',
                'message' => _l('send_true_email') . ' ' . $assSendTrue . '/' . $allSendTrue
            ));
            die();
        }
        echo json_encode(array('success' => false, 'alert_type' => 'success', 'message' => _l('send_not_email')));
        die();
    }

    public function SaveflowChart()
    {
        $toJson = $this->input->post('toJson');
        if (!empty($toJson)) {
            if (update_option('flowChart_Client', $toJson)) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_update_true')
                ));
                die();
            }
        }
        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('cong_update_false')));
        die();
    }

    public function GetTableChild()
    {
        if (!empty($this->input->post('id'))) {
            $data['id'] = $this->input->post('id');
            $data['colspan'] = $this->input->post('colspan');
            $this->db->select(db_prefix() . 'client_info_detail.*, ' . db_prefix() . 'client_info_group.color');
            $this->db->where('id_info_group', $data['id']);
            $this->db->join(db_prefix() . 'client_info_group',
                db_prefix() . 'client_info_group.id = ' . db_prefix() . 'client_info_detail.id_info_group');
            $data['client_info_detail'] = $this->db->get(db_prefix() . 'client_info_detail')->result_array();
            $this->load->view('admin/clients/info_client/table_child/table_child_one', $data);
        }
    }

    public function GetTableChildTwo()
    {
        if (!empty($this->input->post('id'))) {
            $data['id'] = $this->input->post('id');
            $data['colspan'] = $this->input->post('colspan');
            $this->db->where('id_info_detail', $data['id']);
            $data['client_info_detail_value'] = $this->db->get(db_prefix() . 'client_info_detail_value')->result_array();
            $this->load->view('admin/clients/info_client/table_child/table_child_two', $data);
        }
    }

    public function table_info_client_detail($id_Group = "")
    {
        if (!has_permission('customers', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('info_client_detail', array('id_group' => $id_Group));
    }

    public function info_client_detail_value($id = '')
    {
        $data = $this->input->post();
        if ($id == "") {
            $in_array = array(
                'id_info_detail' => $data['info_detail'],
                'name' => $data['name_value']
            );
            $this->db->insert(db_prefix() . 'client_info_detail_value', $in_array);
            if ($this->db->insert_id()) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_add_true')
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_add_false')
                ));
                die();
            }
        } else {
            $this->db->select(db_prefix() . 'client_info_detail.id');
            $this->db->where(db_prefix() . 'client_info_detail_value.id', $id);
            $this->db->join(db_prefix() . 'client_info_detail',
                db_prefix() . 'client_info_detail.id = ' . db_prefix() . 'client_info_detail_value.id_info_detail');
            $detail_old = $this->db->get(db_prefix() . 'client_info_detail_value')->row();
            $in_array = array(
                'id_info_detail' => $data['info_detail'],
                'name' => $data['name_value']
            );
            $this->db->where('id', $id);
            if ($this->db->update('tblclient_info_detail_value', $in_array)) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_update_true'),
                    'detail_id' => $detail_old->id
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ));
                die();
            }
        }
    }

    public function getData_client_detail_value($id = '')
    {
        $this->db->select(db_prefix() . 'client_info_detail_value.*, ' . db_prefix() . 'client_info_detail.id_info_group');
        $this->db->join(db_prefix() . 'client_info_detail',
            db_prefix() . 'client_info_detail.id = ' . db_prefix() . 'client_info_detail_value.id_info_detail');
        $this->db->where(db_prefix() . 'client_info_detail_value.id', $id);
        $data = $this->db->get(db_prefix() . 'client_info_detail_value')->row();
        if (!empty($data)) {
            $this->db->where('id_info_group', $data->id_info_group);
            $data_detail = $this->db->get(db_prefix() . 'client_info_detail')->result_array();
            echo json_encode(array('data' => $data, 'data_detail' => $data_detail));
            die();
        }
        echo json_encode(array());
    }

    public function GetChangeGroup($id = "")
    {
        if (!empty($id)) {
            $this->db->where('id_info_group', $id);
            $data = $this->db->get(db_prefix() . 'client_info_detail')->result_array();
            if (!empty($data)) {
                echo json_encode($data);
                die();
            }
            echo json_encode(array());
            die();
        }
    }

    public function DeleteClientInfoDetailValue($id = '')
    {
        if (!$id) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('cong_delete_false')
            ));
        }
        $this->db->where(db_prefix() . 'client_info_detail_value.id', $id);
        $detail_value = $this->db->get(db_prefix() . 'client_info_detail_value')->row();
        if (!empty($detail_value)) {
            $this->db->select('count(id) as list_id');
            $this->db->where('id_detail', $detail_value->id_info_detail);
            $this->db->where('value', $detail_value->id);
            $client_value = $this->db->get(db_prefix() . 'client_value')->row();
            if (empty($client_value->list_id)) {
                $this->db->where('id', $id);
                $response = $this->db->delete(db_prefix() . 'client_info_detail_value');
            }
        }
        if ($response == true) {
            echo json_encode(array(
                'success' => true,
                'alert_type' => 'success',
                'message' => _l('delete_info_client_detail')
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('cong_isset_data_not_delete')
            ));
        }
    }

    public function hidde_colum_info_client()
    {
        if ($this->input->post()) {
            $group_detail = (object)[];
            $inpu_group_detail = $this->input->post('group_detail');
            if (!empty($inpu_group_detail)) {
                foreach ($inpu_group_detail as $key => $value) {
                    $group_detail->{$value} = $value;
                }
            }
            $staff = get_staff_user_id();
            $this->db->where('id_user', $staff);
            $hidden_colum_client = $this->db->get(db_prefix() . 'hidden_colum_client')->row();
            if (!empty($hidden_colum_client)) {
                $this->db->where('id', $hidden_colum_client->id);
                if ($this->db->update(db_prefix() . 'hidden_colum_client',
                    ['group_detail' => json_encode($group_detail)])) {
                    echo json_encode([
                        'success' => true
                    ]);
                    die();
                } else {
                    $this->db->insert(db_prefix() . 'hidden_colum_client', [
                        'id_user' => $staff,
                        'group_detail' => json_encode($group_detail)
                    ]);
                    if ($this->db->insert_id()) {
                        echo json_encode([
                            'success' => true
                        ]);
                        die();
                    }
                }
            }
        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function hidde_colum_field_customer()
    {
        if (is_array($this->input->post())) {
            $field_customer = (object)[];
            $inpu_field_customer = $this->input->post('field_customer');
            if (!empty($inpu_field_customer)) {
                foreach ($inpu_field_customer as $key => $value) {
                    $field_customer->{$value} = $value;
                }
            }
            $staff = get_staff_user_id();
            $this->db->where('id_user', $staff);
            $hidden_colum_client = $this->db->get(db_prefix() . 'hidden_colum_client')->row();
            if (!empty($hidden_colum_client)) {
                $this->db->where('id', $hidden_colum_client->id);
                if ($this->db->update(db_prefix() . 'hidden_colum_client',
                    ['field_customer' => json_encode($field_customer)])) {
                    echo json_encode([
                        'success' => true
                    ]);
                    die();
                } else {
                    $this->db->insert(db_prefix() . 'hidden_colum_client', [
                        'id_user' => $staff,
                        'field_customer' => json_encode($field_customer)
                    ]);
                    if ($this->db->insert_id()) {
                        echo json_encode([
                            'success' => true
                        ]);
                        die();
                    }
                }
            }
        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function unlinkImg($userid = "", $img)
    {
        if (!empty($userid)) {
            unlink(get_upload_path_by_type('customer') . $userid . '/thumb_' . $img);
            unlink(get_upload_path_by_type('customer') . $userid . '/small_' . $img);
            $this->db->where('userid', $userid);
            $this->db->update('tblclients', ['client_image' => ""]);
        }
    }

    public function addComBoBox()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $this->db->insert('tblcombobox_client', ['name' => $data['name'], 'type' => $data['type']]);
            if ($this->db->insert_id()) {
                $id = $this->db->insert_id();
                echo json_encode([
                    'success' => true,
                    'message' => _l('cong_add_true'),
                    'id' => $id,
                    'name' => $data['name']
                ]);
                die();
            }
        }
        echo json_encode(['success' => false, 'message' => _l('cong_add_false')]);
        die();
    }

    public function advisory_client($client = "")
    {
        if ($this->input->post() && !empty($client)) {
            $data = $this->input->post();
            if (!empty($data['id'])) {
                $id = $data['id'];
                unset($data['id']);
                $array_update = [
                    'type' => $data['type'],
                    'date' => to_sql_date($data['date']),
                    'remind' => to_sql_date($data['remind']),
                    'cycle' => $data['cycle'],
                    'note' => $this->input->post('note', false),
                ];
                $this->db->where('id', $id);
                $success = $this->db->update(db_prefix() . 'advisory_client', $array_update);
                if (!empty($success)) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_update_true')
                    ]);
                    die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ]);
                die();
            } else {
                unset($data['id']);
                $array_add = [
                    'type' => $data['type'],
                    'date' => to_sql_date($data['date']),
                    'leadtime' => get_option('leadtime_client'),
                    'remind' => to_sql_date($data['remind']),
                    'cycle' => $data['cycle'],
                    'create_by' => get_staff_user_id(),
                    'date_create' => date('Y-m-d H:i:s'),
                    'client' => $client,
                    'note' => $this->input->post('note', false),
                ];
                $this->db->insert(db_prefix() . 'advisory_client', $array_add);
                if ($this->db->insert_id()) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_add_true')
                    ]);
                    die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_add_false')
                ]);
                die();
            }
        }
    }

    public function view_modal_advisory()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $client = $this->input->post('client');
            if (!empty($id)) {
                $data['client'] = $client;
                $data['title'] = _l('title_update_advisory-client');
                $data['advisory'] = $this->db->get_where(db_prefix() . 'advisory_client',
                    ['id' => $id, 'client' => $client])->row();
                $this->load->view('admin/clients/modals/advisory_client', $data);
            } else {
                $data['client'] = $client;
                $data['title'] = _l('title_add_advisory-client');
                $this->load->view('admin/clients/modals/advisory_client', $data);
            }
        }
    }

    public function hidden_colum()
    {
        if ($this->input->post()) {
            $field_detail = (object)[];
            $inpu_field = $this->input->post('field');
            if (!empty($inpu_field)) {
                foreach ($inpu_field as $key => $value) {
                    $field_detail->{$value} = $value;
                }
            }
            $staff = get_staff_user_id();
            $this->db->where('id_user', $staff);
            $hidden_colum_client = $this->db->get(db_prefix() . 'hidden_colum_client')->row();
            if (!empty($hidden_colum_client)) {
                $this->db->where('id', $hidden_colum_client->id);
                if ($this->db->update(db_prefix() . 'hidden_colum_client', ['field' => json_encode($field_detail)])) {
                    echo json_encode([
                        'success' => true
                    ]);
                    die();
                } else {
                    $this->db->insert(db_prefix() . 'hidden_colum_client', [
                        'id_user' => $staff,
                        'group_detail' => json_encode($field_detail)
                    ]);
                    if ($this->db->insert_id()) {
                        echo json_encode([
                            'success' => true
                        ]);
                        die();
                    }
                }
            } else {
                $this->db->insert(db_prefix() . 'hidden_colum_client', [
                    'id_user' => $staff,
                    'group_detail' => json_encode($field_detail)
                ]);
                if ($this->db->insert_id()) {
                    echo json_encode([
                        'success' => true
                    ]);
                    die();
                }
            }
        }
        echo json_encode([
            'success' => false
        ]);
        die();
    }

    public function deleteList()
    {
        $listData = $this->input->post('listData');
        $data = [];
        $countDelete = 0;
        if (!empty($listData)) {
            foreach ($listData as $key => $value) {
                $ktConnectClient = getConnectClient($value);
                if (empty($ktConnectClient)) {
                    if ($this->clients_model->delete($value)) {
                        ++$countDelete;
                    }
                } else {
                    $client = get_table_where('tblclients', ['userid' => $value], '', 'row');
                    if (!empty($client)) {
                        $data[] = [
                            'code' => $client->company . (!empty($client->prefix_client) || $client->code_client ? ' (' . $client->prefix_client . $client->code_client . ') ' : ''),
                            'data' => $ktConnectClient
                        ];
                    }
                }
            }
            echo json_encode([
                'alert_type' => $countDelete > 0 ? 'success' : 'danger',
                'success' => $countDelete > 0 ? true : false,
                'ktConnect' => $data,
                'message' => _l('cong_delete_true_count_data') . ' ' . $countDelete . '/' . count($listData)
            ]);
            die();
        }
        echo json_encode([
            'alert_type' => 'danger',
            'success' => false,
            'success' => _l('cong_not_found_data_delete')
        ]);
        die();
    }



    //End công
    //Hoàng CRM bổ xung
    public function dashboard_client()
    {
        $data['title'] = _l('dashboard_client');
        $tickets_awaiting_reply_by_status = $this->dashboard_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();
        $data['tickets_reply_by_status'] = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);
        $data['tickets_reply_by_status_no_json'] = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;
        $data['client_time_stats'] = json_encode($this->dashboard_model->client_time_status());
        print_arrays($data['client_time_stats']);
        $data['leads_time_stats'] = json_encode($this->dashboard_model->lead_time_status());
        $this->db->select('tblactivity_log_v2.*');
        $this->db->where('tblactivity_log_v2.type_parent_obj', 'client');
        $this->db->order_by('tblactivity_log_v2.id DESC');
        $this->db->limit(10);
        $data['dataLog'] = $this->db->get('tblactivity_log_v2')->result_array();
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1);
        $data['staff'] = $this->db->get()->result_array();
        $this->load->view('admin/clients/dashboard_client', $data);
    }

    public function chart_statistics()
    {
        $this->db->select('count(*) count_total');
        $count_total = $this->db->get('tblclients')->row();
        $array_sales_count[] = ($count_total->count_total) ? $count_total->count_total : 0;
        $data['weekly_sales_stats'] = json_encode(
            $chart = array(
                'labels' => "Biểu đồ",
                'datasets' => array(
                    array(
                        'label' => "Số lượng",
                        'backgroundColor' => 'rgba(37,11,35,0.2)',
                        'borderColor' => "#84c529",
                        'borderWidth' => 1,
                        'tension' => false,
                        'data' => $array_sales_count
                    )
                )
            )
        );
        echo $data['weekly_sales_stats'];
        die();
    }

    public function info_client()
    {
        $data['title'] = _l('info_client');
        //Coong them
        $data['info_group'] = get_table_where(db_prefix() . 'client_info_group');
        $data['type_form'] = [
            ['name' => 'input'],
            ['name' => 'date'],
            ['name' => 'checkbox'],
            ['name' => 'select'],
            ['name' => 'select multiple'],
            ['name' => 'radio'],
            ['name' => 'password']
        ];
        //End
        $this->load->view('admin/clients/info_client/manage', $data);
    }

    public function detail_info_client()
    {
        $data['title'] = _l('info_client');
        $data['info_group'] = get_table_where('tblclient_info_group');
        $this->load->view('admin/clients/info_client/detail_info_client', $data);
    }

    public function table_info_client_group($value = '')
    {
        if (!has_permission('customers', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('info_client_group');
    }

    public function info_client_group($id = '')
    {
        $data = $this->input->post();
        if ($id == "") {
            $in = array(
                'name' => $data['name'],
                'color' => $data['color']
            );
            $this->db->insert('tblclient_info_group', $in);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                $get_code = get_table_where('tblclient_info_group', array('id' => $insert_id), '', 'row');
                activity_log_v2('client', 'tblclient_info_group', $insert_id, $get_code->name,
                    'Thêm mới nhóm thông tin khách hàng [' . $get_code->name . ']');
                $this->db->insert('tblclient_info_group', $in);
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_add_true'),
                    'data' => ['id' => $insert_id, 'name' => $data['name']]
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_add_false')
                ));
                die();
            }
        } else {
            $in = array(
                'name' => $data['name'],
                'color' => $data['color']
            );
            $this->db->where('id', $id);
            if ($this->db->update('tblclient_info_group', $in)) {
                $get_code = get_table_where('tblclient_info_group', array('id' => $id), '', 'row');
                activity_log_v2('client', 'tblclient_info_group', $id, $get_code->name,
                    'Cập nhật nhóm thông tin khách hàng [' . $get_code->name . ']');
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_update_true'),
                    'data' => ['id' => $id, 'name' => $data['name']]
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ));
                die();
            }
        }
    }

    public function getData_infoClientGroup($id = '')
    {
        $data = get_table_where('tblclient_info_group   ', array('id' => $id), '', 'row');
        echo json_encode($data);
    }

    public function delete_info_client_group($id = '')
    {
        if (!$id) {
            redirect(admin_url('info_client'));
        }
        $checkExists = get_table_where('tblclient_info_detail', array('id_info_group' => $id), '', 'row');
        if (!$checkExists) {
            $get_code = get_table_where('tblclient_info_group', array('id' => $id), '', 'row');
            activity_log_v2('client', 'tblclient_info_group', $id, $get_code->name,
                'Xóa nhóm thông tin khách hàng [' . $get_code->name . ']');
            $this->db->where('id', $id);
            $response = $this->db->delete('tblclient_info_group');
        } else {
            $response = false;
        }
        if ($response == true) {
            echo json_encode(array(
                'success' => true,
                'alert_type' => 'success',
                'message' => _l('delete_info_client_group')
            ));
            die();
        } else {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('cong_isset_data_not_delete')
            ));
            die();
        }
    }

    public function info_client_detail($id = '')
    {
        $data = $this->input->post();
        if ($id == "") {
            $in = array(
                'id_info_group' => $data['info_group'],
                'name' => $data['name_detail'],
                'type_form' => $data['type_form'],
                'is_required' => (!empty($data['is_required']) ? $data['is_required'] : 0)
            );
            if ($this->db->insert('tblclient_info_detail', $in)) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_add_true')
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_add_false')
                ));
                die();
            }
        } else {
            $in = array(
                'id_info_group' => $data['info_group'],
                'name' => $data['name_detail'],
                'type_form' => $data['type_form'],
                'is_required' => (!empty($data['is_required']) ? $data['is_required'] : 0)
            );
            $this->db->where('id', $id);
            if ($this->db->update('tblclient_info_detail', $in)) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('edit_update_true')
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ));
                die();
            }
        }
    }

    public function getData_client_detail($id = '')
    {
        $data = get_table_where('tblclient_info_detail', array('id' => $id), '', 'row');
        echo json_encode($data);
    }

    public function delete_info_detail($id = '')
    {
        if (empty($id)) {
            redirect(admin_url('detail_info_client'));
        }
        $this->db->select('count(id) as list_id');
        $this->db->where('id_detail', $id);
        $this->db->where('( value is not null and value !="" )');
        $client_value = $this->db->get(db_prefix() . 'client_value')->row();
        if (empty($client_value->list_id)) {
            $this->db->where('id', $id);
            $response = $this->db->delete('tblclient_info_detail');
            if ($response == true) {
                $this->db->where('id_info_detail', $id);
                $this->db->delete(db_prefix() . 'client_info_detail_value');
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('delete_info_client_detail')
                ));
                die();
            } else {
                echo json_encode(array(
                    'success' => false,
                    'alert_type' => 'warning',
                    'message' => _l('cong_delete_false')
                ));
                die();
            }
        }
        echo json_encode(array('success' => false, 'alert_type' => 'warning', 'message' => _l('cong_delete_false')));
        die();
    }

    //end
    public function init_customer()
    {
        $id_customer = $this->input->post('id_customer');
        $this->db->select('tblclients.*, tbltype_client.name as nameType_client, tblcombobox_client.name as nameMarriage, tblcountries.short_name as short_name_countries, tblprovince.name as name_province, tbldistrict.name as name_district, tblward.name as name_ward, tblleads_sources.name as name_sources');
        $this->db->from('tblclients');
        $this->db->join('tbltype_client', 'tbltype_client.id = tblclients.type_client', 'left');
        $this->db->join('tblcombobox_client', 'tblcombobox_client.id = tblclients.marriage', 'left');
        $this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
        $this->db->join('tblprovince', 'tblprovince.provinceid = tblclients.city', 'left');
        $this->db->join('tbldistrict', 'tbldistrict.districtid = tblclients.district', 'left');
        $this->db->join('tblward', 'tblward.wardid = tblclients.ward', 'left');
        $this->db->join('tblleads_sources', 'tblleads_sources.id = tblclients.sources', 'left');
        $this->db->where('userid', $id_customer);
        $data['client'] = $this->db->get()->row();
        echo json_encode([
            'data' => $this->load->view('admin/clients/modals/init_customer', $data, true),
        ]);
    }

    public function searchCustomers($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $customers = $this->clients_model->searchCustomers($term, $limit);
        $leads = $this->clients_model->searchCustomersLeads($term, $limit);
        $data['results'] = [
            [
                'text' => lang('customers'),
                'children' => $customers
            ],
            // [
            //     'text' => lang('tnh_leads'), 'children' => $leads
            // ]
        ];
        if ($id) {
            $id = explode('__', $id);
            $type_customer = $id[0];
            $customer_id = $id[1]; //leads or customers
            if ($type_customer == "customers") {
                $customer = $this->clients_model->rowCustomer($customer_id);
                $data['row'] = [
                    'id' => $type_customer . '__' . $customer['userid'],
                    'text' => $customer['company_short']
                ];
            } elseif ($type_customer == "leads") {
                $lead = $this->clients_model->rowLead($customer_id);
                $data['row'] = ['id' => $type_customer . '__' . $lead['id'], 'text' => $lead['name']];
            } else {
                $data['row'] = ['id' => 0, 'text' => lang('choose')];
            }
        }
        echo json_encode($data);
    }

    public function searchOnlyClients($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $customers = $this->clients_model->searchCustomers($term, $limit);
        $data['results'] = [
            [
                'text' => lang('customers'),
                'children' => $customers
            ]
        ];
        if ($id) {
            $id = explode('__', $id);
            $type_customer = $id[0];
            $customer_id = $id[1]; //leads or customers
            if ($type_customer == "customers") {
                $customer = $this->clients_model->rowCustomer($customer_id);
                $data['row'] = [
                    'id' => $type_customer . '__' . $customer['userid'],
                    'text' => $customer['company_short']
                ];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not'];
            }
        }
        echo json_encode($data);
    }

    public function searchAddressDelivery($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            if ($type_customer == "customers") {
                $results = $this->clients_model->searchShippingClientByClientId($term, $limit, $customer_id);
            } elseif ($type_customer == "leads") {
                $results = $this->clients_model->searchShippingLeadByLeadId($term, $limit, $customer_id);
            }
        }
        $data['results'] = $results;
        if ($id) {
            $shipping = $this->site_model->rowShippingClient($id);
            if (!empty($shipping)) {
                $data['row'] = ['id' => $shipping['id'], 'text' => $shipping['address']];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not'];
            }
        }
        echo json_encode($data);
    }

    public function searchQuotesAddressDelivery($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            if ($type_customer == "customers") {
                $results = $this->clients_model->searchShippingClientByClientId($term, $limit, $customer_id);
            } elseif ($type_customer == "leads") {
                $results = $this->clients_model->searchShippingLeadByLeadId($term, $limit, $customer_id);
            }
        }
        $data['results'] = $results;
        if ($id) {
            $arrData = explode('__', $id);
            $id = $arrData[1];
            $type = $arrData[0];
            if ($type == "customers" && !empty($id)) {
                $shipping = $this->site_model->rowShippingClient($id);
                $data['row'] = ['id' => $shipping['id'], 'text' => $shipping['address']];
            } elseif ($type == "leads" && !empty($id)) {
                $shipping = $this->site_model->tblshipping_lead($id);
                $data['row'] = ['id' => $shipping['id'], 'text' => $shipping['address']];
            } else {
                $data['row'] = ['id' => 0, 'text' => lang('choose')];
            }
        }
        echo json_encode($data);
    }

    public function searchContract($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        $type_customer = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            if ($type_customer == "customers") {
                $results = $this->clients_model->searchContract($term, $limit, $customer_id);
            } elseif ($type_customer == "leads") {
                $results = $this->clients_model->searchContractLead($term, $limit, $customer_id);
            }
        }
        $data['results'] = $results;
        if ($id) {
            $arrData = explode('__', $id);
            $id = $arrData[1];
            $type = $arrData[0];
            if ($type == "customers" && !empty($id)) {
                $contact = $this->site_model->rowContact($id);
                $data['row'] = ['id' => $contact['id'], 'text' => $contact['firstname'] . ' ' . $contact['lastname']];
            } elseif ($type == "leads" && !empty($id)) {
                $contact = $this->site_model->rowContactLead($id);
                $data['row'] = ['id' => $contact['id'], 'text' => $contact['firstname'] . ' ' . $contact['lastname']];
            } else {
                $data['row'] = ['id' => 0, 'text' => lang('choose')];
            }
        }
        echo json_encode($data);
    }

    public function searchContractNew($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        $type_customer = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            if ($type_customer == "customers") {
                $results = $this->clients_model->searchContract($term, $limit, $customer_id);
            } elseif ($type_customer == "leads") {
                $results = $this->clients_model->searchContractLead($term, $limit, $customer_id);
            }
        }
        $data['results'] = $results;
        if ($id) {
            $contact = $this->site_model->rowContact($id);
            $data['row'] = ['id' => $contact['id'], 'text' => $contact['firstname'] . ' ' . $contact['lastname']];
        }
        echo json_encode($data);
    }

    public function delete_shipping($id = '')
    {
        if (!$id) {
            die('Không thể xóa');
        }
        $get_id = get_table_where('tblshipping_client', array('id' => $id), '', 'row');
        if (!empty($get_id->client)) {
            $get_code = get_table_where('tblclients', array('userid' => $get_id->client), '', 'row');
            activity_log_v2('client', 'tblclients', $get_id->client, $get_code->company,
                'Xóa thông tin giao hàng khách hàng [' . $get_code->company . ']');
        }
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->delete('tblshipping_client');
            if ($this->db->affected_rows() > 0) {
                $alert_type = 'success';
                $message = _l('ch_delete');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function addShipping($id)
    {
        // print_arrays($this->input->post());
        if ($this->input->post()) {
            $data = [];
            $this->form_validation->set_rules('name_shipping', lang("cong_name_shipping_client"), 'required');
            $this->form_validation->set_rules('address_shipping', lang("cong_address_shipping_client"), 'required');
            $this->form_validation->set_rules('phone_shipping', lang("cong_phone_shipping_client"), 'required');
            if ($this->form_validation->run() == true) {
                $flag = false;
                $arr = explode('__', $id);
                $type_customer = $arr[0];
                $customer_id = $arr[1];
                $name_shipping = $this->input->post('name_shipping');
                $address_shipping = $this->input->post('address_shipping');
                $phone_shipping = $this->input->post('phone_shipping');
                $address_primary = $this->input->post('address_primary') ? $this->input->post('address_primary') : 0;
                $shipping_id = 0;
                $option = [
                    'name' => $name_shipping,
                    'phone' => $phone_shipping,
                    'address' => $address_shipping,
                    'address_primary' => $address_primary,
                    'date_create' => date('Y-m-d H:i'),
                    'create_by' => get_staff_user_id(),
                ];
                if ($type_customer == "customers") {
                    $option['client'] = $customer_id;
                    $option['city_shipping'] = $this->input->post('city');
                    $option['district_shipping'] = $this->input->post('district');
                    if ($this->db->insert('tblshipping_client', $option)) {
                        $flag = true;
                        $shipping_id = $this->db->insert_id();
                    }
                } elseif ($type_customer == "leads") {
                    $option['lead_id'] = $customer_id;
                    if ($this->db->insert('tblshipping_lead', $option)) {
                        $flag = true;
                        $shipping_id = $this->db->insert_id();
                    }
                }
                // print_arrays($type_customer);
                // print_arrays($this->db->set($option)->get_compiled_insert('tblshipping_client'), FALSE);
                if ($flag) {
                    $data['result'] = 1;
                    $data['shipping_id'] = $shipping_id;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['shipping_id'] = $shipping_id;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['id'] = $id;
            $this->load->view('admin/clients/add_shipping', $data);
        }
    }

    public function searchOnlyCustomers($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $customers = $this->clients_model->searchOnlyCustomers($term, $limit);
        $data['results'] = [
            [
                'text' => lang('customers'),
                'children' => $customers
            ],
        ];
        if ($id) {
            $customer = $this->clients_model->rowCustomer($id);
            $data['row'] = ['id' => $customer['userid'], 'text' => $customer['company']];
        }
        echo json_encode($data);
    }

    public function searchProvince($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $results = false;
        // print_arrays($this->in)
        $results = $this->site_model->searchProvince($term, $limit);
        $data['results'] = $results;
        if ($id) {
            $province = $this->site_model->rowProvince($id);
            if (!empty($shipping)) {
                $data['row'] = ['id' => $province['id'], 'text' => $province['name']];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not'];
            }
        }
        echo json_encode($data);
    }

    public function searchDistrictByProvince($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $province_id = $params['province_id'];
        $results = false;
        if ($province_id) {
            $results = $this->site_model->searchDistrictByProvince($term, $limit, $province_id);
        }
        $data['results'] = $results;
        if ($id) {
            $province = $this->site_model->rowProvince($id);
            if (!empty($province)) {
                $data['row'] = ['id' => $shipping['provinceid'], 'text' => $province['name']];
            } else {
                $data['row'] = ['id' => 0, 'text' => 'Not'];
            }
        }
        echo json_encode($data);
    }

    public function addFlashCustomer()
    {
        if ($this->input->post('company')) {
            $data = [];
            $this->form_validation->set_rules('company', lang("clients_list_company"), 'required');
            $this->form_validation->set_rules('company_short', lang("c_company_short"), 'required');
            if ($this->form_validation->run() == true) {
                $company = $this->input->post('company');
                $company_short = $this->input->post('company_short');
                $phone = $this->input->post('phone');
                $email = $this->input->post('email');
                $zcode = get_option('prefix-client') . '-' . sprintf("%05d",
                        (ch_getMaxID_items('userid', 'tblclients') + 1));
                $option = [
                    'prefix_client' => get_option('prefix-client'),
                    'company' => $company,
                    'company_short' => $company_short,
                    'phonenumber' => $phone,
                    'email_client' => $email,
                    'zcode' => $zcode,
                    'debt_limit' => 0,
                    'debt_limit_day' => 0,
                    'discount' => 0,
                    'active' => 1,
                    'datecreated' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('tblclients', $option);
                $customer_id = $this->db->insert_id();
                if ($customer_id) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $this->load->view('admin/clients/add_flash_customer');
        }
    }

    public function addFlashCustomerai()
    {
        if ($this->input->post('company')) {
            $data = [];
            $this->form_validation->set_rules('company', lang("clients_list_company"), 'required');
            $this->form_validation->set_rules('company_short', lang("c_company_short"), 'required');
            if ($this->form_validation->run() == true) {
                $company = $this->input->post('company');
                $company_short = $this->input->post('company_short');
                $phone = $this->input->post('phone');
                $email = $this->input->post('email');
                $zcode = $this->input->post('zcode');
                // $zcode = get_option('prefix-client') . '-' . sprintf("%05d", (ch_getMaxID_items('userid', 'tblclients') + 1));
                if (empty($zcode)) {
                    $zcode = get_option('prefix-client') . '-' . sprintf("%05d",
                            (ch_getMaxID_items('userid', 'tblclients') + 1));
                }
                $option = [
                    'prefix_client' => get_option('prefix-client'),
                    'company' => $company,
                    'company_short' => $company_short,
                    'phonenumber' => $phone,
                    'email_client' => $email,
                    'zcode' => $zcode,
                    'debt_limit' => 0,
                    'debt_limit_day' => 0,
                    'discount' => 0,
                    'active' => 1,
                    'datecreated' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('tblclients', $option);
                $customer_id = $this->db->insert_id();

                if ($customer_id) {
                    $data['result'] = 1;
                    $data['customer_id'] = $customer_id;
                    $data['zcode'] = $zcode;
                    $data['company'] = $company;
                    $data['message'] = lang('success');
                    $data['reply'] = "✅ Đã tạo thấy khách hàng thành công:<br>
                👤 <b>$company</b> (ID: $customer_id)<br>
                <button onclick=\"confirmCustomer($customer_id, '$company')\">✅ Xác nhận khách này</button>";
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $this->load->view('admin/chatbot/AddClients');
        }
    }

    public function count_all($id = '')
    {
        $status1 = get_table_where_select('count(*) as status1', 'tblpurchases',
            array('status' => 1, 'tblpurchases.staff_create' => get_staff_user_id()), '', 'row');
        $search_date = $this->input->post('search_date');
        if (empty($search_date)) {
            $count = get_table_where_select('COUNT(*) as count', 'tbl_orders', array('customer_id' => $id), '', 'row');
            $total = get_table_where_select('COALESCE(SUM(grand_total),0) as total', 'tbl_orders',
                array('customer_id' => $id), '', 'row');
            $pay = get_table_where_select('COALESCE(SUM(payment),0) as payment', 'tblvouchers_coupon',
                array('customer' => $id), '', 'row');
            $other = get_table_where_select('COALESCE(SUM(total),0) as total', 'tblother_payslips_coupon',
                array('objects' => 1, 'objects_id' => $id), '', 'row');
        } else {
            $data_start = explode(' - ', $search_date);
            $count = get_table_where_select('COUNT(*) as count', 'tbl_orders', array(
                'date >=' => to_sql_date($data_start[0]) . ' 00:00:00',
                'date <=' => to_sql_date($data_start[1]) . ' 23:59:59',
                'customer_id' => $id
            ), '', 'row');
            $total = get_table_where_select('COALESCE(SUM(grand_total),0) as total', 'tbl_orders', array(
                'date >=' => to_sql_date($data_start[0]) . ' 00:00:00',
                'date <=' => to_sql_date($data_start[1]) . ' 23:59:59',
                'customer_id' => $id
            ), '', 'row');
            $pay = get_table_where_select('COALESCE(SUM(payment),0) as payment', 'tblvouchers_coupon', array(
                'date_vouchers >=' => to_sql_date($data_start[0]) . ' 00:00:00',
                'date_vouchers <=' => to_sql_date($data_start[1]) . ' 23:59:59',
                'customer' => $id
            ), '', 'row');
            $other = get_table_where_select('COALESCE(SUM(total),0) as total', 'tblother_payslips_coupon', array(
                'date >=' => to_sql_date($data_start[0]) . ' 00:00:00',
                'date <=' => to_sql_date($data_start[1]) . ' 23:59:59',
                'objects' => 1,
                'objects_id' => $id
            ), '', 'row');
        }
        $data['count_all'] = $count->count;
        $data['total'] = number_format($total->total);
        $data['pay'] = number_format($pay->payment + $other->total);
        echo json_encode($data);
    }

    public function add_admin()
    {
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $staffid_id = $this->input->post('staffid_id');
            $renew = $this->input->post('renew');
            $total_deleted = 0;
            if (is_array($ids) && !empty($staffid_id)) {
                foreach ($ids as $id) {
                    if (!empty($renew)) {
                        $this->db->where('customer_id', $id);
                        $this->db->delete('tblcustomer_admins');
                    }
                    if (is_numeric($staffid_id)) {
                        $ktr = get_table_where('tblcustomer_admins',
                            array('staff_id' => $staffid_id, 'customer_id' => $id), '', 'row');
                        if (empty($ktr)) {
                            $data = array(
                                'staff_id' => $staffid_id,
                                'customer_id' => $id,
                                'date_assigned' => date('Y-m-d')
                            );
                            $this->db->insert('tblcustomer_admins', $data);
                            $total_deleted++;
                        }
                    } elseif (!empty($renew)) {
                        $total_deleted++;
                    }
                }
            }
        }
        echo json_encode([
            'success' => 'success',
            'message' => _l('total_clients_add_admin', $total_deleted)
        ]);
        die();
    }

    public function add_branch_client()
    {
        $data = $this->input->post();
        $check_exist = get_table_where('tbl_client_branch',
            array('client_id' => $data['client_id']), '', 'result_array');
        if (!empty($check_exist)) {
            foreach ($check_exist as $k => $v) {
                $this->db->where('id', $v['id']);
                $this->db->delete('tbl_client_branch');
            }
        }
        if (!empty($data['branch_id'])) {
            foreach ($data['branch_id'] as $key => $value) {
                $data_branch = array(
                    'branch_id' => $value,
                    'client_id' => $data['client_id'],
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                );
                $this->db->insert('tbl_client_branch', $data_branch);
            }
            echo json_encode(array(
                'success' => 1,
                'message' => _l('Chỉnh sửa thành công'),
                'alert_type' => 'success',
            ));
        } else {
            echo json_encode(array(
                'success' => 0,
                'message' => _l('Đã xoá hết chi nhánh'),
                'alert_type' => 'danger',
            ));
            die;
        }
    }

    public function edit_group($id = '')
    {
        $data = [];
        $this->db->where('id', $id);
        $this->db->from('tblcustomers_groups');
        $dtGroup = $this->db->get()->row_array();
        $data['dtGroup'] = $dtGroup;
        $this->load->view('admin/clients/client_group', $data);
    }

    public function export_excel_group()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $this->db->select('
                tblcustomers_groups.*,
            ');
        $this->db->from('tblcustomers_groups');
        $dtGroup = $this->db->get()->result_array();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(20);
        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                    0) : '');
        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $company_vat = get_option('company_vat');
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman'
            ),
        ]);
        $objPHPExcel->getActiveSheet()->setCellValue('A1',
            ('THÔNG TIN BRAND'))->getStyle("A1")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 18,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(25);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
        $sttRow = 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Số Thứ Tự');
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Tên Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Phân Loại');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '',
            'Nhóm Chứng Nhận')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '',
            'Tiêu Chuẩn Áp Dụng')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '',
            'ĐV Kiểm-Chứng Nhận')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Ngày Bắt Đầu');
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Ngày Tái Tục');
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
            'Tiêu Chuẩn Sản Phẩm')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', '%Chiết Khấu');
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:K$sttRow")->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFF00'),
            ),
        ]);
        $rowBegin = $sttRow;
        if (!empty($dtGroup)) {
            foreach ($dtGroup as $key => $value) {
                $rowBegin++;
                $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
                    $value['code'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['classify']));
                $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['certification_group']);
                $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['applied_standard']);
                $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                    $value['verification_unit'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
                    !empty($value['date_start']) ? _dhau($value['date_start']) : '');
                $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                    !empty($value['date_end']) ? _dhau($value['date_end']) : '');
                $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                    $value['product_standards'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                    $value['percent_discount'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:K$rowBegin")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("D$rowBegin:D$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:K$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }
        }
        $filename = lang('thong_tin_brand') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth(15);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function export_excel_client()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $date_start_search = $this->input->post('date_start_search') ? to_sql_date($this->input->post('date_start_search')) . ' 00:00:00' : null;
            $end_start_search = $this->input->post('end_start_search') ? to_sql_date($this->input->post('end_start_search')) . ' 23:59:59' : null;
            $groups_search = $this->input->post('groups_search');

            $tb_tamp_group = "(
                SELECT
                    tblcustomer_groups.customer_id,
                    GROUP_CONCAT(tblcustomers_groups.code SEPARATOR '<br>') as code_group,
                    GROUP_CONCAT(tblcustomers_groups.name SEPARATOR '<br>') as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                GROUP BY tblcustomer_groups.customer_id
            ) tb_tamp_group";

            $tb_tamp_branch = "(
                SELECT
                    tbl_client_branch.client_id as customer_id,
                    GROUP_CONCAT(tblbranch.name SEPARATOR '<br>') as name_branch,
                    GROUP_CONCAT(tblbranch.address SEPARATOR '<br>') as address_branch
                FROM tblbranch
                JOIN tbl_client_branch ON tbl_client_branch.branch_id = tblbranch.id
                GROUP BY tbl_client_branch.client_id
            ) tb_tamp_branch";

            $this->db->select('
                tblclients.userid,
                tblclients.zcode,
                tblclients.company,
                tblclients.vat,
                tblclients.code_xnk,
                tblclients.bank_account,
                tblclients.name_account,
                tblclients.address_bank,
                tblclients.tm_ck,
                tblclients.time_payment,
                tblclients.type_contract,
                tblclients.date_renewal,
                tb_tamp_group.code_group,
                tb_tamp_group.name_group,
                tb_tamp_branch.name_branch,
                tb_tamp_branch.address_branch,
                tblcurrencies.name as name_currence,
                tbltaxes.name as name_tax,
                tblcontacts.firstname,
                tblcontacts.phonenumber,
                tblclients.address as addressClient,
				tblclients.status_clients as status_clients,
				tblstatus_client.name as name_status
            ');
            $this->db->from('tblclients');
            $this->db->join('tbltaxes', 'tbltaxes.id = tblclients.vat_id', 'left');
            $this->db->join('tblcurrencies', 'tblcurrencies.id = tblclients.currency', 'left');
            $this->db->join($tb_tamp_group, 'tb_tamp_group.customer_id = tblclients.userid', 'left');
            $this->db->join($tb_tamp_branch, 'tb_tamp_branch.customer_id = tblclients.userid', 'left');
            $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid', 'left');
            $this->db->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left');
            $this->db->order_by('tblclients.userid asc');
            $where = [];
            if (!empty($date_start_search)) {
                array_push($where, ' AND tblclients.datecreated >= "' . $date_start_search . '"');
            }

            if (!empty($end_start_search)) {
                array_push($where, ' AND tblclients.datecreated <= "' . $end_start_search . '"');
            }

            if (!empty($groups_search)) {
                array_push($where,
                    'AND (tblclients.userid IN (SELECT customer_id FROM tblcustomer_groups WHERE groupid = ' . $groups_search . '))');
            }

            if ($where) {
                $where = stringWhere($where);
                $this->db->where($where, false, false);
            }
            $dtClients = $this->db->get()->result_array();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('THÔNG TIN KHÁCH HÀNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Số Thứ Tự');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Nhóm');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Nhóm KH');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '',
                'Xưởng, Chi Nhánh')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Mã Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Tên Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Mã Số Thuế');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã Số XNK');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'VAT');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
                'Số Tài Khoản Ngân Hàng')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Tên Ngân Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '',
                'Địa Chỉ Ngân Hàng')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '',
                'Phương Thức Thanh Toán')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '',
                'Đơn Vị Thanh Toán')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '',
                'Công Thức Chuyển Đổi Tiền')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '',
                'Thời Hạn Thanh Toán')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Loại Hợp Đồng');
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Ngày Tái Tục');
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '',
                'Địa Chỉ VP-Chi Nhánh')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '',
                'Người Liên Lạc')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '',
                'Điện Thoại')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '',
                'Địa Chỉ Giao Hàng')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '',
                'Phân loại khách hàng')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $sttRow . '',
                'Mã QR')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:X$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00'),
                ),
            ]);
            $this->load->library('ciqrcode');

            $rowBegin = $sttRow;
            if (!empty($dtClients)) {
                foreach ($dtClients as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code_group']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name_group']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin",
                        ($value['name_branch']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['zcode']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin",
                        ($value['company']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                        ($value['vat']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['code_xnk']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['name_tax']));
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['bank_account']);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                        $value['name_account'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin",
                        $value['address_bank'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $htmlPaymentMode = '';
                    if ($value['tm_ck'] == 1) {
                        $htmlPaymentMode = 'Tiền mặt';
                    } elseif ($value['tm_ck'] == 2) {
                        $htmlPaymentMode = 'Chuyển khoản';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",
                        $htmlPaymentMode)->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['name_currence']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin",
                        (!empty($value['name_currence']) ? $value['name_currence'] . '-' . 'VNĐ' : ''));
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                        ($value['time_payment']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                        $value['type_contract'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                        (!empty($value['date_renewal']) ? _dhau($value['date_renewal']) : ''))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                        $value['address_branch'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                        $value['firstname'])->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                        $value['phonenumber'])->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
                        $value['addressClient'])->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin",
                        $value['name_status'])->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);

                    $zcode = $value['zcode'];
                    $params['data'] = $zcode;
                    $params['level'] = 'H';
                    $params['size'] = 1;
                    $params['savename'] = FCPATH . 'uploads/clients_qr/' . $zcode . '.png';
                    $this->ciqrcode->generate($params);

                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    $imgQr = './uploads/clients_qr/' . $zcode . '.png';
                    $objDrawing->setName($zcode);
                    $objDrawing->setDescription($zcode);
                    $objDrawing->setPath($imgQr);
                    $objDrawing->setCoordinates('X' . $rowBegin);
                    $objDrawing->setWidth(50);
                    $objDrawing->setHeight(50);
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(5);
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(60);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:X$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("P$rowBegin:P$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('thong_tin_khach_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
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

    public function modal_excel_import()
    {
        $data['title'] = _l('Import Brand bằng File Excel');
        $this->load->view('admin/clients/excel_import', $data);
    }

    public function excel_import_group()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('V');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;
                }
            }

            $dataArray = [];
            $keyNameDepartments = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[1])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã brand</div>';
                    continue;
                }
                $code = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');

                if (empty($value[2])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy tên brand</div>';
                    continue;
                }
                $name = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');
                $classify = (trim($value[3]));
                $certification_group = trim($value[4]);
                $applied_standard = trim($value[5]);;
                $verification_unit = trim($value[6]);;
                $date_start = ($value[7]);
                if (gettype($date_start) == 'double' || gettype($date_start) == 'int') {
                    $date_start = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_start));
                } elseif (gettype($date_start) == 'string') {
                    $date_start = to_sql_date($date_start);
                }

                $date_end = ($value[8]);
                if (gettype($date_end) == 'double' || gettype($date_end) == 'int') {
                    $date_end = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_end));
                } elseif (gettype($date_end) == 'string') {
                    $date_end = to_sql_date($date_end);
                }
                $product_standards = ($value[9]);
                $percent_discount = number_unformat($value[10]);

                $dataArray[] = [
                    'code' => $code,
                    'name' => $name,
                    'classify' => $classify,
                    'certification_group' => $certification_group,
                    'applied_standard' => $applied_standard,
                    'verification_unit' => $verification_unit,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'product_standards' => $product_standards,
                    'percent_discount' => $percent_discount,
                ];
            }
            $count = 0;
            if (!empty($dataArray)) {
                foreach ($dataArray as $key => $value) {
                    $this->db->from('tblcustomers_groups');
                    $this->db->where('code', $value['code']);
                    $dtGroup = $this->db->get()->row_array();
                    if (!empty($dtGroup)) {
                        $this->db->where('id', $dtGroup['id']);
                        $success = $this->db->update('tblcustomers_groups', $value);
                        if ($success) {
                            $count++;
                        }
                    } else {
                        $this->db->insert('tblcustomers_groups', $value);
                        $success = $this->db->insert_id();
                        if ($success) {
                            $count++;
                        }
                    }
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' brand',
                ]
            );
            die();

        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }

    public function brand()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('brand');
        }
        $data['title'] = _l('Brand');
        $this->load->view('admin/clients/brand', $data);
    }

    public function detail_brand()
    {
        $data = $this->input->post();
        if (empty($data['id_parent'])) {
            $data['id_parent'] = 0;
        }
        if (empty($data['id'])) {
            if (!empty($data['date_start'])) {
                $data['date_start'] = to_sql_date($data['date_start']);
            } else {
                $data['date_start'] = null;
            }
            if (!empty($data['date_end'])) {
                $data['date_end'] = to_sql_date($data['date_end']);
            } else {
                $data['date_end'] = null;
            }
            $this->db->insert('tbl_brand', $data);
            $id = $this->db->insert_id();
            $get_code = get_table_where('tbl_brand', array('id' => $id), '', 'row');
            activity_log_v2('client', 'tblcustomers_groups', $id, $get_code->code,
                'Thêm mới brand [' . $get_code->code . ']');
            $message = $id ? _l('added_successfully', _l('Brand')) : '';
            echo json_encode([
                'success' => $id ? true : false,
                'message' => $message,
                'id' => $id,
                'name' => $data['name'],
            ]);
            die();
        } else {
            if (!empty($data['date_start'])) {
                $data['date_start'] = to_sql_date($data['date_start']);
            } else {
                $data['date_start'] = null;
            }
            if (!empty($data['date_end'])) {
                $data['date_end'] = to_sql_date($data['date_end']);
            } else {
                $data['date_end'] = null;
            }
            $this->db->where('id', $data['id']);
            $success = $this->db->update('tbl_brand', $data);
            $message = '';
            if ($success == true) {
                $get_code = get_table_where('tbl_brand', array('id' => $data['id']), '', 'row');
                activity_log_v2('client', 'tbl_brand', $data['id'], $get_code->code,
                    'Cập nhật Brand [' . $get_code->code . ']');
                $message = _l('updated_successfully', _l('Brand'));
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die();
        }
    }

    public function delete_brand($id)
    {
        if (!$id) {
            redirect(admin_url('clients/brand'));
        }
        $get_code = get_table_where('tbl_brand', array('id' => $id), '', 'row');
        activity_log_v2('client', 'tbl_brand', $id, $get_code->code, 'Xóa brand [' . $get_code->code . ']');
        $this->db->where('tbl_brand.id', $id);
        $response = $this->db->delete('tbl_brand');
        if ($response == true) {
            set_alert('success', _l('deleted', _l('Brand')));
        } else {
            set_alert('warning', _l('cong_delete_false'));
        }
        redirect(admin_url('clients/brand'));
    }

    public function edit_brand($id = '')
    {
        $data = [];
        $this->db->where('id', $id);
        $this->db->from('tbl_brand');
        $dtGroup = $this->db->get()->row_array();
        $data['dtGroup'] = $dtGroup;
        $this->load->view('admin/clients/client_brand', $data);
    }

    public function export_excel_brand()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $this->db->select('
                tbl_brand.*,
            ');
        $this->db->from('tbl_brand');
        $dtGroup = $this->db->get()->result_array();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(20);
        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
        $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                    0) : '');
        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $company_vat = get_option('company_vat');
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name' => 'Times New Roman'
            ),
        ]);
        $objPHPExcel->getActiveSheet()->setCellValue('A1',
            ('THÔNG TIN BRAND'))->getStyle("A1")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 18,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(25);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
        $sttRow = 2;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Số Thứ Tự');
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Tên Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Phân Loại');
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '',
            'Nhóm Chứng Nhận')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '',
            'Tiêu Chuẩn Áp Dụng')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '',
            'ĐV Kiểm-Chứng Nhận')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Ngày Bắt Đầu');
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Ngày Tái Tục');
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
            'Tiêu Chuẩn Sản Phẩm')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', '%Chiết Khấu');
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:K$sttRow")->applyFromArray([
            'font' => array(
                'size' => 12,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFF00'),
            ),
        ]);
        $rowBegin = $sttRow;
        if (!empty($dtGroup)) {
            foreach ($dtGroup as $key => $value) {
                $rowBegin++;
                $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
                    $value['code'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['classify']));
                $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['certification_group']);
                $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['applied_standard']);
                $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",
                    $value['verification_unit'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",
                    !empty($value['date_start']) ? _dhau($value['date_start']) : '');
                $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                    !empty($value['date_end']) ? _dhau($value['date_end']) : '');
                $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                    $value['product_standards'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin",
                    $value['percent_discount'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:K$rowBegin")->applyFromArray([
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("D$rowBegin:D$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
                $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:K$rowBegin")->applyFromArray([
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                ]);
            }
        }
        $filename = lang('thong_tin_brand') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth(15);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function modal_excel_import_brand()
    {
        $data['title'] = _l('Import Brand bằng File Excel');
        $this->load->view('admin/clients/excel_import_brand', $data);
    }

    public function excel_import_brand()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('V');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 3][$col] = $value;
                }
            }

            $dataArray = [];
            $keyNameDepartments = [];
            foreach ($arraydata as $key => $value) {

                if (empty($value[1])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy mã brand</div>';
                    continue;
                }
                $code = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');

                if (empty($value[2])) {
                    $errors .= '<div>Dòng [' . $key . '] Không tìm thấy tên brand</div>';
                    continue;
                }
                $name = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[2])), 'UTF-8');
                $classify = (trim($value[3]));
                $certification_group = trim($value[4]);
                $applied_standard = trim($value[5]);;
                $verification_unit = trim($value[6]);;
                $date_start = ($value[7]);
                if (gettype($date_start) == 'double' || gettype($date_start) == 'int') {
                    $date_start = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_start));
                } elseif (gettype($date_start) == 'string') {
                    $date_start = to_sql_date($date_start);
                }

                $date_end = ($value[8]);
                if (gettype($date_end) == 'double' || gettype($date_end) == 'int') {
                    $date_end = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_end));
                } elseif (gettype($date_end) == 'string') {
                    $date_end = to_sql_date($date_end);
                }
                $product_standards = ($value[9]);
                $percent_discount = number_unformat($value[10]);

                $dataArray[] = [
                    'code' => $code,
                    'name' => $name,
                    'classify' => $classify,
                    'certification_group' => $certification_group,
                    'applied_standard' => $applied_standard,
                    'verification_unit' => $verification_unit,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'product_standards' => $product_standards,
                    'percent_discount' => $percent_discount,
                ];
            }
            $count = 0;
            if (!empty($dataArray)) {
                foreach ($dataArray as $key => $value) {
                    $this->db->from('tbl_brand');
                    $this->db->where('code', $value['code']);
                    $dtGroup = $this->db->get()->row_array();
                    if (!empty($dtGroup)) {
                        $this->db->where('id', $dtGroup['id']);
                        $success = $this->db->update('tbl_brand', $value);
                        if ($success) {
                            $count++;
                        }
                    } else {
                        $this->db->insert('tbl_brand', $value);
                        $success = $this->db->insert_id();
                        if ($success) {
                            $count++;
                        }
                    }
                }
            }
            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => 'Thêm mới và cập nhật thành công ' . $count . ' brand',
                ]
            );
            die();

        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }
}
