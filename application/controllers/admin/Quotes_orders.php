<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotes_orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quotes_orders_model');
        $this->load->model('products_model');
    }

    public function index()
    {
        if (!has_permission('quotes_orders', '', 'view')) {
                access_denied('quotes_orders');
        }
        $data['title']          = _l('cong_quotes_orders');
        $this->load->view('admin/quotes_orders/manage', $data);
    }
    public function table()
    {
        if (!has_permission('quotes_orders', '', 'view')) {
                ajax_access_denied();
        }
        $this->app->get_table_data('quotes_orders');
    }

    public function detail($id = '')
    {
        if($this->input->post())
        {
            $data  = $this->input->post();
            if(empty($id))
            {
                $data['note'] = $this->input->post('note', false);
                $success = $this->quotes_orders_model->add($data);
                if($success)
                {
                    set_alert('success', _l('cong_add_true'));
                    redirect(admin_url('quotes_orders'));
                }
                else
                {
                    set_alert('success', _l('cong_add_true'));
                    redirect(admin_url('quotes_orders/detail'));
                }
            }
            else
            {
                $data['note'] = $this->input->post('note', false);
                $success = $this->quotes_orders_model->update($id, $data);
                if($success)
                {
                    set_alert('success', _l('cong_update_true'));
                    redirect(admin_url('quotes_orders'));
                }
                else
                {
                    set_alert('success', _l('cong_update_true'));
                    redirect(admin_url('quotes_orders/detail'));
                }

            }
        }
        else
        {
            if (!has_permission('quotes_orders', '', 'create')) {
                    ajax_access_denied();
            }
            if($id != '')
            {
                $data['title']          = _l('cong_update_quotes_orders');
                $data['quotes_orders'] = $this->quotes_orders_model->get($id);

                $data['staff'] = get_table_where(db_prefix().'staff', '( active = 1 or staffid = '.$data['quotes_orders']->assigned.' )');

                $this->db->where('userid', $data['quotes_orders']->client);
                $data['clients'] = $this->db->get(db_prefix().'clients')->result_array();

                $data['shipping'] = get_table_where('tblshipping_client', ['client' => $data['quotes_orders']->client]);
            }
            else
            {

                //Lấy 10 khách hàng
                $this->db->limit(10);
                $data['clients'] = $this->db->get(db_prefix().'clients')->result_array();
                //End lấy khách hàng

                $data['staff'] = get_table_where(db_prefix().'staff', ['active' => 1]);
                $data['title']          = _l('cong_add_quotes_orders');
                $data['shipping'] = [];
            }
            $this->load->view('admin/quotes_orders/detail', $data);
        }
    }
    public function getItemsAjax()
    {
        $search = $this->input->post('q');
        if(!empty($search))
        {
            $this->db->select('id, name, code, price, avatar');
            $this->db->like('name', $search);
            $this->db->or_like('code', $search);
            $this->db->limit(100);
            $items = $this->db->get('tblitems')->result_array();
            echo json_encode($items);die();
        }
        echo json_encode([]);die();
    }
    public function getCustomerAjax()
    {
        $search = $this->input->post('q');
        if(!empty($search))
        {
            $this->db->select('userid, company,concat(prefix_client,code_client," - ",code_type) as full_code');
            $this->db->like('company', $search);
            $this->db->limit(100);
            $customer = $this->db->get('tblclients')->result_array();
            echo json_encode($customer);die();
        }
        echo json_encode([]);die();
    }


    //Cập nhật trạng thái

    public function CancelStatus()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $quotes_orders = $this->db->get('tblquotes_orders')->row();
            if(!empty($quotes_orders))
            {
                if($quotes_orders->status == 0)
                {
                    $this->db->where('id', $quotes_orders->id);
                    $success = $this->db->update('tblquotes_orders', ['status' => 2]);
                    if(!empty($success))
                    {
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'success',
                            'message' => _l('cong_cancel_quotes_orders_true')
                        ]);die();
                    }
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => _l('cong_cancel_quotes_orders_false')
                    ]);die();
                }
                else
                {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => _l('cong_not_update_status_quotes_orders')
                    ]);die();
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_update_status_false')
        ]);die();
    }

    public function restore_orders()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $quotes_orders = $this->db->get('tblquotes_orders')->row();
            if(!empty($quotes_orders))
            {
                if($quotes_orders->status == 2)
                {
                    $this->db->where('id', $id);
                    $success = $this->db->update('tblquotes_orders', ['status' => 0]);
                    if(!empty($success))
                    {
                        echo json_encode([
                            'alert_type' => 'success',
                            'success' => true,
                            'message' => _l('cong_restorn_true')
                        ]);die();
                    }
                    echo json_encode([
                        'alert_type' => 'danger',
                        'success' => false,
                        'message' => _l('cong_restorn_false')
                    ]);die();
                }
            }
        }
        echo json_encode([
            'alert_type' => 'danger',
            'success' => false,
            'message' => _l('cong_restorn_false')
        ]);die();
    }

    public function delete_quotes_orders()
    {
        $id = $this->input->post('id');
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $success = $this->db->delete('tblquotes_orders');
            if(!empty($success))
            {
                $this->db->where('id_quotes_orders', $id);
                $this->db->delete('tblquotes_orders_items');
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true')
                ]);die();

            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_delete_false')
        ]);die();
    }


    public function loadViewOrder($id = "")
    {
        if(!empty($id))
        {
            $data['title'] = _l('cong_detail_orders');
            $data['quotes_orders'] = $this->quotes_orders_model->get_view($id);
            echo json_encode(['success' => true, 'data' => $this->load->view('admin/quotes_orders/view_modal', $data , true)]);die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_found_data')]);die();
    }

    public function getShipping()
    {
        $client = $this->input->post('client');
        if(!empty($client))
        {
            $this->db->where('client', $client);
            $shipping_client = $this->db->get('tblshipping_client')->result_array();
            if(!empty($shipping_client))
            {
                echo json_encode($shipping_client);die();
            }
        }
        echo json_encode([]);die();
    }


    public function ViewModalShipping()
    {
        $client = $this->input->post('client');
        $idSelect = $this->input->post('idSelect');
        if(!empty($client))
        {
            echo json_encode([
                'data' => $this->load->view('admin/orders/Addshipping', ['client' => $client, 'idSelect' => $idSelect], true),
                'success' => true
                ]);die();
        }
        echo json_encode([
            'success' => false,
            'message' => _l('cong_not_found_client_shipping')
        ]);die();
    }
}