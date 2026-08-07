<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_admin_head', 'pr_chat_add_head_components');
hooks()->add_action('app_admin_footer', 'pr_chat_init_checkView');
hooks()->add_action('app_admin_footer', 'pr_chat_load_js');
hooks()->add_action('after_pusher_cluster_option', 'pr_chat_pusher_options');
hooks()->add_action('app_admin_head', 'pr_chat_add_js_before_admin_render');
hooks()->add_filter('migration_tables_to_replace_old_links', 'pr_chat_migration_tables_to_replace_old_links');
hooks()->add_action('admin_init', 'prchat_add_settings_tab');


function prchat_add_settings_tab()
{
 $CI = & get_instance();
 $CI->app_tabs->add_settings_tab('prchat-settings', [
   'name'     => ''._l('chat_settings_name').'',
   'view'     => 'prchat/prchat_settings',
   'position' => 36,
]);
}

function pr_chat_load_js()
{
    echo '<script src="'.module_dir_url('prchat', 'assets/js/pr-chat.js').'"></script>';
}

/**
 * Function that will inject the chat messages tables when user changing domain and need to replace old links.
 * @param  Array $tables
 * @return array
 */

function pr_chat_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
        'table' => db_prefix().'chatmessages',
        'field' => 'message',
    ];

    return $tables;
}

/**
 * Injects chat CSS
 * @return null
 */
function pr_chat_add_head_components()
{
    echo '<link href="' . base_url('modules/prchat/assets/css/chat_styles.css') . '" rel="stylesheet">';
}

/**
 * Inject chat JS plugins
 */
function pr_chat_add_js_before_admin_render()
{
    echo '<script src="' . base_url('modules/prchat/assets/js/jscolor.js') . '"></script>';
    echo '<script src="' . base_url('modules/prchat/assets/js/emoparser.js') . '"></script>';
}

/**
 * Loads the chat view
 * @return null
 */
function pr_chat_init_checkView()
{
    $CI = &get_instance();
    $CI->load->model('prchat/prchat_model', 'chat_model');
    $result   = $CI->chat_model->getUnread();

    echo $CI->load->view('prchat/initViewCheck', ['unreadMessages'=>$result], true);
}

/**
 * Function that will check chat URL images and will convert to link
 * @param  string $string
 * @return string
 */
function pr_chat_convertLinkImageToString($string, $sender_id, $reciever_id)
{
    $regexImg = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';

    if (preg_match_all($regexImg, $string)) {
        $string = preg_replace($regexImg, '<a href="' . htmlspecialchars('$0') . '" data-lightbox="pr-chat-image-'.$sender_id.'-'.$reciever_id.'"><img class="convertedImage" style="width:100%;height:100%;padding-top:2px;" src="' . htmlspecialchars('$0') . '"/></a>', $string);
    }

    return $string;
}

/**
 * Get chat color by user id
 * @param  mixed $id
 * @param  mixed $name
 * @return mixed
 */
function pr_get_chat_color($id, $name)
{
    $CI = & get_instance();

    if ($CI->db->field_exists('value', db_prefix().'chatsettings')) {
        return pr_get_chat_option($id, $name);
    } else {
        $CI->db->select('chat_color');
        $CI->db->where('user_id', $id);
    }
    $result = $CI->db->get(db_prefix().'chatsettings')->row();
    if (!$result) {
        return '';
    }

    return $result->chat_color;
}

/**
 * Get chat get chat color on subscribe
 * @param  mixed $id
 * @param  mixed $name
 * @return mixed
 */
function pr_get_chat_option($id, $name)
{
    $CI = & get_instance();
    $CI->db->select('value');
    $CI->db->where('name', $name);
    $CI->db->where('user_id', $id);

    $result = $CI->db->get(db_prefix().'chatsettings')->row();

    if (!$result) {
        return '';
    }

    return $result->value;
}

/**
 * Function that will check check if current message contains image
 * @param  string $string
 * @return string
 */
function prchat_checkMessageIfImageExists($message)
{
    $regexImg = '/(http|https)\:\/\/(([a-zA-Z]{1})|([a-zA-Z]{1}[a-zA-Z]{1})|([a-zA-Z]{1}[0-9]{1})|([0-9]{1}[a-zA-Z]{1})|([a-zA-Z0-9][a-zA-Z0-9-_]{1,61}[a-zA-Z0-9]))\.([a-zA-Z]{2,6}|[a-zA-Z0-9-]{2,30}\.[a-zA-Z]{2,3})+.(\/\S*)?(gif|jpg|jpeg|tiff|png|swf)(\/\S*)?/m';
    if (preg_match_all($regexImg, $message)) {
      return true;
    } else {
        return false;
    }
}
/**
 * Check if message has any picutre links containing
 * @param  string $image
 * @return string
 */
function getImageFullName($image){
  $url_arr = explode ('/', $image);
  $ct = count($url_arr);
  $name = $url_arr[$ct-1];
  $name_div = explode('.', $name);
  $ct_dot = count($name_div);

  return $name;
}
