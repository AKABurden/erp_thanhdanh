<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Scan_qr extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('vietnamese_lang', 'vietnamese');
    }
    public function index()
    {
    	$data['title'] = _l('scan_qr');
        $this->load->view('admin/scan_qr/manage', $data);
    }
    public function getData()
    {
        $data = $this->input->post();
        $result = array();
        $getData_order = get_table_where('tbl_orders',array('reference_no'=>$data['code_order']),'','row');
        if($getData_order) {
            $result['code_order'] = $getData_order->reference_no;
            $result['client'] = get_table_where('tblclients',array('userid'=>$getData_order->customer_id),'','row')->company;
            $keyMain = 0;
            $get_all_item = get_table_where('tbl_order_items',array('order_id'=>$getData_order->id,'type_gift'=>0));
            foreach ($get_all_item as $key => $value) {
                if($value['type_item'] == 'products') {
                    $get_item = get_table_where('tbl_products',array('id'=>$value['item_id']),'','row');
                    $result['items'][$keyMain]['code'] = $get_item->code;
                    $result['items'][$keyMain]['name'] = $get_item->name;
                    $result['items'][$keyMain]['quantity'] = $value['quantity'];
                }
                else if($value['type_item'] == 'items') {
                    $get_item = get_table_where('tblitems',array('id'=>$value['item_id']),'','row');
                    $result['items'][$keyMain]['code'] = $get_item->code;
                    $result['items'][$keyMain]['name'] = $get_item->name;
                    $result['items'][$keyMain]['quantity'] = $value['quantity'];
                }
                $keyMain++;
            }

            //check status is exists
            $this->db->select('*');
            $this->db->where('tbl_orders_workflow.order_id', $getData_order->id);
            $allStatus = $this->db->get('tbl_orders_workflow')->result_array();
            $arrStatus = array();
            if($allStatus) {
                foreach ($allStatus as $key => $value) {
                    $arrStatus[] = $value['workflow_id'];
                }
            }
            $result['arrStatus'] = $arrStatus;
            //end
        }
        echo json_encode($result);
    }

    public function getData_staff_inProcess()
    {
        $data = $this->input->post();
        $getAll_process = get_table_where('tblprocedure_client_detail',array('id_detail'=>4,'default_id'=>$data['process']),'','row');
        $html = '';
        if($getAll_process && !empty($getAll_process->staff_id)) {
            $arrID_staff = explode(',', $getAll_process->staff_id);
            foreach ($arrID_staff as $key => $value) {
                $staff = get_table_where('tblstaff',array('staffid'=>$value),'','row');
                if(empty($staff->profile_image)) {
                    $profile_image = base_url('assets/images/user-placeholder.jpg');
                }
                else {
                    $profile_image = base_url('uploads/staff_profile_images/'.$staff->staffid.'/'.'thumb_'.$staff->profile_image);
                }
                if(!empty($staff->firstname)) {
                    $fullname = $staff->firstname;
                }
                if(!empty($staff->lastname)) {
                    $fullname .= ' '.$staff->lastname;
                }
                $html .= '<img class="img-staff" width="100" height="100" src="'.$profile_image.'" data-id="'.$staff->staffid.'" data-toggle="tooltip" title="'.$fullname.'">';
            }
        }
        echo json_encode($html);
    }

    public function getData_staff()
    {
        $data = $this->input->post();
        $result = array();
        $getData_order = get_table_where('tblstaff',array('code'=>$data['code_staff']),'','row');
        if(!empty($getData_order->code)) {
            $result['staff_id'] = $getData_order->staffid;
            $result['code'] = $getData_order->code;
            if(!empty($getData_order->firstname)) {
                $result['fullname'] = $getData_order->firstname;
            }
            if(!empty($getData_order->lastname)) {
                $result['fullname'] .= ' '.$getData_order->lastname;
            }
        }
        echo json_encode($result);
    }

    public function updateProcessOrders()
    {
        $data = [];
        if ($this->input->post())
        {
            $reference_order = $this->input->post('reference_order');
            $order = $this->site_model->getOrderByReferenceNo($reference_order);
            if (empty($order)) {
                $data['result'] = 0;
                $data['message'] = lang('not_data_exists');
                echo json_encode($data); die;
            }

            $status = $this->input->post('status');
            $staff_id = $this->input->post('staff_id');
            $order_id = $order['id'];
            $workflow = $this->site_model->rowOrdersWorkflow($status, $order_id);
            if (!empty($workflow)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_this_process_has_been_selected');
                echo json_encode($data); die;
            }

            $wf = $this->site_model->insertOrdersWorkflow([
                'workflow_id' => $status,
                'order_id' => $order_id,
                'created_by' => $staff_id,
                'date_created' => date('Y-m-d H:i:s'),
            ]);
            if ($wf) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }
}
