<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_business_results extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('rest');
    }

    public function getDeliveries() {
        $this->db->select('
            tbl_deliveries.id as delivery_id,
            tbl_deliveries.order_id as order_id,
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tbl_deliveries.customer_id as customer_id,
            tbl_deliveries.employee_id as employee_id,
            tbl_deliveries.total_quantity as total_quantity,
            tbl_deliveries.total_amount_items as total_amount_items,
            tbl_deliveries.grand_total_items as grand_total_items,
            tbl_deliveries.total_tax as total_tax,
            tbl_deliveries.grand_total as grand_total,
            tbl_deliveries.id_branch as id_branch,
        ', false);
        $this->db->from('tbl_deliveries');
        $deliveries = $this->db->get()->result_array();
        $this->response($deliveries, REST_Controller::HTTP_OK);
    }

    public function getDeliveriesAndCostGroupMonYear() {

        $query = '(
            SELECT 
                DATE_FORMAT(tb_cs.date, "%Y-%m") as date,
                SUM(tb_cs.grand_total_items) as grand_total_items,
                SUM(tb_cs.total_tax) as total_tax,
                SUM(tb_cs.grand_total) as grand_total,
                SUM(tb_cs.payment) as payment
                FROM (
                    SELECT
                        tbl_deliveries.date as date,
                        tbl_deliveries.total_amount_items as total_amount_items,
                        tbl_deliveries.grand_total_items as grand_total_items,
                        tbl_deliveries.total_tax as total_tax,
                        tbl_deliveries.grand_total as grand_total,
                        0 as payment
                    FROM tbl_deliveries

                    UNION ALL

                    SELECT
                        tblpay_slip.day_vouchers as date,
                        0 as total_amount_items,
                        0 as grand_total_items,
                        0 as total_tax,
                        0 as grand_total,
                        tblpay_slip.payment * tblpay_slip.amount_to_vnd as payment
                    FROM tblpay_slip

                    UNION ALL

                    SELECT
                        tblother_payslips.date as date,
                        0 as total_amount_items,
                        0 as grand_total_items,
                        0 as total_tax,
                        0 as grand_total,
                        tblother_payslips.total as payment
                    FROM tblother_payslips
                    WHERE tblother_payslips.is_advance = 0
                ) tb_cs
            GROUP BY DATE_FORMAT(tb_cs.date, "%Y-%m")
        )';
        $deliveries_cost = $this->db->query($query)->result();
        $this->response($deliveries_cost, REST_Controller::HTTP_OK);            
    }
}