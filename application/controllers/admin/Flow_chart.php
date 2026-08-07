<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flow_chart extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function add()
    {
        if($this->input->post()) {
            $data=$this->input->post();
            var_dump($data);die;
            $in = array(
                'solution' => $data['solution'],
            );
            $this->db->where('id',$id);
            $result = $this->db->update('tblwarranty',$in);
            if($result)
            {
                $message=_l('updated_successfuly');
                $alert_type='success';
            }
            echo json_encode(array(
                            'success' => $result,
                            'message' => $message,
                            'alert_type'=>$alert_type
                        ));
                        die;
        }
    }
    public function getData_main()
    {
        $get[0]['CEO'] = get_option('general_manager');
        $get[0]['title'] = _l('general_manager');
        echo json_encode($get);
    }
    public function getData_sub()
    {
        $get_all_departments = get_table_where('tbldepartments');
        foreach ($get_all_departments as $key => $value) {
            $get['departments'][$key]['name'] = $value['name'];
            $get_role = get_table_where('tblroles',array('departments_id'=>$value['departmentid']));
            foreach ($get_role as $key_role => $value_role) {
                $get['departments'][$key]['role'][$key_role]['name'] = $value_role['name'];
            }
        }
        echo json_encode($get);
    }
}
