<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Register module activation hook
 * @param  string $module   module system name
 * @param  mixed $function  function for the hook
 * @return mixed
 */
function register_activation_hook($module, $function)
{
    hooks()->add_action('activate_' . $module . '_module', $function);
}

/**
 * Register module deactivation hook
 * @param  string $module   module system name
 * @param  mixed $function  function for the hook
 * @return mixed
 */
function register_deactivation_hook($module, $function)
{
    hooks()->add_action('deactivate_' . $module . '_module', $function);
}

/**
 * Register module uninstall hook
 * @param  string $module   module system name
 * @param  mixed $function  function for the hook
 * @return mixed
 */
function register_uninstall_hook($module, $function)
{
    hooks()->add_action('uninstall_' . $module . '_module', $function);
}

/**
 * Register merge fields for specific feature
 * @param  mixed $for
 * The $for parameter should be array of loadeable libraries compatible for merge field e.q. in module_name/libraries create folder merge_fields
 * then create class Module_name_merge_fields.php, in this case, you pass like array('module_name/merge_fields/module_name_merge_fields')
 * @return null
 */
function register_merge_fields($for)
{
    get_instance()->app_merge_fields->register($for);
}

/**
 * Register module cron task, the cron task is executed after the core cron tasks are finished
 * @param  mixed $function  function/class parameter for the hook
 * @return null
 */
function register_cron_task($function)
{
    hooks()->add_action('after_cron_run', $function);
}

/**
 * Module list URL for admin area
 * @return string
 */
function modules_list_url()
{
    return admin_url('modules');
}

/**
 * Register payment gateway
 * @param  string $id     the ID of the payment gateway
 * @param  string $module module system name
 * @return null
 */
function register_payment_gateway($id, $module)
{
    $CI = &get_instance();

    if (!class_exists('payment_modes_model', false)) {
        $CI->load->model('payment_modes_model');
    }

    $CI->payment_modes_model->add_payment_gateway($id, $module);
}

/**
 * Register active customers area theme hook to initialize CSS/Javascript assets
 * This function should be called only once from the theme functions.php file
 * @param  string $function function to call
 * @return boolean
 */
function register_theme_assets_hook($function)
{
    if (hooks()->has_action('app_client_assets', $function)) {
        return false;
    }

    return hooks()->add_action('app_client_assets', $function, 1);
}

/**
 * Module views path
 * e.q. modules/module_name/views
 * @param  string $module module system name
 * @param  string $concat append string to the path
 * @return string
 */
function module_views_path($module, $concat = '')
{
    return module_dir_path($module) . 'views/' . $concat;
}

/**
 * Module libraries path
 * e.q. modules/module_name/libraries
 * @param  string $module module name
 * @param  string $concat append additional string to the path
 * @return string
 */
function module_libs_path($module, $concat = '')
{
    return module_dir_path($module) . 'libraries/' . $concat;
}

/**
 * Module directory absolute path
 * @param  string $module module system name
 * @param  string $concat append additional string to the path
 * @return string
 */
function module_dir_path($module, $concat = '')
{
    return APP_MODULES_PATH . $module . '/' . $concat;
}

/**
 * Module URL
 * e.q. https://crm-installation.com/module_name/
 * @param  string $module  module system name
 * @param  string $segment additional string to append to the URL
 * @return string
 */
function module_dir_url($module, $segment = '')
{
    return site_url(basename(APP_MODULES_PATH) . '/' . $module . '/' . ltrim($segment, '/'));
}

/**
 * Register module language files to support custom_lang.php file
 * @param  string $module    module system name
 * @param  array  $languages array of language file names without the _lang.php
 * @return null
 */
function register_language_files($module, $languages = [])
{
    // To use like register_language_files(THEME_STYLE_MODULE_NAME);
    // Without passing the second parameter if it's one language file the same like the module name
    if (is_null($languages) || count($languages) === 0) {
        $languages = [$module];
    }

    $languageLoader = function ($language) use ($languages, $module) {
        $CI = &get_instance();

        $path = APP_MODULES_PATH . $module . '/language/' . $language . '/';
        foreach ($languages as $file_name) {
            $file_path = $path . $file_name . '_lang' . '.php';
            if (file_exists($file_path)) {
                $CI->lang->load($module . '/' . $file_name, $language);
            } elseif ($language != 'english' && !file_exists($file_path)) {
                /**
                 * The module language is not yet translated nor exists in the language that the customer is using
                 * For this reason we will load the english language
                 */
                $CI->lang->load($module . '/' . $file_name, 'english');
            }
        }
        if (file_exists($path . 'custom_lang.php')) {
            $CI->lang->load($module . '/custom', $language);
        }
    };

    hooks()->add_action('after_load_admin_language', $languageLoader);
    hooks()->add_action('after_load_client_language', $languageLoader);
}

/**
 * List of uninstallable modules
 * In most cases these are the default modules that comes with the installation
 * @return array
 */
function uninstallable_modules()
{
    return ['theme_style', 'menu_setup', 'backup', 'surveys', 'goals'];
}

/**
 *  When an action hook is deprecated, the hooks()->do_action() call is replaced with hooks()->do_action_deprecated(),
 *  which triggers a deprecation notice and then fires the original hook.
 * @param  string  $tag          The name of the action hook
 * @param  array   $args         Array of additional function arguments to be passed to hooks()->do_action().
 * @param  string  $version      The version that deprecated the hook.
 * @param  string  $replacement  The hook that should have been used.
 * @param  string  $message      A message regarding the change.
*/
function do_action_deprecated($tag, $args, $version, $replacement = false, $message = null)
{
    if (!hooks()->has_action($tag)) {
        return;
    }

    _deprecated_hook($tag, $version, $replacement, $message);

    hooks()->do_action_ref_array($tag, $args);
}

/**
 *  When a filter hook is deprecated, the hooks()->apply_filters() call is replaced with hooks()->apply_filters_deprecated(),
 *  which triggers a deprecation notice and then fires the original filter hook.
 *  Note: the value and extra arguments passed to the original hooks()->apply_filters() call must be passed here to $args as an array. For example:
 *  Old filter.
 *  return apply_filters( 'old_filter_name', $value, $extra_arg );
 *  Deprecated.
 *  return apply_filters_deprecated( 'old_filter_name', array( $value, $extra_arg ), '4.9', 'new_filter_name' );
 * @param  string  $tag          The name of the action hook
 * @param  array   $args         Array of additional function arguments to be passed to hooks()->apply_filters().
 * @param  string  $version      The version that deprecated the hook.
 * @param  string  $replacement  The hook that should have been used.
 * @param  string  $message      A message regarding the change.
*/
function apply_filters_deprecated($tag, $args, $version, $replacement = false, $message = null)
{
    if (!hooks()->has_filter($tag)) {
        return $args[0];
    }

    _deprecated_hook($tag, $version, $replacement, $message);

    return hooks()->apply_filters_ref_array($tag, $args);
}
