<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminController extends App_Controller
{
    private $current_db_version;

    public function __construct()
    {
        parent::__construct();

        $this->current_db_version = $this->app->get_current_db_version();

        if ($this->app->is_db_upgrade_required($this->current_db_version)) {
            if ($this->input->post('upgrade_database')) {
                hooks()->do_action('pre_upgrade_database');

                $this->app->upgrade_database();
            }

            die(include_once(VIEWPATH . 'admin/includes/db_update_required.php'));
        }

        hooks()->do_action('pre_admin_init');

        $this->load->model('authentication_model');
        $this->authentication_model->autologin();
        if (!is_staff_logged_in()) {
            if (strpos(current_full_url(), get_admin_uri() . '/authentication') === false) {
                redirect_after_login_to_current_url();
            }

            redirect(admin_url('authentication'));
        }

        if (CI_VERSION != '3.1.10') {
            echo '<h2>Additionally you will need to replace the <b>system</b> folder. We updated Codeigniter to 3.1.10.</h2>';
            echo '<p>From the newest downloaded files upload the <b>system</b> folder to your installation directory.';
            die;
        }

        if (!extension_loaded('mbstring') && (!function_exists('mb_strtoupper') || !function_exists('mb_strtolower'))) {
            die('<h1>"mbstring" PHP extension is not loaded. Enable this extension from cPanel or consult with your hosting provider to assist you enabling "mbstring" extension.</h4>');
        }

        if ($this->uri->segment(3) != 'notifications_check') {
            // In case staff have setup logged in as client - This is important don't change it
            foreach (['client_user_id', 'contact_user_id', 'client_logged_in', 'logged_in_as_client'] as $sk) {
                if ($this->session->has_userdata($sk)) {
                    $this->session->unset_userdata($sk);
                }
            }
        }

        // Update staff last activity
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update(db_prefix() . 'staff', ['last_activity' => date('Y-m-d H:i:s')]);

        $this->load->model('staff_model');

        // Do not check on ajax requests
        if (!$this->input->is_ajax_request()) {
            if (ENVIRONMENT == 'production' && is_admin()) {
                if ($this->config->item('encryption_key') === '') {
                    die('<h1>Encryption key not sent in application/config/app-config.php</h1>For more info visit <a href="https://help.perfexcrm.com/encryption-key-explained/">encryption key explained</a>');
                } elseif (strlen($this->config->item('encryption_key')) != 32) {
                    die('<h1>Encryption key length should be 32 charachters</h1>For more info visit <a href="https://help.perfexcrm.com/encryption-key-explained/">encryption key explained</a>');
                }
            }

            _maybe_system_setup_warnings();

            is_mobile() ? $this->session->set_userdata(['is_mobile' => true]) : $this->session->unset_userdata('is_mobile');
        }

        $currentUser = $this->staff_model->get(get_staff_user_id());

        // Deleted or inactive but have session
        if (!$currentUser || $currentUser->active == 0) {
            $this->authentication_model->logout();
            redirect(admin_url('authentication'));
        }

        $GLOBALS['current_user'] = $currentUser;
        $GLOBALS['language']     = load_admin_language();
        $GLOBALS['locale']       = get_locale_key($GLOBALS['language']);

        init_admin_assets();

        hooks()->do_action('admin_init');

        $vars = [
            'current_user'    => $currentUser,
            'app_language'    => $GLOBALS['language'],
            'locale'          => $GLOBALS['locale'],
            'current_version' => $this->current_db_version,
            'task_statuses'   => $this->tasks_model->get_statuses(),
        ];

        if (!$this->input->is_ajax_request()) {
            $vars['name_menu'] = '';
            $vars['img_menu'] = '';
            $vars['sidebar_menu'] = array(); //sidebar dynamic
            $vars['setup_menu']   = json_decode(get_option('setup_menu_active')); //setup dynamic
            $current_url = explode('admin/', current_full_url());
            $name_url = array_pop($current_url);

            // if (!empty($name_url) && strpos($name_url, 'personnel_assessment/view') !== false) {
            //     // 1. Lấy ID từ URL
            //     preg_match('/\/view\/(\d+)/', $name_url, $matches);
            //     $id_evaluation_employee = $matches[1] ?? null;

            //     if ($id_evaluation_employee) {
            //         // 2. Truy vấn Database
            //         $this->db->where('id', $id_evaluation_employee);
            //         $ktEva = $this->db->get('tbl_evaluation_employee')->row();

            //         // 3. Kiểm tra xem bản ghi có tồn tại không trước khi check type
            //         if ($ktEva) {
            //             if($ktEva->type == 2) {
            //                 $name_url = 'personnel_assessment?type=2';
            //             }
            //             else {
            //                 $name_url = 'personnel_assessment';
            //             }
            //         }
            //     }
            // }
            if (!empty($name_url)) {
                if (strpos($name_url, 'personnel_assessment/detail') !== false || strpos($name_url, 'personnel_assessment/view') !== false) {
                    // 1. Lấy ID từ URL
                    preg_match('/\/(detail|view)\/(\d+)/', $name_url, $matches);
                    $id_evaluation_employee = $matches[2] ?? null;

                    if ($id_evaluation_employee) {
                        // 2. Truy vấn Database
                        $this->db->where('id', $id_evaluation_employee);
                        $ktEva = $this->db->get('tbl_evaluation_employee')->row();

                        // 3. Kiểm tra xem bản ghi có tồn tại không trước khi check type
                        if ($ktEva) {
                            if ($ktEva->type == 2) {
                                $name_url = 'personnel_assessment?type=2';
                            } else {
                                $name_url = 'personnel_assessment';
                            }
                        }
                    } else {
                        if (strpos($name_url, 'type=2') !== false) {
                            $name_url = 'personnel_assessment?type=2';
                        } else {
                            $name_url = 'personnel_assessment';
                        }
                    }
                } elseif (strpos($name_url, 'probationary_assessment/detail') !== false) {
                    preg_match('/\/detail\/(\d+)/', $name_url, $matches);
                    $id_probationary_assessment = $matches[1] ?? null;

                    if ($id_probationary_assessment) {
                        $this->db->where('id', $id_probationary_assessment);
                        $ktProb = $this->db->get('tbl_probationary_assessment')->row();
                        if ($ktProb) {
                            if ($ktProb->type == 2) {
                                $name_url = 'probationary_assessment?type=2';
                            } else {
                                $name_url = 'probationary_assessment';
                            }
                        }
                    } else {
                        if (strpos($name_url, 'type=2') !== false) {
                            $name_url = 'probationary_assessment?type=2';
                        } else {
                            $name_url = 'probationary_assessment';
                        }
                    }
                }
            }

            $vars['name_url'] = $name_url;
            $arr_aside_menu = json_decode(get_option('aside_menu_active'));

//            if (!empty($arr_aside_menu)) {
//                foreach ($arr_aside_menu as $key_aside_menu => $value_aside_menu)
//                {
//                    if(!empty($value_aside_menu->url) && $value_aside_menu->url === $name_url) {
//                        if(!empty($value_aside_menu->children))
//                        {
//                            foreach ($value_aside_menu->children as $key_children => $value_children)
//                            {
//                                array_push($vars['sidebar_menu'], $value_children);
//                                $vars['name_menu'] = _l($value_aside_menu->name);
//                                $vars['img_menu'] = _l($value_aside_menu->img);
//                            }
//                        }
//                    }
//                    else
//                    {
//                        if(!empty($value_aside_menu->children))
//                        {
//                            foreach ($value_aside_menu->children as $key_children => $value_children)
//                            {
//                                if(isset($value_children->url) && isset($name_url) && $value_children->url === $name_url)
//                                {
//                                    foreach ($value_aside_menu->children as $key_aside => $value_aside)
//                                    {
//
//                                        if(!empty($value_aside_menu->not_show_parent)) {
//                                            if($value_aside->url == $name_url) {
//                                                array_push($vars['sidebar_menu'], $value_aside);
//                                                break;
//                                            }
//                                        }
//                                        else {
//                                            array_push($vars['sidebar_menu'], $value_aside);
//                                        }
//                                        $vars['name_menu'] = _l($value_aside_menu->name);
//                                        $vars['img_menu'] = _l($value_aside_menu->img);
//                                    }
//                                    break;
//                                }
//                                if(!empty($value_children->children))
//                                {
//                                    foreach ($value_children->children as $key_aside => $value_aside)
//                                    {
//                                        if(isset($value_aside->url) && isset($name_url) && $value_aside->url === $name_url)
//                                        {
//                                            foreach ($value_aside_menu->children as $key_aside => $value_aside)
//                                            {
//                                                if(!empty($value_aside_menu->not_show_parent)) {
//                                                    if($value_aside->url == $name_url) {
//                                                        array_push($vars['sidebar_menu'], $value_aside);
//                                                    }
//                                                }
//                                                else {
//                                                    array_push($vars['sidebar_menu'], $value_aside);
//                                                }
//                                                $vars['name_menu'] = _l($value_aside_menu->name);
//                                                $vars['img_menu'] = _l($value_aside_menu->img);
//                                            }
//                                            break;
//                                        }
//
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }



            if (!empty($arr_aside_menu)) {
                foreach ($arr_aside_menu as $key_aside_menu => $value_aside_menu) {
                    $is_active_branch = false;

                    // 1. Kiểm tra khớp ở cấp 1 (Menu gốc)
                    if (!empty($value_aside_menu->url) && $value_aside_menu->url === $name_url) {
                        $value_aside_menu->active = true;
                        $is_active_branch = true;
                    }

                    // 2. Kiểm tra các cấp con nếu cấp 1 chưa khớp hoặc cần duyệt sâu hơn
                    if (!empty($value_aside_menu->children)) {
                        foreach ($value_aside_menu->children as $key_children => $value_children) {

                            // Kiểm tra khớp cấp 2
                            if (isset($value_children->url) && $value_children->url === $name_url) {
                                $value_children->active = true;
                                $value_aside_menu->active = true; // Active cha cấp 1
                                $is_active_branch = true;
                            }

                            // Kiểm tra khớp cấp 3 (Trường hợp của bạn: personnel -> group_b_1 -> evaluation)
                            if (!empty($value_children->children)) {
                                foreach ($value_children->children as $key_sub => $value_sub) {
                                    if (isset($value_sub->url) && $value_sub->url === $name_url) {
                                        $value_sub->active = true;      // Active item hiện tại
                                        $value_children->active = true; // Active cha cấp 2 (group_b_1)
                                        $value_aside_menu->active = true; // Active gốc cấp 1 (personnel)
                                        $is_active_branch = true;
                                    }
                                }
                            }
                        }
                    }

                    // 3. Nếu nhánh này active, đổ dữ liệu vào sidebar_menu
                    if ($is_active_branch) {
                        $vars['name_menu'] = _l($value_aside_menu->name);
                        $vars['img_menu']  = _l($value_aside_menu->img);

                        if (!empty($value_aside_menu->children)) {
                            foreach ($value_aside_menu->children as $child) {
                                // Xử lý logic not_show_parent nếu cần
                                if (!empty($value_aside_menu->not_show_parent)) {
                                    if ($child->active) {
                                        array_push($vars['sidebar_menu'], $child);
                                    }
                                } else {
                                    array_push($vars['sidebar_menu'], $child);
                                }
                            }
                        }
                        // Nếu đã tìm thấy nhánh active rồi thì có thể dừng vòng lặp cha để tiết kiệm tài nguyên
                        // break;
                    }
                }
            }
            if(empty($vars['sidebar_menu']))
            {
                if(!empty($arr_aside_menu)) {
                    foreach ($arr_aside_menu as $key_aside_menu => $value_aside_menu)
                    {
                        $string_aside_menu_url = substr($name_url, 0, strlen($value_aside_menu->url));
                        if(!empty($value_aside_menu->url) && !empty($string_aside_menu_url) && $string_aside_menu_url === $value_aside_menu->url )
                        {
                            if(!empty($value_aside_menu->children))
                            {
                                foreach ($value_aside_menu->children as $key_children => $value_children)
                                {
                                    array_push($vars['sidebar_menu'], $value_children);
                                    $vars['name_menu'] = _l($value_aside_menu->name);
                                    $vars['img_menu'] = _l($value_aside_menu->img);
                                }
                                break;
                            }
                        }
                        else
                        {
                            if(!empty($value_aside_menu->children))
                            {
                                foreach ($value_aside_menu->children as $key_children => $value_children)
                                {
                                    if(!empty($value_children->url))
                                    {
                                        $string_url = substr($name_url, 0, strlen($value_children->url));
                                        if(isset($value_children->url) && !empty($string_url) && $string_url === $value_children->url)
                                        {
                                            foreach ($value_aside_menu->children as $key_aside => $value_aside)
                                            {
                                                array_push($vars['sidebar_menu'], $value_aside);
                                                $vars['name_menu'] = _l($value_aside_menu->name);
                                                $vars['img_menu'] = _l($value_aside_menu->img);
                                            }
                                            break;
                                        }
                                    }
                                    if(!empty($value_children->children))
                                    {
                                        foreach ($value_children->children as $key_child => $value_aside)
                                        {
                                            if(!empty($value_aside->url)) {
                                                $string_url_children = substr($name_url, 0, strlen($value_aside->url));
                                                if (isset($value_aside->url) && !empty($string_url_children) && substr($name_url, 0, strlen($value_aside->url)) === $value_aside->url) {
                                                    $value_children->children->{$key_child}->active = true;
                                                    $value_aside_menu->children->{$key_children}->active = true;
                                                    foreach ($value_aside_menu->children as $key_aside => $value_aside) {
                                                        array_push($vars['sidebar_menu'], $value_aside);
                                                        $vars['name_menu'] = _l($value_aside_menu->name);
                                                        $vars['img_menu'] = _l($value_aside_menu->img);
                                                    }
                                                    break;
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $current_url = explode('admin/', current_full_url());
            if(!empty($current_url[1])){
                $ch = substr($current_url[1], 0,6);
                if($ch == 'videos')
                {
                    $type = get_table_where('tbl_videos_groups');
                    foreach ($type as $key => $value) {
                        $videos = get_table_where('tblvideos',array('type'=>$value['id']));
                        if(!empty($videos))
                        {
                            $type[$key] = array_merge($type[$key],array('videos'=>$videos));
                        }else
                        {
                            unset($type[$key]);
                        }
                    }
                    $vars['menu_hau'] = $type;
                }
            }

            // if(empty(is_admin())) {
            //     foreach($vars['sidebar_menu'] as $key => $value) {
            //         if(!empty($value->children)) {
            //             if(!has_permission($value->id, '', 'view') && !has_permission($value->id, '', 'view_own')) {
            //                 unset($vars['sidebar_menu'][$key]);
            //                 continue;
            //             }
            //             foreach($value->children as $keyChild => $valChild) {
            //                 if(!has_permission($keyChild, '', 'view') && !has_permission($keyChild, '', 'view_own')) {
            //                     unset($vars['sidebar_menu'][$key]->children->{$keyChild});
            //                     continue;
            //                 }
            //             }
            //         }
            //         else {
            //             if(!has_permission($value->id, '', 'view') && !has_permission($value->id, '', 'view_own')) {
            //                 unset($vars['sidebar_menu'][$key]);
            //                 continue;
            //             }
            //         }
            //     }
            // }
        }

        /**
         * Autoloaded view variables
         * @var array
         */
        $vars = hooks()->apply_filters('admin_area_auto_loaded_vars', $vars);
        $this->load->vars($vars);

        if (!$this->input->is_ajax_request()) {
            $this->init_quick_actions_links();
        }
    }



    private function init_quick_actions_links()
    {
        $this->app->add_quick_actions_link([
            'name'       => _l('invoice'),
            'permission' => 'invoices',
            'url'        => 'invoices/invoice',
            'position'   => 5,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('estimate'),
            'permission' => 'estimates',
            'url'        => 'estimates/estimate',
            'position'   => 10,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('proposal'),
            'permission' => 'proposals',
            'url'        => 'proposals/proposal',
            'position'   => 15,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('credit_note'),
            'permission' => 'credit_notes',
            'url'        => 'credit_notes/credit_note',
            'position'   => 20,
        ]);


        $this->app->add_quick_actions_link([
            'name'       => _l('client'),
            'permission' => 'customers',
            'url'        => 'clients/client',
            'position'   => 25,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('subscription'),
            'permission' => 'subscriptions',
            'url'        => 'subscriptions/create',
            'position'   => 30,
        ]);


        $this->app->add_quick_actions_link([
            'name'       => _l('project'),
            'url'        => 'projects/project',
            'permission' => 'projects',
            'position'   => 35,
        ]);


        $this->app->add_quick_actions_link([
            'name'            => _l('task'),
            'url'             => '#',
            'custom_url'      => true,
            'href_attributes' => [
                'onclick' => 'new_task();return false;',
            ],
            'permission' => 'tasks',
            'position'   => 40,
        ]);

        $this->app->add_quick_actions_link([
            'name'            => _l('lead'),
            'url'             => '#',
            'custom_url'      => true,
            'permission'      => 'is_staff_member',
            'href_attributes' => [
                'onclick' => 'init_lead(); return false;',
            ],
            'position' => 45,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('expense'),
            'permission' => 'expenses',
            'url'        => 'expenses/expense',
            'position'   => 50,
        ]);


        $this->app->add_quick_actions_link([
            'name'       => _l('contract'),
            'permission' => 'contracts',
            'url'        => 'contracts/contract',
            'position'   => 55,
        ]);


        $this->app->add_quick_actions_link([
            'name'       => _l('kb_article'),
            'permission' => 'knowledge_base',
            'url'        => 'knowledge_base/article',
            'position'   => 60,
        ]);

        $tickets = [
            'name'     => _l('ticket'),
            'url'      => 'tickets/add',
            'position' => 65,
        ];

        if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()) {
            $tickets['permission'] = 'is_staff_member';
        }

        $this->app->add_quick_actions_link($tickets);

        $this->app->add_quick_actions_link([
            'name'       => _l('staff_member'),
            'url'        => 'staff/member',
            'permission' => 'staff',
            'position'   => 70,
        ]);

        $this->app->add_quick_actions_link([
            'name'       => _l('calendar_event'),
            'url'        => 'utilities/calendar?new_event=true&date=' . _d(date('Y-m-d')),
            'permission' => '',
            'position'   => 75,
        ]);
    }
}
