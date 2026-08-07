<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Software_extension extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = 'Gia hạn phần mềm';
        $this->load->view('admin/software_extension/manage', $data);
    }

    public function getView_modal_payment()
    {
        $data = $this->input->post();
        $dataResults = array();
        //gửi mail
        $email = 'info@fososoft.com';
        $message = 'Yêu cầu gia hạn phần mềm cho công ty: <span style="font-size:20px;">'.get_option('invoice_company_name').'</span>';
        $message .= '<br>Nội dung chuyển khoản: <span style="font-size:18px;">'.get_option('invoice_company_name').'</span>';
        $message .= '<br>Số tháng: <span style="font-size:18px;">'.$data['time'].'</span>';
        $message .= '<br>Số user thêm: <span style="font-size:18px;">'.$data['user_quantity'].'</span>';

        $total = (1200000 * $data['time']);
        if($data['user_quantity'] <= 5) {
            $total += 200000 * $data['user_quantity'] * $data['time'];
        } else {
            $new_quantity = $data['user_quantity'] - 5;
            $total += 200000 * 5 * $data['time'];
            $total += 100000 * $new_quantity * $data['time'];
        }
        $message .= '<br>Tổng thanh toán: <span style="font-size:20px;">'.number_format($total).'</span>';
        $message .= '<br><br><a href="'.admin_url('software_extension/updateDateSoftware/'.$data['time']).'" style="background: #4fb949; font-size: 20px; color: #fff; padding: 10px 15px; border-radius: 2px; text-transform: uppercase; text-decoration: none;">gia hạn ngay</a>';

        $this->email->initialize();
        $this->email->set_newline("\r\n");
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->set_mailtype("html");
        $this->email->to($email);

        $this->email->subject('Yêu cầu gia hạn phần mềm');
        $this->email->message($message);
        //end
        if($this->email->send()) {
            $this->load->view('admin/software_extension/view_modal_payment', $dataResults);
        }
        else {
            $this->load->view('admin/software_extension/view_modal_payment_false', $dataResults);
        }
    }

    public function updateDateSoftware($time = '')
    {
        $dataResults = array();
        $dataResults['time'] = $time;

        $getDateCheck = to_sql_date(get_option('expire_software'));
        $dateNew = date("d/m/Y", strtotime($getDateCheck.' +'.$time.' month'));
        $dataResults['dateTime'] = $dateNew;

        $this->load->view('admin/software_extension/check_security', $dataResults);
    }

    public function updateDateSoftware_update($time = '')
    {
        $data = $this->input->post();
        if($data['security_code'] == 'adminfoso') {
            $getDateCheck = to_sql_date(get_option('expire_software'));
            $dateNew = date("d/m/Y", strtotime($getDateCheck.' +'.$time.' month'));
            $this->db->set('value', $dateNew);
            $this->db->where('name', 'expire_software');
            $this->db->update('tbloptions');
            echo 1;
        }
        else {
            echo 0;
        }
    }
}