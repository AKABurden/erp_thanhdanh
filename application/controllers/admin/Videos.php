<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Videos extends AdminController
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {

        $data['title'] = _l('Video hướng dẫn sử dụng');
        $id = $this->input->get('id');
        $data['detail_videos'] = array();
        if(!empty($id)){
        $data['detail_videos'] =get_table_where('tblvideos',array('id'=>$id),'','row');
        }
        $type = get_table_where('tbl_videos_groups');
        foreach ($type as $key => $value) {
            $videos = get_table_where('tblvideos',array('type'=>$value['id']));
            if(!empty($videos))
            {
                $type[$key] = array_merge($type[$key],array('videos'=>$videos));
            }else
            {
                unset($type[$key]);
            }
        }
        $data['videos'] = $type;
        $this->load->view('admin/videos/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('videos');
    }
    public function table_groups()
    {
        $this->app->get_table_data('table_groups_videos');
    }
    public function add_group()
    {       
            $data = $this->input->post();
            $this->db->insert(db_prefix() . '_videos_groups', $data);
            $alert_type = 'success';
            $message    = _l('ch_added_successfuly');
            echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }
    public function update_group($id)
    {
        $data = $this->input->post();
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . '_videos_groups', $data);
        $alert_type = 'success';
        $message    = _l('updated_successfully');
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }

    public function delete_group($id)
    {
            $response= false;

            $videos = get_table_where('tblvideos',array('type'=>$id));
            if(!empty($videos))
            {
                echo json_encode(array(
                    'alert_type' =>'warning',
                    'message' => 'Đã tồn tại trong videos không thể xóa!'
                    ));
            }else
            {
                $response    = $this->db->delete('tbl_videos_groups',array('id'=>$id));
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
    public function edit_upload($id='')
    {
        $data = $this->input->post();
        $_data['name'] = $data['title'];
        $_data['type'] = $data['type'];
        $_data['note'] = $this->input->post('note',false);
        $this->db->update('tblvideos',$_data,array('id'=>$id));
        $alert_type = 'success';
        $message    = _l('Sửa thành công');
        echo json_encode(array(
            'success' => true,
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function upload()
    {
        $data = $this->input->post();
        if(!empty($_FILES['file']))
        {   
            $_FILES['file']['name'] = time().'_'.vn_to_str($_FILES['file']['name']);
            if(is_uploaded_file($_FILES['file']['tmp_name']))
            {
                sleep(1);
                $source_path = $_FILES['file']['tmp_name'];
                $_data['type_videos'] = $_FILES['file']['type'];
                $target_path = 'uploads/videos/' . $_FILES['file']['name'];
                if(move_uploaded_file($source_path, $target_path))
                {
                    $_data['link'] = $target_path;
                }
            }
        }
        $_data['name'] = $data['title'];
        $_data['type'] = $data['type'];
        $_data['note'] = $data['note'];
        $this->db->insert('tblvideos',$_data);
        $alert_type = 'success';
        $message    = _l('ch_added_successfuly');
        echo json_encode(array(
            'success' => true,
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function view_video($id='')
    {   
        $data['tnh'] = true;
        $data['type'] = get_table_where('tbl_videos_groups');
        if($id != 'undefined')
        { 
            $data['main'] = get_table_where('tblvideos',array('id'=>$id),'','row');
            $this->load->view('admin/videos/edit_modal', $data);
        }else
        {
            $this->load->view('admin/videos/view_modal',$data);
        }
    }
    public function detail()
    {
        $data['title'] = _l('Thêm và sửa videos');
        $this->load->view('admin/videos/detail', $data);
        
    }
    /* Get task data in a right pane */
    public function delete($id)
    {
        $ktr = get_table_where('tblvideos',array('id'=>$id),'','row');
        if (!$id) {
            die('ch_no_items');
        }
        $ktr = get_table_where('tblvideos',array('id'=>$id),'','row');
        $success    = $this->db->delete('tblvideos',array('id'=>$id));
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            if (file_exists($ktr->link)){
                unlink($ktr->link);
            }
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));

    }
     public function add_unit()
    {
        if ($this->input->post()) {
            $message = '';
                $id = $this->unit_model->add_unit($this->input->post(NULL, FALSE));
                if ($id) {
                    $success = true;
                    $message = _l('added_successfuly', _l('als_units'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            die;
        }
    }
    public function update_unit($id="")
    {
        if($id!=""){
            $message    = '';
            $alert_type = 'warning';
            if ($this->input->post()) {
                $success = $this->unit_model->update_unit($this->input->post(), $id);
                if ($success) {
                    $message    = 'ch_updatee_items';
                };
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
        else
        {
            if ($this->input->post()) {
                $success = $this->unit_model->add_unit($this->input->post());
                if ($success) {
                    $alert_type = 'success';
                    $message    = 'ch_adde_items';
                }
            }
            echo json_encode(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
        }
        die;
    }



    public function get_row_unit($id)
    {
        echo json_encode($this->unit_model->get_row_unit($id));
    }


}
