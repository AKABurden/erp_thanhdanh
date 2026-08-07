<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_admin_head', 'pr_chat_add_head_components');
hooks()->add_action('app_admin_footer', 'pr_chat_init_checkView');
hooks()->add_action('app_admin_footer', 'pr_chat_load_js');
hooks()->add_action('after_pusher_cluster_option', 'pr_chat_pusher_options');
hooks()->add_action('app_admin_head', 'pr_chat_add_js_before_admin_render');
hooks()->add_filter('migration_tables_to_replace_old_links', 'pr_chat_migration_tables_to_replace_old_links');
hooks()->add_action('admin_init', 'prchat_add_settings_tab');

if (!function_exists('prchat_add_settings_tab_ch')) {
    function prchat_add_settings_tab_ch()
    {
        $CI = &get_instance();
        $CI->app_tabs->add_settings_tab('prchat-settings', [
            'name'     => '' . _l('chat_settings_name') . '',
            'view'     => 'prchat/prchat_settings',
            'position' => 36,
        ]);
    }
}
if (!function_exists('pr_chat_load_js_ch')) {
    function pr_chat_load_js_ch()
    {
        echo '<script src="' . module_dir_url('prchat', 'assets/js/pr-chat.js') . '"></script>';
    }
}
/**
 * Function that will inject the chat messages tables when user changing domain and need to replace old links.
 * @param  Array $tables
 * @return array
 */
if (!function_exists('pr_chat_migration_tables_to_replace_old_links_ch')) {
    function pr_chat_migration_tables_to_replace_old_links_ch($tables)
    {
        $tables[] = [
            'table' => db_prefix() . 'chatmessages',
            'field' => 'message',
        ];

        return $tables;
    }
}
/**
 * Injects chat CSS
 * @return null
 */
if (!function_exists('pr_chat_add_head_components_ch')) {
    function pr_chat_add_head_components_ch()
    {
        echo '<link href="' . base_url('modules/prchat/assets/css/chat_styles.css') . '" rel="stylesheet">';
    }
}
/**
 * Inject chat JS plugins
 */
if (!function_exists('pr_chat_add_js_before_admin_render_ch')) {
    function pr_chat_add_js_before_admin_render_ch()
    {
        echo '<script src="' . base_url('modules/prchat/assets/js/jscolor.js') . '"></script>';
        echo '<script src="' . base_url('modules/prchat/assets/js/emoparser.js') . '"></script>';
    }
}
/**
 * Loads the chat view
 * @return null
 */
if (!function_exists('pr_chat_init_checkView_ch')) {
    function pr_chat_init_checkView_ch()
    {
        $CI = &get_instance();
        $CI->load->model('prchat/prchat_model', 'chat_model');
        $result   = $CI->chat_model->getUnread();

        echo $CI->load->view('prchat/initViewCheck', ['unreadMessages' => $result], true);
    }
}
/**
 * Function that will check chat URL images and will convert to link
 * @param  string $string
 * @return string
 */
if (!function_exists('pr_chat_convertLinkImageToString_ch')) {
    function pr_chat_convertLinkImageToString_ch($string, $sender_id, $reciever_id)
    {
        $regexImg = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
        // $regexFile = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
        if (preg_match_all($regexImg, $string)) {
            return $string;
        }
        return $string;
    }
}
if (!function_exists('pr_chat_CheckLinkImageToString_ch')) {
    function pr_chat_CheckLinkImageToString_ch($string, $sender_id, $reciever_id)
    {
        // $regexImg = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
        // // $regexFile = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
        // if (preg_match_all($regexImg, $string)) {
        //     return 'img';
        // }else
        // {
        //     return 'text';
        // }
        return mime_content_type(str_replace("http://192.168.1.178/FOSOERP/modules/prchat/uploads/","",$string));
    }
}
/**
 * Get chat color by user id
 * @param  mixed $id
 * @param  mixed $name
 * @return mixed
 */
if (!function_exists('pr_get_chat_color_ch')) {
    function pr_get_chat_color_ch($id, $name)
    {
        $CI = &get_instance();

        if ($CI->db->field_exists('value', db_prefix() . 'chatsettings')) {
            return pr_get_chat_option($id, $name);
        } else {
            $CI->db->select('chat_color');
            $CI->db->where('user_id', $id);
        }
        $result = $CI->db->get(db_prefix() . 'chatsettings')->row();
        if (!$result) {
            return '';
        }

        return $result->chat_color;
    }
}
/**
 * Get chat get chat color on subscribe
 * @param  mixed $id
 * @param  mixed $name
 * @return mixed
 */
if (!function_exists('pr_get_chat_option_ch')) {
    function pr_get_chat_option_ch($id, $name)
    {
        $CI = &get_instance();
        $CI->db->select('value');
        $CI->db->where('name', $name);
        $CI->db->where('user_id', $id);

        $result = $CI->db->get(db_prefix() . 'chatsettings')->row();

        if (!$result) {
            return '';
        }

        return $result->value;
    }
}
/**
 * Function that will check check if current message contains image
 * @param  string $string
 * @return string
 */
if (!function_exists('prchat_checkMessageIfImageExists_ch')) {
    function prchat_checkMessageIfImageExists_ch($message)
    {
        $regexImg = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
        if (preg_match_all($regexImg, $message)) {
            return true;
        } else {
            return false;
        }
    }
}
/**
 * Check if message has any picutre links containing
 * @param  string $image
 * @return string
 */
if (!function_exists('getImageFullName_ch')) {
    function getImageFullName_ch($image)
    {
        $url_arr = explode('/', $image);
        $ct = count($url_arr);
        $name = $url_arr[$ct - 1];
        $name_div = explode('.', $name);
        $ct_dot = count($name_div);

        return $name;
    }
}
function get_staff_full_name_chat($userid = '')
    {
        $tmpStaffUserId = get_staff_user_id();
        if ($userid == '' || $userid == $tmpStaffUserId) {
            if (isset($GLOBALS['current_user'])) {
                return $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname;
            }
            $userid = $tmpStaffUserId;
        }

        $CI = &get_instance();


        $CI->db->where('staffid', $userid);
        $staff = $CI->db->select('firstname,lastname')->from(db_prefix() . 'staff')->get()->row();

        return $staff ? $staff->firstname . ' ' . $staff->lastname : '';
    }
