<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Invoice_items extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->model('category_model');
        
    }
    public function import()
    {
        $data['title'] = _l('Nhập dữ liệu hàng hóa');
        $data['colum_suppliers'] = $this->db->list_fields(db_prefix().'suppliers');
        $data['colum_suppliers'] = array_diff($data['colum_suppliers'], [
            'default_language' ,
            'default_currency',
        ]);

        $data['colum_info_suppliers'] = $this->db->get(db_prefix().'suppliers_info_detail')->result_array();
        $data['columsExcel'] = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        $this->load->view('admin/import_excel/import_items', $data);
    }
    public function items($type='')
    {
    //HAU
        if($type == 'items')
        {
            echo json_encode($this->invoice_items_model->get_items_ch());
        }
        if($type == 'product')
        {
            echo json_encode(get_table_where('tbl_products'));
        }
    } 
    public function mainstream_itemss($id = '')
    {
        $this->app->get_table_data('mainstream_itemss',array('id'=>$id));
    }
    /* List all available items */
    public function index()
    {
        if (!has_permission('invoice_items', '', 'view') && !has_permission('invoice_items', '', 'view_own')) {
            access_denied('Invoice Items');
        }

        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['items_brand'] = $this->invoice_items_model->get_brands();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('ch_items_s');
        $this->load->view('admin/invoice_items/manage', $data);
    }
    public function table_brands()
    {
        $this->app->get_table_data('table_brands');
    }
    public function table_groups()
    {
        $this->app->get_table_data('table_groups');
    }

    public function table()
    {
       
        $this->app->get_table_data('invoice_items');
    }
    public function combo_items($id=NULL,$type=0)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('combo_items',array('id'=>$id,'type'=>$type));
        }
    }
    public function delete_combo($id=NULL)
    {
        if(is_numeric($id))
        {
            $this->db->where('id',$id);
            if($this->db->delete('tblcombo_items'))
            {
                echo json_encode(array(
                        'success' => true,
                        'alert_type'=>'success',
                        'message' => _l('ch_delete')
                    ));die();
            }
            else
            {
                echo json_encode(array(
                        'success' => false,
                        'alert_type'=>'danger',
                        'message' => _l('ch_no_delete')
                    ));die();
            }
        }
        echo json_encode(array(
                        'success' => false,
                        'alert_type'=>'danger',
                        'message' => _l('ch_no_delete')
                    ));die();
    }
    public function update_quantity_combo($id=NULL)
    {
        if(is_numeric($id))
        {
            $quantity=$this->input->post('quantity');
            $this->db->where('id',$id);
            if($this->db->update('tblcombo_items',array('quantity'=>$quantity)))
            {
                echo json_encode(array(
                        'success' => true,
                        'alert_type'=>'success',
                        'message' => _l('ch_updatee_items')
                    ));die();
            }
            else
            {
                echo json_encode(array(
                        'success' => false,
                        'alert_type'=>'danger',
                        'message' => _l('ch_updatee_items_no')
                    ));die();
            }
        }
        echo json_encode(array(
                        'success' => false,
                        'alert_type'=>'danger',
                        'message' => _l('ch_updatee_items_no')
                    ));die();
    } 
    public function combo_item($id=NULL)
    {
        $result=array(
            'alert_type'=>'danger',
            'message'=>'',
            'success'=>false
            );
        if(is_numeric($id))
        {
            $data=$this->input->post();
            $data['rel_id']=$id;
            $this->db->insert('tblcombo_items',$data);
            if($this->db->insert_id())
            {
                $result=array(
                    'alert_type'=>'success',
                    'message'=>_l('added_successfuly',_l('combo_item')),
                    'success'=>true
                    );
            }
        }
        echo json_encode($result);
    }
    public function invoice_item_price_history($id)
    {
        if($id!='') {
            if($this->input->is_ajax_request()) {
                $this->app->get_table_data('invoice_item_price_history', array(
                    'rel_id' => $id,
                ));
            }
        }
    }
    public function invoice_item_price_single_history($id)
    {
        if($id!='') {
            if($this->input->is_ajax_request()) {
                $this->app->get_table_data('invoice_item_price_single_history', array(
                    'rel_id' => $id,
                ));
            }
        }
    }    
    public function delete_image_product($product_id=NULL) {
        if ($this->input->is_ajax_request()) {
            if(empty($image)) $image=$this->input->post('image');
            exit(json_encode($this->invoice_items_model->delete_image_product($image,$product_id)));
        }
    }  
    public function delete_image_avatar($product_id=NULL) 
    {
        if ($this->input->is_ajax_request()) {
            exit(json_encode($this->invoice_items_model->delete_image_avatar($product_id)));
        }
    }
    public function change_items_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->invoice_items_model->change_items_status($id, $status);
        }
    }
    public function change_items_calculated($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('id', $id);
            $this->db->update('tblitems', [
                'calculated_on_sales' => $status,
            ]);
        }
    }
    /* Edit or update items / ajax request /*/
    public function manage()
    {
            if ($this->input->post()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission('invoice_items', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id      = $this->invoice_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->invoice_items_model->get($id),
                    ]);
                } else {
                    if (!has_permission('invoice_items', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
    }

    // public function import()
    // {
    //     if (!has_permission('invoice_items', '', 'create')) {
    //         access_denied('Items Import');
    //     }

    //     $this->load->library('import/import_items', [], 'import');

    //     $this->import->setDatabaseFields($this->db->list_fields(db_prefix().'items'))
    //                  ->setCustomFields(get_custom_fields('items'));

    //     if ($this->input->post('download_sample') === 'true') {
    //         $this->import->downloadSample();
    //     }

    //     if ($this->input->post()
    //         && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
    //         $this->import->setSimulation($this->input->post('simulate'))
    //                       ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
    //                       ->setFilename($_FILES['file_csv']['name'])
    //                       ->perform();

    //         $data['total_rows_post'] = $this->import->totalRows();

    //         if (!$this->import->isSimulation()) {
    //             set_alert('success', _l('import_total_imported', $this->import->totalImported()));
    //         }
    //     }

    //     $data['title'] = _l('import');
    //     $this->load->view('admin/invoice_items/import', $data);
    // }
    public function add_brand()
    {
        if ($this->input->post() && is_admin()) {
           $response = $this->invoice_items_model->add_brand($this->input->post());
        }
            $alert_type = 'warning';
            $message    = _l('ch_added_successfuly_not');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_added_successfuly');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }

    public function update_brand($id)
    {
        if ($this->input->post() && is_admin()) {
            $response = $this->invoice_items_model->edit_brand($this->input->post(), $id);
        }
            $alert_type = 'warning';
            $message    = _l('ch_edit_items');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('updated_successfully');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));

    }

    public function delete_brand($id)
    {
        $response= false;
        if (is_admin()) {
            if ($this->invoice_items_model->delete_brand($id)) {
                $response= true;
            }
        }
        $alert_type = 'warning';
            $message    = _l('ch_delete_successfuly_no');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete_successfuly');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }
    public function add_group()
    {
        if ($this->input->post() && is_admin()) {
            $response = $this->invoice_items_model->add_group($this->input->post());
        }

            $alert_type = 'warning';
            $message    = _l('ch_added_successfuly_not');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_added_successfuly');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }

    public function update_group($id)
    {
        if ($this->input->post() && is_admin()) {
            $response = $this->invoice_items_model->edit_group($this->input->post(), $id);
        }
        $alert_type = 'warning';
            $message    = _l('ch_edit_items');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('updated_successfully');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }

    public function delete_group($id)
    {
        $response= false;
        if (is_admin()) {
            if ($this->invoice_items_model->delete_group($id)) {
                $response= true;
            }
        }
        $alert_type = 'warning';
            $message    = _l('ch_delete_successfuly_no');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete_successfuly');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }

    /* Delete item*/
    public function check_items($id='')
    {

        if (!has_permission('items', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }else{
            if(Check_Exists_Items($id))
            {
               echo json_encode(array(
                'type'=>1,
                'alert_type' =>  'warning',
                'message' => _l('ch_note_delete_items')
                ));die; 
            }elseif(Check_combo_Items($id) == 2)
            {
                $combo = get_table_where('tblcombo_items',array('product_id'=>$id),'','row');
                echo json_encode(array(
                'type'=>3,
                ));die; 
            }elseif(Check_combo_Items($id) == 1)
            {
                $combo = get_table_where('tblcombo_items',array('product_id'=>$id),'','row');

                echo json_encode(array(
                'type'=>2,
                'alert_type' =>  'warning',
                'message' => _l('ch_exsit_items_order'),
                ));die; 
            }else
            {
               echo json_encode(array(
                'type'=>5,
                ));die;  
            }
        }
    }
    public function delete($id)
    {
        if (!has_permission('invoice_items', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }else{

            if(Check_Exists_Items($id))
            {
                echo json_encode(array(
                'alert_type' =>  'warning',
                'message' => _l('ch_note_delete_items')
                ));die;
            }else
            {
            $response = $this->invoice_items_model->delete($id);
            }
            $alert_type = 'warning';
            $message    = _l('ch_delete_successfuly_no');
            if ($response) {
            $this->db->delete('tblmainstream_goods',array('id_items'=>$id,'type'=>'items'));
            $alert_type = 'success';
            $message    = _l('ch_delete_successfuly');
            }    
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));die;
        
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_items');
        $total_deleted = 0;
        $total_nodeleted = 0;
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $has_permission_delete = has_permission('items', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if(Check_Exists_Items($id))
                            {
                                $total_nodeleted++;
                            }elseif(Check_combo_Items($id))
                            {
                                $total_nodeleted++;
                            }
                            else
                            {
                             if ($this->invoice_items_model->delete($id)) {
                                $total_deleted++;
                            }   
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            if($total_nodeleted > 0)
            {
            set_alert('success', _l('ch_total_items_deleted', $total_deleted).' , '.$total_nodeleted.'  '._l('ch_note_delete_items'));
            }else
            {
            set_alert('success', _l('ch_total_items_deleted', $total_deleted));    
            }
        }
    }

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }
    public function item($id = '')
    {
        if (!has_permission('invoice_items', '', 'view') && !has_permission('invoice_items', '', 'view_own')) {
            if ($id != '') {
                access_denied('invoice_items');
            }
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('invoice_items', '', 'create')) {
                    access_denied('Invoice items');
                }
                $data                 = $this->input->post();
          
                $data['price']=str_replace(',','',$data['price']);
                $data['price_single']=str_replace(',','',$data['price_single']);
               
                $data['minimum_quantity']=str_replace(',','',$data['minimum_quantity']);
                $data['maximum_quantity']=str_replace(',','',$data['maximum_quantity']);
                $data['product_features']=htmlspecialchars_decode($data['product_features']);
                
                
                $id = $this->invoice_items_model->add($data);
                if ($id) {
                    handle_item_avatar_image_upload($id);
                    handle_item_product_image_upload($id);
                    set_alert('success', _l('ch_added_successfuly', _l('ch_items')));
                    if(get_option('prefix_add_continuous')==0)
                    {
                        redirect(admin_url('invoice_items'));
                    }
                    else
                    {
                        redirect(admin_url('invoice_items'));
                    }
                }
            } else {
                if (!has_permission('invoice_items', '', 'edit')) {
                    access_denied('Invoice items');
                }
                $data = $this->input->post();
                $data['price']=str_replace(',','',$data['price']);
                $data['price_single']=str_replace(',','',$data['price_single']);
                
                $data['minimum_quantity']=str_replace(',','',$data['minimum_quantity']);
                $data['maximum_quantity']=str_replace(',','',$data['maximum_quantity']);
                $data['product_features']=htmlspecialchars_decode($data['product_features']);
                $success = $this->invoice_items_model->edit($data, $id);
                $success_avatar = handle_item_avatar_image_upload($id);
                // var_dump(handle_item_product_image_upload($id));die()
                $success_avatar = handle_item_product_image_upload($id);
                if ($success == true || $success_avatar == true) {
                    set_alert('success', _l('updated_successfully', _l('ch_items')));
                }
                redirect(admin_url('invoice_items'));
            }
        }
        
        if ($id == '') {
            if (!has_permission('invoice_items', '', 'create')) {
                    access_denied('Invoice items');
                }
            $title = _l('add_new', _l('ch_items'));
       
            
        } else {
             if (!has_permission('invoice_items', '', 'edit')) {
                    access_denied('Invoice items');
                }
            $title = _l('invoice_item_edit_heading');
            $item = $this->invoice_items_model->get_full_edit($id);
            $data['item'] = $item;
            // if($item->color_id)
            // {
            //     $data['color']= get_options_search_cbo('color',$item->color_id);
            // }

        }
        $data['color'] = get_table_where('tbl_colors');
        $data['packaging'] = get_table_where('tbl_packaging');
        $data['items'] = get_table_where('tblitems');
        $data['categories'] = [];
        $this->category_model->get_by_id(0,$data['categories']);
        $data['lightbox_assets'] = true;
        $data['title'] = $title;
        $this->load->view('admin/invoice_items/item_details', $data);
    }
    public function get_tax($id_tax) {
      
        if ($this->input->is_ajax_request()) {
            $this->load->model('taxes_model');
            exit(json_encode($this->taxes_model->get($id_tax)));
        }
    }    
    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->invoice_items_model->get($id);
            $item->long_description   = nl2br($item->long_description);
            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
            $item->custom_fields      = [];

            $cf = get_custom_fields('items');

            foreach ($cf as $custom_field) {
                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
                if ($custom_field['type'] == 'textarea') {
                    $val = clear_textarea_breaks($val);
                }
                $custom_field['value'] = $val;
                $item->custom_fields[] = $custom_field;
            }

            echo json_encode($item);
        }
    }
    public function int_items_view($id='')
    {
        if(!empty($id))
        {
            $data['items'] = $this->invoice_items_model->get_full_edit($id);

            $data['title']=$data['items']->name;
            $data['id'] = $id;
            $this->load->view('admin/invoice_items/view_items',$data);
        }
        echo false;
        
    }
    public function getList_items()
    {
        if ($this->input->post()) {
            $result = array();
            $data = $this->input->post();
            foreach ($data['arrID'] as $key => $value) {
                $result[$key] = get_table_where('tblitems',array('id'=>$value),'','row');
            }
            echo json_encode($result);
        }
    }
    public function pdf()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $result = array();
            $result['total_number'] = 0;
            foreach ($data['item'] as $key => $value) {
                $result['item'][$key] = get_table_where('tblitems',array('id'=>$value['id_item']),'','row');
                $result['item'][$key]->quantity_print = $value['quantity_print'];
                $result['total_number'] += $value['quantity_print'];
            }
            $result['print_show'] = $data['type_show'];
            // echo "<pre>";
            // var_dump($result);die;
            if($data['type_size'] == 0) {
                $this->load->view('admin/invoice_items/printBarcode_1_stamp', $result);
            }
            else if($data['type_size'] == 1) {
                $this->load->view('admin/invoice_items/printBarcode_2_stamp', $result);
            }
            else if($data['type_size'] == 2) {
                $this->load->view('admin/invoice_items/printBarcode_3_stamp', $result);
            }
            else if($data['type_size'] == 3) {
                $this->load->view('admin/invoice_items/printBarcode_100_stamp', $result);
            }
        }
    }
}
