<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    class Puchases_gantt_order_model extends App_Model
    {
        public function countProductionsGantt()
        {
            $productions_orders = $this->input->post('productions_orders');
            $this->db->select('COUNT(*) as count');
            $this->db->join('tblpurchase_order','tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
            $this->db->where('tblpurchase_order.cancel',0);
            if (!empty($productions_orders)) {
                $this->db->where("tblsuppliers.id", $productions_orders);
            }
            $this->db->group_by('tblsuppliers.id');
            return $this->db->get('tblsuppliers')->row()->count;
        }
        public function loadGanttProductions($start, $limit)
        {
            $productions_orders = $this->input->post('productions_orders');
            $productions_orders_id = $this->input->post('productions_orders_id');

            $data = [];
            $this->db->select("
                tblsuppliers.id as id,
                tblsuppliers.company as company,
                concat(tblsuppliers.prefix,'-',tblsuppliers.code) as code
            ", false);
            $this->db->join('tblpurchase_order','tblpurchase_order.suppliers_id = tblsuppliers.id', 'left');
            $this->db->where('tblpurchase_order.cancel',0);
            $this->db->group_by('tblsuppliers.id');
            if (!empty($productions_orders)) {
                $this->db->where("tblsuppliers.id", $productions_orders);
            }
            $this->db->from('tblsuppliers');
            // print_arrays($this->db->get_compiled_select(), FALSE);
            $suppliers = $this->db->get()->result_array();
            $warningDuePOD = get_option('warning_due_pod');
            if (!empty($suppliers)) {
                foreach ($suppliers as $key => $value) {
                    $suppliers_id = $value['id'];
                    $suppliers_code = $value['code'];
                    $productionOrder = [
                        'production_order_id' => $suppliers_id,
                        'values' => false,
                        'desc' => 'productions_orders',
                        'name' => $suppliers_code,
                        'text' => $value['company'],
                    ];
                    array_push($data, $productionOrder);
                    $this->db->select("
                        tblpurchase_order.id as id,
                        tblpurchase_order.date as date_create,
                        tblpurchase_order.delivery_date as delivery_date,
                        tblpurchase_order.totalAll_suppliers as totalAll_suppliers,
                        concat(tblpurchase_order.prefix,'-',tblpurchase_order.code) as code
                    ", false);
                    $this->db->where('tblpurchase_order.suppliers_id',$value['id']);
                    $this->db->where('tblpurchase_order.cancel',0);
                    $this->db->from('tblpurchase_order');
                    if (!empty($productions_orders_id)) {
                        $this->db->where("tblpurchase_order.id", $productions_orders_id);
                    }
                    $order = $this->db->get()->result_array();
                    foreach ($order as $k => $v) {
                    $productionOrderDetailId = $v['id'];
                    $referenceDetailNo = $v['code'];

                    $dateStart = strftime('%Y/%m/%d', strtotime($v['date_create']));
                    $dateEnd = strftime('%Y/%m/%d', strtotime($v['delivery_date']));

                    $descPOD = '
                        <div>'.lang('tnh_reference_no').': '.$referenceDetailNo.'</div>
                        <div>'.lang('total_price').': '.number_format($v['totalAll_suppliers']).'</div>
                        <div>'.lang('date_start').': '._d($v['date_create']).'</div>
                        <div>'.lang('date_end').': '._d($v['delivery_date']).'</div>
                    ';

                    //handling status pod
                    $customClassPOD = 'ganttPrimary';
                    $dateDuePOD = (strtotime($v['delivery_date']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                        if ($dateDuePOD < 0) {
                            //trễ hạn
                            $customClassPOD = 'ganttRed';
                            //
                        } else if ($dateDuePOD == 0) {
                            //Tới hạn
                            $customClassPOD = 'ganttGreen';
                        } else {
                            //sắp tới hạn
                            if (($dateDuePOD - $warningDuePOD) < 0) {
                                $customClassPOD = 'ganttYellow';
                            }
                        }
                    //end handling status pod
                    $dataPOD = [
                        'production_order_detail_id' => $productionOrderDetailId,
                        'values' => [
                            [
                                'from' => $dateStart,
                                'to' => $dateEnd,
                                'desc' => $descPOD,
                                'label' => $referenceDetailNo,
                                'customClass' => $customClassPOD,
                                'dataObj' => [
                                    'production_order_detail_id' => $productionOrderDetailId
                                ]
                            ]
                        ],
                        'desc' => '<b class="SO_parrens">'.$referenceDetailNo.' Tổng tiền ('.number_format($v['totalAll_suppliers']).')'.'</b>',
                        'name' => '',
                    ];
                    array_push($data, $dataPOD);
                                $this->db->select("
                                    tblpurchase_order_items.id as id,
                                    tblpurchase_order_items.product_id as product_id,
                                    tblpurchase_order_items.type as type,
                                    tblpurchase_order_items.quantity_suppliers as quantity_suppliers,
                                ", false);
                                $this->db->where('tblpurchase_order_items.id_purchase_order',$v['id']);
                                $this->db->from('tblpurchase_order_items');
                                $orders_items = $this->db->get()->result_array();
                                foreach ($orders_items as $ks => $vs) {
                                $get_items = get_items($vs['product_id'],$vs['type']);    
                                $productionOrderDetailId = $vs['id'];
                                $referenceDetailNo = $get_items->name;
                                $descPOD = '
                                ';

                                //handling status pod
                                $customClassPOD = 'ganttPrimary';
                                $dateDuePOD = (strtotime($v['delivery_date']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                                if ($dateDuePOD < 0) {
                                    //trễ hạn
                                    $customClassPOD = 'ganttRed';
                                    //
                                } else if ($dateDuePOD == 0) {
                                    //Tới hạn
                                    $customClassPOD = 'ganttGreen';
                                } else {
                                    //sắp tới hạn
                                    if (($dateDuePOD - $warningDuePOD) < 0) {
                                        $customClassPOD = 'ganttYellow';
                                    }
                                }
                            //end han
                                //end handling status pod
                                $dataPOD = [
                                    'production_order_detail_id' => $productionOrderDetailId,
                                    'values' => [
                                        [
                                            'from' => $dateStart,
                                            'to' => $dateEnd,
                                            'desc' => $descPOD,
                                            'label' => $referenceDetailNo,
                                            'customClass' => $customClassPOD,
                                            'dataObj' => [
                                                'production_order_detail_id' => $productionOrderDetailId
                                            ]
                                        ]
                                    ],
                                    'desc' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$referenceDetailNo.' [Số lượng ('.number_format($vs['quantity_suppliers']).')]'.'</b>',
                                    'name' => '',
                                ];
                                array_push($data, $dataPOD);
                            }
                    }
                }
            }
            // print_arrays($data);

            return $data;
        }
    }