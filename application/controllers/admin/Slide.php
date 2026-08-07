<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Slide extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('announcements_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('slide');
        }
        $data['title'] = _l('asc_slide');
        $this->load->view('admin/slide/manage', $data);
    }

    public function getData_add()
    {
        $stt = 1;
        $this->db->select('MAX(stt) as stt');
        $result = $this->db->get('tbl_slideshow')->row();
        if($result) {
            $stt = $result->stt + 1;
        }
        echo $stt;
    }

    public function getData_edit($id='')
    {
        $data = get_table_where('tbl_slideshow',array('id'=>$id),'','row');
        echo json_encode($data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = htmlspecialchars_decode($data['content']);

            $in = array(
                'stt' => $data['stt'],
                'content' => $data['content']
            );
            $this->db->insert('tbl_slideshow',$in);
            $insert_id = $this->db->insert_id();

            $message = _l('add_slide_false');
            $alert_type = 'danger';
            $success = false;
            if($insert_id) {
                handle_slide_image_upload($insert_id);
                $message = _l('add_slide_true');
                $alert_type = 'success';
                $success = true;
            }
            echo json_encode(array('message'=>$message, 'alert_type'=>$alert_type, 'success'=>$success));
        }
    }

    public function edit($id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['content'] = htmlspecialchars_decode($data['content']);

            $in = array(
                'stt' => $data['stt'],
                'content' => $data['content']
            );
            $this->db->where('id',$id);
            $insert_id = $this->db->update('tbl_slideshow',$in);

            $message = _l('edit_slide_false');
            $alert_type = 'danger';
            $success = false;
            if($insert_id) {
                $message = _l('edit_slide_true');
                $alert_type = 'success';
                $success = true;
            }
            echo json_encode(array('message'=>$message, 'alert_type'=>$alert_type, 'success'=>$success));
        }
    }

    public function delete($id='')
    {
        $folder = 'uploads/slide/'.$id;
        $files = glob($folder . '/*');
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }

        $this->db->where('id',$id);
        $this->db->delete('tbl_slideshow');

        set_alert('success', _l('Xóa thành công!'));
        redirect(admin_url('slide'));
    }
}