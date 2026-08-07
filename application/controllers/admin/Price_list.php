<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Price_list extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('price_list_model');
    }

    public function index()
    {
        $data['title'] = lang('price_list');
        $this->load->view('admin/price_list/index', $data);
    }

    public function getPriceList() {
        $this->load->view('admin/price_list/list');
    }

    public function changePriceList() {

        $data = [];

        if ($this->input->post()) {
            $year = $this->input->post('year');
            $category_products_id = $this->input->post('category_products_id');
            $customers_groups_id = $this->input->post('customers_groups_id');
            $price = number_unformat($this->input->post('price'));
            $date_updated = date('Y-m-d H:i:s');
            $updated_by = get_staff_user_id();

            $price_list = $this->price_list_model->getRowPriceList($year, $category_products_id, $customers_groups_id);
            if (!empty($price_list)) {
                $arrUpdate = [
                    'price' => $price,
                    'date_updated' => $date_updated,
                    'updated_by' => $updated_by,
                ];
                $rs = $this->price_list_model->updatePriceList($price_list['id'], $arrUpdate);
                if ($rs) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $arrInsert = [
                    'year' => $year,
                    'category_products_id' => $category_products_id,
                    'customers_groups_id' => $customers_groups_id,
                    'price' => $price,
                    'date_updated' => $date_updated,
                    'updated_by' => $updated_by,
                ];
                $rs = $this->price_list_model->insertPriceList($arrInsert);
                if ($rs) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            }
        }
        echo json_encode($data);
    }

    public function getDataPriceList() {
        $data = [];
        if ($this->input->post()) {
            $item_id = $this->input->post('item_id');
            $customers_price_list = $this->input->post('customers_price_list');
            $year = $this->input->post('date');

            if (!empty($item_id) && !empty($customers_price_list)) {
                $dataPrice = $this->price_list_model->showPrice($item_id, $customers_price_list, $year);
                $data = $dataPrice;
            }
        }
        echo json_encode($data);
    }
}