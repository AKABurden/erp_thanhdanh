<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Feedback extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->staffid = get_staff_user_id();

        $this->load->helper('notification');
    }

    //Feed back purchase_order
    public function add_feed_back_purchase_order() {
    	$id_purchase_order = $this->input->post('id');
    	$feedback = $this->input->post('comment_feedback');
	    $tag_comment  = $this->input->post('tag_comment');

	    $staffTag = [];
	    $data_tag_comment = json_decode($tag_comment);
	    foreach($data_tag_comment as $key => $value) {
		    $feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.$value->name.'</i>', $feedback);
		    $staffTag[] = $value->id;
	    }
    	if(empty($feedback) && empty($_FILES)) {
		    echo json_encode([
			    'success' => false,
			    'message' => _l('Thêm feedback không thành công'),
			    'alert_type' => 'danger'
		    ]);die();
	    }
    	$this->db->where('id_purchase_order', $id_purchase_order);
    	$success = $this->db->insert('tblpurchase_order_feedback', [
    		'id_purchase_order' => $id_purchase_order,
    		'feedback' => $feedback,
    		'create_by' => get_staff_user_id(),
    		'date_create' => date('Y-m-d H:i:s'),
	    ]);
    	if(!empty($success)) {
		    $id_feed_back = $this->db->insert_id();
		    if(!empty($id_feed_back)) {
		    	if(!empty($_FILES['file'])) {
				    if (!file_exists(PURCHASE_ORDER_FEEDBACK . '/')) {
					    mkdir(PURCHASE_ORDER_FEEDBACK . '/');
					    fopen(PURCHASE_ORDER_FEEDBACK . '/' . 'index.html', 'w');
				    }
				    if (!file_exists(PURCHASE_ORDER_FEEDBACK . $id_feed_back . '/')) {
					    mkdir(PURCHASE_ORDER_FEEDBACK . $id_feed_back . '/');
					    fopen(PURCHASE_ORDER_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
				    }
				    $filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
				    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					    $typeFile = $_FILES['file']['type'];
					    $source_path = $_FILES['file']['tmp_name'];
					    $target_path = PURCHASE_ORDER_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
					    if (move_uploaded_file($source_path, $target_path)) {
						    $file = 'uploads/' . $id_feed_back . '/' . $_FILES['file']['name'];
						    if (!empty($file)) {
							    $this->db->insert('tblfiles', [
								    'rel_id' => $id_feed_back,
								    'rel_type' => 'feedback_pr',
								    'file_name' => $filename,
								    'filetype' => $typeFile,
								    'staffid' => get_staff_user_id(),
								    'dateadded' => date('Y-m-d H:i:s'),
							    ]);
						    }
					    }
				    }
			    }
		    }

		    $this->db->where('id', $id_feed_back);
			$purchase_order_feedback = $this->db->get('tblpurchase_order_feedback')->row();

			$this->db->select('tblpurchase_order.*, tblsuppliers.company');
			$this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id');
			$purchase_order = $this->db->get_where('tblpurchase_order', ['tblpurchase_order.id' => $id_purchase_order])->row();

			$this->db->join('tbl_staff_child_permission_v2', 'tbl_staff_child_permission_v2.id_staff = tblstaff.staffid AND tbl_staff_child_permission_v2.obj_permission = "purchase_order"', 'left');
			$this->db->group_start();
			$this->db->where('can_view', 1);
			$this->db->or_where('tblstaff.admin', 1);
		        $this->db->or_group_start();
		            $this->db->where('can_view_own', 1);
		            $this->db->where('staffid', $purchase_order->staff_create);
				$this->db->group_end();
			$this->db->group_end();

		    if(!empty($staffTag)) {
			    $this->db->where_not_in('staffid', $staffTag);
		    }
			$staff = $this->db->get('tblstaff')->result_array();
			if(!empty($staff)) {
				foreach($staff as $key => $value) {
					add_notification([
						'description'     => "<a onclick='view_purchase_order($purchase_order->id); return false;'> Vừa có bình luận mới cho đơn hàng (PO) ".$purchase_order->code." - ".$purchase_order->company. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value['staffid'],
						'link'            => ''
					]);
				}
			}

		    if(!empty($staffTag)) {
			    foreach($staffTag as $key => $value) {
				    add_notification([
					    'description'     => "<a onclick='view_purchase_order($purchase_order->id); return false;'> Vừa nhắc bạn vào bình luận từ đơn hàng (PO) ".$purchase_order->code." - ".$purchase_order->company. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
					    'touserid'        => $value,
					    'link'            => ''
				    ]);
			    }
		    }

			$htmlFeedBack = $this->view_feed_back_purchase_order($id_feed_back, false);
		    $purchase_order_feedback->html = $htmlFeedBack;

		    ConnectPusher($purchase_order_feedback, 'event_purchase_order', 'feed_back');
    		echo json_encode([
    			'success' => true,
			    'message' => _l('Thêm feedback thành công'),
			    'alert_type' => 'success',
			    'id' => $id_feed_back,
			    'html' => $htmlFeedBack
		    ]);die();
	    }
	    echo json_encode([
		    'success' => false,
		    'message' => _l('Thêm feedback không thành công'),
		    'alert_type' => 'danger'
	    ]);die();
    }

    public function view_feed_back_purchase_order($id_feed_back, $type = true) {
		$this->db->where('id', $id_feed_back);
		$purchase_order_feedback = $this->db->get('tblpurchase_order_feedback')->row();
		if(!empty($purchase_order_feedback)) {
			$this->db->where('rel_id', $purchase_order_feedback->id);
			$this->db->where('rel_type', 'feedback_pr');
			$purchase_order_feedback->file = $this->db->get('tblfiles')->result();
		}

		if($type == false) {
			return $this->load->view('admin/feedback/purchase_order/comment_feedback', ['feedback' => $purchase_order_feedback], true); die();
		}
		else {
			return $this->load->view('admin/feedback/purchase_order/comment_feedback', ['feedback' => $purchase_order_feedback]);
		}
    }
    
    public function remove_feed_back_purchase_order($id = "") {
		if(!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblpurchase_order_feedback');
			if(!empty($delete)) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_pr');
				$files_delete = $this->db->get('tblfiles')->result_array();
				foreach($files_delete as $key => $value) {
					unlink(PURCHASE_ORDER_FEEDBACK . $id.'/' . $value['file_name']);
				}

				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_pr');
				$this->db->delete('tblfiles');

				ConnectPusher(['id' => $id], 'event_purchase_order', 'remove_feed_back');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => _l('Xóa feedback thành công')
				]);die();
			}
		}
	    echo json_encode([
		    'success' => false,
		    'alert_type' => 'danger',
		    'message' => _l('Xóa feedback không thành công')
	    ]);die();
    }
	//end Feed back purchase_order


	//Feed back purchases
	public function add_feed_back_purchases() {
		$id_purchases = $this->input->post('id');
		$feedback = $this->input->post('comment_feedback');
		$tag_comment  = $this->input->post('tag_comment');

		$staffTag = [];
		$data_tag_comment = json_decode($tag_comment);
		foreach($data_tag_comment as $key => $value) {
			$feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.$value->name.'</i>', $feedback);
			$staffTag[] = $value->id;
		}


		if(empty($feedback) && empty($_FILES)) {
			echo json_encode([
				'success' => false,
				'message' => _l('Thêm feedback không thành công'),
				'alert_type' => 'danger'
			]);die();
		}

		$this->db->where('id_purchases', $id_purchases);
		$success = $this->db->insert('tblpurchases_feedback', [
			'id_purchases' => $id_purchases,
			'feedback' => $feedback,
			'create_by' => get_staff_user_id(),
			'date_create' => date('Y-m-d H:i:s'),
		]);
		if(!empty($success)) {
			$id_feed_back = $this->db->insert_id();
			if(!empty($id_feed_back)) {
				if(!empty($_FILES['file'])) {
					if (!file_exists(PURCHASES_FEEDBACK . '/')) {
						mkdir(PURCHASES_FEEDBACK . '/');
						fopen(PURCHASES_FEEDBACK . '/' . 'index.html', 'w');
					}
					if (!file_exists(PURCHASES_FEEDBACK . $id_feed_back . '/')) {
						mkdir(PURCHASES_FEEDBACK . $id_feed_back . '/');
						fopen(PURCHASES_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
					}
					$filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
					if (is_uploaded_file($_FILES['file']['tmp_name'])) {
						$typeFile = $_FILES['file']['type'];
						$source_path = $_FILES['file']['tmp_name'];
						$target_path = PURCHASES_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
						if (move_uploaded_file($source_path, $target_path)) {
							$file = 'uploads/' . $id_feed_back . '/' . $_FILES['file']['name'];
							if (!empty($file)) {
								$this->db->insert('tblfiles', [
									'rel_id' => $id_feed_back,
									'rel_type' => 'feedback_p',
									'file_name' => $filename,
									'filetype' => $typeFile,
									'staffid' => get_staff_user_id(),
									'dateadded' => date('Y-m-d H:i:s'),
								]);
							}
						}
					}
				}
			}

			$this->db->where('id', $id_feed_back);
			$purchases_feedback = $this->db->get('tblpurchases_feedback')->row();

			$purchases = $this->db->get_where('tblpurchases', ['tblpurchases.id' => $id_purchases])->row();

			$this->db->join('tbl_staff_child_permission_v2', 'tbl_staff_child_permission_v2.id_staff = tblstaff.staffid AND tbl_staff_child_permission_v2.obj_permission = "purchases"', 'left');
			$this->db->group_start();
			$this->db->where('can_view', 1);
			$this->db->or_where('tblstaff.admin', 1);
			$this->db->or_group_start();
			$this->db->where('can_view_own', 1);
			$this->db->where('staffid', $purchases->staff_create);
			$this->db->group_end();
			$this->db->group_end();

			if(!empty($staffTag)) {
				$this->db->where_not_in('staffid', $staffTag);
			}

			$staff = $this->db->get('tblstaff')->result_array();
			if(!empty($staff)) {
				foreach($staff as $key => $value) {
					add_notification([
						'description'     => "<a href='#' onclick='view_purchases($purchases->id); return false;'>Vừa có bình luận mới cho YCMH ".$purchases->code. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value['staffid'],
						'link'            => ''
					]);
				}
			}

			if(!empty($staffTag)) {
				foreach($staffTag as $key => $value) {
					add_notification([
						'description'     => "<a onclick='view_purchases($purchases->id); return false;'> Vừa nhắc bạn vào bình luận cho YCMH ".$purchases->code . ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value,
						'link'            => ''
					]);
				}
			}

			$htmlFeedBack = $this->view_feed_back_purchases($id_feed_back, false);
			$purchases_feedback->html = $htmlFeedBack;

			ConnectPusher($purchases_feedback, 'event_purchases', 'feed_back');
			echo json_encode([
				'success' => true,
				'message' => _l('Thêm feedback thành công'),
				'alert_type' => 'success',
				'id' => $id_feed_back,
				'html' => $htmlFeedBack
			]);die();
		}
		echo json_encode([
			'success' => false,
			'message' => _l('Thêm feedback không thành công'),
			'alert_type' => 'danger'
		]);die();
	}

	public function view_feed_back_purchases($id_feed_back, $type = true) {
		$this->db->where('id', $id_feed_back);
		$purchases_feedback = $this->db->get('tblpurchases_feedback')->row();
		if(!empty($purchases_feedback)) {
			$this->db->where('rel_id', $purchases_feedback->id);
			$this->db->where('rel_type', 'feedback_p');
			$purchases_feedback->file = $this->db->get('tblfiles')->result();
		}

		if($type == false) {
			return $this->load->view('admin/feedback/purchases/comment_feedback', ['feedback' => $purchases_feedback], true); die();
		}
		else {
			return $this->load->view('admin/feedback/purchases/comment_feedback', ['feedback' => $purchases_feedback]);
		}
	}

	public function remove_feed_back_purchases($id = "") {
		if(!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblpurchases_feedback');
			if(!empty($delete)) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_p');
				$files_delete = $this->db->get('tblfiles')->result_array();
				foreach($files_delete as $key => $value) {
					unlink(PURCHASES_FEEDBACK . $id.'/' . $value['file_name']);
				}

				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_p');
				$this->db->delete('tblfiles');

				ConnectPusher(['id' => $id], 'event_purchases', 'remove_feed_back');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => _l('Xóa feedback thành công')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('Xóa feedback không thành công')
		]);die();
	}
	//END Feed back purchases


	//Feed back import
	public function add_feed_back_import() {
		$id_import = $this->input->post('id');
		$feedback = $this->input->post('comment_feedback');
		$tag_comment  = $this->input->post('tag_comment');

		$staffTag = [];
		$data_tag_comment = json_decode($tag_comment);
		foreach($data_tag_comment as $key => $value) {
			$feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.$value->name.'</i>', $feedback);
			$staffTag[] = $value->id;
		}

		if(empty($feedback) && empty($_FILES)) {
			echo json_encode([
				'success' => false,
				'message' => _l('Thêm feedback không thành công'),
				'alert_type' => 'danger'
			]);die();
		}

		$this->db->where('id_import', $id_import);
		$success = $this->db->insert('tblimport_feedback', [
			'id_import' => $id_import,
			'feedback' => $feedback,
			'create_by' => get_staff_user_id(),
			'date_create' => date('Y-m-d H:i:s'),
		]);
		if(!empty($success)) {
			$id_feed_back = $this->db->insert_id();
			if(!empty($id_feed_back)) {
				if(!empty($_FILES['file'])) {
					if (!file_exists(IMPORT_FEEDBACK . '/')) {
						mkdir(IMPORT_FEEDBACK . '/');
						fopen(IMPORT_FEEDBACK . '/' . 'index.html', 'w');
					}
					if (!file_exists(IMPORT_FEEDBACK . $id_feed_back . '/')) {
						mkdir(IMPORT_FEEDBACK . $id_feed_back . '/');
						fopen(IMPORT_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
					}
					$filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
					if (is_uploaded_file($_FILES['file']['tmp_name'])) {
						$typeFile = $_FILES['file']['type'];
						$source_path = $_FILES['file']['tmp_name'];
						$target_path = IMPORT_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
						if (move_uploaded_file($source_path, $target_path)) {
							$file = 'uploads/' . $id_feed_back . '/' . $_FILES['file']['name'];
							if (!empty($file)) {
								$this->db->insert('tblfiles', [
									'rel_id' => $id_feed_back,
									'rel_type' => 'feedback_i',
									'file_name' => $filename,
									'filetype' => $typeFile,
									'staffid' => get_staff_user_id(),
									'dateadded' => date('Y-m-d H:i:s'),
								]);
							}
						}
					}
				}
			}

			$this->db->where('id', $id_feed_back);
			$import_feedback = $this->db->get('tblimport_feedback')->row();

			$import = $this->db->get_where('tblimport', ['tblimport.id' => $id_import])->row();

			$this->db->join('tbl_staff_child_permission_v2', 'tbl_staff_child_permission_v2.id_staff = tblstaff.staffid AND tbl_staff_child_permission_v2.obj_permission = "import"', 'left');
			$this->db->group_start();
			$this->db->where('can_view', 1);
			$this->db->or_where('tblstaff.admin', 1);
			$this->db->or_group_start();
			$this->db->where('can_view_own', 1);
			$this->db->where('staffid', $import->staff_create);
			$this->db->group_end();
			$this->db->group_end();
			if(!empty($staffTag)) {
				$this->db->where_not_in('staffid', $staffTag);
			}

			$staff = $this->db->get('tblstaff')->result_array();
			if(!empty($staff)) {
				foreach($staff as $key => $value) {
					add_notification([
						'description'     => "<a onclick='view_import($import->id); return false;'> Vừa có bình luận mới cho đơn nhập hàng ".$import->prefix . '-' . $import->code. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value['staffid'],
						'link'            => ''
					]);
				}
			}
			if(!empty($staffTag)) {
				foreach($staffTag as $key => $value) {
					add_notification([
						'description'     => "<a onclick='view_import($import->id); return false;'> Vừa nhắc bạn vào bình luận cho đơn nhập hàng ".$import->prefix . '-' . $import->code. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value,
						'link'            => ''
					]);
				}
			}

			$htmlFeedBack = $this->view_feed_back_import($id_feed_back, false);
			$import_feedback->html = $htmlFeedBack;

			ConnectPusher($import_feedback, 'event_import', 'feed_back');
			echo json_encode([
				'success' => true,
				'message' => _l('Thêm feedback thành công'),
				'alert_type' => 'success',
				'id' => $id_feed_back,
				'html' => $htmlFeedBack
			]);die();
		}
		echo json_encode([
			'success' => false,
			'message' => _l('Thêm feedback không thành công'),
			'alert_type' => 'danger'
		]);die();
	}

	public function view_feed_back_import($id_feed_back, $type = true) {
		$this->db->where('id', $id_feed_back);
		$import_feedback = $this->db->get('tblimport_feedback')->row();
		if(!empty($import_feedback)) {
			$this->db->where('rel_id', $import_feedback->id);
			$this->db->where('rel_type', 'feedback_i');
			$import_feedback->file = $this->db->get('tblfiles')->result();
		}

		if($type == false) {
			return $this->load->view('admin/feedback/import/comment_feedback', ['feedback' => $import_feedback], true); die();
		}
		else {
			return $this->load->view('admin/feedback/import/comment_feedback', ['feedback' => $import_feedback]);
		}
	}

	public function remove_feed_back_import($id = "") {
		if(!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblimport_feedback');
			if(!empty($delete)) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_i');
				$files_delete = $this->db->get('tblfiles')->result_array();
				foreach($files_delete as $key => $value) {
					unlink(IMPORT_FEEDBACK . $id.'/' . $value['file_name']);
				}

				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_i');
				$this->db->delete('tblfiles');

				ConnectPusher(['id' => $id], 'event_import', 'remove_feed_back');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => _l('Xóa feedback thành công')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('Xóa feedback không thành công')
		]);die();
	}
	//END Feed back import


	//Feed back orders
	public function add_feed_back_orders() {
		$id_orders = $this->input->post('id');
		$feedback = $this->input->post('comment_feedback');

		$tag_comment  = $this->input->post('tag_comment');
		$staffTag = [];
		$data_tag_comment = json_decode($tag_comment);
		foreach($data_tag_comment as $key => $value) {
			$feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.$value->name.'</i>', $feedback);
			$staffTag[] = $value->id;
		}



		if(empty($feedback) && empty($_FILES)) {
			echo json_encode([
				'success' => false,
				'message' => _l('Thêm feedback không thành công'),
				'alert_type' => 'danger'
			]);die();
		}

		$this->db->where('id_orders', $id_orders);
		$success = $this->db->insert('tblorders_feedback', [
			'id_orders' => $id_orders,
			'feedback' => $feedback,
			'create_by' => get_staff_user_id(),
			'date_create' => date('Y-m-d H:i:s'),
		]);
		if(!empty($success)) {
			$id_feed_back = $this->db->insert_id();
			if(!empty($id_feed_back)) {
				if(!empty($_FILES['file'])) {

					if (!file_exists(ORDERS_FEEDBACK . '/')) {
						mkdir(ORDERS_FEEDBACK . '/');
						fopen(ORDERS_FEEDBACK . '/' . 'index.html', 'w');
					}

					if (!file_exists(ORDERS_FEEDBACK . $id_feed_back . '/')) {
						mkdir(ORDERS_FEEDBACK . $id_feed_back . '/');
						fopen(ORDERS_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
					}

					$filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
					if (is_uploaded_file($_FILES['file']['tmp_name'])) {
						$typeFile = $_FILES['file']['type'];
						$source_path = $_FILES['file']['tmp_name'];
						$target_path = ORDERS_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
						if (move_uploaded_file($source_path, $target_path)) {
							$file = 'uploads/' . $id_feed_back . '/' . $_FILES['file']['name'];
							if (!empty($file)) {
								$this->db->insert('tblfiles', [
									'rel_id' => $id_feed_back,
									'rel_type' => 'feedback_o',
									'file_name' => $filename,
									'filetype' => $typeFile,
									'staffid' => get_staff_user_id(),
									'dateadded' => date('Y-m-d H:i:s'),
								]);
							}
						}
					}
				}
			}

			$this->db->where('id', $id_feed_back);
			$orders_feedback = $this->db->get('tblorders_feedback')->row();

			$orders = $this->db->get_where('tbl_orders', ['tbl_orders.id' => $id_orders])->row();

			$this->db->join('tbl_staff_child_permission_v2', 'tbl_staff_child_permission_v2.id_staff = tblstaff.staffid AND tbl_staff_child_permission_v2.obj_permission = "orders"', 'left');
			$this->db->group_start();
			$this->db->where('can_view', 1);
			$this->db->or_where('tblstaff.admin', 1);
			$this->db->or_group_start();
			$this->db->where('can_view_own', 1);
			$this->db->where('staffid', $orders->created_by);
			$this->db->group_end();
			$this->db->group_end();

			if(!empty($staffTag)) {
				$this->db->where_not_in('staffid', $staffTag);
			}

			$staff = $this->db->get('tblstaff')->result_array();
			if(!empty($staff)) {
				foreach($staff as $key => $value) {
					add_notification([
						'description'     => "<a  data-tnh='modal' class='tnh-modal' href='".admin_url('orders/view_order/'.$orders->id)."' data-toggle='modal' data-target='#myModal'> Vừa có bình luận mới cho đơn hàng ".$orders->reference_no. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value['staffid'],
						'link'            => ''
					]);
				}
			}

			if(!empty($staffTag)) {
				foreach($staffTag as $key => $value) {
					add_notification([
						'description'     => "<a  data-tnh='modal' class='tnh-modal' href='".admin_url('orders/view_order/'.$orders->id)."' data-toggle='modal' data-target='#myModal'> Vừa nhắc bạn cho bình luận mới của đơn hàng ".$orders->reference_no. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value,
						'link'            => ''
					]);
				}
			}


			$htmlFeedBack = $this->view_feed_back_orders($id_feed_back, false);
			$orders_feedback->html = $htmlFeedBack;

			ConnectPusher($orders_feedback, 'event_orders', 'feed_back');
			echo json_encode([
				'success' => true,
				'message' => _l('Thêm feedback thành công'),
				'alert_type' => 'success',
				'id' => $id_feed_back,
				'html' => $htmlFeedBack
			]);die();
		}
		echo json_encode([
			'success' => false,
			'message' => _l('Thêm feedback không thành công'),
			'alert_type' => 'danger'
		]);die();
	}

	public function view_feed_back_orders($id_feed_back, $type = true) {
		$this->db->where('id', $id_feed_back);
		$orders_feedback = $this->db->get('tblorders_feedback')->row();
		if(!empty($orders_feedback)) {
			$this->db->where('rel_id', $orders_feedback->id);
			$this->db->where('rel_type', 'feedback_o');
			$orders_feedback->file = $this->db->get('tblfiles')->result();
		}
		if($type == false) {
			return $this->load->view('admin/feedback/orders/comment_feedback', ['feedback' => $orders_feedback], true); die();
		}
		else {
			return $this->load->view('admin/feedback/orders/comment_feedback', ['feedback' => $orders_feedback]);
		}
	}

	public function remove_feed_back_orders($id = "") {
		if(!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblorders_feedback');
			if(!empty($delete)) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_o');
				$files_delete = $this->db->get('tblfiles')->result_array();
				foreach($files_delete as $key => $value) {
					unlink(ORDERS_FEEDBACK . $id.'/' . $value['file_name']);
				}

				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_o');
				$this->db->delete('tblfiles');

				ConnectPusher(['id' => $id], 'event_orders', 'remove_feed_back');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => _l('Xóa feedback thành công')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('Xóa feedback không thành công')
		]);die();
	}
	//END Feed back orders




    //Feed back order_production_details
    public function add_feed_back_order_production_details() {
        $id_order_production_details = $this->input->post('id');
        $feedback = $this->input->post('comment_feedback');
        $tag_comment  = $this->input->post('tag_comment');

        $staffTag = [];
        $data_stages = [];
        $data_tag_comment = json_decode($tag_comment);

        foreach($data_tag_comment as $key => $value) {
            if($value->type == 'stages') {
                $id_stages = explode('_', $value->id)[1];
                $feedback = str_replace($value->name, '<i class="stages_tag" data-id="'.$id_stages.'">'.$value->name.'</i>', $feedback);
                $data_stages[] = $id_stages;
            }
            else {
                $name = $value->name;
                $value_name = explode(':', $value->name);
                if(count($value_name) > 1) {
                    $name = $value_name[1];
                }
                $feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.trim($name).'</i>', $feedback);
            }
            if(is_numeric($value->id)) {
                $staffTag[] = $value->id;
            }
        }



        if(empty($feedback) && empty($_FILES)) {
            echo json_encode([
                'success' => false,
                'message' => _l('Thêm feedback không thành công'),
                'alert_type' => 'danger'
            ]);die();
        }

        $this->db->where('id_order_production_details', $id_order_production_details);
        $success = $this->db->insert('tblorder_production_details_feedback', [
            'id_order_production_details' => $id_order_production_details,
            'feedback' => $feedback,
            'create_by' => get_staff_user_id(),
            'date_create' => date('Y-m-d H:i:s'),
        ]);
        if(!empty($success)) {
            $id_feed_back = $this->db->insert_id();

            if(!empty($data_stages)) {
                foreach($data_stages as $key => $value) {
                    $this->db->insert('tblfeedback_stages', [
                        'id_feedback' => $id_feed_back,
                        'id_stages' => $value,
                    ]);
                }
            }


            if(!empty($id_feed_back)) {
                if(!empty($_FILES['file'])) {
                    if (!file_exists(ORDER_PRODUCTION_DETAILS_FEEDBACK . '/')) {
                        mkdir(ORDER_PRODUCTION_DETAILS_FEEDBACK . '/');
                        fopen(ORDER_PRODUCTION_DETAILS_FEEDBACK . '/' . 'index.html', 'w');
                    }

                    if (!file_exists(ORDER_PRODUCTION_DETAILS_FEEDBACK . $id_feed_back . '/')) {
                        mkdir(ORDER_PRODUCTION_DETAILS_FEEDBACK . $id_feed_back . '/');
                        fopen(ORDER_PRODUCTION_DETAILS_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
                    }
                    $filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
                    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                        $typeFile = $_FILES['file']['type'];
                        $source_path = $_FILES['file']['tmp_name'];
                        $target_path = ORDER_PRODUCTION_DETAILS_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
                        if (move_uploaded_file($source_path, $target_path)) {
                            $file = 'uploads/' . $id_feed_back . '/' . $_FILES['file']['name'];
                            if (!empty($file)) {
                                $this->db->insert('tblfiles', [
                                    'rel_id' => $id_feed_back,
                                    'rel_type' => 'feedback_opd',
                                    'file_name' => $filename,
                                    'filetype' => $typeFile,
                                    'staffid' => get_staff_user_id(),
                                    'dateadded' => date('Y-m-d H:i:s'),
                                ]);
                            }
                        }
                    }
                }
            }

            $this->db->where('id', $id_feed_back);
            $order_production_details_feedback = $this->db->get('tblorder_production_details_feedback')->row();

            $this->db->select('tbl_productions_orders_details.*, tbl_productions_orders_items.items_name as item_name');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
            $productions_orders_details = $this->db->get_where('tbl_productions_orders_details', ['tbl_productions_orders_details.id' => $id_order_production_details])->row();

            $this->db->join('tbl_staff_child_permission_v2', 'tbl_staff_child_permission_v2.id_staff = tblstaff.staffid AND tbl_staff_child_permission_v2.obj_permission = "manufactures_order_production_details"', 'left');
            $this->db->group_start();
            $this->db->where('can_view', 1);
            $this->db->or_where('tblstaff.admin', 1);
            $this->db->or_group_start();
            $this->db->where('can_view_own', 1);
            $this->db->where('staffid', $productions_orders_details->created_by);
            $this->db->group_end();
            $this->db->group_end();
            if(!empty($staffTag)) {
                $this->db->where_not_in('staffid', $staffTag);
            }
            $staff = $this->db->get('tblstaff')->result_array();
            if(!empty($staff)) {
                foreach($staff as $key => $value) {
                    add_notification([
                        'description'     => "<a target='_blank' href='".admin_url('manufactures/detail_productions/'.$productions_orders_details->id)."'> Vừa có bình luận mới cho lệnh sản xuất chi tiết ".$productions_orders_details->reference_no . ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
                        'touserid'        => $value['staffid'],
                        'link'            => ''
                    ]);

                    $this->db->select('player_id, staffid');
                    $this->db->where('staffid', $value['staffid']);
                    $player_id = $this->db->get('tblplayer_id')->result_array();
                    foreach($player_id as $k => $v) {
                        $description = get_staff_full_name() . " Vừa có bình luận mới cho lệnh sản xuất chi tiết ".$productions_orders_details->reference_no . ' Vào lúc '._dt(date('Y-m-d H:i:s'));
                        $_data = [];
                        $_data['message'] =  $description;
                        $_data['title'] = 'Bình luận lệnh sản xuất chi tiết';
                        $_data['code'] = $productions_orders_details->reference_no;

                        $_data['item_name'] = $productions_orders_details->item_name;
                        $_data['total'] = 0;
                        $_data['user_id'] = $v['player_id'];
                        $_data['staff_name'] = $value['firstname']. ' ' . $value['lastname'];
                        $_data['staff_id'] = $v['staffid'];
						$_data['created_by'] = get_staff_user_id();
                        SendOnesignal($_data, 'feedback', $id_order_production_details);
                    }
                }
            }
            if(!empty($staffTag)) {
                foreach($staffTag as $key => $value) {
                    add_notification([
                        'description'     => "<a target='_blank' href='".admin_url('manufactures/detail_productions/'.$productions_orders_details->id)."'> Vừa nhắc bạn vào bình luận cho  lệnh sản xuất chi tiết ".$productions_orders_details->reference_no . ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
                        'touserid'        => $value,
                        'link'            => '',
                        'additional_data' => '0',
                        'type' => 0
                    ]);

                    $this->db->select('player_id, staffid');
                    $this->db->where('staffid', $value);
                    $player_id = $this->db->get('tblplayer_id')->result_array();

                    foreach($player_id as $k => $v) {
                        $description = get_staff_full_name() . " Vừa nhắc bạn vào bình luận cho lệnh sản xuất chi tiết ".$productions_orders_details->reference_no . ' Vào lúc '._dt(date('Y-m-d H:i:s'));
                        $_data = [];
                        $_data['message'] =  $description;
                        $_data['title'] = 'Bình luận lệnh sản xuất chi tiết';
                        $_data['code'] = $productions_orders_details->reference_no;

                        $_data['item_name'] = $productions_orders_details->item_name;
                        $_data['total'] = 0;
                        $_data['user_id'] = $v['player_id'];
                        $_data['staff_name'] = get_staff_full_name($value);
                        $_data['staff_id'] = $value;
						$_data['created_by'] = get_staff_user_id();
                        SendOnesignal($_data, 'feedback', $id_order_production_details);
                    }
                }
            }

            $htmlFeedBack = $this->view_feed_back_order_production_details($id_feed_back, false);
            $order_production_details_feedback->html = $htmlFeedBack;
            ConnectPusher($order_production_details_feedback, 'event_order_production_details', 'feed_back');
            echo json_encode([
                'success' => true,
                'message' => _l('Thêm feedback thành công'),
                'alert_type' => 'success',
                'id' => $id_feed_back,
                'html' => $htmlFeedBack
            ]);die();
        }
        echo json_encode([
            'success' => false,
            'message' => _l('Thêm feedback không thành công'),
            'alert_type' => 'danger'
        ]);die();
    }

    public function view_feed_back_order_production_details($id_feed_back, $type = true) {

        $this->db->where('id', $id_feed_back);
        $order_production_details_feedback = $this->db->get('tblorder_production_details_feedback')->row();
        if(!empty($order_production_details_feedback)) {
            $this->db->where('rel_id', $order_production_details_feedback->id);
            $this->db->where('rel_type', 'feedback_opd');
            $order_production_details_feedback->file = $this->db->get('tblfiles')->result();
        }

        if($type == false) {
            return $this->load->view('admin/feedback/order_production_details/comment_feedback', ['feedback' => $order_production_details_feedback], true); die();
        }
        else {
            return $this->load->view('admin/feedback/order_production_details/comment_feedback', ['feedback' => $order_production_details_feedback]);
        }
    }

    public function remove_feed_back_order_production_details($id = "") {
        if(!empty($id)) {

            if(!is_admin()) {
                $staffCreate = get_staff_user_id();
                $this->db->where('create_by', $staffCreate);
            }
            $this->db->where('id', $id);
            $get_data_delete = $this->db->get('tblorder_production_details_feedback')->row();


            if(!is_admin()) {
                $staffCreate = get_staff_user_id();
                $this->db->where('create_by', $staffCreate);
            }
            $this->db->where('id', $id);
            $delete = $this->db->delete('tblorder_production_details_feedback');
            if(!empty($delete)) {
                if(!empty($get_data_delete)) {
                    $this->db->where('id_feedback', $get_data_delete->id);
                    $this->db->delete('tblfeedback_stages');
                }



                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'feedback_opd');
                $files_delete = $this->db->get('tblfiles')->result_array();
                foreach($files_delete as $key => $value) {
                    unlink(ORDER_PRODUCTION_DETAILS_FEEDBACK . $id.'/' . $value['file_name']);
                }

                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'feedback_opd');
                $this->db->delete('tblfiles');

                ConnectPusher(['id' => $id], 'event_order_production_details', 'remove_feed_back');
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('Xóa feedback thành công')
                ]);die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('Xóa feedback không thành công')
        ]);die();
    }
    //END Feed back order_production_details


	//Feed back orders
	public function add_feed_back_violation_records() {
		$id_violation_records = $this->input->post('id');
		$feedback = $this->input->post('comment_feedback');

		$tag_comment  = $this->input->post('tag_comment');
		$staffTag = [];
		$data_tag_comment = json_decode($tag_comment);
		foreach($data_tag_comment as $key => $value) {
			$feedback = str_replace($value->name, '<i class="i_tag" data-id="'.$value->id.'">'.$value->name.'</i>', $feedback);
			$staffTag[] = $value->id;
		}


		if(empty($feedback) && empty($_FILES)) {
			echo json_encode([
				'success' => false,
				'message' => _l('Thêm feedback không thành công'),
				'alert_type' => 'danger'
			]);die();
		}

		$this->db->where('id_violation_records', $id_violation_records);
		$success = $this->db->insert('tblviolation_records_feedback', [
			'id_violation_records' => $id_violation_records,
			'feedback' => $feedback,
			'create_by' => get_staff_user_id(),
			'date_create' => date('Y-m-d H:i:s'),
		]);
		if(!empty($success)) {
			$id_feed_back = $this->db->insert_id();
			if(!empty($id_feed_back)) {
				if(!empty($_FILES['file'])) {

					if (!file_exists(VIOLATION_RECORDS_FEEDBACK . '/')) {
						mkdir(VIOLATION_RECORDS_FEEDBACK . '/');
						fopen(VIOLATION_RECORDS_FEEDBACK . '/' . 'index.html', 'w');
					}

					if (!file_exists(VIOLATION_RECORDS_FEEDBACK . $id_feed_back . '/')) {
						mkdir(VIOLATION_RECORDS_FEEDBACK . $id_feed_back . '/');
						fopen(VIOLATION_RECORDS_FEEDBACK . $id_feed_back . '/' . 'index.html', 'w');
					}

					$filename = $_FILES['file']['name'] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name']));
					if (is_uploaded_file($_FILES['file']['tmp_name'])) {
						$typeFile = $_FILES['file']['type'];
						$source_path = $_FILES['file']['tmp_name'];
						$target_path = VIOLATION_RECORDS_FEEDBACK . $id_feed_back . '/' . $_FILES['file']['name'];
						if (move_uploaded_file($source_path, $target_path)) {
							$this->db->insert('tblfiles', [
								'rel_id' => $id_feed_back,
								'rel_type' => 'feedback_vr',
								'file_name' => $filename,
								'filetype' => $typeFile,
								'staffid' => get_staff_user_id(),
								'dateadded' => date('Y-m-d H:i:s'),
							]);
						}
					}
				}
			}

			$this->db->where('id', $id_feed_back);
			$orders_feedback = $this->db->get('tblviolation_records_feedback')->row();

			$violation_records = $this->db->get_where('tblviolation_records', ['tblviolation_records.id' => $id_violation_records])->row();

			if(!empty($staffTag)) {
				foreach($staffTag as $key => $value) {
					add_notification([
						'description'     => "<a class='c_modal' href='".admin_url('violation_records/view/'.$violation_records->id)."' > Vừa nhắc bạn cho bình luận mới của biên bản vi phạm ".$violation_records->code. ' Vào lúc '._dt(date('Y-m-d H:i:s')).'</a>',
						'touserid'        => $value,
						'link'            => ''
					]);
				}
			}


			$htmlFeedBack = $this->view_feed_back_violation_records($id_feed_back, false);
			$orders_feedback->html = $htmlFeedBack;

			ConnectPusher($orders_feedback, 'event_violation_records', 'feed_back');
			echo json_encode([
				'success' => true,
				'message' => _l('Thêm feedback thành công'),
				'alert_type' => 'success',
				'id' => $id_feed_back,
				'html' => $htmlFeedBack
			]);die();
		}
		echo json_encode([
			'success' => false,
			'message' => _l('Thêm feedback không thành công'),
			'alert_type' => 'danger'
		]);die();
	}

	public function view_feed_back_violation_records($id_feed_back, $type = true) {
		$this->db->where('id', $id_feed_back);
		$orders_feedback = $this->db->get('tblviolation_records_feedback')->row();
		if(!empty($orders_feedback)) {
			$this->db->where('rel_id', $orders_feedback->id);
			$this->db->where('rel_type', 'feedback_vr');
			$orders_feedback->file = $this->db->get('tblfiles')->result();
		}
		if($type == false) {
			return $this->load->view('admin/feedback/violation_records/comment_feedback', ['feedback' => $orders_feedback], true); die();
		}
		else {
			return $this->load->view('admin/feedback/violation_records/comment_feedback', ['feedback' => $orders_feedback]);die();
		}
	}

	public function remove_feed_back_violation_records($id = "") {
		if(!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblviolation_records_feedback');
			if(!empty($delete)) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_vr');
				$files_delete = $this->db->get('tblfiles')->result_array();
				foreach($files_delete as $key => $value) {
					@unlink(VIOLATION_RECORDS_FEEDBACK . $id.'/' . $value['file_name']);
				}

				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'feedback_vr');
				$this->db->delete('tblfiles');

				ConnectPusher(['id' => $id], 'event_violation_records', 'remove_feed_back');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => _l('Xóa feedback thành công')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('Xóa feedback không thành công')
		]);die();
	}
	//END Feed back orders



    public function setupDataBase() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `tblimport_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_import` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tblpurchase_order_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_purchase_order` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tblpurchases_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_purchases` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tblorders_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_orders` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tblorders_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_orders` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tblorder_production_details_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_order_production_details` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tblfeedback_stages` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_stages` int(11) NOT NULL,
		  `id_feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$this->db->query("CREATE TABLE IF NOT EXISTS `tblviolation_records_feedback` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_violation_records` int(11) NOT NULL,
		  `feedback` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `create_by` int(11) NOT NULL,
		  `date_create` datetime NOT NULL,
		  `update_by` int(11) DEFAULT NULL,
		  `date_update` datetime DEFAULT NULL,
    		PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");


    }
}
