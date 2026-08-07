<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('IdAppFB', '706225529746809');
add_option('VersionAppFB', 'v3.3');
$CI = &get_instance();
$CI->db->query('ALTER TABLE `tblclients` ADD `id_facebook` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `userid`;');