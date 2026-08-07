<?php

defined('BASEPATH') or exit('No direct script access allowed');
function ch_getMaxID_items($id,$table)
{
	$CI =& get_instance();
    $table = trim($table);
    if (isset($id)) {
        $CI->db->select_max($id);
        return $CI->db->get($table)->row()->{$id};
    }
    return 0;
}
function get_taxes_dropdown_template($name=NULL,$tax_id=NULL)
{
    $CI =& get_instance();
    $CI->load->model('taxes_model');
    $taxes = $CI->taxes_model->get();
    // Clear the duplicates
    $taxes            = array_map("unserialize", array_unique(array_map("serialize", $taxes)));
    $select           = '<select class="selectpicker display-block tax" data-width="100%" name="' . $name . '" data-none-selected-ted="' . _l('dropdown_non_selected_tex') . '" data-live-search="true">';
    $_no_tax_selected = '';
    $select .= '<option value="" ' . $_no_tax_selected . ' data-taxrate="0">' . _l('no_tax') . '</option>';
    foreach ($taxes as $tax) 
    {
        $selected='';
        if($tax_id && $tax_id==$tax['id']) $selected='selected';
        $select .= '<option value="' . $tax['id'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-subtext="' . $tax['taxrate'] . '%">' . $tax['name'] . '</option>';
    }
    $select .= '</select>';
    return $select;
}
function get_taxes_dropdown_template_new($name=NULL,$tax_id=NULL)
{
    $CI =& get_instance();
    $CI->load->model('taxes_model');
    $taxes = $CI->taxes_model->get();
    // Clear the duplicates
    $taxes            = array_map("unserialize", array_unique(array_map("serialize", $taxes)));
    $select           = '<select class="selectpicker display-block tax none-event" onchange="changeTax(this)" data-width="100%" name="' . $name . '" data-none-selected-ted="' . _l('dropdown_non_selected_tex') . '" data-live-search="true">';
    $_no_tax_selected = '';
    $select .= '<option value="" ' . $_no_tax_selected . ' data-taxrate="0">' . _l('no_tax') . '</option>';
    foreach ($taxes as $tax) 
    {
        $selected='';
        if($tax_id && $tax_id==$tax['id']) $selected='selected';
        $select .= '<option value="' . $tax['id'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-subtext="' . $tax['taxrate'] . '%">' . $tax['name'] . '</option>';
    }
    $select .= '</select>';
    return $select;
}
function ch_get_brands($id = '') {
    $CI =& get_instance();
    if(!empty($id))
    {
        $CI->db->where('id',$id);
        $items = $CI->db->get('tblitems_brands')->row();
        if(!empty($items)) 
        {
            return $items;
        }else
        {
            return '';
        }
    }
    return $CI->db->get('tblitems_brands')->result_array();
}
function ch_get_all_taxes() {
    $CI =& get_instance();
    return $CI->db->get('tbltaxes')->result_array();
}
function ch_get_units() {
    $CI =& get_instance();
    $units = $CI->db->get('tblunits')->result_array();
    return $units;
}
function ch_get_item_groups($id = '') {
    $CI =& get_instance();
    if(!empty($id))
    {
        $CI->db->where('id',$id);   
        return $CI->db->get('tblitems_groups')->row();  
    }
    $groups = $CI->db->get('tblitems_groups')->result_array();
    return $groups;
}
function ch_get_item_brands() {
    $CI =& get_instance();
    $groups = $CI->db->get('tblbrands_groups')->result_array();
    return $groups;
}
/**
 * Handle upload item avatar
 * @return boolean
 */
function handle_item_avatar_image_upload($item_id = '')
{
    if (isset($_FILES['item_avatar']['name']) && $_FILES['item_avatar']['name'] != '') {
        //do_action('before_upload_item_avatar_image');
        if($item_id == ''){
            // $item_id = get_contact_user_id();
            // echo "21312312";
            return;
        }
        $path        = get_upload_path_by_type_ch('items') . $item_id . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['item_avatar']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES["item_avatar"]["name"]);
            $extension          = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));
                return false;
            }
            // Setup our new file path
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . '/index.html', 'w');
            }
             $filename    = unique_filename($path, vn_to_str($_FILES["item_avatar"]["name"]));
             $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config                   = array();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 160;
                $config['height']         = 160;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 32;
                $config['height']         = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();

                $CI->db->where('id', $item_id);
                $CI->db->update('tblitems', array(
                    'avatar' => substr($path, strpos($path,'uploads')) . $filename,
                ));
                // Remove original image
                return true;
            }
        }
    }
    return false;
}
function handle_item_product_image_upload($item_id = '')
{
    if (isset($_FILES['images_product']['name']) && $_FILES['images_product']['name'] != array()) {
        $images="";
        foreach($_FILES['images_product']['name'] as $key=> $rom){
            if($item_id == ''){
                return;
            }
            $path        = get_upload_path_by_type_ch('items') . $item_id . '/';
            // Get the temp file path
            $tmpFilePath = $_FILES['images_product']['tmp_name'][$key];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                // Getting file extension
                $path_parts         = pathinfo($_FILES["images_product"]["name"][$key]);
                $extension          = $path_parts['extension'];
                $extension = strtolower($extension);
                $allowed_extensions = array(
                    'jpg',
                    'jpeg',
                    'png'
                );
                if (!in_array($extension, $allowed_extensions)) {
                    set_alert('warning', _l('file_php_extension_blocked'));
                    return false;
                }
                // Setup our new file path
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . '/index.html', 'w');
                }
                $filename    = unique_filename($path, $_FILES["images_product"]["name"][$key]);
                $newFilePath = $path . $filename;
                // Upload the file into the company uploads dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $CI =& get_instance();
                    $config                   = array();
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $newFilePath;
                    $config['new_image']      = 'thumb_' . $filename;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 160;
                    $config['height']         = 160;
                    $CI->load->library('image_lib', $config);
                    $CI->image_lib->resize();
                    $CI->image_lib->clear();
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $newFilePath;
                    $config['new_image']      = 'small_' . $filename;
                    $config['maintain_ratio'] = TRUE;
                    $config['width']          = 32;
                    $config['height']         = 32;
                    $CI->image_lib->initialize($config);
                    $CI->image_lib->resize();
                    $images=$images.','.substr($path, strpos($path,'uploads')) . $filename;
                    // Remove original image
                }
            }
        }
        if($images!="")
        {
            $images=str_replace(',,',',',trim($images,','));
            $CI->db->where('id', $item_id);
            $CI->db->update('tblitems', array('images_product'=>$images));
        }
        return true;
    }
    return false;
}

function delete_dir_ch($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir_ch($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }
    return false;
}

/**
 * Is file image
 * @param  string  $path file path
 * @return boolean
 */

/**
 * Function that return full path for upload based on passed type
 * @param  string $type
 * @return string
 */
function get_upload_path_by_type_ch($type){
    switch($type){
        case 'items':
            return 'uploads/items/';
        break;
        default:
        return false;
    }
}