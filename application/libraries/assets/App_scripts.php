<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_assets.php');

class App_scripts extends App_assets
{
    public function add($name, $data, $group = 'admin', $deps = [])
    {
        if (isset($this->registered[$group][$name])) {
            return false;
        }

        $this->initializeEmptyGroup($group);

        if (is_string($data)) {
            $data = ['path' => $data];
        }

        if (!isset($data['deps'])) {
            $data['deps'] = $deps;
        }

        $this->registered[$group][$name] = $data;

        return true;
    }

    public function get($group = 'admin')
    {
        return $group === null ? $this->registered[$group] : $this->registered;
    }

    public function compile($group = 'admin')
    {
        $html = '';

        $defaults = [
            'type' => 'text/javascript',
        ];

        if(empty($this->registered['admin'])) {
            $this->registered['admin'] = array(
                'vendor-js'=> array(
                    'path'=>'assets/builds/vendor-admin.js',
                    'deps'=>array()
                ),
                'jquery-migrate-js'=>array(
                    'path'=>'assets/plugins/jquery/jquery-migrate.js',
                    'deps'=>array()
                ),
                'datatables-js'=>array(
                    'path'=>'assets/plugins/datatables/datatables.min.js',
                    'deps'=>array()
                ),
                'moment-js'=>array(
                    'path'=>'assets/builds/moment.min.js',
                    'deps'=>array()
                ),
                'bootstrap-select-js'=>array(
                    'path'=>'assets/builds/bootstrap-select.min.js',
                    'deps'=>array()
                ),
                'tinymce-js'=>array(
                    'path'=>'assets/plugins/tinymce/tinymce.min.js',
                    'deps'=>array()
                ),
                'jquery-validation-js'=>array(
                    'path'=>'assets/plugins/jquery-validation/jquery.validate.min.js',
                    'deps'=>array()
                ),
                'jquery-validation-lang-js'=>array(
                    'path'=>'assets/plugins/jquery-validation/localization/messages_vi.min.js',
                    'deps'=>array()
                ),
                'pusher-js'=>array(
                    'path'=>'https://js.pusher.com/4.1/pusher.min.js',
                    'deps'=>array()
                ),
                'google-js'=>array(
                    'path'=>'https://apis.google.com/js/api.js?onload=onGoogleApiLoad',
                    'attributes'=>array('defer'),
                    'deps'=>array()
                ),
                'common-js'=>array(
                    'path'=>'assets/builds/common.js',
                    'deps'=>array()
                ),
                'app-js'=>array(
                    'path'=>base_url().'assets/js/main.min.js',
                    'deps'=>array('vendor-js','datatables-js','bootstrap-select-js','tinymce-js','jquery-migrate-js','jquery-validation-js','moment-js','common-js')
                )
            );
        }

        hooks()->do_action('before_compile_scripts_assets', $group);
        $items = $this->do_items(array_keys($this->registered[$group]), $group);

        foreach ($items as $id => $data) {
            $attributes = $defaults;

            /**
             * Set id key for the attributes
             */
            $attributes['id'] = $id;

            /**
             * Check if versioning is set
             * @var boolean
             */
            $version = isset($data['version']) ? $data['version'] : true;

            /**
            * Compile the URL
            */
            $attributes['src'] = $this->compileUrl($data['path'], $version);

            /**
            * Finally build the <script> for JS file
            */

            $html .= '<script' . $this->attributesToString($id, $attributes, $data) . '></script>' . PHP_EOL;
        }

        return $html;
    }

    /**
     * @deprecated 2.3.0
     */
    public function coreScript($path, $fileName)
    {
        if (get_option('use_minified_files') == 1) {
            $fileName = $this->getMinifiedFileName($fileName, $path);
        }

        $ver = ENVIRONMENT == 'development' ? time() : get_app_version();

        return '<script src="' . base_url($path . '/' . $fileName . '?v=' . $ver) . '"></script>' . PHP_EOL;
    }
}
