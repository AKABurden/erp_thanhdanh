<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: facebook
Description: Default module manager facebook
Version: 1.0.0
Requires at least: 1.0.0
*/



define('MESSAGER_MODULE_NAME', 'messager');

define('MESSAGER_MODULE_UPLOAD_FOLDER', module_dir_path(MESSAGER_MODULE_NAME, 'uploads'));

$CI = &get_instance();

/**
 * Load the module helper
 */
$CI->load->helper(MESSAGER_MODULE_NAME . '/messager');


/**
 * Register activation module hook
 */
register_activation_hook(MESSAGER_MODULE_NAME, 'messager_activation_hook');

hooks()->add_action('admin_init', 'messager_init_menu_items');


register_language_files(MESSAGER_MODULE_NAME, [MESSAGER_MODULE_NAME]);

function messager_activationy_hook()
{
    require_once(__DIR__ . '/install.php');
}
hooks()->add_filter('hook_messager', 'hook_messager_active');

function hook_messager_active($actions)
{
    $actions[] = '<a href="' . admin_url('messager') . '">' . _l('settings_fb') . '</a>';
    return $actions;
}


function messager_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
        'name'       => _l('fanpage_fb'),
        'permission' => 'is_admin',
        'url'        => 'messager',
        'position'   => 69,
    ]);

    $CI->app_menu->add_sidebar_menu_item('Fanpage Facebook', [
        'name'     => _l('fanpage_fb'),
        'href'     => admin_url('messager'),
        'position' => 40,
        'icon'     => 'fa fa-facebook-square"',
    ]);
}