<?php

defined('BASEPATH') or exit('No direct script access allowed');
function view_result($id = '')
{
    $CI =& get_instance();
    $dem_temp = 0;
    $dataMain = get_table_where('tblpromotion', array('id' => $id), '', 'row');
    $type = '';
    if ($dataMain->type == 'discount') {
        $type = _l('promotion_by_discount');
    } elseif ($dataMain->type == 'item') {
        $type = _l('promotion_by_item');
    } elseif ($dataMain->type == 'sales') {
        $type = _l('promotion_by_sales');
    }

    $data['dataResult'] = array();
    $key_main = 0;
    // type = discount
    if ($dataMain->type == 'discount') {
        $data['typeResult'] = 'discount';
        $get_promotion_discount = get_table_where('tblpromotion_discount', array('promotion_id' => $id), '', 'row');
        $get_promotion_discount_amount = get_table_where('tblpromotion_discount_amount', array('promotion_id' => $id),
            'limit_sales DESC');
        //k xét riêng khách hàng
        if ($dataMain->method_of_application != 'other') {
            //xét tất cả khách hàng
            if ($dataMain->area_of_application == 'all') {
                //xét tất cả sản phẩm - thành phẩm
                if ($get_promotion_discount->type_discount == 1) {
                    //lấy khách hàng có đơn thỏa mãn
                    $get_all_customer_order = get_table_where('tbl_orders', array(
                        'status' => 'approved',
                        'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                        'DATE(date) <=' => $get_promotion_discount->time_sales_end
                    ), '', 'result_array', 'customer_id');
                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                            'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $total += $value_customer_order['grand_total_items'];
                        }
                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                            if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                $result_true = true;
                                $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                $result_discount = $value_promotion_discount_amount['limit_discount'];
                                break;
                            }
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                } elseif ($get_promotion_discount->type_discount == 2) {
                    //lấy khách hàng có đơn thỏa mãn
                    $get_all_customer_order = get_table_where('tbl_orders', array(
                        'status' => 'approved',
                        'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                        'DATE(date) <=' => $get_promotion_discount->time_sales_end
                    ), '', 'result_array', 'customer_id');
                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                            'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $get_items = get_table_where('tbl_order_items',
                                array('order_id' => $value_customer_order['id']));
                            foreach ($get_items as $key_items => $value_items) {
                                if ($value_items['type_item'] == 'products') {
                                    $get_product = get_table_where('tbl_products',
                                        array('id' => $value_items['item_id']), '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                                if ($value_items['type_item'] == 'items') {
                                    $get_product = get_table_where('tblitems', array('id' => $value_items['item_id']),
                                        '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                            }
                        }

                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                            if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                $result_true = true;
                                $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                $result_discount = $value_promotion_discount_amount['limit_discount'];
                                break;
                            }
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                }
            } elseif ($dataMain->area_of_application == 'area') {
                if (!empty($dataMain->groups_in) && $dataMain->groups_in != '') {
                    $arrID_group = explode(',', $dataMain->groups_in);
                    $arrID_customer = array();
                    foreach ($arrID_group as $key_group => $value_group) {
                        $get_group = get_table_where('tblcustomer_groups', array('groupid' => $value_group));
                        foreach ($get_group as $k => $v) {
                            $arrID_customer[] = $v['customer_id'];
                        }
                    }
                    if ($get_promotion_discount->type_discount == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                                'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }
                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    } elseif ($get_promotion_discount->type_discount == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                                'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',
                                    array('order_id' => $value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if ($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if ($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    }
                }
            } elseif ($dataMain->area_of_application == 'other') {
                $arrID_customer = array();
                $get_customer = get_table_where('tblpromotion_customer', array('promotion_id' => $id));
                foreach ($get_customer as $key_customer => $value_customer) {
                    $arrID_customer[] = $value_customer['customer_id'];
                }
                if ($arrID_customer != array()) {
                    if ($get_promotion_discount->type_discount == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                                'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }
                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    } elseif ($get_promotion_discount->type_discount == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                                'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',
                                    array('order_id' => $value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if ($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if ($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                                if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                    $result_true = true;
                                    $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                    $result_discount = $value_promotion_discount_amount['limit_discount'];
                                    break;
                                }
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    }
                }
            }
        } elseif ($dataMain->method_of_application == 'other') {
            $arrID_customer = array();
            $get_customer = get_table_where('tblpromotion_customer', array('promotion_id' => $id));
            foreach ($get_customer as $key_customer => $value_customer) {
                $arrID_customer[] = $value_customer['customer_id'];
            }
            if ($arrID_customer != array()) {
                if ($get_promotion_discount->type_discount == 1) {
                    //lấy khách hàng có đơn thỏa mãn
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                    $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                    $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                    $CI->db->group_by('tbl_orders.customer_id');
                    $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                            'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $total += $value_customer_order['grand_total_items'];
                        }
                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                            if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                $result_true = true;
                                $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                $result_discount = $value_promotion_discount_amount['limit_discount'];
                                break;
                            }
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                } elseif ($get_promotion_discount->type_discount == 2) {
                    //lấy khách hàng có đơn thỏa mãn
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_discount->time_sales_start);
                    $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_discount->time_sales_end);
                    $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                    $CI->db->group_by('tbl_orders.customer_id');
                    $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_discount->time_sales_start,
                            'DATE(date) <=' => $get_promotion_discount->time_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $get_items = get_table_where('tbl_order_items',
                                array('order_id' => $value_customer_order['id']));
                            foreach ($get_items as $key_items => $value_items) {
                                if ($value_items['type_item'] == 'products') {
                                    $get_product = get_table_where('tbl_products',
                                        array('id' => $value_items['item_id']), '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                                if ($value_items['type_item'] == 'items') {
                                    $get_product = get_table_where('tblitems', array('id' => $value_items['item_id']),
                                        '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                            }
                        }

                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        foreach ($get_promotion_discount_amount as $key_promotion_discount_amount => $value_promotion_discount_amount) {
                            if ($total >= $value_promotion_discount_amount['limit_sales']) {
                                $result_true = true;
                                $result_type = $value_promotion_discount_amount['type_limit_discount'];
                                $result_discount = $value_promotion_discount_amount['limit_discount'];
                                break;
                            }
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                }
            }
        }
    } // type = sales
    elseif ($dataMain->type == 'sales') {
        $data['typeResult'] = 'sales';
        $data['item_gift'] = array();
        $get_promotion_sales = get_table_where('tblpromotion_sales', array('promotion_id' => $id), '', 'row');
        $get_promotion_sales_item_gift = get_table_where('tblpromotion_sales_item_gift', array('promotion_id' => $id));
        foreach ($get_promotion_sales_item_gift as $key_item_gift => $value_item_gift) {
            if ($value_item_gift['type_item'] == 'items') {
                $get_item_gift = get_table_where('tblitems', array('id' => $value_item_gift['id_item']), '', 'row');
                if ($get_item_gift) {
                    $data['item_gift'][$key_item_gift]['name'] = $get_item_gift->name . ' (' . $get_item_gift->code . ')';
                    $data['item_gift'][$key_item_gift]['type'] = _l('ch_items');
                    $data['item_gift'][$key_item_gift]['quantity'] = number_format($value_item_gift['quantity']);
                }
            } elseif ($value_item_gift['type_item'] == 'product') {
                $get_item_gift = get_table_where('tbl_products', array('id' => $value_item_gift['id_item']), '', 'row');
                if ($get_item_gift) {
                    $data['item_gift'][$key_item_gift]['name'] = $get_item_gift->name . ' (' . $get_item_gift->code . ')';
                    $data['item_gift'][$key_item_gift]['type'] = _l('product');
                    $data['item_gift'][$key_item_gift]['quantity'] = number_format($value_item_gift['quantity']);
                }
            }
        }

        if ($dataMain->method_of_application != 'other') {
            if ($dataMain->area_of_application == 'all') {
                if ($get_promotion_sales->type_sales == 1) {
                    $get_all_customer_order = get_table_where('tbl_orders', array(
                        'status' => 'approved',
                        'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                        'DATE(date) <=' => $get_promotion_sales->date_active_sales_end
                    ), '', 'result_array', 'customer_id');
                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                            'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $total += $value_customer_order['grand_total_items'];
                        }

                        $result_true = false;
                        //áp dụng tiền
                        if ($total >= $get_promotion_sales->limit_points) {
                            $result_true = true;
                            $result_type = $get_promotion_sales->type_limit_points;
                            $result_discount = $get_promotion_sales->limit_points;
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                } elseif ($get_promotion_sales->type_sales == 2) {
                    //lấy khách hàng có đơn thỏa mãn
                    $get_all_customer_order = get_table_where('tbl_orders', array(
                        'status' => 'approved',
                        'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                        'DATE(date) <=' => $get_promotion_sales->date_active_sales_end
                    ), '', 'result_array', 'customer_id');
                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                            'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $get_items = get_table_where('tbl_order_items',
                                array('order_id' => $value_customer_order['id']));
                            foreach ($get_items as $key_items => $value_items) {
                                if ($value_items['type_item'] == 'products') {
                                    $get_product = get_table_where('tbl_products',
                                        array('id' => $value_items['item_id']), '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                                if ($value_items['type_item'] == 'items') {
                                    $get_product = get_table_where('tblitems', array('id' => $value_items['item_id']),
                                        '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                            }
                        }

                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        //áp dụng tiền
                        if ($total >= $get_promotion_sales->limit_points) {
                            $result_true = true;
                            $result_type = $get_promotion_sales->type_limit_points;
                            $result_discount = $get_promotion_sales->limit_points;
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                }
            } elseif ($dataMain->area_of_application == 'area') {
                if (!empty($dataMain->groups_in) && $dataMain->groups_in != '') {
                    $arrID_group = explode(',', $dataMain->groups_in);
                    $arrID_customer = array();
                    foreach ($arrID_group as $key_group => $value_group) {
                        $get_group = get_table_where('tblcustomer_groups', array('groupid' => $value_group));
                        foreach ($get_group as $k => $v) {
                            $arrID_customer[] = $v['customer_id'];
                        }
                    }
                    if ($get_promotion_sales->type_sales == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                                'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }

                            $result_true = false;
                            //áp dụng tiền
                            if ($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    } elseif ($get_promotion_sales->type_sales == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                                'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',
                                    array('order_id' => $value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if ($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if ($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            //áp dụng tiền
                            if ($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    }
                }
            } elseif ($dataMain->area_of_application == 'other') {
                $arrID_customer = array();
                $get_customer = get_table_where('tblpromotion_customer', array('promotion_id' => $id));
                foreach ($get_customer as $key_customer => $value_customer) {
                    $arrID_customer[] = $value_customer['customer_id'];
                }
                if ($arrID_customer != array()) {
                    if ($get_promotion_sales->type_sales == 1) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                                'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $total += $value_customer_order['grand_total_items'];
                            }

                            $result_true = false;
                            //áp dụng tiền
                            if ($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    } elseif ($get_promotion_sales->type_sales == 2) {
                        //lấy khách hàng có đơn thỏa mãn
                        $CI->db->select('tbl_orders.*');
                        $CI->db->where('tbl_orders.status', 'approved');
                        $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                        $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                        $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                        $CI->db->group_by('tbl_orders.customer_id');
                        $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                        foreach ($get_all_customer_order as $key => $value) {
                            //tổng đơn hàng của khách hàng
                            $get_customer_order = get_table_where('tbl_orders', array(
                                'status' => 'approved',
                                'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                                'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                                'customer_id' => $value['customer_id']
                            ));
                            $total = 0;
                            foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                                $get_items = get_table_where('tbl_order_items',
                                    array('order_id' => $value_customer_order['id']));
                                foreach ($get_items as $key_items => $value_items) {
                                    if ($value_items['type_item'] == 'products') {
                                        $get_product = get_table_where('tbl_products',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                    if ($value_items['type_item'] == 'items') {
                                        $get_product = get_table_where('tblitems',
                                            array('id' => $value_items['item_id']), '', 'row');
                                        if ($get_product->calculated_on_sales == 1) {
                                            $total += $value_items['total_amount'];
                                        }
                                    }
                                }
                            }

                            //kiểm tra giá trị khuyến mãi
                            $result_true = false;
                            //áp dụng tiền
                            if ($total >= $get_promotion_sales->limit_points) {
                                $result_true = true;
                                $result_type = $get_promotion_sales->type_limit_points;
                                $result_discount = $get_promotion_sales->limit_points;
                            }
                            if ($result_true == true) {
                                $dem_temp++;
                            }
                        }
                    }
                }
            }
        } elseif ($dataMain->method_of_application == 'other') {
            $arrID_customer = array();
            $get_customer = get_table_where('tblpromotion_customer', array('promotion_id' => $id));
            foreach ($get_customer as $key_customer => $value_customer) {
                $arrID_customer[] = $value_customer['customer_id'];
            }
            if ($arrID_customer != array()) {
                if ($get_promotion_sales->type_sales == 1) {
                    //lấy khách hàng có đơn thỏa mãn
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                    $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                    $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                    $CI->db->group_by('tbl_orders.customer_id');
                    $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                            'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $total += $value_customer_order['grand_total_items'];
                        }

                        $result_true = false;
                        //áp dụng tiền
                        if ($total >= $get_promotion_sales->limit_points) {
                            $result_true = true;
                            $result_type = $get_promotion_sales->type_limit_points;
                            $result_discount = $get_promotion_sales->limit_points;
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                } elseif ($get_promotion_sales->type_sales == 2) {
                    //lấy khách hàng có đơn thỏa mãn
                    $CI->db->select('tbl_orders.*');
                    $CI->db->where('tbl_orders.status', 'approved');
                    $CI->db->where('DATE(tbl_orders.date) >=', $get_promotion_sales->date_active_sales_start);
                    $CI->db->where('DATE(tbl_orders.date) <=', $get_promotion_sales->date_active_sales_end);
                    $CI->db->where_in('tbl_orders.customer_id', $arrID_customer);
                    $CI->db->group_by('tbl_orders.customer_id');
                    $get_all_customer_order = $CI->db->get('tbl_orders')->result_array();

                    foreach ($get_all_customer_order as $key => $value) {
                        //tổng đơn hàng của khách hàng
                        $get_customer_order = get_table_where('tbl_orders', array(
                            'status' => 'approved',
                            'DATE(date) >=' => $get_promotion_sales->date_active_sales_start,
                            'DATE(date) <=' => $get_promotion_sales->date_active_sales_end,
                            'customer_id' => $value['customer_id']
                        ));
                        $total = 0;
                        foreach ($get_customer_order as $key_customer_order => $value_customer_order) {
                            $get_items = get_table_where('tbl_order_items',
                                array('order_id' => $value_customer_order['id']));
                            foreach ($get_items as $key_items => $value_items) {
                                if ($value_items['type_item'] == 'products') {
                                    $get_product = get_table_where('tbl_products',
                                        array('id' => $value_items['item_id']), '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                                if ($value_items['type_item'] == 'items') {
                                    $get_product = get_table_where('tblitems', array('id' => $value_items['item_id']),
                                        '', 'row');
                                    if ($get_product->calculated_on_sales == 1) {
                                        $total += $value_items['total_amount'];
                                    }
                                }
                            }
                        }

                        //kiểm tra giá trị khuyến mãi
                        $result_true = false;
                        //áp dụng tiền
                        if ($total >= $get_promotion_sales->limit_points) {
                            $result_true = true;
                            $result_type = $get_promotion_sales->type_limit_points;
                            $result_discount = $get_promotion_sales->limit_points;
                        }
                        if ($result_true == true) {
                            $dem_temp++;
                        }
                    }
                }
            }
        }
    }
    // end
    return $dem_temp;
}

function handle_slide_image_upload($item_id = '')
{
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != array()) {
        $images = "";
        if ($item_id == '') {
            return;
        }
        $path = get_upload_path_by_slide('slide') . $item_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["image"]["name"]);
            $extension = $path_parts['extension'];
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
            $filename = unique_filename($path, $_FILES["image"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $images = $images . ',' . substr($path, strpos($path, 'uploads')) . $filename;
                // Remove original image
            }
        }
        if ($images != "") {
            $images = str_replace(',,', ',', trim($images, ','));
            $CI->db->where('id', $item_id);
            $CI->db->update('tbl_slideshow', array('image' => $images));
        }
        return true;
    }
    return false;
}

function get_upload_path_by_slide($type)
{
    switch ($type) {
        case 'slide':
            return 'uploads/slide/';
            break;
        default:
            return false;
    }
}

function activity_log_v2($type_parent_obj = '', $table_obj = '', $id_obj = '', $name_obj = '', $content = '',$action = '')
{
    $in = array(
        'type_parent_obj' => $type_parent_obj,
        'table_obj' => $table_obj,
        'id_obj' => $id_obj,
        'name_obj' => $name_obj,
        'content' => $content,
        'staff_id' => get_staff_user_id(),
        'date' => date('Y-m-d H:i:s'),
        'actions' => $action
    );
    $CI =& get_instance();
    $CI->db->insert('tblactivity_log_v2', $in);
    return true;
}


function recursive_Category_By_Items($id = 0, &$output = null, $parent_id = 0, $indent = null, $stt = 0)
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('tblcategories');
    $CI->db->where('tblcategories.category_parent', $parent_id);
    $CI->db->order_by('tblcategories.category_parent');
    $query = $CI->db->get()->result_array();
    if ($query && $stt == 0) {
        $output .= '<option disabled value="">NHÓM HÀNG HÓA</option>';
    }
    foreach ($query as $key => $item) {
        if ($item['category_parent'] == $parent_id) {
            $disabled = '';
            if ($item['id'] == $id && $id != 0) {
                continue;
            }
            $output .= '<option ' . $disabled . '  value="items_' . $item['id'] . '">' . $indent . '➪ ' . $item['category'] . "</option>";
            recursive_Category_By_Items($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;", 1);
        }
    }
    return $output;
}

function recursive_Category_By_Products($id = 0, &$output = null, $parent_id = 0, $indent = null, $stt = 0)
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('tbl_category_products');
    $CI->db->where('tbl_category_products.parent_id', $parent_id);
    $CI->db->order_by('tbl_category_products.parent_id');
    $query = $CI->db->get()->result_array();
    if ($query && $stt == 0) {
        $output .= '<option disabled value="">NHÓM THÀNH PHẨM</option>';
    }
    foreach ($query as $key => $item) {
        if ($item['parent_id'] == $parent_id) {
            $disabled = '';
            if ($item['id'] == $id && $id != 0) {
                continue;
            }
            $output .= '<option ' . $disabled . '  value="product_' . $item['id'] . '">' . $indent . '➪ ' . $item['name'] . "</option>";
            recursive_Category_By_Products($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;", 1);
        }
    }
    return $output;
}

function employee_manage_staff()
{
    $CI = &get_instance();
    $arrStaffId = array();
    $staffid = get_staff_user_id();

    $arrID_child = array();
    get_childs_id_helper($staffid, $arrID_child);

    array_push($arrID_child, $staffid);

    if ($arrID_child) {
        $arrStaffId = array_unique($arrID_child);
    }

    return $arrStaffId;
}

function employee_manage_staff_app($staffid)
{
    $CI = &get_instance();
    $arrStaffId = array();

    $arrID_child = array();
    get_childs_id_helper($staffid, $arrID_child);

    array_push($arrID_child, $staffid);

    if ($arrID_child) {
        $arrStaffId = array_unique($arrID_child);
    }

    return $arrStaffId;
}

if (!function_exists('get_staff_user_id_branch_app')) {
    function get_staff_user_id_branch_app($staff_user_id)
    {
        $staff = get_table_where('tblstaff', array('staffid' => $staff_user_id), '', 'row')->id_branch;
        return $staff;
    }
}
if (!function_exists('get_staff_user_id_branch')) {
    function get_staff_user_id_branch()
    {
        $staff = get_table_where('tblstaff', array('staffid' => get_staff_user_id()), '', 'row')->id_branch;
        return $staff;
    }
}

function get_childs_id_helper($parent_id = '', &$result = array())
{
    $CI = &get_instance();
    $CI->db->where('staff_id', $parent_id);
    $items = $CI->db->get('tbl_employee_manage_staff')->result();

    foreach ($items as $value) {
        array_push($result, $value->employee_id);
        get_childs_id_helper($value->employee_id, $result);
    }
}

function add_notification_app1($values, $staff_id)
{
    $CI = &get_instance();
    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }

    $data['fromuserid'] = $staff_id;
    $data['fromclientid'] = 0;
    $data['from_fullname'] = get_staff_full_name($staff_id);

    if (isset($data['fromcompany'])) {
        unset($data['fromuserid']);
        unset($data['from_fullname']);
    }

    $data['date'] = date('Y-m-d H:i:s');
    $data = hooks()->apply_filters('notification_data', $data);

    // Prevent sending notification to non active users.
    if (isset($data['touserid']) && $data['touserid'] != 0) {
        $CI->db->where('staffid', $data['touserid']);
        $user = $CI->db->get(db_prefix() . 'staff')->row();
        if (!$user || $user && $user->active == 0) {
            return false;
        }
    }

    $CI->db->insert(db_prefix() . 'notifications', $data);

    if ($notification_id = $CI->db->insert_id()) {
        hooks()->do_action('notification_created', $notification_id);
    }

    return true;
}


function total_rows_join($table, $where = array(), $join = array(), $group_by)
{
    $CI = &get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } else {
        if (strlen($where) > 0) {
            $CI->db->where($where);
        }
    }
    if (sizeof($join) > 0) {
        $CI->db->join($join);
    }
    if ($group_by) {
        $CI->db->group_by($group_by);
    }

    return $CI->db->count_all_results($table);
}

function _dnew($date)
{
    $formatted = '';
    $format = "%d/%m/%Y %H:%M";
    $formatted = strftime($format, strtotime($date));

    return $formatted;
}

if (!function_exists('sumTotalRowFirst')) {
    function sumTotalRowFirst($product_id, $type = '', $items = array(), $index = 0, $field = '', $break = '')
    {
        $CI = &get_instance();
        $total = 0;
        if (is_numeric($product_id) && isset($items)) {
            for ($i = $index; $i < count($items); $i++) {
                $row = (object)$items[$i];
                $info = null;
                if ($row->type_item == "products") {
                    $info = $CI->products_model->rowProduct($row->item_id);
                } elseif ($row->type_item == "items") {
                    $info = $CI->items_model->rowItems($row->item_id);
                } elseif ($row->type_item == "materials") {
                    $info = $CI->items_model->rowMaterial($row->item_id);;
                }
                if ($info['code'] != $break) {
                    break;
                }
                if (($row->item_id == $product_id) && ($row->type_item == $type)) {
                    $total += $row->{$field};
                }
                if (($row->item_id != $product_id) && ($row->type_item == $type)) {
                    break;
                }
            }
        }
        return $total;
    }
}

function recursiveCategoryStagesNew()
{
    $CI = &get_instance();

    $CI->db->select('*');
    $CI->db->from('tbl_category_stages');
    $query = $CI->db->get()->result_array();
    $category = array();
    $dem = 0;
    foreach ($query as $key => $item) {

        $CI->db->select('*');
        $CI->db->where('category_stages', $item['id']);
        $CI->db->from('tbl_stages');
        $detail = $CI->db->get()->result_array();
        if (!empty($detail)) {
            $item['id'] = 'main__' . $item['id'];
            $item['main'] = 1;
            $item['name'] = $item['name'];
            $category[$dem] = $item;
            $dem++;
            foreach ($detail as $k => $v) {
                $v['id'] = 'detail__' . $v['id'];
                $v['main'] = 0;
                $v['name'] = '&nbsp;&nbsp;&nbsp;&nbsp;➪ ' . $v['name'];
                $category[$dem] = $v;
                $dem++;
            }
        }
    }
    return $category;
}

function countHourCheckOut($startTime, $endTime)
{
    $sTime = strtotime($startTime);
    $eTime = strtotime($endTime);

    $minute = date("i", $sTime);
    $hour = date("H", $sTime);

    $convert = strtotime("-$minute minutes", $eTime);
    $convert = strtotime("-$hour hours", $convert);
    $new_time = date('H:i', $convert);

    return $new_time;
}

function countHourCheckOutNew($new_time)
{
    if ((string)$new_time === (string)0) {
        return $new_time;
    }
    $HourNew = date("H", strtotime($new_time));
    $minuteNew = date("i", strtotime($new_time));
    $minuteNew = formatNumber($minuteNew / 60);
    $newTime = (float)$HourNew + (float)$minuteNew;

    return $newTime;
}

function countHourCheckOut30($startTime, $endTime)
{
    $sTime = strtotime($startTime);
    $eTime = strtotime($endTime);

    $minute = date("i", $sTime);
    $hour = date("H", $sTime);

    $convert = strtotime("+$minute minutes", $eTime);
    $convert = strtotime("+$hour hours", $convert);
    $new_time = date('H:i', $convert);

    return $new_time;
}

function countHourDetail($startTime, $endTime)
{

    $new_time = (float)$startTime + (float)$endTime;

    return $new_time;
}


function sumHourStaff($sTime, $eTime = "00:00")
{

    $hour = 00;
    $shour = explode(':', $sTime);
    $ehour = explode(':', $eTime);
    $shours = 00;
    $ehours = 00;
    $sminutes = 00;
    $eminutes = 00;

    if (!empty(isset($shour[1]))) {
        $sminutes = $shour[1];
    }
    if (!empty(isset($ehour[1]))) {
        $eminutes = $ehour[1];
    }
    if (!empty(isset($shour[0]))) {
        $shours = $shour[0];
    }
    if (!empty(isset($ehour[0]))) {
        $ehours = $ehour[0];
    }

    $minute = $sminutes + $eminutes;

    $hour = $shours + $ehours;

    if ($minute < 10) {
        $minute = '0' . $minute;
    }

    if ($minute >= 60) {
        $minute = $minute - 60;
        $hour = $hour + 1;
        if ($minute != '00') {
            $hour = $hour . '.' . $minute;
        }
    } else {
        if ($minute != '00') {
            $hour = $hour . '.' . $minute;
        }
    }

    return $hour;
}

if (!function_exists('getAllDateInMonth')) {
    function getAllDateInMonth($month, $year, $format = "d/m/Y")
    {
        $list = [];

        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $ymd = date('Y-m-d', $time);
                $list[$ymd] = date($format, $time);
            }
        }

        return $list;
    }
}

if (!function_exists('convertDate')) {
    function convertDate($date_work)
    {
        $date = '';
        switch (true) {
            case $date_work == 'Mon':
                $date = 'T2';
                break;
            case $date_work == 'Tue':
                $date = 'T3';
                break;
            case $date_work == 'Wed':
                $date = 'T4';
                break;
            case $date_work == 'Thu':
                $date = 'T5';
                break;
            case $date_work == 'Fri':
                $date = 'T6';
                break;
            case $date_work == 'Sat':
                $date = 'T7';
                break;
            case $date_work == 'Sun':
                $date = 'CN';
                break;
            default:
                $date = $date_work;
                break;
        }

        return $date;
    }
}
if (!function_exists('getStaffDeparment')) {
    function getStaffDeparment($arrDepartment = [])
    {
        $arrData = [];
        $keyStep = 0;
        $CI = &get_instance();

        $CI->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name
        ');
        $CI->db->from('tblstaff');
        $staffs = $CI->db->get()->result_array();
        if (!empty($staffs)) {
            $arrData[$keyStep]['departmentid'] = 0;
            $arrData[$keyStep]['name'] = lang('Nhân viên');
            $arrData[$keyStep]['staffs'] = $staffs;
        }

        return $arrData;
    }
}

function getPersonDeparmentdt($type = 1, $or_where_in = [])
{
    $arrData = [];
    $arrStaffId = [];
    $keyStep = 0;

    $CI = &get_instance();
    $tbDepartment = "(
        SELECT
            tblstaff_departments.staffid as staffid,
            GROUP_CONCAT(tbl_room.name) as name_department
        FROM tbldepartments
        JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
        JOIN tbl_room ON tbl_room.id = tbldepartments.room_id
        GROUP BY tblstaff_departments.staffid
    ) tb_department";
    $CI->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
            tblroles.name as name_roles,
            tb_department.name_department as name_department,
            tblstaff.salary_bhxh_new as salary_bhxh_new,
            tblstaff.salary_bhxh as salary_bhxh,
        ');
    $CI->db->from('tblstaff');
    $CI->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
    $CI->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
    $CI->db->group_start();
    $CI->db->where('active', 1);
    if (!empty($or_where_in)) {
        $CI->db->or_where_in('staffid', $or_where_in);
    }
    $CI->db->group_end();
    $staffs = $CI->db->get()->result_array();
    if (!empty($staffs)) {
        $arrData[$keyStep]['departmentid'] = 0;
        $arrData[$keyStep]['name'] = lang('Nhân viên');
        $arrData[$keyStep]['staffs'] = $staffs;
    }

    return $arrData;
}

if (!function_exists('getMonth')) {
    function getMonth()
    {
        $option[''] = '';
        $option['01'] = lang('01');
        $option['02'] = lang('02');
        $option['03'] = lang('03');
        $option['04'] = lang('04');
        $option['05'] = lang('05');
        $option['06'] = lang('06');
        $option['07'] = lang('07');
        $option['08'] = lang('08');
        $option['09'] = lang('09');
        $option['10'] = lang('10');
        $option['11'] = lang('11');
        $option['12'] = lang('12');

        return $option;
    }
}

if (!function_exists('getYear')) {
    function getYear()
    {
        $year = [];
        $year[''] = '';
        for ($i = -1; $i < 5; $i++) {
            $date = date('Y', strtotime(date('Y') . ' -' . $i . ' year'));
            $year[$date] = $date;
        }

        return $year;
    }
}

if (!function_exists('getListOvertime')) {
    function getListOvertime()
    {
        $listOvertime = [
            [
                'id' => 1,
                'name' => 'Lễ tết',
            ],
        ];

        return $listOvertime;
    }
}

function footerPdf($year = '')
{
    if (empty($year)) {
        $year = date('Y');
    }
    echo '<table>
        <tr>
            <td class="text-center bold"></td>
            <td class="text-center bold"></td>
            <td class="text-center bold">Ngày.....Tháng.....Năm ' . $year . '</td>
        </tr>
        <tr>
            <td class="text-center"><span class="bold">NGƯỜI LẬP</span> <div>(Ký,ghi rõ họ tên)</div></td>
            <td class="text-center"><span class="bold">KẾ TOÁN</span> <div>(Ký,ghi rõ họ tên)</div></td>
            <td class="text-center"><span class="bold">GIÁM ĐỐC</span> <div>(Ký,ghi rõ họ tên)</div></td>
        </tr>
        </table>';
}

function get_branch_staff($staffid = 0)
{
    $staff_id = get_staff_user_id();
    if (!empty($staffid)) {
        $staff_id = $staffid;
    }
    $arrBranch = [];
    $CI = &get_instance();
    $CI->db->select("tblstaff_branch.id_branch");
    $CI->db->from('tblstaff_branch');
    $CI->db->where('staffid', $staff_id);
    $branchStaff = $CI->db->get()->result_array();
    if (!empty($branchStaff)) {
        foreach ($branchStaff as $key => $value) {
            $arrBranch[] = $value['id_branch'];
        }
    }
    return $arrBranch;
}

function getListBranch()
{
    $CI = &get_instance();
    $arrBranch = get_branch_staff();
    $CI->db->select('tblbranch.*');
    $CI->db->from('tblbranch');
    if (!is_admin()) {
        if (!empty($arrBranch)) {
            $convertStr = implode(',', $arrBranch);
            $CI->db->where('EXISTS (
                    SELECT id_branch
                    FROM tblstaff_branch
                    WHERE tblstaff_branch.id_branch = tblbranch.id AND tblbranch.id IN (' . $convertStr . ')
                )');
        } else {
            $CI->db->where('tblbranch.id', 0);
        }
    }
    return $CI->db->get()->result_array();
}

function createDateRangeArray($month, $year)
{
    $date_array = array();
    $start_date = mktime(0, 0, 0, $month, 1, $year);
    $end_date = mktime(0, 0, 0, $month + 1, 0, $year);

    while ($start_date <= $end_date) {
        $date_array[] = date('Y-m-d', $start_date);
        $start_date = strtotime('+1 day', $start_date);
    }

    return $date_array;
}


function get_childs_id_role($parent_id = '', &$result = array())
{
    $CI = &get_instance();
    array_push($result, $parent_id);
    $CI->db->where('roles_parent', $parent_id);
    $items = $CI->db->get('tblroles')->result();
    foreach ($items as $value) {
        get_childs_id_role($value->roleid, $result);
    }
}

function get_full_item_new($id, $type)
{
    $CI = &get_instance();
    if ($type == 'items') {
        $CI->db->select('tblitems.*,tblunits.unit as unit_name,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,"items" as type_item,"" as name_species,"" as name_category,"" as name_mode')->distinct();
        $CI->db->from('tblitems');
        $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
        $CI->db->order_by('tblitems.id', 'desc');
        if (is_numeric($id)) {

            $CI->db->where('tblitems.id', $id);
            $item = $CI->db->get()->row();
            if (empty($item)) {
                return null;
            }
            $whereJoin = array();
            $whereJoin['where'] = array(
                'id_items ' => $id,
                'type_items ' => $type,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'product_quantity';
            $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
            $item->quantity_warehoue = $quantity_warehoue;
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    } elseif ($type == 'nvl') {
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
        $CI->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,' . $table . '.recipe,paper,longs,wide,"materials" as type_item,tbl_species.name as name_species,tbl_category_items.name as name_category,tbl_mode_materials.name as name_mode')->distinct();
        $CI->db->from($table);
        $CI->db->join('tbl_category_items', 'tbl_category_items.id=' . $table . '.category_id', 'inner');
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
        $CI->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
        $CI->db->join('tbl_species', 'tbl_species.id=' . $table . '.species', 'left');
        $CI->db->join('tbl_mode_materials', 'tbl_mode_materials.id=' . $table . '.mode_id', 'left');
        $CI->db->order_by($table . '.id', 'desc');
        if (is_numeric($id)) {

            $CI->db->where($table . '.id', $id);
            $item = $CI->db->get()->row();
            if (empty($item)) {
                return null;
            }
            $whereJoin = array();
            $whereJoin['where'] = array(
                'id_items ' => $id,
                'type_items ' => $type,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'product_quantity';
            $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
            $item->quantity_warehoue = $quantity_warehoue;
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    } elseif ($type == 'product') {
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
        $CI->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblunits.unit as unit_name_payment,stock_unit.unit as unit_name_stock,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,type_products as type_item,tbl_species.name as name_species,tbl_category_products.name as name_category,"" as name_mode')->distinct();
        $CI->db->from($table);
        $CI->db->join('tbl_category_products', 'tbl_category_products.id=' . $table . '.category_id', 'inner');
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
        $CI->db->join('tbl_species', 'tbl_species.id=' . $table . '.species', 'left');
        $CI->db->order_by($table . '.id', 'desc');
        if (is_numeric($id)) {
            $CI->db->where($table . '.id', $id);
            $item = $CI->db->get()->row();
            if (empty($item)) {
                return null;
            }
            $whereJoin = array();
            $whereJoin['where'] = array(
                'id_items ' => $id,
                'type_items ' => $type,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'product_quantity';
            $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
            $item->quantity_warehoue = $quantity_warehoue;
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    } else {
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row');
        if (!empty($table->table)) {
            $table = $table->table;
        } else {
            return null;
        }
        $CI->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,"" as name_species,"" as name_category,"" as name_mode')->distinct();
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->order_by($table . '.id', 'desc');
        if (is_numeric($id)) {

            $CI->db->where($table . '.id', $id);
            $item = $CI->db->get()->row();
            if (empty($item)) {
                return null;
            }
            $whereJoin = array();
            $whereJoin['where'] = array(
                'id_items ' => $id,
                'type_items ' => $type,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'product_quantity';
            $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
            $item->quantity_warehoue = $quantity_warehoue;
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    }
}

function _dt_new($date, $is_timesheet = false)
{
    $original = $date;

    if ($date == '' || is_null($date) || $date == '0000-00-00 00:00:00') {
        return '';
    }

    $format = get_current_date_format_new();
    $hour12 = (get_option('time_format') == 24 ? false : true);

    if ($is_timesheet == false) {
        $date = strtotime($date);
    }

    if ($hour12 == false) {
        $tf = '%H:%M:%S';
        if ($is_timesheet == true) {
            $tf = '%H:%M';
        }
        $date = strftime($format . ' ' . $tf, $date);
    } else {
        $date = date(get_current_date_format_new(true) . ' g:i A', $date);
    }

    return hooks()->apply_filters('after_format_datetime', $date,
        ['original' => $original, 'is_timesheet' => $is_timesheet]);
}

function get_current_date_format_new($php = false)
{
    $format = "Y/m/d|%Y/%m/%d";
    $format = explode('|', $format);

    $format = hooks()->apply_filters('get_current_date_format_new', $format, $php);

    if ($php == false) {
        return $format[1];
    }

    return $format[0];
}

function get_level_role($id = 0)
{
    $CI = &get_instance();
    $ktr = get_table_where('tblroles', array('roleid' => $id), '', 'row_array');
    $lever = 0;
    if (!empty($ktr)) {
        if ($ktr['roles_parent'] == null || $ktr['roles_parent'] == 0) {
            $lever = 1;
        } else {
            $lever = 1;
            $parent = $ktr['roles_parent'];
            while ($parent > 0) {
                $ktr = get_table_where('tblroles', array('roleid' => $parent), '', 'row');
                $parent = !empty($ktr) ? $ktr->roles_parent : 0;
                $lever++;
            }
        }
    }
    return $lever;
}

function ViewHtmlImagesDt($url = '')
{
    if (empty($url)) {
        $url = base_url('assets/images/tnh/no_image.png');
    }
    return '<div class="td-image">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:120px;height:80px;margin: auto;">
                                <a href="' . $url . '" data-lightbox="customer-profile" class="display-block mbot5">
                                    <div class="">
                                        <img src="' . $url . '" style="border-radius: 5%;width:120px;height:80px">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
}

if (!function_exists('responseData')) {
    function responseData($data, $statusCode = 200, $headers = []) {

        http_response_code($statusCode);
        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

function diffDate($first_date = '',$second_date = ''){
    if (empty($first_date)){
        return 0;
    }
    $first_date = strtotime($first_date);
    $second_date = strtotime($second_date);
    $datediff = abs($first_date - $second_date);
    return floor($datediff / (60*60*24));
}

function recursiveRoles($id = 0, &$output = null, $parent_id = 0, $indent = null)
{
    $CI = &get_instance();

    $CI->db->select('*');
    $CI->db->from('tblroles');
    $CI->db->where('tblroles.roles_parent', $parent_id);
    $CI->db->where('tblroles.active_role', 1);
    $query = $CI->db->get()->result_array();

    foreach ($query as $key => $item) {
        if ($item['roles_parent'] == $parent_id) {
            $disabled = '';
            if ($item['roleid'] == $id && $id != 0) {
                continue;
            }
            $output .= '<option value="' . $item['roleid'] . '">' . $indent . '➪ ' . $item['name'] . "</option>";
            recursiveRoles($id, $output, $item['roleid'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
        }
    }

    return $output;
}

function getListFiveCoreValue($id = 0){
    $data = [
        [
            'id' => 1,
            'name' => 'Không có khiếu nại về sự thiếu tôn trọng khách hàng hay đồng nghiệp'
        ],
        [
            'id' => 2,
            'name' => 'Thực hiện hỗ trợ đồng nghiệp ngay khi có yêu cầu hoặc sẵn sàng nhận nhiệm vụ mới'
        ],
        [
            'id' => 3,
            'name' => 'Công việc chỉ bàn giao khi đạt yêu cầu và phải đúng hạn'
        ],
        [
            'id' => 4,
            'name' => 'Công việc nếu có nguy cơ không hoàn thành phải báo cáo ngay cấp trên để xin chỉ đạo, hỗ trợ'
        ],
        [
            'id' => 5,
            'name' => 'Phải xác nhận bàn giao và có hướng dẫn thực hiện công việc'
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getListFollow($id = 0){
    $data = [
        [
            'id' => 1,
            'name' => 'Quy tắc ứng xử'
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getListColumTable(){
    $data = [
        [
           'id' => 'quota_time',
           'name' => 'Định mức thời gian'
        ],
        [
            'id' => 'time_expected',
            'name' => 'Thời gian dự kiến'
        ],
        [
            'id' => 'date_start',
            'name' => 'Thời gian bắt đầu'
        ],
        [
            'id' => 'date_end',
            'name' => 'Thời gian kết thúc'
        ],
        [
            'id' => 'staff',
            'name' => 'Nhân viên'
        ]
    ];
    return $data;
}

function getDataModeration($suggest_id = 0,$colum_id = 0,$table = '',$type = '',$excel = false){
    $CI = &get_instance();
    $CI->db->select('tbl_category_recommended.*');
    $CI->db->from('tbl_category_recommended');
    $CI->db->where('name_table',$table);
    if (!empty($type)){
        $CI->db->where('type',$type);
    }
    if ($table == 'tbl_suggest_check'){
        $CI->db->where('ballot_type',$type);
    }
    $dtData = $CI->db->get()->row_array();
    $dtTask = [];
    $result = null;
    if (!empty($dtData)){

        $tb_tamp = "(
            SELECT
                tbltask_assigned.taskid as task_id,
                GROUP_CONCAT(tblstaff.staffid) as staff_task,
                GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
            FROM tbltask_assigned
            JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid
            GROUP BY tbltask_assigned.taskid
        ) tb_tamp";

        $CI->db->select('
            tbltasks.id,
            tbltasks.name,
            startdate,
            datefinished,
            tblcategory_tasks.time as time,
            tb_tamp.staff_task,
            tb_tamp.staff_name_task');
        $CI->db->from('tbltasks');
        $CI->db->join('tblcategory_tasks','tblcategory_tasks.id = tbltasks.category_tasks','left');
        $CI->db->join($tb_tamp,'tb_tamp.task_id = tbltasks.id','left');
        $CI->db->where('suggest_id',$suggest_id);
        $CI->db->where('category_recommended_id',$dtData['id']);
        $CI->db->order_by('id desc');
        $dtTask = $CI->db->get()->row_array();
    }
    if (!empty($dtTask)){
        if ($colum_id == 'quota_time'){
            $result = $dtTask['time'];
        }
        if ($colum_id == 'time_expected'){
            $date = date( 'Y-m-d H:i' , strtotime($dtTask['startdate']));
            $newdate = strtotime ( '+ '.(!empty($dtTask['time']) ? $dtTask['time'] : 0).' minute' , strtotime ($date)) ;
            $date_expected = date( 'Y-m-d H:i' , $newdate);
            $result = _dt($date_expected);
        }
        if ($colum_id == 'date_start'){
            $result = _dt($dtTask['startdate']);
        }
        if ($colum_id == 'date_end'){
            $result = !empty($dtTask['datefinished']) ? _dt($dtTask['datefinished']) : '';
        }
        if ($colum_id == 'staff'){
            if ($excel){
                $result .= $dtTask['staff_name_task'].', ';
                $result = trim($result,', ');
            } else {
                $result = format_members_by_ids_and_names($dtTask['staff_task'], $dtTask['staff_name_task']);
            }
        }
    }
    return $result;
}

function getDataModerationNew($po_id = 0,$stage_id = 0,$colum_id = 0,$excel = false){
    $CI = &get_instance();
    $tb_tamp = "(
        SELECT
            tbltask_assigned.taskid as task_id,
            GROUP_CONCAT(tblstaff.staffid) as staff_task,
            GROUP_CONCAT(CONCAT(tblstaff.firstname,' ',tblstaff.lastname)) as staff_name_task
        FROM tbltask_assigned
        JOIN tblstaff ON tblstaff.staffid = tbltask_assigned.staffid
        GROUP BY tbltask_assigned.taskid
    ) tb_tamp";

    $CI->db->select('
        tbltasks.id,
        tbltasks.name,
        startdate,
        datefinished,
        tblcategory_tasks.time as time,
        tb_tamp.staff_task,
        tb_tamp.staff_name_task');
    $CI->db->from('tbltasks');
    $CI->db->join('tblcategory_tasks','tblcategory_tasks.id = tbltasks.category_tasks','left');
    $CI->db->join($tb_tamp,'tb_tamp.task_id = tbltasks.id','left');
    $CI->db->where('po_id',$po_id);
    $CI->db->where('stage_id',$stage_id);
    $CI->db->order_by('id desc');
    $dtTask = $CI->db->get()->row_array();
    $result = '';
    if (!empty($dtTask)){
        if ($colum_id == 'quota_time'){
            $result = $dtTask['time'];
        }
        if ($colum_id == 'time_expected'){
            $date = date( 'Y-m-d H:i' , strtotime($dtTask['startdate']));
            $newdate = strtotime ( '+ '.(!empty($dtTask['time']) ? $dtTask['time'] : 0).' minute' , strtotime ($date)) ;
            $date_expected = date( 'Y-m-d H:i' , $newdate);
            $result = _dt($date_expected);
        }
        if ($colum_id == 'date_start'){
            $result = _dt($dtTask['startdate']);
        }
        if ($colum_id == 'date_end'){
            $result = !empty($dtTask['datefinished']) ? _dt($dtTask['datefinished']) : '';
        }
        if ($colum_id == 'staff'){
            if ($excel){
                $result .= $dtTask['staff_name_task'].', ';
                $result = trim($result,', ');
            } else {
                $result = format_members_by_ids_and_names($dtTask['staff_task'], $dtTask['staff_name_task']);
            }
        }
    }
    return $result;
}

function recursiveListCriteriaDepartment($data, $parentId = 0,$level = 0)
{
    $array = array();
    foreach ($data as $key => $value) {
        if ($value['parent_id'] == $parentId) {
            $value['level'] = $level;
            $count = 1;
            $children = recursiveListCriteriaDepartment($data, $value['id'],$level+1);
            if ($children) {
                $value['children'] = $children;
                $count += count($children);
            }
            $value['count'] = $count;
            $array[] = ($value);
        }
    }

    return $array;
}

function flatten_children($array) {
    $result = [];

    foreach ($array as $item) {
        // Thêm mục hiện tại vào kết quả
        $result[] = $item;

        // Kiểm tra và thêm children nếu có
        if (!empty($item['children'])) {
            // Gọi đệ quy để lấy các children
            $result = array_merge($result, flatten_children($item['children']));
        }
    }

    return $result;
}

function intToRoman($num) {
    // Mảng các giá trị và ký tự La Mã
    $val = [
        1000, 900, 500, 400,
        100, 90, 50, 40,
        10, 9, 5, 4,
        1
    ];
    $syb = [
        "M", "CM", "D", "CD",
        "C", "XC", "L", "XL",
        "X", "IX", "V", "IV",
        "I"
    ];

    $romanNum = '';
    $i = 0;

    // Lặp qua các giá trị để trừ dần từ num
    while ($num > 0) {
        // Kiểm tra xem giá trị hiện tại có thể trừ được không
        while ($num >= $val[$i]) {
            $romanNum .= $syb[$i]; // Thêm ký tự La Mã vào kết quả
            $num -= $val[$i];      // Giảm giá trị số tự nhiên đi
        }
        $i++;
    }

    return $romanNum;
}

function get_child_kpi_department($id)
{
    $CI = &get_instance();
    $arrData = array();

    $arrID_child = array();
    get_child_kpi_department_helper($id, $arrID_child);

    array_push($arrID_child, $id);

    if ($arrID_child) {
        $arrData = array_unique($arrID_child);
    }

    return $arrData;
}

function get_child_kpi_department_helper($parent_id = '', &$result = array())
{
    $CI = &get_instance();
    $CI->db->where('parent_id', $parent_id);
    $items = $CI->db->get('tbl_kpi_list_criteria_department')->result();

    foreach ($items as $value) {
        array_push($result, $value->id);
        get_child_kpi_department_helper($value->id, $result);
    }
}

function ratingKpiDepartmentOld($point = 0){
    $data = [
        [
            'id' => 1,
            'name' => 'A+',
            'min' => 95,
            'max' => 100,
            'color' => '#f5b896',
            'bonus' => [
                '40% Tháng 13',
                'Rỷ lệ % Thưởng Khác',
                'Lưu CV, Xét Thăng Chức'
            ],
            'discipline' => []
        ],
        [
            'id' => 2,
            'name' => 'A',
            'min' => 81,
            'max' => 94,
            'color' => '#f1a572',
            'bonus' => [
                '20% Tháng 13',
                'Rỷ lệ % Thưởng Khác',
                'Lưu CV, Xét Thăng Chức'
            ],
            'discipline' => []
        ],
        [
            'id' => 3,
            'name' => 'B',
            'min' => 70,
            'max' => 80,
            'color' => '#e68e4d',
            'bonus' => [
                '5% Tháng 13',
                'Rỷ lệ % Thưởng Khác',
                'Đào Tạo Chuyên Môn'
            ],
            'discipline' => []
        ],
        [
            'id' => 4,
            'name' => 'C',
            'min' => 55,
            'max' => 69,
            'color' => '#e17a28',
            'bonus' => [
                'Đào Tạo Lại',
            ],
            'discipline' => [
                '30% Tháng 13',
                '30% Thưởng Khác',
                'Không Xét Tăng Lương',
                'Đào Tạo Lại',
            ]
        ],
        [
            'id' => 5,
            'name' => 'D',
            'min' => 0,
            'max' => 54,
            'color' => '#ce6500',
            'bonus' => [
                'Đào Tạo Lại',
            ],
            'discipline' => [
                '50% Tháng 13',
                'Không Có',
                'Không Xét Tăng Lương',
                'Thuyên Chuyển',
                'Giáng Chức',
                'Thôi Việc',
            ]
        ]
    ];
    if (!empty($point)){
        $data = array_reduce($data, function ($carry, $item) use ($point){
            if ($point >= $item['min'] && $point <= $item['max']) {
                $carry[] = $item;
            }
            return $carry;
        });
        return $data;
    } else {
        return $data;
    }
}

function ratingKpiDepartment($point = -1,$id = 0){
    $CI = &get_instance();
    $CI->db->from('tbl_category_evaluate_kpi');
    $data = $CI->db->get()->result_array();
    if (!empty($data)){
        foreach ($data as $key => $value){
            $data[$key]['bonus'] = get_table_where('tbl_category_evaluate_kpi_detail',['category_evaluate_kpi_id' => $value['id'],'type' => '1'],'','result_array');
            $data[$key]['discipline'] =get_table_where('tbl_category_evaluate_kpi_detail',['category_evaluate_kpi_id' => $value['id'],'type' => '2'],'','result_array');
        }
    }
    if ($point != -1){
        $data = array_reduce($data, function ($carry, $item) use ($point){
            if ($point >= $item['point_min'] && $point <= $item['point_max']) {
                $carry[] = $item;
            }
            return $carry;
        });
        return $data;
    } elseif(!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        $data = array_values($data);
        return ($data);
    } else {
        return $data;
    }

}

if (!function_exists('getPrecious')) {
    function getPrecious()
    {
        $option[''] = '';
        $option['1'] = lang('Quý 1');
        $option['2'] = lang('Quý 2');
        $option['3'] = lang('Quý 3');
        $option['4'] = lang('Quý 4');

        return $option;
    }
}

if (!function_exists('recursiveOrganization')) {
    function recursiveOrganization($id = 0, &$output = null, $parent_id = 0, $indent = null)
    {
        $CI = &get_instance();

        $CI->db->select('*');
        $CI->db->from('tbl_organization');
        $CI->db->where('tbl_organization.parent_id', $parent_id);
        $CI->db->order_by('tbl_organization.parent_id');
        $query = $CI->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $disabled = '';
                if ($item['id'] == $id && $id != 0) {
                    continue;
                }

                $item['code'] = str_replace('\'', '', $item['code']);
                $item['code'] = str_replace('"', '', $item['code']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('"', '', $item['name']);
                $item['name'] = str_replace('/n', '', $item['name']);
                $item['name'] = str_replace('\n', '', $item['name']);
                $item['code'] = str_replace('\n', '', $item['code']);
                $item['code'] = str_replace('/n', '', $item['code']);
                $output .= '<option data-code="' . $item['code'] . '" ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['name']  . "</option>";
                recursiveOrganization($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }

        return $output;
    }
}

function get_parent_id_referral_level($data, $parentId = 0,$level = 0)
{
    $array = array();
    foreach ($data as $key => $value) {
        if ($value['parent_id'] == $parentId) {
            $value['level'] = $level;
            $value['color'] = getLevelColor($level);
            $children = get_parent_id_referral_level($data, $value['id'],$level+1);
            if ($children) {
                $value['children'] = $children;
            }
            $array[] = ($value);
        }
    }

    return $array;
}

function getLevelColor($level) {
    $colors = [
        '#3498DB', // 0 - Xanh dương
        '#2ECC71', // 1 - Xanh lá
        '#F1C40F', // 2 - Vàng
        '#E67E22', // 3 - Cam
        '#E67E22', // 4 - Đỏ
        '#E67E22', // 5 - Tím
        '#E67E22', // 6 - Ngọc
        '#E67E22', // 7 - Xám đậm
        '#E67E22', // 8 - Cam đậm
        '#E67E22', // 9 - Xanh lá đậm
        '#E67E22', // 10 - Xanh dương đậm
        '#E67E22', // 11 - Tím đậm
        '#E67E22', // 12 - Đỏ đậm
        '#E67E22', // 13 - Xanh ngọc đậm
        '#E67E22', // 14 - Xám
        '#E67E22', // 14 - Xám
        '#E67E22', // 14 - Xám
        '#E67E22', // 14 - Xám
        '#E67E22', // 14 - Xám
        '#E67E22', // 14 - Xám
    ];

    // nếu level > 14 thì lặp lại từ đầu
    return $colors[$level % count($colors)];
}

function getTypeOrganization($id = ''){
    $data = [
        [
            'id' => 'room',
            'name' => 'Phòng',
            'table' => 'tbl_room'
        ],
        [
            'id' => 'department',
            'name' => 'Bộ phận',
            'table' => 'tbldepartments'
        ],
        [
            'id' => 'block',
            'name' => 'Khối',
            'table' => 'tbl_block'
        ],
        [
            'id' => 'nest',
            'name' => 'Tổ',
            'table' => 'tbl_nest'
        ],
        [
            'id' => 'group',
            'name' => 'Nhóm',
            'table' => 'tbl_group'
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getTypeQuestion($id = ''){
    $data = [
        [
            'id' => '5GTCL',
            'name' => '5GTCL',
        ],
        [
            'id' => 'TUAN_THU',
            'name' => 'TUAN_THU',
        ],
        [
            'id' => 'CHUYEN_MON',
            'name' => 'CHUYEN_MON',
        ],
        [
            'id' => 'KY_NANG',
            'name' => 'KY_NANG',
        ],
        [
            'id' => 'TU_DUY',
            'name' => 'TU_DUY',
        ],
        [
            'id' => 'CEO_STRATEGY',
            'name' => 'CEO_STRATEGY',
        ],
        [
            'id' => 'CEO_LEADERSHIP',
            'name' => 'CEO_LEADERSHIP',
        ],
        [
            'id' => 'CEO_RISK',
            'name' => 'CEO_RISK',
        ],
        [
            'id' => 'CEO_CULTURE',
            'name' => 'CEO_CULTURE',
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getLevelAnswer($id = ''){
    $data = [
        [
            'id' => 'A',
            'name' => 'Đáp án A',
        ],
        [
            'id' => 'B',
            'name' => 'Đáp án B',
        ],
        [
            'id' => 'C',
            'name' => 'Đáp án C',
        ],
        [
            'id' => 'D',
            'name' => 'Đáp án D',
        ],
        [
            'id' => 'E',
            'name' => 'Đáp án E',
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getListTypeEvaluationEmployee($id = ''){
    $data = [
        [
            'id' => 1,
            'name' => 'Nhân viên',
        ],
        [
            'id' => 2,
            'name' => 'Ứng viên',
        ],
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}
function getDiffDayMonth($start,$end){
    $start = to_sql_date($start);
    $end = to_sql_date($end);
    $diff = abs(strtotime($end) - strtotime($start));
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    return $months +($years * 12);
}

function getTypeOperation($id = ''){
    $data = [
        [
            'id' => 1,
            'name' => '=',
        ],
        [
            'id' => 2,
            'name' => '>=',
        ],
        [
            'id' => 3,
            'name' => '<',
        ]
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

function getTypeCheckList($id = ''){
    $data = [
        [
            'id' => 'A',
            'name' => 'ĐIỀU KIỆN GATE (BẮT BUỘC – FAIL = KHÔNG ĐẠT)',
            'name_result' => 'ĐIỀU KIỆN GATE (BẮT BUỘC – FAIL = KHÔNG ĐẠT)'
        ],
        [
            'id' => 'B',
            'name' => 'KPI THEO THỜI GIAN (P2 – 60%)',
            'name_result' => 'KPI P2'
        ],
        [
            'id' => 'C',
            'name' => 'TUÂN THỦ & KỶ LUẬT (KPI L604 – 20%)',
            'name_result' => 'Tuân thủ (L604)'
        ],
        [
            'id' => 'D',
            'name' => 'NĂNG LỰC & PHÙ HỢP LEVEL (20%)',
            'name_result' => 'Năng lực/Level'
        ]
    ];
    if (!empty($id)){
        $data = array_filter($data,function ($item) use ($id){
            return $item['id'] == $id;
        });
        if (!empty($data)) {
            $data = array_values($data);
            return ($data[0]);
        } else {
            return null;
        }
    } else {
        return $data;
    }
}

if (!function_exists('getReferenceNew')) {
    function getReferenceNew($field,$dataPrefix = '')
    {
        $CI = &get_instance();
        $q = $CI->db->get_where('tbl_order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'salary_3p':
                    $prefix = $dataPrefix;
                    break;
                default:
                    $prefix = '';
            }

            $separator = get_option('separator');
            $format_date_prefix = get_option('format_date_prefix');
            $ref_no = (!empty($prefix)) ? $prefix . "$separator" : '';
            $ref_no .= date("$format_date_prefix") . sprintf("%02s", $ref->{$field});
            return $ref_no;
        }
        return FALSE;
    }
}

function fomart_hour($time)
{
    return date("H:i", strtotime($time));
}

function getListDay()
{
    return [
        [
            'id' => 'Mon',
            'name' => 'Thứ 2'
        ],
        [
            'id' => 'Tue',
            'name' => 'Thứ 3'
        ],
        [
            'id' => 'Wed',
            'name' => 'Thứ 4'
        ],
        [
            'id' => 'Thu',
            'name' => 'Thứ 5'
        ],
        [
            'id' => 'Fri',
            'name' => 'Thứ 6'
        ],
        [
            'id' => 'Sat',
            'name' => 'Thứ 7'
        ],
        [
            'id' => 'Sun',
            'name' => 'Chủ nhật'
        ],
    ];
}

function formatMoneyPayroll($number)
{
    $decimals = 0;

    // Làm tròn theo số chữ số thập phân
    $number = round((float)$number, $decimals);

    return number_format($number, $decimals);
}


function get_parent_ids_role($role_id = '', &$result = array())
{
    $CI = &get_instance();

    $CI->db->where('roleid', $role_id);
    $item = $CI->db->get('tblroles')->row();

    if ($item && !empty($item->roles_parent)) {

        if (!in_array($item->roles_parent, $result)) {
            $result[] = $item->roles_parent;
            get_parent_ids_role($item->roles_parent, $result);
        }
    }
}

function get_child_ids_role($parent_id = '', &$result = array())
{
    $CI = &get_instance();

    $CI->db->where('roles_parent', $parent_id);
    $items = $CI->db->get('tblroles')->result();

    foreach ($items as $value) {

        if (!in_array($value->roleid, $result)) {
            $result[] = $value->roleid;
            get_child_ids_role($value->roleid, $result);
        }
    }
}

function get_all_related_roles($role_id = '')
{
    $result = [];

    // Thêm chính nó
    $result[] = $role_id;

    // Lấy parent
    get_parent_ids_role($role_id, $result);

    // Lấy child
    get_child_ids_role($role_id, $result);

    return array_unique($result);
}