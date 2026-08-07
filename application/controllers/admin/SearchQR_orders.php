<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SearchQR_orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function check_qr()
    {
        $code = $this->input->get('code');
        if ($code) {
            $code = explode('||', $code);
            // materials||8511
            if ($code[0] == 'materials') {
                $check = get_table_where('tbl_materials', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('items/view_item/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy mặt hàng';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'products') {
                // products||5707
                $check = get_table_where('tbl_products', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('products/view_product/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy mặt hàng';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'productions_orders') {
                // productions_orders||3004
                $check = get_table_where('tbl_productions_orders', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 2;
                    $data['link'] = admin_url('manufactures/detail_productions_orders/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy phiếu';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'orders') {
                // orders||6933
                $check = get_table_where('tbl_orders', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('orders/view_order/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy phiếu';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'internal_proposal') {
                // internal_proposal||588
                $check = get_table_where('tblinternal_proposal', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('internal_proposal/view/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy phiếu';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'production_report') {
                // production_report||197
                $check = get_table_where('tblproduction_report', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('production_report/modal/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy phiếu';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'quotes') {
                // quotes||49
                $check = get_table_where('tbl_quotes', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['link'] = admin_url('quotes/view_quotes/') . $code[1];
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy phiếu';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            $data['message'] = 'Không tìm thấy số phiếu';
            $data['result'] = false;
            echo json_encode($data);
            die;
        } else {
            $data['result'] = false;
            echo json_encode($data);
            die;
        }
    }
}
