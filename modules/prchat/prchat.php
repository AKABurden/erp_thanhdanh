<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex CRM Employee Chat
Description: Chat for Perfex CRM
Version: 1.1.3
Author: Aleksandar Stojanov
Author URI: https://aleksandarstojanov.com
Requires at least: 2.3.2
*/

define('PR_CHAT_MODULE_NAME', 'prchat');
define('PR_CHAT_MODULE_UPLOAD_FOLDER', module_dir_path(PR_CHAT_MODULE_NAME, 'uploads'));

$CI = &get_instance();

/**
 * Register the activation chat
 */
register_activation_hook(PR_CHAT_MODULE_NAME, 'prchat_activation_hook');

/**
 * The activation function
 */
function prchat_activation_hook()
{
    require(__DIR__ .'/install.php');
}

/**
 * Register chat language files
 */
register_language_files(PR_CHAT_MODULE_NAME, ['chat']);

/**
 * Load the chat helper
 */
$CI->load->helper(PR_CHAT_MODULE_NAME.'/prchat');
