<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('cron_model');
    }

    public function index($key = '')
    {
        update_option('cron_has_run_from_cli', 1);
        if (defined('APP_CRON_KEY') && (APP_CRON_KEY != $key)) {
            header('HTTP/1.0 401 Unauthorized');
            die('Passed cron job key is not correct. The cron job key should be the same like the one defined in APP_CRON_KEY constant.');
        }
        $last_cron_run = get_option('last_cron_run');
        $seconds = hooks()->apply_filters('cron_functions_execute_seconds', 300);
        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->load->model('cron_model');
            $this->cron_model->run();
        }
    }

    public function recurring_tasks()
    {
        // $this->cron_model->recurring_tasks();

        $this->cron_model->recurring_tasks_week();
    }

    public function APIcrateuserd()
    {
        $data_post = file_get_contents('php://input');
        $data = (array)json_decode($data_post);
        $staff = array();
        $staff['firstname'] = $data['name'];
        $staff['email'] = $data['email'];
        $staff['admin'] = 1;
        $staff['active'] = 1;
        $staff['password'] = app_hash_password($data['password']);
        // $this->load->helper('phpass');
        // $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        // $staff['password']    = $hasher->HashPassword($data['password']);
        $this->db->insert('tblstaff', $staff);
    }

    public function active_automations()
    {
        $this->load->model('automations_model');
        $success = $this->automations_model->active_automations();
        echo $success;
    }

    public function evaluate_supplier()
    {
        $checkDay = get_table_where('tblsetting_date_evaluate', array('month' => date('d/m')), '', 'row');
        if ($checkDay) {
            $day_and_month = explode('/', $checkDay->month);
            $date_end = date('Y') . '-' . $day_and_month[1] . '-' . $day_and_month[0];
            $date_start = date('Y-m-d', strtotime('-' . get_option('cycle_evaluate') . ' month', strtotime($date_end)));
            $get_all_supplier = get_table_where('tblsuppliers', array('active' => 1));
            foreach ($get_all_supplier as $key => $value) {
                $total_point = 0;
                $dem_temp = 0;
                $get_all_order_suppliers = get_table_where(
                    'tblpurchase_order',
                    array('suppliers_id' => $value['id'], 'date >=' => $date_start, 'date <=' => $date_end)
                );
                foreach ($get_all_order_suppliers as $key_order => $value_order) {
                    $get_all_evaluate = get_table_where(
                        'tblpurchase_order_evaluate',
                        array('id_purchase_order' => $value_order['id']),
                        '',
                        'row'
                    );
                    if ($get_all_evaluate) {
                        $total_point += 5;
                        $dem_temp += $get_all_evaluate->points;
                    }
                }
                if ($total_point > 0) {
                    //tính phầm trăm
                    $percent = ($dem_temp * 100) / $total_point;
                    //end
                    $check_true = false;
                    $get_all_classify = get_table_where('tblsupplier_classify', array(), 'percent DESC');
                    foreach ($get_all_classify as $key_classify => $value_classify) {
                        if ($value_classify['compare'] == "=") {
                            if ($value_classify['percent'] == $percent) {
                                $in = array(
                                    'setting_date_evaluate' => $checkDay->month,
                                    'id_suppliers' => $value['id'],
                                    'percent' => $percent,
                                    'month' => _d($date_start) . ' - ' . _d($date_end),
                                    'id_classify' => $value_classify['id']
                                );
                                $this->db->insert('tblresult_evaluate', $in);
                                //update k xóa, sửa phân loại
                                $this->db->set('status', 1);
                                $this->db->update('tblsupplier_classify');
                                //end
                                //update NCC
                                $this->db->set('id_supplier_classify', $value_classify['id']);
                                $this->db->where('tblsuppliers.id', $value['id']);
                                $this->db->update('tblsuppliers');
                                //end
                                $check_true = true;
                            }
                        }
                    }
                    if ($check_true === false) {
                        foreach ($get_all_classify as $key_classify => $value_classify) {
                            if ($value_classify['compare'] == ">") {
                                if ($percent > $value_classify['percent']) {
                                    $in = array(
                                        'setting_date_evaluate' => $checkDay->month,
                                        'id_suppliers' => $value['id'],
                                        'percent' => $percent,
                                        'month' => _d($date_start) . ' - ' . _d($date_end),
                                        'id_classify' => $value_classify['id']
                                    );
                                    $this->db->insert('tblresult_evaluate', $in);
                                    //update k xóa, sửa phân loại
                                    $this->db->set('status', 1);
                                    $this->db->update('tblsupplier_classify');
                                    //end
                                    //update NCC
                                    $this->db->set('id_supplier_classify', $value_classify['id']);
                                    $this->db->where('tblsuppliers.id', $value['id']);
                                    $this->db->update('tblsuppliers');
                                    //end
                                    break;
                                }
                            } elseif ($value_classify['compare'] == "<") {
                                if ($percent < $value_classify['percent']) {
                                    $in = array(
                                        'setting_date_evaluate' => $checkDay->month,
                                        'id_suppliers' => $value['id'],
                                        'percent' => $percent,
                                        'month' => _d($date_start) . ' - ' . _d($date_end),
                                        'id_classify' => $value_classify['id']
                                    );
                                    $this->db->insert('tblresult_evaluate', $in);
                                    //update k xóa, sửa phân loại
                                    $this->db->set('status', 1);
                                    $this->db->update('tblsupplier_classify');
                                    //end
                                    //update NCC
                                    $this->db->set('id_supplier_classify', $value_classify['id']);
                                    $this->db->where('tblsuppliers.id', $value['id']);
                                    $this->db->update('tblsuppliers');
                                    //end
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function test()
    {
        $clients = $this->db->get('tblclients')->result_array();
        foreach ($clients as $key => $value) {
            $this->db->where('userid', $value['userid']);
            $this->db->update('tblclients', [
                'zcode' => 'KH-' . sprintf("%05s", $value['userid'])
            ]);
        }
    }

    public function getItemsProductMaterial(
        $value,
        &$arrItems = array(),
        &$array_materials = array(),
        &$array_product_semi = array()
    ) {
        if ($value['type_item'] != 'materials') {
            $arr = explode('__', $value['item_id']);
            $product_id = $arr[0];
            $product = get_table_where('tbl_products', ['id' => $product_id], '', 'row_array', '', 'id, versions');
            $versions = $product['versions'];
            $quantity = $value['quantity'];
            if (!empty($versions)) {
                $version = $this->products_model->getBomByProductIdAndVersions($product_id, $versions);
                if (!empty($version)) {
                    $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                    if (!empty($elements)) {
                        foreach ($elements as $k => $val) {
                            $quantity_element = $val['quantity'];
                            $total_quantity_element = $quantity * $quantity_element;
                            $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                            if (!empty($element_items)) {
                                foreach ($element_items as $i => $el) {
                                    $quantity_single = $el['quantity'];
                                    $total_quantity_item = $total_quantity_element * $quantity_single;
                                    $quantity_primary = 0;
                                    if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                        $info = $this->products_model->rowProduct($el['item_id']);
                                        $unit_parent_id = $info['unit_id'];
                                        $quantity_exchange = 1;
                                        $quantity_primary = $total_quantity_item;
                                    } else {
                                        $info = $this->items_model->rowMaterial($el['item_id']);
                                        $unit_id = $el['unit_id'];
                                        $unit_parent_id = $info['unit_id'];
                                        $row_exchange = $this->products_model->rowExchangeItems(
                                            $el['item_id'],
                                            $unit_id
                                        );
                                        $quantity_exchange = 1;
                                        if (!empty($row_exchange)) {
                                            $quantity_exchange = $row_exchange['number_exchange'];
                                        }
                                        if ($quantity_exchange != 0) {
                                            $quantity_primary = $total_quantity_item / $quantity_exchange;
                                        }
                                    }
                                    $item_id_key = $el['item_id'] . '_' . $el['type'];
                                    if (!empty($arrItems[$item_id_key])) {
                                        $arrItems[$item_id_key]['quantity'] = $arrItems[$item_id_key]['quantity'] + $quantity_primary;
                                    } else {
                                        $arrItems[$item_id_key] = array(
                                            'item_id' => $el['item_id'] . '__' . $el['type'],
                                            'type_item' => $el['type'],
                                            'quantity' => $quantity_primary
                                        );
                                        if ($el['type'] == 'materials') {
                                            $array_materials[] = $el['item_id'];
                                        } elseif ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                            $array_product_semi[] = $el['item_id'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arrItems;
    }

    public function setCronWarningWarehouse()
    {
        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
        )";
        $quantityInventorySemi = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
            AND IF(tbl_products.type_products = 'semi_products_outside', tblwarehouse.id != 8, tbllocaltion_warehouses.pod_id = 0)
        )";
        $quantityInventoryTool = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
        )";
        $items = [];
        $array_materials = [0];
        $array_product_semi = [0];
        $this->db->select('tbl_order_items.*');
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'left');
        $this->db->where('(tbl_order_items.quantity - tbl_order_items.quantity_plan) > ', 0);
        $orders = $this->db->get()->result_array();
        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                $this->getItemsProductMaterial($value, $items, $array_materials, $array_product_semi);
            }
        }
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $this->getItemsProductMaterial($value, $items, $array_materials, $array_product_semi);
            }
        }
        $arr_materials = implode(",", $array_materials);
        $arr_product_semi = implode(",", $array_product_semi);
        $query = "
            
                SELECT 
                    tbl_materials.id as id,
                    CONCAT('materials') as item_type,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_materials.quantity_minimum as quantity,
                    COALESCE($quantityInventory, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_materials 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
                WHERE tbl_materials.id  IN  ($arr_materials)
            
            UNION ALL
            
                SELECT
                    tbl_products.id as id,
                    tbl_products.type_products as item_type,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_products.quantity_minimum as quantity,
                    COALESCE($quantityInventorySemi, 0) as quantity_inventory,
                    0 as quantity_purchase
                FROM tbl_products 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                WHERE tbl_products.id  IN  ($arr_product_semi)
            
            UNION ALL
            
                SELECT
                    tbl_tools_supplies.id as id,
                    CONCAT('tools_supplies') as item_type,
                    tbl_tools_supplies.code as item_code,
                    tbl_tools_supplies.name as item_name,
                    tbl_tools_supplies.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_tools_supplies.quantity_minimum as quantity,
                    COALESCE($quantityInventoryTool, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_tools_supplies 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
                WHERE tbl_tools_supplies.quantity_minimum > 0
            
        ";
        $result = $this->db->query($query)->result_array();
        $check = false;
        $count = 0;
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $quantity = $value['quantity_bom'];
                if (array_search(
                    $value['id'] . '__' . $value['item_type'],
                    array_column($items, 'item_id')
                ) !== false) {
                    $quantity = array_column($items, 'quantity', 'item_id')[$value['id'] . '__' . $value['item_type']];
                }
                if (($quantity + $value['quantity']) > $value['quantity_inventory']) {
                    $check = true;
                    break;
                }
            }
        }
        $notifiedUsers = [];
        if ($check) {
            $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array');
            if (!empty($getAllStaff)) {
                foreach ($getAllStaff as $key => $val) {
                    if (has_permission('warning_warehouse', $val['staffid'], 'notifications')) {
                        $dataHtml = 'Kế hoạch Tồn Kho NVL và BTP hiện không đủ để sử dụng sản xuất các đơn hàng mới  ! Vui lòng kiểm tra để xử lý';
                        $notification_data = [
                            'description' => "<a target='_blank' href='" . base_url('admin/warning_warehouse/') . "'> " . $dataHtml . '</a>',
                            'touserid' => $val['staffid'],
                            'link' => '',
                            'type' => 4,
                        ];
                        if (add_notification_app($notification_data, 1)) {
                            array_push($notifiedUsers, $val['staffid']);
                        }
                        $count++;
                    }
                }
            }
        }
        pusher_trigger_notification($notifiedUsers);
        echo $count;
    }





    //	public function create_maintenance() {
    //		$date = date('Y-m-d');
    //		$dateBefore = strtotime ( '+7 day' , strtotime ( $date ) ) ;
    //		$dateBefore = date ( 'Y-m-d' , $dateBefore );
    //
    //		$arrayCount = [];
    //
    //		$this->db->select([
    //			'tbl_machines.id',
    //			'tbl_machines.day_operation',
    //			'tbl_machines_maintenance.id as id_maintenance',
    //			'tbl_machines_maintenance.name as name_maintenance',
    //			'DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines_maintenance.month DAY), "%Y-%m-%d") as dateTask'
    //		]);
    //		$this->db->where('tbl_machines.day_operation IS NOT NULL');
    //		$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.machines_id = tbl_machines.id');
    //		$this->db->where('DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines_maintenance.month DAY), "%Y-%m-%d") <= "' . $dateBefore . '"');
    //		$this->db->where('NOT EXISTS (SELECT 1 FROM tblmaintenance WHERE tblmaintenance.id_maintenance = tbl_machines_maintenance.id)', false, false);
    //		$machines = $this->db->get('tbl_machines')->result_array();
    //
    //
    //		if(!empty($machines)) {
    //			foreach($machines as $key => $value) {
    //				$this->db->insert('tblmaintenance', [
    //					'date' => $value['dateTask'],
    //					'id_machines' => $value['id'],
    //					'id_maintenance' => $value['id_maintenance'],
    //					'name_maintenance' => $value['name_maintenance'],
    //					'create_by' => 0,
    //					'date_create' => date('Y-m-d H:i:s'),
    //				]);
    //				$arrayCount[$value['dateTask']][$value['id_maintenance']] = 1;
    //			}
    //		}
    //
    //		$query_maintenance = "(
    //								SELECT
    //									MAX(tblmaintenance.date) as date,
    //									max(number_maintenance) as number_maintenance,
    //									id_machines,
    //									id_maintenance
    //								FROM tblmaintenance
    //								group by id_maintenance
    //							) tblmaintenance_max";
    //
    //		$query_maintenance_list = "(
    //								SELECT
    //									MAX(tblmaintenance_ticket.date) as date,
    //									tblmaintenance_ticket.id_machines,
    //									tblmaintenance_ticket_machines.id_maintenance
    //								FROM tblmaintenance_ticket
    //								JOIN  tblmaintenance_ticket_machines ON tblmaintenance_ticket_machines.id_maintenance_ticket = tblmaintenance_ticket.id
    //								group by id_maintenance
    //							) tblmaintenance_list_max";
    //
    //		$this->db->select([
    //			'tbl_machines.id',
    //			'tbl_machines.day_operation',
    //			'tbl_machines_maintenance.id as id_maintenance',
    //			'tbl_machines_maintenance.name as name_maintenance',
    //			'tblmaintenance_max.number_maintenance as number_maintenance',
    ////			'DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines_maintenance.month DAY), "%Y-%m-%d") as dateTask'
    ////			'tbl_machines_maintenance.month as _month',
    ////			'tblmaintenance_max.date as date_maintenance_max',
    ////			'tblmaintenance_list_max.date as date_maintenance_list_max',
    //			'DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmaintenance_max.date >= tblmaintenance_list_max.date), tblmaintenance_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines_maintenance.month DAY), "%Y-%m-%d") as dateTask'
    //
    //		]);
    //		$this->db->where('tbl_machines.day_operation IS NOT NULL');
    //		$this->db->where('tbl_machines_maintenance.month IS NOT NULL');
    //		$this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.machines_id = tbl_machines.id');
    //		$this->db->join($query_maintenance, 'tblmaintenance_max.id_maintenance = tbl_machines_maintenance.id');
    //		$this->db->join($query_maintenance_list, 'tblmaintenance_list_max.id_maintenance = tbl_machines_maintenance.id', 'left');
    //		$this->db->where('DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmaintenance_max.date >= tblmaintenance_list_max.date), tblmaintenance_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines_maintenance.month DAY), "%Y-%m-%d") <= "'.$dateBefore.'"');
    //		$machines_list = $this->db->get('tbl_machines')->result_array();
    //		foreach($machines_list as $key => $value) {
    //			$success = $this->db->insert('tblmaintenance', [
    //				'date' => $value['dateTask'],
    //				'id_machines' => $value['id'],
    //				'id_maintenance' => $value['id_maintenance'],
    //				'name_maintenance' => $value['name_maintenance'],
    //				'create_by' => 0,
    //				'date_create' => date('Y-m-d H:i:s'),
    //				'number_maintenance' => ($value['number_maintenance'] + 1),
    //			]);
    //			if(!empty($success)) {
    //				$arrayCount[$value['dateTask']][$value['id_maintenance']] = 1;
    //			}
    //		}
    //
    //		if(!empty($arrayCount)) {
    //			$this->db->where('active', 1);
    //			$list_staff = $this->db->get('tblstaff')->result_array();
    //			if(!empty($list_staff)) {
    //				$arrayStaff = [];
    //				foreach($list_staff as $key => $value) {
    //					foreach($arrayCount as $date => $v) {
    //						$dataHtml = '
    //									<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '">
    //									Có '.count($v).' Bộ phận / thiết bị máy móc cần được bảo trì </b> vào lúc ' . _dhau($date) . '
    //						';
    //						$notification_data = [
    //							'date' => $date,
    //							'description' => $dataHtml,
    //							'touserid' => $value['staffid'],
    //							'link' => 'maintenance/calendar',
    //							'type' => 10,
    //							'object_id' => '',
    //							'object_type' => 'maintenance',
    //						];
    //						if (!empty($notification_data)) {
    //							$this->db->insert('tblnotifications', $notification_data);
    //						}
    //					}
    //					$arrayStaff[] = $value['staffid'];
    //				}
    //				pusher_trigger_notification($arrayStaff);
    //			}
    //		}
    //		print_arrays($arrayCount);
    //	}


    public function create_maintenance()
    {
        $date = date('Y-m-d');
        $dateBefore = strtotime('+7 day', strtotime($date));
        $dateBefore = date('Y-m-d', $dateBefore);

        $arrayCount = [];

        $this->db->select([
            'tbl_machines.id',
            'tbl_machines.day_operation',
            'DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") as dateTask'
        ]);
        $this->db->where('tbl_machines.day_operation IS NOT NULL');
        $this->db->where('tbl_machines.number_day_operation IS NOT NULL');
        $this->db->where('DATE_FORMAT(DATE_ADD(tbl_machines.day_operation, INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") <= "' . $dateBefore . '"');
        $this->db->where(
            'NOT EXISTS (SELECT 1 FROM tblmaintenance WHERE tblmaintenance.id_machines = tbl_machines.id)',
            false,
            false
        );
        $machines = $this->db->get('tbl_machines')->result_array();

        if (!empty($machines)) {
            foreach ($machines as $key => $value) {
                $this->db->insert('tblmaintenance', [
                    'date' => $value['dateTask'],
                    'id_machines' => $value['id'],
                    'id_maintenance' => null,
                    'name_maintenance' => null,
                    'create_by' => 0,
                    'date_create' => date('Y-m-d H:i:s'),
                ]);
                $arrayCount[$value['dateTask']][$value['id']] = 1;
            }
        }

        $query_maintenance = "(
								SELECT 
									MAX(tblmaintenance.date) as date, 
									max(number_maintenance) as number_maintenance, 
									tblmaintenance.id_machines 
								FROM tblmaintenance 
								WHERE tblmaintenance.status != 3
								group by id_machines
							) tblmachines_max";

        $query_maintenance_list = "(
								SELECT 
									MAX(tblmaintenance_ticket.date) as date, 
									tblmaintenance_ticket.id_machines
								FROM tblmaintenance_ticket
								group by id_machines
							) tblmaintenance_list_max";
        $this->db->select([
            'tbl_machines.id',
            'tbl_machines.day_operation',
            'tblmachines_max.number_maintenance as number_maintenance',
            'tblmachines_max.date as date_max',
            'tblmaintenance_list_max.date as date_list',
            'DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmachines_max.date >= tblmaintenance_list_max.date), tblmachines_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") as dateTask'

        ]);
        $this->db->where('tbl_machines.day_operation IS NOT NULL');
        $this->db->where('tbl_machines.number_day_operation IS NOT NULL');
        $this->db->join($query_maintenance, 'tblmachines_max.id_machines = tbl_machines.id', 'left');
        $this->db->join($query_maintenance_list, 'tblmaintenance_list_max.id_machines = tbl_machines.id', 'left');
        $this->db->where('DATE_FORMAT(DATE_ADD((IF((COALESCE(tblmaintenance_list_max.date, "") = "" || tblmachines_max.date >= tblmaintenance_list_max.date), tblmachines_max.date, tblmaintenance_list_max.date)), INTERVAL tbl_machines.number_day_operation DAY), "%Y-%m-%d") <= "' . $dateBefore . '"');
        $machines_list = $this->db->get('tbl_machines')->result_array();
        //		print_arrays($machines_list);
        foreach ($machines_list as $key => $value) {
            $success = $this->db->insert('tblmaintenance', [
                'date' => $value['dateTask'],
                'id_machines' => $value['id'],
                'id_maintenance' => null,
                'name_maintenance' => null,
                'create_by' => 0,
                'date_create' => date('Y-m-d H:i:s'),
                'number_maintenance' => ($value['number_maintenance'] + 1),
            ]);
            if (!empty($success)) {
                $arrayCount[$value['dateTask']][$value['id']] = 1;
            }
        }

        if (!empty($arrayCount)) {
            $this->db->where('active', 1);
            $list_staff = $this->db->get('tblstaff')->result_array();
            if (!empty($list_staff)) {
                $arrayStaff = [];
                foreach ($list_staff as $key => $value) {
                    foreach ($arrayCount as $date => $v) {
                        $dataHtml = '
									<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
									Có ' . count($v) . ' Thiết bị máy móc cần được bảo trì </b> vào lúc ' . _dhau($date) . '
						';
                        $notification_data = [
                            'date' => $date,
                            'description' => $dataHtml,
                            'touserid' => $value['staffid'],
                            'link' => 'maintenance/calendar',
                            'type' => 10,
                            'object_id' => '',
                            'object_type' => 'maintenance',
                        ];
                        if (!empty($notification_data)) {
                            $this->db->insert('tblnotifications', $notification_data);
                        }
                    }
                    $arrayStaff[] = $value['staffid'];
                }
                pusher_trigger_notification($arrayStaff);
            }
        }
    }

    function CronPersonnelTimekeeping()
    {
        $date_end = date("Y-m-t");
        $date_now = date("Y-m-d");

        if ($date_end != $date_now) {
            die('123');
        }
        $data = [];
        $month = (date("m") + 1) > 12 ? 01 : (date("m") + 1);
        $month = $month < 10 ? '0' . $month : $month;
        $year = date("m") == 12 ? (date("Y") + 1) : date("Y");

        $listDate = getAllDateInMonth($month, $year, 'd/m');

        $countDate = 0;
        foreach ($listDate as $k => $value) {
            $countDate++;
            $day = date("d", strtotime($k));
            $format = 'D';
            $time = mktime(12, 0, 0, $month, $day, $year);
            $date_word = '';
            if (date('m', $time) == $month) {
                $date_word = date($format, $time);
            }
            $date_word = convertDate($date_word);
        }

        //timekeeping
        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        } else {
            $this->db->insert('tbl_timekeeping', [
                'month' => $month,
                'year' => $year,
                'count_date' => $countDate,
            ]);
            $timekeepingId = $this->db->insert_id();
        }
        //end timekeeping

        //check detail
        $this->db->select('*');
        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
        $details = $this->db->get()->row_array();
        $array_staff = [];
        if (!empty($details)) {

            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,tbl_timekeeping_detail_hour.type as type');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'left');
            $this->db->join(
                'tbl_timekeeping_detail_hour',
                'tbl_timekeeping_detail_hour.timekeeping_detail_id= tbl_timekeeping_detail.id',
                'left'
            );
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $this->db->group_by('tbl_timekeeping_detail_hour.type');
            $personnel = $this->db->get()->result_array();


            if (empty($staff)) {
                foreach ($personnel as $key => $value) {
                    $array_staff[] = $value['staffid'];
                }
                if (!empty($array_staff)) {
                    array_unique($array_staff);
                    $isTimes = "(
                        SELECT COUNT(*)
                        FROM tbl_timekeeping
                        LEFT JOIN tbl_timekeeping_detail on tbl_timekeeping_detail.timekeeping_id = tbl_timekeeping.id
                        WHERE tbl_timekeeping.month = '$month' AND tbl_timekeeping.year = '$year' AND tbl_timekeeping_detail.staff_id = tblstaff.staffid
                    )";

                    $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,0 as type');
                    $this->db->from('tblstaff');
                    $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
                    $this->db->where('active', 1);
                    $this->db->where_not_in('tblstaff.staffid', $array_staff);
                    $this->db->where("($isTimes = 0)");
                    $staffs = $this->db->get()->result_array();

                    if (!empty($staffs)) {
                        $personnel = array_merge($personnel, $staffs);
                    }
                }
            }
        } else {
            $this->db->select('tblstaff.staffid as staffid,tblstaff.code as code, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,tblroles.name as name_role,0 as type');
            $this->db->from('tblstaff');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->where('active', 1);
            $personnel = $this->db->get()->result_array();
        }

        $arrPersonnel = [];
        $staff_id = '';
        $i = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $personnel_id = $value['staffid'];
                foreach ($listDate as $k => $val) {
                    $date = $k;
                    $day = date("d", strtotime($k));
                    $hourIn = '';
                    $hourOut = '';
                    $textHour = '';
                    $imageIn = '';
                    $imageOut = '';
                    $date_word = '';
                    $image = '';


                    $format = 'D';
                    $time = mktime(12, 0, 0, $month, $day, $year);
                    if (date('m', $time) == $month) {
                        $date_word = date($format, $time);
                    }

                    $this->db->from('tbl_timekeeping_detail');
                    $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail.staff_id', $personnel_id);
                    $this->db->where('tbl_timekeeping_detail.date', $date);
                    $this->db->where('tbl_timekeeping_detail.day', $day);
                    $this->db->limit(1);
                    $isTimeKeepingDetail = $this->db->get()->row_array();

                    if (empty($isTimeKeepingDetail)) {
                        if ($date_word == "Sun") {
                            $check_sun = 1;
                        } else {
                            $check_sun = '';
                        }
                        $type = 'X';
                        $arrPersonnel[] = [
                            'timekeeping_id' => $timekeepingId,
                            'staff_id' => $personnel_id,
                            'date' => $date,
                            'day' => $day,
                            'type' => $type,
                            'date_word' => $date_word,
                            'check_sun' => $check_sun
                        ];
                    }
                }
            }
        }

        if (!empty($arrPersonnel)) {
            $this->db->insert_batch('tbl_timekeeping_detail', $arrPersonnel);
        }

        $arrPersonnelDetailHour = [];
        if (!empty($arrPersonnel)) {
            $this->db->select('tbl_timekeeping_detail.*');
            $this->db->from('tbl_timekeeping_detail');
            $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
            $personnelDetails = $this->db->get()->result_array();
            if (!empty($personnelDetails)) {
                foreach ($personnelDetails as $key => $value) {
                    $this->db->from('tbl_timekeeping_detail_hour');
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $value['id']);
                    $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                    $hourIns = $this->db->get()->num_rows();

                    $this->db->from('tbl_timekeeping_detail_hour');
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_id', $timekeepingId);
                    $this->db->where('tbl_timekeeping_detail_hour.timekeeping_detail_id', $value['id']);
                    $this->db->where('tbl_timekeeping_detail_hour.type', 2);
                    $hourOuts = $this->db->get()->num_rows();

                    if (empty($hourIns) || empty($hourOuts)) {
                        if (empty($hourIns)) {
                            $arrPersonnelDetailHour[] = [
                                'timekeeping_id' => $timekeepingId,
                                'timekeeping_detail_id' => $value['id'],
                                'type' => 1,
                            ];
                        }
                        if (empty($hourOuts)) {
                            $arrPersonnelDetailHour[] = [
                                'timekeeping_id' => $timekeepingId,
                                'timekeeping_detail_id' => $value['id'],
                                'type' => 2,
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($arrPersonnelDetailHour)) {
            $this->db->insert_batch('tbl_timekeeping_detail_hour', $arrPersonnelDetailHour);
        }
    }

    function createAutoAdvance()
    {
        $month = date('m');
        $year = date('Y');
        $day = date('d');
        $date_auto = get_option('date_auto_advance');

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        if (!empty($timekeepingId)) {
            if ($day == $date_auto) {
                $countHourBhxh = "(
                    SELECT 
                        tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                        tbl_timekeeping_detail.staff_id as staff_id,
                        COALESCE(COUNT(id),0) as count
                    FROM tbl_timekeeping_detail
                    WHERE (count_hour - count_hour_overtime) >= 4 AND check_sun = 0 AND timekeeping_id = $timekeepingId
                    GROUP BY timekeeping_id,staff_id
                ) tb_count_hour_bhxh";

                $this->db->select('
                    tbl_timekeeping_detail.staff_id,
                    COALESCE(tb_count_hour_bhxh.count,0) as total_date,
                    tblstaff.total_advance
                ');
                $this->db->from('tblstaff');
                $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'inner');
                $this->db->join(
                    'tbl_timekeeping',
                    'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id',
                    'inner'
                );
                $this->db->join(
                    "$countHourBhxh",
                    'tb_count_hour_bhxh.timekeeping_id = tbl_timekeeping.id AND tb_count_hour_bhxh.staff_id = tblstaff.staffid',
                    'left'
                );
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $this->db->where('tbl_timekeeping.id', $timekeepingId);
                $this->db->where('COALESCE(tb_count_hour_bhxh.count,0) > ', 18);
                $this->db->group_by('tbl_timekeeping_detail.staff_id');
                $dtTimedetail = $this->db->get()->result_array();
                $options = [];
                if (!empty($dtTimedetail)) {
                    foreach ($dtTimedetail as $key => $value) {
                        $code = get_option('prefix_payroll_payment') . sprintf(
                            '%06d',
                            ch_getMaxID('id', 'tbl_payroll_payment') + 1
                        );
                        $date = date('Y-m-d');
                        $staff_id = $value['staff_id'];
                        $amount = $value['total_advance'];
                        $note = 'Tạm ứng lương tháng ' . $month . ' năm ' . $year;

                        $this->db->from('tbl_payroll_payment');
                        $this->db->where('month(date)', $month);
                        $this->db->where('year(date)', $year);
                        $this->db->where('staff_id', $staff_id);
                        $checkExists = $this->db->count_all_results();
                        if (!empty($checkExists)) {
                            continue;
                        }

                        $options[] = [
                            'code' => $code,
                            'date' => $date,
                            'staff_id' => $staff_id,
                            'amount' => $amount,
                            'note' => $note,
                            'date_created' => date('Y-m-d H:i'),
                            'created_by' => 1,
                        ];
                    }
                }
                $count = 0;
                if (!empty($options)) {
                    foreach ($options as $key => $value) {
                        $this->db->insert('tbl_payroll_payment', $value);
                        $id = $this->db->insert_id();
                        if ($id) {
                            $count++;
                        }
                    }
                }
                echo $count;
            }
        }
    }

    function insertBranchClients()
    {
        $client = get_table_where('tblclients', []);
        $option = [];
        if (!empty($client)) {
            foreach ($client as $key => $value) {
                $option[] = [
                    'branch_id' => 5,
                    'client_id' => $value['userid'],
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y:m:d H:i:s')
                ];
            }
        }
        if (!empty($option)) {
            $this->db->insert_batch('tbl_client_branch', $option);
        }
    }

    function insertBranchSuppliers()
    {
        $suppliers = get_table_where('tblsuppliers', []);
        $option = [];
        if (!empty($suppliers)) {
            foreach ($suppliers as $key => $value) {
                $option[] = [
                    'branch_id' => 5,
                    'suppliers_id' => $value['id']
                ];
            }
        }
        if (!empty($option)) {
            $this->db->insert_batch('tbl_suppliers_branch', $option);
        }
    }

    function sendListStaffNotCheckIn()
    {
        $date = date('Y-m-d');
        $this->db->select('roleid,name');
        $this->db->from('tblroles');
        //        $this->db->where('roleid',19);
        $this->db->where('roles_parent ', 0);
        $dtRole = $this->db->get()->result_array();
        if (!empty($dtRole)) {
            foreach ($dtRole as $key => $value) {
                $arrRole = [-1];
                get_childs_id_role($value['roleid'], $arrRole);
                $this->db->select('tbl_timekeeping_detail.staff_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join(
                    'tbl_timekeeping_detail_hour',
                    'tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id'
                );
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_timekeeping_detail.staff_id');
                $this->db->where_in('tblstaff.role', $arrRole);
                $this->db->where('tbl_timekeeping_detail_hour.type', 1);
                $this->db->where('tbl_timekeeping_detail.date', $date);
                $this->db->where('(tbl_timekeeping_detail_hour.hour IS NULL OR tbl_timekeeping_detail_hour.hour = "")');
                $dtStaff = $this->db->get()->result_array();
                $dtRole[$key]['arrStaff'] = $dtStaff;

                $this->db->select('staffid,email');
                $this->db->from('tblstaff');
                $this->db->where('tblstaff.role', $value['roleid']);
                $this->db->order_by('email desc');
                $dtStaffEmail = $this->db->get()->result_array();
                $dtRole[$key]['dtStaffEmail'] = $dtStaffEmail;
            }
        }
        $count = 0;
        if (!empty($dtRole)) {
            foreach ($dtRole as $key => $value) {
                if (empty($value['dtStaffEmail']) || empty($value['arrStaff'])) {
                    continue;
                }
                $list_staff = $value['dtStaffEmail'][0]['email'];

                $html = '';
                foreach ($value['arrStaff'] as $kk => $vv) {
                    $html .= '<div>' . $vv['name_staff'] . '</div>';
                }
                $systemBCC = '';
                foreach ($value['dtStaffEmail'] as $kk => $vv) {
                    if ($kk == 0) {
                        continue;
                    }
                    $systemBCC .= $vv['email'] . ' ,';
                }

                $systemBCC = trim($systemBCC, ' ,');

                $this->load->config('email');
                $template = new StdClass();
                $template->message = get_option('email_header') . '<br/> Danh sách nhân viên trong nhóm của bạn chưa chấm công vào ngày ' . _dhau(date('Y-m-d')) . '<br/>
				 <b>Nhân viên :</b><br/>
				 ' . $html . '';
                $template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
                $template->subject = 'DANH SÁCH NHÂN VIÊN CHƯA CHẤM CÔNG';
                $this->email->initialize();
                if (get_option('mail_engine') == 'phpmailer') {
                    $this->email->set_debug_output(function ($err) {
                        return false;
                    });
                }
                $this->email->set_newline(config_item('newline'));
                $this->email->set_crlf(config_item('crlf'));
                $this->email->from(get_option('smtp_email'), $template->fromname);
                $this->email->to($list_staff);
                if ($systemBCC != '') {
                    $this->email->bcc($systemBCC);
                }
                $this->email->subject($template->subject);
                $this->email->message($template->message);
                if ($this->email->send(true)) {
                    $count++;
                }
            }
        }
        echo $count;
    }

    public function updateShowRole()
    {
        $this->db->select('tbl_recommended_list.*');
        $this->db->from('tbl_recommended_list');
        $this->db->where('tbl_recommended_list.type_show', 1);
        $this->db->where('tbl_recommended_list.parent_id', 0);
        $dtData = $this->db->get()->result_array();
        if (!empty($dtData)) {
            foreach ($dtData as $key => $value) {
                $this->db->select('tbl_recommended_list.*');
                $this->db->from('tbl_recommended_list');
                $this->db->where('tbl_recommended_list.parent_id', $value['id']);
                $dtData1 = $this->db->get()->result_array();
                if (!empty($dtData1)) {
                    foreach ($dtData1 as $kk => $vv) {
                        $this->db->where('tbl_recommended_list.id', $vv['id']);
                        $this->db->update('tbl_recommended_list', [
                            'type_show' => 1
                        ]);

                        $this->db->select('tbl_recommended_list.*');
                        $this->db->from('tbl_recommended_list');
                        $this->db->where('tbl_recommended_list.parent_id', $vv['id']);
                        $dtData2 = $this->db->get()->result_array();
                        if (!empty($dtData2)) {
                            foreach ($dtData2 as $kkk => $vvv) {
                                $this->db->where('tbl_recommended_list.id', $vvv['id']);
                                $this->db->update('tbl_recommended_list', [
                                    'type_show' => 1
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function phpinfo()
    {
        print_arrays(123);
        phpinfo();
    }
    function create_tasks_work_plan()
    {
        $this->load->model('work_plan_model');
        $this->db->select('tbl_work_plan_task.*');
        $this->db->where('tbl_work_plan_task.date_tasks !=', NULL);
        $this->db->where('tbl_work_plan_task.date_tasks <=', date('Y-m-d'));
        $work_plan_task = $this->db->get('tbl_work_plan_task')->result_array();

        foreach ($work_plan_task as $key => $value) {
            $work_plan_task_id = $value['id'];
            $taskRel = $this->work_plan_model->getTaskRel($work_plan_task_id);
            if (empty($taskRel)) { // Chưa có phiếu công việc
                $this->work_plan_model->createTask($work_plan_task_id);
            }
        }
    }
    function process_tasks_work_plan()
    {
        // $this->load->model('work_plan_model');
        $this->db->select('tbl_work_plan_items.*,tbltask_checklist_items.taskid,tbltasks.duedate,tbltask_checklist_items.date_finished,finished');
        // $this->db->select('tbl_work_plan_items.*');

        $this->db->where('tbl_work_plan_items.is_check', 0);
        $this->db->where('tbl_work_plan_items.process_id >', 0);
        $this->db->join('tbl_work_plan_task', 'tbl_work_plan_task.id = tbl_work_plan_items.work_plan_task_id', 'left');
        $this->db->join('tbltasks', 'tbltasks.rel_id = tbl_work_plan_task.id and tbltasks.rel_type = "work_plan_task"', 'left');
        $this->db->join('tbltask_checklist_items', 'tbltask_checklist_items.process_id = tbl_work_plan_items.process_id and tbltask_checklist_items.taskid = tbltasks.id', 'INNER');
        $this->db->group_by('tbl_work_plan_items.id');
        $work_plan_task = $this->db->get('tbl_work_plan_items')->result_array();

        foreach ($work_plan_task as $key => $value) {
            $color = '123';
            $pass_status = NULL;
            $check_pass_status = 0;
            if (!empty($value['finished'])) {
                $check_pass_status = 1;
                $pass_status = 1;
                $date_status = '';
                if (!empty($value['date_finished'])) {
                    $date_status = explode(' ', $value['date_finished'])[0] . ' 00:00:00';
                }
                if ($value['duedate'] . ' 23:59:59' < $date_status) {
                    $pass_status = 0;
                }
            } else {
                if (!empty($value['duedate'])) {
                    if ($value['duedate'] < date('Y-m-d 00:0:00')) {
                        $pass_status = 0;
                    }
                }
            }
            $ins = [];
            $ins['pass_status'] = $pass_status;
            $ins['is_check'] = $check_pass_status;
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_work_plan_items', $ins);
        }
    }

    function hold_trans_waiting_production()
    {
        $this->db->select('
            tbl_order_items.id as id,
            tbl_order_items.item_id as item_id,
            tbl_order_items.quantity as quantity,
        ', false);
        $this->db->from('tbl_order_items');
        $this->db->where('
            NOT EXISTS (
                SELECT 1
                FROM tbltransfer_warehouse_detail
                WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id
            )

            AND NOT EXISTS (
                SELECT 1
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.order_item_id = tbl_order_items.id
            )

            AND NOT EXISTS (
                SELECT 1
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                WHERE tbl_productions_orders_items.production_plan_item_id = tbl_order_items.id AND tbl_productions_orders_details.quantity_warehoused = 0
            )

            AND EXISTS (
                SELECT 1
                FROM tbl_orders
                WHERE tbl_orders.id = tbl_order_items.order_id AND tbl_orders.is_cancel = 0
            )
        ', false, false);
        // $this->db->where('tbl_order_items.quantity_productions_orders >', 0);
        $order_items = $this->db->get()->result_array();

        $arrInsert = [];
        if (!empty($order_items)) {
            $arrOrderItems = [];
            foreach ($order_items as $key => $value) {
                $arrOrderItems[] = $value['id'];
            }

            $tb_production_order_tranfer = "
                SELECT
                    tbl_tranfer_business_item.order_item_id as order_item_id,
                    SUM(tbl_tranfer_business_item.quantity) as quantity
                FROM tbl_tranfer_business_item
                WHERE tbl_tranfer_business_item.order_item_id IN (" . implode(',', $arrOrderItems) . ")
                GROUP BY tbl_tranfer_business_item.order_item_id
            ";
            $tranfer_business_item = $this->db->query($tb_production_order_tranfer)->result_array();
            if (!empty($tranfer_business_item)) {
                $tranfer_business_item = array_reduce($tranfer_business_item, function ($carry, $item) {
                    $carry[$item['order_item_id']] = $item;
                    return $carry;
                });
            }

            foreach ($order_items as $key => $value) {
                $order_item_id = $value['id'];
                $dtTransferBusiness = $tranfer_business_item[$order_item_id] ?? null;
                $quantity_transfer = $dtTransferBusiness['quantity'] ?? 0;

                if ($quantity_transfer > 0) {
                    $arrInsert[] = [
                        'id' => $value['id'],
                        'item_id' => $value['item_id'],
                        'quantity' => $quantity_transfer,
                    ];
                }
            }
        }

        $this->db->where('id >', 0);
        $this->db->delete('tbl_order_items_transfer');
        if (!empty($arrInsert)) {
            $this->db->insert_batch('tbl_order_items_transfer', $arrInsert);
        }
    }

    //Cập nhật lại kế hoạch nội bộ
    function update_total_business_plan()
    {
        totalBusinessPlan(null);
    }

    //Cập nhật số lượng đã giữ trên truyền
    function update_total_transfer_business()
    {
        totalTransferBusinessItem(null);
    }
    function getreports($type = 1, $limit = 500, $page = 1)
    {
        $start = ($page - 1) * $limit;
        $this->db->select(
            'tbl_products.id as product_id,
            tbl_products.name as name_products,
            tbl_products.code as code_products,
            CONCAT("product") as type_items,
            stock_unit.unit as unit_name_stock,
            tbl_category_products.name as name_cate_product'
        );
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tblunits stock_unit', 'stock_unit.unitid=tbl_products.conversion_unit', 'left');
        $this->db->limit($limit, $start);
        $this->db->ORDER_BY('tbl_products.id', 'ASC');
        $main = $this->db->get('tbl_products')->result_array();

        $beginMonth = '2025-02-01';
        $endMonth = '2025-04-18';
        $warehouse_id = '';
        foreach ($main as $key => $value) {
            $sumFExistsQall = getStartInventoryArray($value['product_id'], $value['type_items'], $warehouse_id, $beginMonth);
            $sumFExistsQall_price = getStartInventory_v2Array($value['product_id'], $value['type_items'], $warehouse_id, $beginMonth);
            $price_trongki = getStartInventory_trongkiArray($value['product_id'], $value['type_items'], $warehouse_id, $beginMonth, $endMonth);
            $sumFExistsQall_import = getStartInventory_importArray($value['product_id'], $value['type_items'], $warehouse_id, $beginMonth, $endMonth);
            $sumFExistsQall_export = getStartInventory_exportArray($value['product_id'], $value['type_items'], $warehouse_id, $beginMonth, $endMonth);
            $ins = [];
            $ins['danhmuc'] = $value['name_cate_product'];
            $ins['mahang'] = $value['code_products'];
            $ins['tenhang'] = $value['name_products'];
            $ins['donvitinh'] = $value['unit_name_stock'];
            $ins['tondau'] = $sumFExistsQall;
            $ins['giatridau'] = $sumFExistsQall_price;
            $ins['nhapkho'] = $sumFExistsQall_import;
            $ins['nhapkhogiatri'] = $price_trongki['exists_quantity_import'];
            $ins['xuatkho'] = $sumFExistsQall_export;
            $ins['xuatkhogiatri'] = $price_trongki['exists_quantity_export'];
            $ins['toncuoiki'] = ($sumFExistsQall + $sumFExistsQall_import - $sumFExistsQall_export);
            $ins['giatricuoi'] = $sumFExistsQall_price + $price_trongki['exists_quantity_import'] - $price_trongki['exists_quantity_export'];
            $ins['id_items'] = $value['product_id'];
            $ins['type'] = $type;
            $check = get_table_where('tbl_report_warehouse', array('id_items' => $value['product_id'], 'type' => $type), 'id DESC', 'row');
            if (empty($check)) {
                $this->db->insert('tbl_report_warehouse', $ins);
            }
        }
        echo '<pre>';
        print_arrays($main);
        die;
    }
    function exportreport()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $style_excel['Background_header_one'] = $style_excel['Background_header'];
        $style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';




        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(20);
        // Danh Mục Mã Mặt Hàng Mã Mặt Hàng Đơn Vị Tính Tồn đầu Kỳ Số lượng Tồn đầu Kỳ Giá trị  Nhập Kho Số lượng   Nhập Kho Giá trị    Xuất Kho Số lượng   Xuất Kho Giá trị    Tồn Cuối Kỳ Số lượng    Tồn Cuối Kỳ Giá trị

        $numberRow = 1;
        $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Danh Mục')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Mã Mặt Hàng')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Mặt Hàng')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Đơn Vị Tính')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Tồn đầu Kỳ Số lượng')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Tồn đầu Kỳ Giá trị')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Nhập Kho Số lượng')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'Nhập Kho Giá trị')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'Xuất Kho Số lượng')->getStyle("I$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", 'Xuất Kho Giá trị')->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", 'Tồn Cuối Kỳ Số lượng')->getStyle("K$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $objPHPExcel->getActiveSheet()->SetCellValue("L$numberRow", 'Tồn Cuối Kỳ Giá trị')->getStyle("L$numberRow")->applyFromArray($style_excel['Background_header_one']);
        $numberRow++;

        $data_warehouse = $this->db->get('tbl_report_warehouse')->result_array();
        if (!empty($data_warehouse)) {
            foreach ($data_warehouse as $key => $value) {
                $i = 0;
                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['danhmuc'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['mahang'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['tenhang'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['donvitinh'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['tondau'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['giatridau'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['nhapkho'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['nhapkhogiatri'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['xuatkho'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['xuatkhogiatri'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['toncuoiki'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['giatricuoi'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $i++;

                $numberRow++;
            }
        }


        $filename = lang('NHAP_XUAT_TON') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //    function deletetasks() {
    //        $this->db->where('DATE_FORMAT(dateadded, "%Y-%m-%d") = "2025-11-12"', false, false);
    //        $this->db->where('DATE_FORMAT(date_request_recurring, "%Y-%m-%d") = "2025-11-12"', false, false);
    //        $this->db->where('(SELECT COUNT(t2.id) FROM tbltasks t2 WHERE t2.is_recurring_from = tbltasks.is_recurring_from) > 1', false, false);
    //        $this->db->order_by('tbltasks.id', 'desc');
    //        $tasks = $this->db->get('tbltasks')->result_array();
    //        $inDelete = [];
    //        foreach($tasks as $key => $value) {
    //            $inDelete[$value['is_recurring_from']][$value['startdate']][] = $value['id'];
    //        }
    //        print_arrays($inDelete);
    //    }

    public function updateStatusExportPO($limit = 5000, $id_last = 0, $id = 0)
    {
        $this->load->model('manufactures_model');
        $this->db->select('id', false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('id >', $id_last);
        if ($id) {
            $this->db->where('id', $id);
        }
        if ($limit) {
            $this->db->limit($limit);
        }
        $productions_orders = $this->db->get()->result_array();
        //Lấy id cuối cùng
        $id_last = end($productions_orders)['id'] ?? 0;
        foreach ($productions_orders as $key => $value) {
            $this->manufactures_model->checkPrepareMaterialsTotal(['po_id' => $value['id']]);
        }

        echo 'Last ID: ' . $id_last;
    }

    public function updateRoleLevelRole()
    {

        $this->db->from('tblroles');
        $this->db->where('tblroles.active_role', 1);
        $this->db->where('tblroles.type', 0);
        $dtRole = $this->db->get()->result_array();
        $arrInsert = [];
        if (!empty($dtRole)) {
            foreach ($dtRole as $key => $value) {
                $arrInsert[] = [
                    'role_id' => $value['roleid'],
                    'role_level_id' => 1
                ];
                $arrInsert[] = [
                    'role_id' => $value['roleid'],
                    'role_level_id' => 2
                ];
                $arrInsert[] = [
                    'role_id' => $value['roleid'],
                    'role_level_id' => 4
                ];
                $arrInsert[] = [
                    'role_id' => $value['roleid'],
                    'role_level_id' => 5
                ];
            }
        }
        $this->db->insert_batch('tbl_role_role_level', $arrInsert);
    }

    public function cronEvaluateTV()
    {
        return;
        $day_evaluate = 14;

        $this->db->from('tblstaff');
        $this->db->where('tblstaff.status_work', 0);
        $this->db->where('day_in IS NOT NULL', null, false);

        $this->db->where("
            NOT EXISTS (
                SELECT 1
                FROM tbl_probationary_assessment
                WHERE tbl_probationary_assessment.staff_id = tblstaff.staffid
            )
        ", null, false);

        $this->db->where("DATE_ADD(day_in, INTERVAL $day_evaluate DAY) < CURDATE()", null, false);

        $dtStaff = $this->db->get()->result_array();

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $date_start = $value['day_in'];
                $date_end = date(
                    'Y-m-d',
                    strtotime("+$day_evaluate days", strtotime($value['day_in']))
                );
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => getReference('probationary_assessment'),
                    'staff_id' => $value['staffid'],
                    'role_id' => $value['role'] ?? 0,
                    'date_start' => ($date_start),
                    'date_end' => ($date_end),
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];

                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if (!empty($insert_id)) {
                    updateReference('probationary_assessment');
                    $count++;
                }
            }
        }
        echo $count;
    }
    // function deleteEvaluateCT()
    // {
    //     // Lấy danh sách staff có check_salary = 1
    //     $this->db->select('staffid');
    //     $this->db->from('tblstaff');
    //     $this->db->where('tblstaff.check_salary', 1);
    //     $dtStaff = $this->db->get()->result_array();

    //     if (empty($dtStaff)) {
    //         echo '0';
    //         return;
    //     }

    //     $staffIds = array_column($dtStaff, 'staffid');

    //     // Xóa các phiếu đánh giá CT (type = 2) được tạo nhầm cho staff check_salary = 1
    //     $this->db->where_in('staff_id', $staffIds);
    //     $this->db->where('type', 2);
    //     $this->db->delete('tbl_probationary_assessment');

    //     echo $this->db->affected_rows();
    // }
    public function cronEvaluateCT()
    {
        return;

        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $this->db->where('tblstaff.check_salary', 0);
        $this->db->where('tblstaff.status_work', 1);
        $this->db->where('tblroles.day_evaluate != 0', null, false);

        $this->db->where("
            DATE_ADD(
                IFNULL(
                    (
                        SELECT DATE(MAX(e.date))
                        FROM tbl_probationary_assessment e
                        WHERE e.staff_id = tblstaff.staffid
                        AND e.type = 2
                    ),
                    '2026-02-01'
                ),
                INTERVAL tblroles.day_evaluate DAY
            ) < CURDATE()
        ", null, false);

        $dtStaff = $this->db->get()->result_array();

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $staffid = $value['staffid'];
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => date('Y-m-d'),
                    'date_end' => date('Y-m-d'),
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }

    public function cronEvaluateCTOld()
    {

        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');

        $this->db->where('tblstaff.status_work', 1);
        $this->db->where('tblroles.day_evaluate != 0', null, false);

        // $this->db->where("
        //     NOT EXISTS (
        //         SELECT 1
        //         FROM tbl_evaluation_employee
        //         WHERE tbl_evaluation_employee.staff_id = tblstaff.staffid
        //         AND tbl_evaluation_employee.type = 1
        //         AND tbl_evaluation_employee.rating_list = 0
        //     )
        // ", null, false);
        $this->db->where("
            DATE_ADD(
                IFNULL(
                    (
                        SELECT DATE(MAX(e.date))
                        FROM tbl_evaluation_employee e
                        WHERE e.staff_id = tblstaff.staffid
                        AND e.type = 1
                    ),
                    '2026-02-01'
                ),
                INTERVAL tblroles.day_evaluate DAY
            ) < CURDATE()
        ", null, false);

        $dtStaff = $this->db->get()->result_array();

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $this->db->from('tbl_question_bank');
                $this->db->where('role_id', $value['role'] ?? 0);
                $this->db->where('role_level_id', $value['role_level_id'] ?? 0);
                $dtDataQuestion = $this->db->get()->result_array();
                if (empty($dtDataQuestion)) {
                    continue;
                }
                foreach ($dtDataQuestion as $k => $v) {
                    $arrAnswer = [];
                    $this->db->from('tbl_question_bank_answer');
                    $this->db->where('tbl_question_bank_answer.question_bank_id', $v['id']);
                    $dtAnswer = $this->db->get()->result_array();
                    if (!empty($dtAnswer)) {
                        foreach ($dtAnswer as $kk => $vv) {
                            $arrAnswer[] = [
                                'prefix' => $vv['prefix'],
                                'answer' => $vv['answer'],
                                'point' => $vv['point'],
                            ];
                        }
                    }
                    $arrItems[] = [
                        'question_bank_id' => $v['id'],
                        'weight' => $v['weight'],
                        'items' => $arrAnswer
                    ];
                }
                $code = 'DG-' . sprintf('%06d', ch_getMaxID('id', 'tbl_evaluation_employee') + 1);
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'type' => 1,
                    'staff_id' => $value['staffid'],
                    'role_id' => $value['role'],
                    'role_level_id' => $value['role_level_id'],
                    'note' => null,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                $this->db->insert('tbl_evaluation_employee', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    foreach ($arrItems as $k => $v) {
                        $items = $v['items'];
                        unset($v['items']);
                        $v['evaluation_employee_id'] = $insert_id;
                        $this->db->insert('tbl_evaluation_employee_question', $v);
                        $insert_id_item = $this->db->insert_id();
                        if ($insert_id_item) {
                            if (!empty($items)) {
                                foreach ($items as $kk => $vv) {
                                    $vv['evaluation_employee_id'] = $insert_id;
                                    $vv['evaluation_employee_question_id'] = $insert_id_item;
                                    $this->db->insert('tbl_evaluation_employee_question_answer', $vv);
                                }
                            }
                        }
                    }
                    $count++;
                }
            }
        }
        echo $count;
    }
    public function cronEvaluateCTOneDay()
    {
        if (date('Y-m-d') != '2026-03-10') {
            return;
        }
        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');

        $this->db->where('tblstaff.status_work', 1);
        // $this->db->where('tblroles.day_evaluate != 0', null, false);

        // $this->db->where("
        //     DATE_ADD(
        //         IFNULL(
        //             (
        //                 SELECT DATE(MAX(e.date))
        //                 FROM tbl_probationary_assessment e
        //                 WHERE e.staff_id = tblstaff.staffid
        //                 AND e.type = 2
        //             ),
        //             '2026-02-01'
        //         ),
        //         INTERVAL tblroles.day_evaluate DAY
        //     ) < CURDATE()
        // ", null, false);

        $dtStaff = $this->db->get()->result_array();
        // echo '<pre>';
        // print_arrays($dtStaff);
        // die;
        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $staffid = $value['staffid'];
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => date('Y-m-d'),
                    'date_end' => date('Y-m-d'),
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }
    public function cronEvaluateCTTuan()
    {
        $today = date('Y-m-d');
        if (date('N', strtotime($today)) != 1) {
            return;
        }
        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $this->db->where('tblstaff.check_salary', 0);
        $this->db->where_in('tblstaff.status_work', [0, 1]);
        $this->db->where('tblroles.day_evaluate != 0', null, false);
        $dtStaff = $this->db->get()->result_array();

        $date_end = $today;
        $type_ki = 1;
        $day = date('j', strtotime($date_end));
        $week = ceil($day / 7);
        $check_day = $week * 7;
        $date_start = date('Y-m-d', strtotime($today . ' -' . $check_day . ' days'));
        $first_day_of_month = date('Y-m-01', strtotime($today));
        if ($date_start < $first_day_of_month) {
            $date_start = $first_day_of_month;
        }
        $ki = 0;

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $staffid = $value['staffid'];
                $this->db->where('staff_id', $staffid);
                $this->db->where('date_end', $date_end);
                $this->db->where('type_ki', 1);
                $check_data = $this->db->get('tbl_probationary_assessment')->row_array();
                if (!empty($check_data)) {
                    continue;
                }
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'ki' => $week,
                    'type_ki' => $type_ki,
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }
    public function cronEvaluateCTThang()
    {
        $today = date('Y-m-d');
        $md = date('m-d', strtotime($today));
        if (!in_array($md, ['03-31', '06-30', '09-30', '12-31'])) {
            return;
        }
        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $this->db->where('tblstaff.check_salary', 0);
        $this->db->where_in('tblstaff.status_work', [0, 1]);
        $this->db->where('tblroles.day_evaluate != 0', null, false);
        $dtStaff = $this->db->get()->result_array();
        $type_ki = 2;
        $month = date('m');
        $date_end = date('Y-m-d'); // Khai báo date_end còn thiếu
        $date_start = date('Y-m-d', strtotime(date('Y-m-d') . ' -' . $month . ' month'));
        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];

                // Kiểm tra nếu đã tồn tại phiếu cùng ngày kết thúc
                $this->db->where('staff_id', $staffid);
                $this->db->where('date_end', $date_end);
                $this->db->where('type_ki', 2);
                $check_data = $this->db->get('tbl_probationary_assessment')->row_array();
                if (!empty($check_data)) {
                    continue;
                }

                $arrItems = [];
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'ki' => $month,
                    'type_ki' => $type_ki,
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }
    public function script_sync_all_assessments()
    {
        // Lấy tất cả các phiếu
        $this->db->where('run', 0);
        $assessments = $this->db->get('tbl_probationary_assessment')->result_array();

        // Lấy danh mục checklist
        $this->db->from('tbl_checklist_probationary_assessment');
        $checkList = $this->db->get()->result_array();

        // Lấy danh mục xếp loại
        $this->db->from('tbl_result_checklist');
        $ratings = $this->db->get()->result_array();

        $count = 0;
        foreach ($assessments as $pa) {
            $id = $pa['id'];
            $staff_id = $pa['staff_id'];

            // Dùng date_start/date_end lưu trong phiếu
            $start = $pa['date_start'];
            $end = $pa['date_end'];

            // 1. Task Stats
            $this->db->select("COUNT(t.id) as total, SUM(CASE WHEN t.status = 5 THEN 1 ELSE 0 END) as done");
            $this->db->from('tbltasks t');
            $this->db->join('tbltask_assigned ta', 'ta.taskid = t.id');
            $this->db->where('ta.staffid', $staff_id);
            $this->db->where('t.startdate >=', $start . ' 00:00:00');
            $this->db->where('t.startdate <=', $end . ' 23:59:59');
            $task_stats = $this->db->get()->row_array();



            $task_percent = !empty($task_stats['total']) ? round(($task_stats['done'] / $task_stats['total']) * 100, 1) : 0;

            // 2. Production Stats
            $this->db->select("SUM(CASE WHEN type_report = 1 THEN 1 ELSE 0 END) as count_kph, SUM(CASE WHEN type_report = 4 THEN 1 ELSE 0 END) as count_vp, SUM(CASE WHEN type_report = 2 THEN 1 ELSE 0 END) as count_lap_lai");
            $this->db->from('tblproduction_report');
            $this->db->where('staff_responsible', $staff_id);
            $this->db->where('date >=', $start . ' 00:00:00');
            $this->db->where('date <=', $end . ' 23:59:59');
            $prod_stats = $this->db->get()->row_array();
            $count_kph = (int)($prod_stats['count_kph'] ?? 0);
            $count_vp = (int)($prod_stats['count_vp'] ?? 0);
            $count_lap_lai = (int)($prod_stats['count_lap_lai'] ?? 0);

            $task_total = (int)($task_stats['total'] ?? 0);
            $qa_percent = 100;
            if ($task_total > 0) {
                $qa_dat = max(0, $task_total - $count_kph);
                $qa_percent = round(($qa_dat / $task_total) * 100, 1);
            }

            // Fetch existing items
            $existing_items = $this->db->get_where('tbl_probationary_assessment_item', ['probationary_assessment_id' => $id])->result_array();
            $items_by_checklist = [];
            foreach ($existing_items as $ei) {
                $items_by_checklist[$ei['check_list_id']] = $ei;
            }

            $point_b = 0;
            $point_c = 0;
            $point_d = 0;
            $hasGateFail = false;

            foreach ($checkList as $cl) {
                $cid = $cl['id'];
                $type = $cl['type'];
                $name = mb_strtolower($cl['name'], 'UTF-8');
                $max_point = (float)$cl['point'];
                $condition = trim($cl['conditions']);

                $is_new = false;
                if (isset($items_by_checklist[$cid])) {
                    $item = $items_by_checklist[$cid];
                } else {
                    $is_new = true;
                    $item = [
                        'probationary_assessment_id' => $id,
                        'check_list_id' => $cid,
                        'type_check_list' => $type,
                        'gate' => ($type == 'A') ? 1 : 0, // Mặc định pass gate
                        'percent' => 0,
                        'point' => 0,
                        'note' => ''
                    ];
                }

                if ($type == 'D') {
                    $item['point'] = $max_point;
                    $point_d += $item['point'];
                } elseif ($type == 'B' || $type == 'C') {
                    // 1. Xác định giá trị thực tế
                    if (strpos($name, 'hoàn thành công việc') !== false) {
                        $item['percent'] = $task_percent;
                    } elseif (strpos($name, 'chất lượng') !== false || strpos($name, 'qa') !== false) {
                        $item['percent'] = $qa_percent;
                    } elseif (strpos($name, 'lặp lại') !== false) {
                        $item['percent'] = $count_lap_lai;
                    } elseif (strpos($name, 'kỷ luật') !== false || strpos($name, 'vi phạm') !== false) {
                        $item['percent'] = $count_vp;
                    } elseif (strpos($name, 'tuân thủ nội quy') !== false) {
                        $item['percent'] = 100;
                    } elseif (strpos($name, 'tuân thủ sop') !== false || strpos($name, 'hồ sơ') !== false || strpos($name, 'báo cáo') !== false) {
                        $item['percent'] = 0; // Thực tế 0 → full điểm
                    } else {
                        $item['percent'] = 0;
                    }

                    // 2. Tính điểm dựa trên thực tế và chuẩn
                    if (strpos($name, 'chất lượng') !== false || strpos($name, 'qa') !== false || strpos($name, 'hoàn thành công việc') !== false || strpos($name, 'tuân thủ nội quy') !== false) {
                        $item['point'] = round(($item['percent'] * $max_point / 100), 1);
                    } elseif (strpos($name, 'tuân thủ sop') !== false || strpos($name, 'hồ sơ') !== false || strpos($name, 'báo cáo') !== false) {
                        // Tuân thủ SOP, Hồ sơ & báo cáo đầy đủ: thực tế = 0 → full điểm
                        if ($item['percent'] == 0) {
                            $item['point'] = $max_point;
                        } else {
                            $standard_num = (int)$condition;
                            $item['point'] = ($item['percent'] <= $standard_num) ? $max_point : 0;
                        }
                    } else {
                        // Kỷ luật, vi phạm, lặp lại... (các lỗi đếm được)
                        $standard_num = (int)$condition;
                        if ($item['percent'] <= $standard_num) {
                            $item['point'] = $max_point;
                        } else {
                            $item['point'] = 0;
                        }
                    }

                    if ($item['point'] > $max_point) $item['point'] = $max_point;
                    if ($item['point'] < 0) $item['point'] = 0;

                    if ($type == 'B') $point_b += $item['point'];
                    if ($type == 'C') $point_c += $item['point'];
                }

                if ($type == 'A' && $item['gate'] == '0') {
                    $hasGateFail = true;
                }

                if ($is_new) {
                    $this->db->insert('tbl_probationary_assessment_item', $item);
                } else {
                    $this->db->where('id', $item['id'])->update('tbl_probationary_assessment_item', [
                        'percent' => $item['percent'],
                        'point' => $item['point']
                    ]);
                }
            }

            $total_point = $point_b + $point_c + $point_d;

            $rating_list = 0;
            $rating_name = 'Chưa xếp loại';
            if ($hasGateFail) {
                foreach ($ratings as $rt) {
                    if (stripos($rt['name'], 'chấm dứt') !== false || $rt['id'] == 1) {
                        $rating_list = $rt['id'];
                        $rating_name = "KHÔNG ĐẠT (VI PHẠM GATE)";
                        break;
                    }
                }
            } else {
                foreach ($ratings as $rt) {
                    if ($total_point >= $rt['point_start'] && $total_point <= $rt['point_end']) {
                        $rating_list = $rt['id'];
                        $rating_name = mb_strtoupper($rt['name'], 'UTF-8');
                        break;
                    }
                }
            }
            $this->db->where('id', $id)->update('tbl_probationary_assessment', [
                'point_b' => $point_b,
                'point_c' => $point_c,
                'point_d' => $point_d,
                'point' => $total_point,
                'rating_list' => $rating_list,
                'rating' => $rating_name,
                'run' => 1,
            ]);

            $count++;
        }

        echo "Đã đồng bộ thành công $count phiếu đánh giá!";
    }
}
