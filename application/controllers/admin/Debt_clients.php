<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Debt_clients extends AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (!has_permission('debt_clients', '', 'view')) {
			access_denied('Debt clients');
		}
		$data['title'] = _l('debt_clients');
		$this->load->view('admin/debt_clients/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('debt_client');
	}

	public function table_detail_client($uerid)
	{
		$this->app->get_table_data('detail_client', array('uerid' => $uerid));
	}

	public function count_debt()
	{
		$id = array();
		$clients_id = $this->input->post('clients_id');
		if (!empty($clients_id)) {
			$id = explode(',', $clients_id);
		}

		$this->db->where('((COALESCE((
            SELECT SUM(tbl_orders.grand_total)
            FROM tbl_orders 
            WHERE tbl_orders.customer_id = tblclients.userid
            ),0)) - (
				COALESCE(
				  (
					SELECT COALESCE(SUM(tblvouchers_coupon.payment),0) 
					FROM tblvouchers_coupon 
					WHERE tblvouchers_coupon.customer = tblclients.userid ),0)
				  
			   + 
				COALESCE(
				  (
					SELECT SUM(tblother_payslips_coupon.total) 
					FROM tblother_payslips_coupon 
					WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
					),0)
				  
			  ) + (COALESCE((
						SELECT SUM(tbl_orders.grand_total)
						FROM tbl_orders 
						WHERE tbl_orders.customer_id = tblclients.userid
						AND tbl_orders.id = -1) - 
				COALESCE(
				  (
					SELECT SUM(tblvouchers_coupon.payment) 
					FROM tblvouchers_coupon 
					WHERE tblvouchers_coupon.customer = tblclients.userid AND tblvouchers_coupon.id = -1),0) - 
				COALESCE(
				  (
					SELECT SUM(tblother_payslips_coupon.total) 
					FROM tblother_payslips_coupon 
					WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
					AND tblother_payslips_coupon.id = -1),0)
			   - 
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid AND tbl_returned_goods.id = -1),0), 0)) - 
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ),0) > tblclients.debt_limit)', false, false);
		$this->db->where('tblclients.debt_limit <> 0');
		$this->db->group_by('tblclients.userid');
		if(!empty($id)) {
			$this->db->where_in('tblclients.userid', $id);
		}
		$count_limit = $this->db->get('tblclients')->result_array();
		$data['count_limit'] = count($count_limit);





		if (!empty($id)) {
			$this->db->where_in('tblclients.userid', $id);
		}
		$this->db->join('tbl_orders', 'tbl_orders.customer_id = tblclients.userid');
		$this->db->group_by('tblclients.userid');
		$this->db->where('tbl_orders.id IN (
									SELECT tbl_orders.id 
									FROM tbl_orders 
									WHERE tbl_orders.status < 2
									AND tbl_orders.customer_id = tblclients.userid
									AND DATEDIFF(CURDATE(), tbl_orders.date) > tblclients.debt_limit_day
									AND tblclients.debt_limit_day <> 0
								)', false, false);
		$count_limit_day= $this->db->get('tblclients')->result_array();
		$data['all_debt_limit_day'] = count($count_limit_day);


		if (!empty($id)) {
			$this->db->where_in('tblclients.userid', $id);
		}
		$this->db->where('((COALESCE((
            SELECT SUM(tbl_orders.grand_total)
            FROM tbl_orders 
            WHERE tbl_orders.customer_id = tblclients.userid
            ),0)) - (
				COALESCE(
				  (
					SELECT COALESCE(SUM(tblvouchers_coupon.payment),0) 
					FROM tblvouchers_coupon 
					WHERE tblvouchers_coupon.customer = tblclients.userid ),0)
				  
			   + 
				COALESCE(
				  (
					SELECT SUM(tblother_payslips_coupon.total) 
					FROM tblother_payslips_coupon 
					WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
					),0)
				  
			  ) + (COALESCE((
						SELECT SUM(tbl_orders.grand_total)
						FROM tbl_orders 
						WHERE tbl_orders.customer_id = tblclients.userid
						AND tbl_orders.id = -1) - 
				COALESCE(
				  (
					SELECT SUM(tblvouchers_coupon.payment) 
					FROM tblvouchers_coupon 
					WHERE tblvouchers_coupon.customer = tblclients.userid AND tblvouchers_coupon.id = -1),0) - 
				COALESCE(
				  (
					SELECT SUM(tblother_payslips_coupon.total) 
					FROM tblother_payslips_coupon 
					WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
					AND tblother_payslips_coupon.id = -1),0)
			   - 
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid AND tbl_returned_goods.id = -1),0), 0)) - 
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ),0) > 0)', false, false);

		$this->db->group_by('tblclients.userid');
		$count_order = $this->db->get('tblclients')->result_array();
		$data['all'] = count($count_order);
		echo json_encode($data);
	}

	public function client_detail($id = '')
	{
		$data['uerid'] = $id;
		$data['code'] = get_option('prefix_coupon') . sprintf('%06d', ch_getMaxID('id', 'tblvouchers_coupon') + 1);
		$data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
		$client = get_table_where('tblclients', array('userid' => $id), '', 'row')->company;
		$data['title'] = _l('debt_customer') . ' ' . $client;
		$data['filterType'] = $this->input->get('filterType');
		if ($data['filterType'] == 2) {
			$data['title'] .= ' (Vượt mức thời gian thanh toán)';
		}
		$this->load->view('admin/debt_clients/client_detail', $data);
	}

	public function show_daital()
	{
		$__data = $this->input->post('data');
		$id = $__data[0];
		$this->load->model('orders_model');
		$this->load->model('quotes_orders_model');
		$this->load->model('manufactures_model');
		$this->load->model('products_model');
		$this->load->model('items_model');
		$this->load->model('unit_model');
		$this->load->model('deliveries_model');
		$this->load->model('returned_goods_model');
		$this->load->model('deliveries_model');
		$order = $this->orders_model->rowOrderById($id);
		$address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
		$items = $this->orders_model->getOrderItemsByOrderId($id);
		$table_price = $this->site_model->rowSetPricesById($order['table_price_id']);
		$table_discount = $this->site_model->rowDiscountById($order['table_discount_id']);
		$ckView = checkView('orders', $order['list_users'], $id);
		$data['flagView'] = $ckView;
		$bodyItems = '';
		if (!empty($items)) {
			foreach ($items as $key => $value) {
				$type_item = $value['type_item'];
				$items_id = $value['item_id'];
				if ($type_item == "products") {
					$info = $this->products_model->rowProduct($items_id);
					$unit = $this->unit_model->rowUnit($info['unit_id']);
					if (!empty($info['images'])) {
						$images = base_url('uploads/products/' . $info['images']);
					}
				} else if ($type_item == "items") {
					$info = $this->items_model->rowItems($items_id);
					$unit = $this->unit_model->rowUnit($info['unit']);
					if (!empty($info['avatar'])) {
						$images = base_url($info['avatar']);
					}
				}
				if (empty($images)) {
					$images = base_url('assets/images/tnh/no_image.png');
				}
				$sub_date = $this->orders_model->getOrderItemShippingsByOrderItemId($value['id']);
				$html_sub_date = '';
				if (!empty($sub_date)) {
					foreach ($sub_date as $k => $val) {
						$html_sub_date .= '<div class="">' .
							'<div class="col-md-8" style="padding: 0px;">' . _d($val['date_shipping']) . ' </div>' .
							'<div class="col-md-4" style="padding: 0px;"> - ' . number_format($val['quantity_shipping']) . '</div>' .
							'</div>';
					}
				}
				$tdNumber = '<td>' . (++$key) . '</td>';
				$tdTem = '';
				$tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
				$tdCode = '<td>' . $info['code'] . '<div class="type-item">' . (($type_item == "products") ? '<span class="label label-success">' . lang($type_item) . '</span>' : '<span class="label label-primary">' . lang('ch_items') . '</span>') . '</div>' . $tdTem . '</td>';
				$tdName = '<td style="text-align: left;">' . $info['name'] . '</td>';
				$tdUnit = '<td>' . $unit['unit'] . '</td>';
				$tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
				$tdUnitPrice = '<td class="text-right">' . formatMoney($value['price']) . '</td>';
				$tdTotalAmount = '<td class="text-right">' . formatMoney($value['amount']) . '</td>';
				$tdTaxItem = '<td class="text-center">' . $value['tax_name_item'] . '</td>';
				$tdDiscountPercent = '<td class="text-center">' . $value['discount_percent_item'] . '</td>';
				$tdDiscountDirect = '<td class="text-right">' . formatMoney($value['discount_direct_amount_item']) . '</td>';
				$tdGrandTotal = '<td class="text-right">' . formatMoney($value['total_amount']) . '</td>';
				$tdShipping = '<td>' . $html_sub_date . '</td>';
				$tdNote = '<td>' . $value['note_item'] . '</td>';
				$bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdDiscountPercent . '
                    ' . $tdDiscountDirect . '
                    ' . $tdGrandTotal . '
                </tr>';
			}
		}
		$data['bodyItems'] = $bodyItems;
		$this->load->view('admin/debt_clients/show_daital', $data);
	}
}