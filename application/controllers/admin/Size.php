<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Size extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('adjusted_model');
        $this->load->model('invoice_items_model');
        $this->title = '';
        $type_title = $this->input->get('type_title');
        if($type_title == 'materials'){
            $this->title = _l('materials');
        }
        if($type_title == 'products'){
            $this->title = _l('products');
        }
        $this->type_title = $type_title;
    }
    public function index() {
        if (!is_admin()) {
                access_denied('size');
        }
        $data['title']          = _l('c_size').' '.$this->title;
        $data['type_title'] = $this->title;
        $this->load->view('admin/size/manage', $data);
    }


    public function table() {
        $this->app->get_table_data('size');
    }

    public function detail($id = "") {
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {
				$this->db->where('id', $id);
				$size = $this->db->get('tblsize')->row();
				if($size->code_size != trim($data['code_size'])) {
					$ktSize = $this->db->get_where('tblsize', ['code_size' => $data['code_size']])->row();
					if(!empty($ktSize)) {
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => _l('Mã Size đã tồn tại vui lòng nhập mã khác')
						]);die();
					}
				}
				
                $this->db->where('id', $id);
                $success = $this->db->update('tblsize', [
                    'code_size' => trim($data['code_size']),
                    'name' => trim($data['name']),
                ]);
                if(!empty($success)) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_update_true')
                    ]);die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_update_false')
                ]);
                die();
            }
            else {
				$ktSize = $this->db->get_where('tblsize', ['code_size' => trim($data['code_size'])])->row();
				if(!empty($ktSize)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => _l('Mã Size đã tồn tại vui lòng nhập mã khác')
					]);die();
				}
				
                $this->db->where('id', $id);
                $success = $this->db->insert('tblsize', [
					'code_size' => trim($data['code_size']),
                    'name' => trim($data['name']),
                    'create_by' => get_staff_user_id(),
                    'date_create' => date('Y-m-d H:i:s')
                ]);

                if(!empty($success)) {
                    echo json_encode([
                        'success' => true,
                        'alert_type' => 'success',
                        'message' => _l('cong_insert_true')
                    ]);
                    die();
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_insert_false')
                ]);
                die();
            }
        }
        else {
            if(!empty($id)) {
                $this->db->where('id', $id);
                $size = $this->db->get('tblsize')->row();
                echo json_encode($size);die();
            }
        }
    }

    function delete($id) {
        if(!empty($id) && is_admin()) {
            $this->db->where('id', $id);
            $delete = $this->db->delete('tblsize');
            if(!empty($delete)) {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('c_delete_true')
                ]);die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('c_delete_false')
        ]);die();
    }
}