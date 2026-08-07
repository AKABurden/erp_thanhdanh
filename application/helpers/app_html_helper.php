<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Truncated @since 2.3.2, the contents is moved in template_helper.php
 * This file sits here only for backward compatibilities, the Codeigniter framework auto load the file because of the app_ prefix, if removed will throw errors upon upgrade because of the App_ prefix change
 * For example, after 2-3 years, the file should be removed because probably no one will be using 2.3. version :)
 */
function get_status_client_label($id)
{
    $label = 'default';
    if ($id == 1) {
        $label = 'primary';
    } 
    else if ($id == 2) {
        $label = 'light-green';
    } else if ($id == 6) {
        $label = 'default';
    } else if ($id == 4) {
        $label = 'info';
    } else if ($id == 5) {
        $label = 'success';
    } else if ($id == 3) {
        $label = 'warning';
    }
    else {
    	$label = 'default';
    }
    return $label;
}
