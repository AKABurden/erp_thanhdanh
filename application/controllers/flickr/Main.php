<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('products_model');
        // require_once(APPPATH . "third_party/google-api-php-client-2.4.0/vendor/autoload.php");

        // print_arrays($_SERVER['PHP_AUTH_USER']);
        // print_arrays($this->get_client_ip());
        // print_arrays(apache_request_headers());

        


        // $this->data_headers = 

        $this->data = [];
        $this->token = '';
        if($this->input->post()) {
            $this->data = $this->input->post();
        }
        else {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $this->data = $data_post;
                }
            }
        }
    }

    public function add() {
        $this->apache_request = apache_request_headers();
        if($this->apache_request['Account'] != '4lc96u56b5o80oq2k44qcdka8p8e57ag') {
            header("HTTP/1.1 1002 error account");
            echo _l('false_denied 2');
            die();
        }

        if($this->apache_request['Keyaccount'] != 'qcdka8p8e57ag') {
            header("HTTP/1.1 1001 error key");
            echo _l('false_denied 1');
            die();
        }

        if(!empty($this->data)) {
            $data = [];

            $this->db->where('company', $this->data['customerName']);
            $clients = $this->db->get('tblclients')->row();
            if(!empty($clients)) {
                $data['customer_id'] = !empty($clients->userid) ? $clients->userid : '';
                $data['customer_name'] = !empty($clients->company) ? $clients->company : '';
            }
            else {
                if(!empty($this->data['customerName'])) {
                    $success = $this->db->insert('tblclients', [
                        'company' => !empty($this->data['customerName']) ? $this->data['customerName'] : '',
                        'datecreated' => date('Y-m-d H:i:s'),
                        'zcode' => 0,
                        'status_clients' => 1,
                    ]);
                    if(!empty($success)) {
                        $id_clients = $this->db->insert_id();

                        $this->db->where('userid', $id_clients);
                        $clients = $this->db->get('tblclients')->row();
                        if(!empty($clients)) {
                            
                            $this->db->where('userid', $id_clients);
                            $this->db->update('tblclients', [
                                'zcode' => 'KH-' . sprintf("%05s", ($clients->userid))
                            ]);
                        }


                        $data['customer_id'] = $clients->userid;
                        $data['customer_name'] = $clients->company;
                    }
                }
            }


            $data['referenceId_api'] = !empty($this->data['referenceId']) ? $this->data['referenceId'] : NULL;
            $data['date'] = date('Y-m-d H:i:s');
            $data['note'] = !empty($this->data['orderNote']) ? $this->data['orderNote'] : NULL;
            $data['employee_id'] = 0;
            $data['status'] = 'un_approved';
            $data['created_by'] = 1;
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['reference_no'] = !empty($this->data['originalOrderId']) ? $this->data['originalOrderId'] : '';
            $data['id_order_api'] = !empty($this->data['originalOrderId']) ? $this->data['originalOrderId'] : '';

            if(!empty($this->data['shippingAddress']) && empty($this->data['id_shippingAddress'])) {
                $this->db->where('name', (!empty($this->data['shippingAddress']->contactName) ? $this->data['shippingAddress']->contactName : ''));
                $this->db->where('phone', (!empty($this->data['shippingAddress']->phone) ? $this->data['shippingAddress']->phone : ''));
                $this->db->where('address', (!empty($this->data['shippingAddress']->addressLine1) ? $this->data['shippingAddress']->addressLine1 : ''));
                $this->db->where('client', $data['customer_id']);
                $shipping_client = $this->db->get('tblshipping_client')->row();
                if(!empty($shipping_client)) {
                    $data['address_delivery_id'] = $shipping_client->id;
                }
                else {
                    $insertShipping = $this->db->insert('tblshipping_client', [
                        'name' => !empty($this->data['shippingAddress']->contactName) ? $this->data['shippingAddress']->contactName : '',
                        'phone' => !empty($this->data['shippingAddress']->phone) ? $this->data['shippingAddress']->phone : '',
                        'address' => !empty($this->data['shippingAddress']->addressLine1) ? $this->data['shippingAddress']->addressLine1 : '',
                        'address_v2' => !empty($this->data['shippingAddress']->addressLine2) ? $this->data['shippingAddress']->addressLine2 : '',
                        'date_create' => date('Y-m-d H:i:s'),
                        'client' => $data['customer_id'],
                        'create_by' => 0,
                        'name_v2' => '',
                        'delivery_area' => '',
                        'city_shipping' => '',
                        'district_shipping' => '',
                    ]);
                    if(!empty($insertShipping)) {
                        $data['address_delivery_id'] = $this->db->insert_id();
                    }
                }
            }

            $count_items = 0;
            $total_quantity = 0;

            $data_items = [];
            $items = $this->data['orderItems'];
            if(!empty($items)) {
                foreach($items as $key => $value) {
                    $this->db->where('code', $value->sku);
                    $this->db->where('type_products', 'products');
                    $products = $this->db->get('tbl_products')->row();
                    if(!empty($products)) {
                        if(empty($products->images) && !empty($value->mockupImage1)) {
                            $images  = '';
                            $images_mutil  = '';
                            $paste_img = FCPATH . 'uploads/products/';
                            _maybe_create_upload_path($paste_img);
                            $time = time();
                            if(!empty($value->mockupImage1)) {
                                $mockupImage1 = $value->mockupImage1;
                                @copy($mockupImage1, $paste_img . 'mockup_1_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                                $images = 'mockup_1_' . $time . '.jpg';
                            }
                            if(!empty($value->mockupImage2)) {
                                $mockupImage2 = $value->mockupImage2;
                                @copy($mockupImage2, $paste_img . 'mockup_2_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                                $images_mutil = 'mockup_2_' . $time . '.jpg';
                            }

                            $product_multiple = !empty($products->images_multiple) ? ($products->images_multiple) : '';
                            if(!empty($images_mutil)) {
                                $product_multiple = $images_mutil . '||' . $product_multiple;
                            }


                            $this->db->where('id', $products->id);
                            $this->db->update('tbl_products', [
                                'images' => $images,
                                'images_multiple' => !empty($product_multiple) ? ($product_multiple) : NULL
                            ]);
                        }


                        $data_items[] = [
                            'mockupImage1' => $value->mockupImage1,
                            'mockupImage2' => $value->mockupImage2,
                            'type_item' => 'products',
                            'item_id' => $products->id,
                            'item_code' => $products->code,
                            'item_name' => $products->name,
                            'quantity' => $value->quantity
                        ];
                    }
                    else {

                        $images  = '';
                        $images_mutil  = '';
                        $paste_img = FCPATH . 'uploads/products/';
                        _maybe_create_upload_path($paste_img);
                        $time = time();
                        if(!empty($value->mockupImage1)) {
                            $mockupImage1 = $value->mockupImage1;
                            @copy($mockupImage1, $paste_img . 'mockup_1_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                            $images = 'mockup_1_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg';
                        }
                        if(!empty($value->mockupImage2)) {
                            $mockupImage2 = $value->mockupImage2;
                            @copy($mockupImage2, $paste_img . 'mockup_2_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                            $images_mutil = 'mockup_2_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg';
                        }


                        $product_insert = [
                            'category_id' => 0,
                            'unit_id' => 0,
                            'type_products' => 'products',
                            'code' => $value->sku,
                            'name' => !empty($value->name) ? $value->name : $value->sku,
                        ];

                        if(!empty($images)) {
                            $product_insert['images'] = $images;
                        }
                        if(!empty($images2)) {
                            $product_insert['images_multiple'] = $images_mutil;
                        }

                        $insertProducts = $this->db->insert('tbl_products', $product_insert);
                        if(!empty($insertProducts)) {
                            $id_products = $this->db->insert_id();

                            $this->products_model->handlingDesignStages($id_products);

                            $data_items[] = [

                                'mockupImage1' => $value->mockupImage1,
                                'mockupImage2' => $value->mockupImage2,
                                'type_item' => 'products',
                                'item_id' => $id_products,
                                'item_code' => $value->sku,
                                'item_name' => !empty($value->name) ? $value->name : $value->sku,
                                'quantity' => $value->quantity
                            ];
                        }
                        else {
                            echo json_encode(['success' => false]);die();
                        }

                    }

                    $total_quantity+= $value->quantity;
                }
            }

            $count_items = count($data_items);
            $data['count_items'] = $count_items;
            $data['total_quantity'] = $total_quantity;
            $data['status '] = 'approved';
            $data['user_status '] = 1;
            $data['date_status '] = date('Y-m-d H:i:s');

            if(!empty($data)) {
                $success = $this->db->insert('tbl_orders', $data);
                if(!empty($success)) {
                    $id_order = $this->db->insert_id();
                    foreach($data_items as $key => $value) {
                        $value['order_id'] = $id_order;


                        $mockupImage1 = $value['mockupImage1'];
                        unset($value['mockupImage1']);
                        $mockupImage2 = $value['mockupImage2'];
                        unset($value['mockupImage2']);


                        
                        $id_items = $this->orders_model->insertOrderItemsNew($value);

                        $paste_img = FCPATH . 'uploads/products/'.$id_items.'/';
                        _maybe_create_upload_path($paste_img);
                        $time = time();
                        if(!empty($mockupImage1)) {
                            @copy($mockupImage1, $paste_img . 'mk_1_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                            $images_items = 'mk_1_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg';
                        }
                        if(!empty($mockupImage2)) {
                            @copy($mockupImage2, $paste_img . 'mk_2_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg');
                            $images_mutil_items = 'mk_2_' . $time .'_'.rand(0,10000).rand(0,10000). '.jpg';
                        }

                        if(!empty($images_items) || !empty($images_mutil_items)) {
                            $this->db->where('id', $id_items);
                            $this->db->update('tbl_order_items',  [
                                'image_product' => !empty($images_items) ? $images_items : NULL,
                                'image_product_mutil' => !empty($images_mutil_items) ? $images_mutil_items : NULL,
                            ]);
                        }

                        
                        $date = date('Y-m-d');
                        $date_shipping = strtotime ( '+3 day' , strtotime ( $date ) ) ;
                        $date_shipping = date ( 'Y-m-d' , $date_shipping );
                        $this->db->insert('tbl_order_item_shippings', [
                            'order_item_id' => $id_items,
                            'date_shipping' => $date_shipping,
                            'quantity_shipping' => $value['quantity'],
                        ]);
                    }

                    
                    $this->orders_model->handlingStagesOrders($id_order);
                    echo json_encode([
                        'success' => true,
                        'status' => 200,
                        'message' => 'Thêm thành công'
                    ]);die();
                }
            }
            echo json_encode([
                'success' => false,
                'status' => 500,
                'message' => 'Thêm không thành công'
            ]);die();


        }
        echo json_encode([
            'success' => false,
            'status' => 400,
            'message' => 'Thêm tìm thấy dữ liệu'
        ]);die();
    }

    public function get() {
        $this->db->where('name', 'get_api_json');
        $success = $this->db->get('tbloptions')->row('value');
        if(!empty($success)) {
            echo $success;die();
        }
        echo 'false';die();
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}