<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $GLOBALS['EXT']->call_hook('pre_controller_constructor');


/*            if(!$this->input->is_ajax_request()){
                $this->output->enable_profiler(TRUE);
            }
*/

        /**
         * Fix for users who don't replace all files during update !!!
         */
        if (!class_exists('ForceUTF8\Encoding') && file_exists(APPPATH . 'vendor/autoload.php')) {
            require_once(APPPATH . 'vendor/autoload.php');
        }

        if (is_dir(FCPATH . 'install') && ENVIRONMENT != 'development') {
            echo '<h3>Delete the install folder</h3>';
            die;
        }

        $this->db->reconnect();

        /**
         * Set system timezone based on selected timezone from options
         * @var string
         */
        $timezone = get_option('default_timezone');
        if ($timezone != '') {
            date_default_timezone_set($timezone);
        }

        /**
         * Clear last upgrade copy data
         * @var object
         */
        if ($lastUpdate = get_last_upgrade_copy_data()) {
            if ((time() - $lastUpdate->time) > _delete_temporary_files_older_then()) {
                @unlink($lastUpdate->path);
                update_option('last_upgrade_copy_data', '');
            }
        }

        hooks()->do_action('app_init');
    }
}
