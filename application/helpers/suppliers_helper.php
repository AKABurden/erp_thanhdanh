<?php

defined('BASEPATH') or exit('No direct script access allowed');

function render_groups_suppliers_source_select($sources, $selected = '', $lang_key = '', $name = 'groups_in', $select_attrs = [])
{
    if (is_admin()) {
        echo render_select_with_input_group($name, $sources, ['id', 'name'], $lang_key, $selected, '<a href="#" onclick="new_suppliers_source_inline();return false;" class="suppliers-field-new"><i class="fa fa-plus"></i></a>', $select_attrs);
    } else {
        echo render_select($name, $sources, ['id', 'name'], $lang_key, $selected, $select_attrs);
    }
}
function render_ch_input($name,$lang_key = '')
{
        echo render_input_with_input_group($name, $lang_key,'', '<a href="#" onclick="new_suppliers_source_inline();return false;" class="suppliers-field-new"><i class="fa fa-plus"></i></a>');

}
function ch_getMaxID($id,$table)
{
	$CI =& get_instance();
    $table = trim($table);
    if (isset($id)) {
        $CI->db->select_max($id);
        return $CI->db->get($table)->row()->{$id};
    }
    return 0;
}
function text_align($strVal,$type='center')
{
    return "<p class='text-".$type."'>".$strVal."</p>";
}
/**
 * Load lead language
 * Used in public GDPR form
 * @param  string $lead_id
 * @return string return loaded language
 */