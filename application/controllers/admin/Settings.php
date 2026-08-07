<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('settings_model');
        $this->preViewSetting = has_permission('settings', '', 'view');
        $this->preEditSetting = has_permission('settings', '', 'edit');
        $this->preDeleteSetting = has_permission('settings', '', 'delete');

        $this->isAdmin = is_admin();

        $this->preViewSetting = true;
        $this->preEditSetting = true;
        $this->preDeleteSetting = true;
        $this->isAdmin = true;
    }

    /* View all settings */
    public function index()
    {
        if (!$this->preViewSetting) {
            access_denied('settings');
        }

        $tab = $this->input->get('group');

        if ($this->input->post()) {
            if (!$this->preEditSetting) {
                access_denied('settings');
            }
            $logo_uploaded = (handle_company_logo_upload() ? true : false);
            $favicon_uploaded = (handle_favicon_upload() ? true : false);
            $signatureUploaded = (handle_company_signature_upload() ? true : false);

            $post_data = $this->input->post();
            $tmpData = $this->input->post(null, false);
            if (!empty($tab) && $tab == 'config_ip') {
                if (isset($post_data['settings']['phone_login_ip'])) {
                    update_option('phone_login_ip', $post_data['settings']['phone_login_ip'], 1);
                }
                update_option('phone_login_active', $post_data['settings']['phone_login_active'], 1);
                update_option('day_login_ip', $post_data['settings']['day_login_ip'], 1);


                $this->db->where('ip !=', 0);
                $this->db->delete('tbl_ip_login');
                if (!empty($post_data['config_ip'])) {
                    foreach ($post_data['config_ip'] as $key => $value) {
                        $this->db->insert('tbl_ip_login', ['ip' => $value]);
                    }
                }
                set_alert('success', _l('settings_updated'));
                redirect(admin_url('settings?group=' . $tab), 'refresh');
            }
            if (!empty($tab) && $tab == 'dashboard_srceen') {
                if (isset($post_data['settings']['date_dashboard_srceen_production'])) {
                    $post_data['settings']['date_dashboard_srceen_production'] = to_sql_date($post_data['settings']['date_dashboard_srceen_production']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_production'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_export'])) {
                    $post_data['settings']['date_dashboard_srceen_export'] = to_sql_date($post_data['settings']['date_dashboard_srceen_export']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_export'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_sales'])) {
                    $post_data['settings']['date_dashboard_srceen_sales'] = to_sql_date($post_data['settings']['date_dashboard_srceen_sales']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_sales'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_planning'])) {
                    $post_data['settings']['date_dashboard_srceen_planning'] = to_sql_date($post_data['settings']['date_dashboard_srceen_planning']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_planning'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_dxnb'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_dxnb'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_dxnb']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_dxnb'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_ckhq'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_ckhq'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_ckhq']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_ckhq'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_hdmckk'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_hdmckk'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_hdmckk']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_hdmckk'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_ycc'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_ycc'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_ycc']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_ycc'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_hdbs'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_hdbs'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_hdbs']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_hdbs'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_accounting_dxtc'])) {
                    $post_data['settings']['date_dashboard_srceen_accounting_dxtc'] = to_sql_date($post_data['settings']['date_dashboard_srceen_accounting_dxtc']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_accounting_dxtc'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_warehouse_import'])) {
                    $post_data['settings']['date_dashboard_srceen_warehouse_import'] = to_sql_date($post_data['settings']['date_dashboard_srceen_warehouse_import']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_warehouse_import'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_warehouse_import_kh'])) {
                    $post_data['settings']['date_dashboard_srceen_warehouse_import_kh'] = to_sql_date($post_data['settings']['date_dashboard_srceen_warehouse_import_kh']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_warehouse_import_kh'] = '';
                }
                if (isset($post_data['settings']['date_dashboard_srceen_purchases_internal'])) {
                    $post_data['settings']['date_dashboard_srceen_purchases_internal'] = to_sql_date($post_data['settings']['date_dashboard_srceen_purchases_internal']);
                } else {
                    $post_data['settings']['date_dashboard_srceen_purchases_internal'] = '';
                }
                update_option('check_update_dashboard', get_option('check_update_dashboard') + 1);
                sendSocket(['data' => 123], [], 'update_dashboard');
            }
            if (!empty($tab) && $tab == 'production_report') {
                $this->db->where('tbl_setting_production_report.id >', 0);
                $this->db->delete('tbl_setting_production_report');
                $this->db->where('tbl_setting_production_report_inspection_criteria.id >', 0);
                $this->db->delete('tbl_setting_production_report_inspection_criteria');
                // $this->db->where('tbl_setting_production_report_roles.id >', 0);
                // $this->db->delete('tbl_setting_production_report_roles');
                $config_production_report = $this->db->get('tbl_process')->result_array();
                foreach ($config_production_report as $key => $v) {
                    // if ($v['id'] == 1 || $v['id'] == 2) {
                    //     continue;
                    // }
                    $ins = [];
                    $ins['id_role'] = $post_data['role_id'][$v['id']];
                    $ins['id_process'] = $v['id'];
                    $this->db->insert('tbl_setting_production_report', $ins);
                    $id_insert = $this->db->insert_id();
                    if (!empty($id_insert)) {
                        if (!empty($post_data['inspection_criteria'][$v['id']])) {
                            foreach ($post_data['inspection_criteria'][$v['id']] as $kk => $vv) {
                                $ins_detail = [];
                                $ins_detail['id_setting_production_report'] = $id_insert;
                                $ins_detail['id_inspection_criteria'] = $vv;
                                $this->db->insert('tbl_setting_production_report_inspection_criteria', $ins_detail);
                            }
                        }
                        // if (!empty($post_data['role_id'][$v['id']])) {
                        //     foreach ($post_data['role_id'][$v['id']] as $kk => $vv) {
                        //         $ins_detail = [];
                        //         $ins_detail['id_setting_production_report'] = $id_insert;
                        //         $ins_detail['id_role'] = $vv;
                        //         $this->db->insert('tbl_setting_production_report_roles', $ins_detail);
                        //     }
                        // }
                    }
                }
                set_alert('success', _l('settings_updated'));
                redirect(admin_url('settings?group=' . $tab), 'refresh');
            }
            if (!empty($tab) && $tab == 'salary') {
                $countPhuCap = !empty($post_data['countPhuCap']) ? $post_data['countPhuCap'] : [];
                $arrPc = [];
                if (!empty($countPhuCap)) {
                    foreach ($countPhuCap as $key => $value) {
                        $title = $post_data['title'][$value];
                        $id_pc = !empty($post_data['id_pc'][$value]) ? $post_data['id_pc'][$value] : 0;
                        $amount = number_unformat($post_data['amount'][$value]);
                        if (empty($title)) {
                            continue;
                        }
                        $arrPc[] = [
                            'id' => !empty($id_pc) ? $id_pc : 0,
                            'category_id' => $title,
                            'amount' => $amount,
                            'date_created' => date('Y-m-d H:i:s'),
                            'staff_id' => get_staff_user_id(),
                        ];
                    }
                }
                if (!empty($arrPc)) {
                    $this->db->where('id !=', 0);
                    $this->db->delete('tbl_salary_allowance');

                    $this->db->insert_batch('tbl_salary_allowance', $arrPc);
                }

                $countGiamTru = !empty($post_data['countGiamTru']) ? $post_data['countGiamTru'] : [];
                $arrGt = [];
                if (!empty($countGiamTru)) {
                    foreach ($countGiamTru as $key => $value) {
                        $title = $post_data['title_gt'][$value];
                        $id_gt = !empty($post_data['id_gt'][$value]) ? $post_data['id_gt'][$value] : 0;
                        $amount = number_unformat($post_data['amount_gt'][$value]);
                        if (empty($title)) {
                            continue;
                        }
                        $arrGt[] = [
                            'id' => !empty($id_gt) ? $id_gt : 0,
                            'category_id' => $title,
                            'amount' => $amount,
                            'date_created' => date('Y-m-d H:i:s'),
                            'staff_id' => get_staff_user_id(),
                        ];
                    }
                }
                if (!empty($arrGt)) {
                    $this->db->where('id !=', 0);
                    $this->db->delete('tbl_salary_reduce');

                    $this->db->insert_batch('tbl_salary_reduce', $arrGt);
                }
                //                set_alert('success', _l('settings_updated'));
                //                redirect(admin_url('settings?group=' . $tab), 'refresh');
            }

            if (isset($post_data['settings']['email_header'])) {
                $post_data['settings']['email_header'] = $tmpData['settings']['email_header'];
            }

            if (isset($post_data['settings']['email_footer'])) {
                $post_data['settings']['email_footer'] = $tmpData['settings']['email_footer'];
            }

            if (isset($post_data['settings']['email_signature'])) {
                $post_data['settings']['email_signature'] = $tmpData['settings']['email_signature'];
            }

            if (isset($post_data['settings']['smtp_password'])) {
                $post_data['settings']['smtp_password'] = $tmpData['settings']['smtp_password'];
            }

            if (isset($post_data['settings']['note_quotes'])) {
                $post_data['settings']['note_quotes'] = $tmpData['settings']['note_quotes'];
            }

            if (isset($post_data['settings']['salary_minimum'])) {
                $post_data['settings']['salary_minimum'] = number_unformat($tmpData['settings']['salary_minimum']);
            }

            if (isset($post_data['settings']['salary_minimum_new'])) {
                $post_data['settings']['salary_minimum_new'] = number_unformat($tmpData['settings']['salary_minimum_new']);
            }

            if (isset($post_data['settings']['vat_dauvao'])) {
                $post_data['settings']['vat_dauvao'] = number_unformat($tmpData['settings']['vat_dauvao']);
            }

            if (isset($post_data['settings']['money_vat'])) {
                $post_data['settings']['money_vat'] = number_unformat($tmpData['settings']['money_vat']);
            }

            if (isset($post_data['settings']['money_reduce'])) {
                $post_data['settings']['money_reduce'] = number_unformat($tmpData['settings']['money_reduce']);
            }

            if (isset($post_data['settings']['rice_money'])) {
                $post_data['settings']['rice_money'] = number_unformat($tmpData['settings']['rice_money']);
            }

            if (isset($post_data['settings']['rice_money_audit'])) {
                $post_data['settings']['rice_money_audit'] = number_unformat($tmpData['settings']['rice_money_audit']);
            }

            if (isset($post_data['settings']['coefficient'])) {
                $post_data['settings']['coefficient'] = ($tmpData['settings']['coefficient']);
            }

            if (isset($post_data['settings']['coefficient_sunday'])) {
                $post_data['settings']['coefficient_sunday'] = ($tmpData['settings']['coefficient_sunday']);
            }

            if (isset($post_data['settings']['coefficient_holiday'])) {
                $post_data['settings']['coefficient_holiday'] = ($tmpData['settings']['coefficient_holiday']);
            }

            $success = $this->settings_model->update($post_data);
            if (!empty($success)) {
                if (!empty($tab) && $tab == 'download_app' && !empty($send_tb)) {
                    $listPlayerid = $this->db->get('tblplayer_id')->result_array();

                    foreach ($listPlayerid as $key => $value) {
                        $_data = [];
                        $_data['message'] = 'Phiên bản ' . get_option('version_app') . ' vừa mới cập nhật , vui lòng update bản cài đặt mới';
                        $_data['title'] = 'Cập nhật phiên bản mới';
                        $_data['staff_id'] = $value['staffid'];
                        $_data['staff_name'] = get_staff_full_name($value['staffid']);
                        $_data['user_id'] = [$value['player_id']];

                        SendOnesignal($_data, 'info_vesion');
                    }
                }
            }
            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }

            if ($logo_uploaded || $favicon_uploaded) {
                set_debug_alert(_l('logo_favicon_changed_notice'));
            }

            // Do hard refresh on general for the logo
            if ($tab == 'general') {
                redirect(admin_url('settings?group=' . $tab), 'refresh');
            } elseif ($signatureUploaded) {
                redirect(admin_url('settings?group=pdf&tab=signature'));
            } else {
                $redUrl = admin_url('settings?group=' . $tab);
                if ($this->input->get('active_tab')) {
                    $redUrl .= '&tab=' . $this->input->get('active_tab');
                }
                redirect($redUrl);
            }
        }

        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $this->load->model('leads_model');
        $this->load->model('currencies_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['ticket_priorities'] = $this->tickets_model->get_priority();
        $data['ticket_priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['roles'] = $this->roles_model->get();
        $data['leads_sources'] = $this->leads_model->get_source();
        $data['leads_statuses'] = $this->leads_model->get_status();
        $data['title'] = _l('options');

        $data['admin_tabs'] = ['update', 'info'];


        if (!$tab || (!in_array($tab, $data['admin_tabs']) && !$this->isAdmin)) {
            $tab = 'general';
        }
        if (!empty($tab) && $tab == 'config_ip') {
            $data['config_ip'] = $this->db->get('tbl_ip_login')->result_array();
        }

        if (!empty($tab) && $tab == 'salary') {
            $data['salary_allowance'] = $this->db->get('tbl_salary_allowance')->result_array();
            $data['salary_reduce'] = $this->db->get('tbl_salary_reduce')->result_array();
        }
        if (!empty($tab) && $tab == 'production_report') {
            // $data['config_production_report'] = $this->db->get('tbl_process')->result_array();
            $this->db->select('tbl_process.*,tbl_setting_production_report.id_role,tbl_setting_production_report.id as idd');
            $this->db->join('tbl_setting_production_report', 'tbl_setting_production_report.id_process = tbl_process.id', 'left');
            $this->db->order_by('tbl_process.id', 'asc');
            $data['config_production_report'] = $this->db->get('tbl_process')->result_array();
            foreach ($data['config_production_report'] as $key => $value) {
                $this->db->select('tbl_setting_production_report_inspection_criteria.*');
                $this->db->where('tbl_setting_production_report_inspection_criteria.id_setting_production_report', $value['idd']);
                $setting_production_report_inspection_criteria = $this->db->get('tbl_setting_production_report_inspection_criteria')->result_array();
                $data['config_production_report'][$key]['setting_production_report_inspection_criteria'] = [];
                foreach ($setting_production_report_inspection_criteria as $k => $v) {
                    $data['config_production_report'][$key]['setting_production_report_inspection_criteria'][] = $v['id_inspection_criteria'];
                }

                // $this->db->select('tbl_setting_production_report_roles.*');
                // $this->db->where('tbl_setting_production_report_roles.id_setting_production_report', $value['idd']);
                // $setting_production_report_roles = $this->db->get('tbl_setting_production_report_roles')->result_array();
                // $data['config_production_report'][$key]['setting_production_report_roles'] = [];
                // foreach ($setting_production_report_roles as $k => $v) {
                //     $data['config_production_report'][$key]['setting_production_report_roles'][] = $v['id_role'];
                // }
                // 
            }
            // $this->db->where('tblroles.type', 0);
            // $this->db->where('tblroles.active_role', 1);
            // $data['data_roles'] = $this->db->get('tblroles')->result_array();
            $data['data_roles'] = [];
            $this->get_parent(0, $data['data_roles']);
            $data['data_inspection_criteria'] = $this->db->get('tbl_inspection_criteria')->result_array();
        }
        $data['tabs'] = $this->app_tabs->get_settings_tabs();
        if (!in_array($tab, $data['admin_tabs'])) {
            $data['tab'] = $this->app_tabs->filter_tab($data['tabs'], $tab);
        } else {
            // Core tabs are not registered
            $data['tab']['slug'] = $tab;
            $data['tab']['view'] = 'admin/settings/includes/' . $tab;
        }

        if (!$data['tab']) {
            show_404();
        }

        if ($data['tab']['slug'] == 'update') {
            if (!extension_loaded('curl')) {
                $data['update_errors'][] = 'CURL Extension not enabled';
                $data['latest_version'] = 0;
                $data['update_info'] = json_decode('');
            } else {
                $data['update_info'] = $this->app->get_update_info();
                if (strpos($data['update_info'], 'Curl Error -') !== false) {
                    $data['update_errors'][] = $data['update_info'];
                    $data['latest_version'] = 0;
                    $data['update_info'] = json_decode('');
                } else {
                    $data['update_info'] = json_decode($data['update_info']);
                    $data['latest_version'] = $data['update_info']->latest_version;
                    $data['update_errors'] = [];
                }
            }

            if (!extension_loaded('zip')) {
                $data['update_errors'][] = 'ZIP Extension not enabled';
            }

            $data['current_version'] = $this->app->get_current_db_version();
        }

        $data['staff'] = $this->site_model->getStaff();
        $data['contacts_permissions'] = get_contact_permissions();
        $data['payment_gateways'] = $this->payment_modes_model->get_payment_gateways(true);

        $this->load->view('admin/settings/all', $data);
    }
    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
            $this->db->where('tblroles.type', 0);
            $this->db->where('tblroles.active_role', 1);
            $current_level = $this->db->get('tblroles')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_category, $current_level[$key]);
                    $this->get_parent($value['roleid'], $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }
    public function delete_tag($id)
    {
        if (!$id) {
            redirect(admin_url('settings?group=tags'));
        }

        if (!$this->preDeleteSetting) {
            access_denied('settings');
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tags');
        $this->db->where('tag_id', $id);
        $this->db->delete(db_prefix() . 'taggables');

        redirect(admin_url('settings?group=tags'));
    }

    public function remove_signature_image()
    {
        if (!$this->preDeleteSetting) {
            access_denied('settings');
        }

        $sImage = get_option('signature_image');
        if (file_exists(get_upload_path_by_type('company') . '/' . $sImage)) {
            unlink(get_upload_path_by_type('company') . '/' . $sImage);
        }

        update_option('signature_image', '');

        redirect(admin_url('settings?group=pdf&tab=signature'));
    }

    /* Remove company logo from settings / ajax */
    public function remove_company_logo($type = '')
    {
        hooks()->do_action('before_remove_company_logo');

        if (!$this->preDeleteSetting) {
            access_denied('settings');
        }

        $logoName = get_option('company_logo');
        if ($type == 'dark') {
            $logoName = get_option('company_logo_dark');
        }

        $path = get_upload_path_by_type('company') . '/' . $logoName;
        if (file_exists($path)) {
            unlink($path);
        }

        update_option('company_logo' . ($type == 'dark' ? '_dark' : ''), '');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function remove_favicon()
    {
        hooks()->do_action('before_remove_favicon');
        if (!$this->preDeleteSetting) {
            access_denied('settings');
        }
        if (file_exists(get_upload_path_by_type('company') . '/' . get_option('favicon'))) {
            unlink(get_upload_path_by_type('company') . '/' . get_option('favicon'));
        }
        update_option('favicon', '');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function delete_option($name)
    {
        if (!$this->preDeleteSetting) {
            access_denied('settings');
        }

        echo json_encode([
            'success' => delete_option($name),
        ]);
    }
}
