<?php
defined('BASEPATH') or exit('No direct script access allowed');
$hasPermissionDelete = has_permission('debt_suppliers', '', 'delete');
$this->ci->db->query("SET sql_mode = ''");
$server_pay = '
        COALESCE(
        (
            SELECT SUM(tbl_services.payment) 
            FROM tbl_services 
            WHERE tbl_services.suppliers_id = tblsuppliers.id
        ),0)
    ';
$return_suppliers = '
    SUM(
        COALESCE(
            (
                SELECT SUM(tblreturn_suppliers.total)
                FROM tblreturn_suppliers 
                WHERE tblreturn_suppliers.suppliers_id = tblsuppliers.id AND  tblreturn_suppliers.treatment_methods = 2 and tblreturn_suppliers.treatment_methods = 2),0)
            )
      ';
$server = '
        COALESCE(
        (
            SELECT SUM(tbl_services.subtotal) 
            FROM tbl_services 
            WHERE tbl_services.suppliers_id = tblsuppliers.id and tbl_services.status = 1
            GROUP by tbl_services.suppliers_id
        ),0)
    ';
$aColumns = [
	'tblsuppliers.id as id',
	'tblsuppliers.company as company',
	'( ' . $server . ' + 
		debt_begin + COALESCE(SUM(tblimport.total),0) + 
		COALESCE(
			(
				select sum(tbl_outsource.grand_total) 
				from tbl_outsource 
				where tbl_outsource.supplier_id=tblsuppliers.id 
				AND tbl_outsource.status_pay < 2 
			),0) - '.$return_suppliers.'

	) as total_import',
	'(
		COALESCE(SUM(tblpurchase_order.amount_paid),0) + 
		COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + 
		COALESCE( ' . $server_pay . ' + 
			SUM(tblpurchase_invoice.amount_paid),0) + 
			COALESCE(
				(
					select sum(tbl_outsource.amount_paid) 
					from tbl_outsource where tbl_outsource.supplier_id = tblsuppliers.id AND tbl_outsource.status_pay < 2 
				),0)
	)  as amount_paid_import',
	// '( ' . $server_pay . ' + SUM(tblpurchase_order.price_other_expenses)) as price_other_expenses_import',
	'4',
	'5',
	'6',
	'7',
];
$sIndexColumn = 'id';
$sTable = 'tblsuppliers';
$where = [];
$having = 'HAVING (total_import - amount_paid_import) > 0';
array_push($where, 'AND ((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))');
$filter = [];
$join = [
	'LEFT JOIN tblpurchase_order ON tblpurchase_order.suppliers_id=tblsuppliers.id',
	'LEFT JOIN tblimport ON tblimport.id_order=tblpurchase_order.id',
	'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice',
	// 'LEFT JOIN tbl_outsource ON tbl_outsource.supplier_id=tblsuppliers.id',
];
$suppliers_id = $this->ci->input->post('suppliers_id');
array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
if (!empty($suppliers_id)) {
	array_push($where, 'AND tblsuppliers.id IN(' . trim($suppliers_id, ',') . ')');
}
array_push($where, 'AND tblsuppliers.type = 0');
$filterStatus = $this->ci->input->post('filterStatus');
if ($filterStatus == 1) {
	array_push(
		$where,
		'AND 
		tblsuppliers.debt_limit > 0 
		AND tblsuppliers.debt_limit < (
			(
				select(SUM(tblimport.total)) 
				from tblimport 
				where tblimport.suppliers_id=tblsuppliers.id 
			) 
			- 
			( select(
				(
					COALESCE(SUM(tblpurchase_order.amount_paid),0) + 
					COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + 
					COALESCE(SUM(tblpurchase_invoice.amount_paid),0)
				)
			) 
			from tblpurchase_order 
			left JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblpurchase_order.red_invoice 
			where (
					(
						tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) 
					or (
						tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0
					)
				) AND tblpurchase_order.suppliers_id=tblsuppliers.id)
			)'
	);
} else if ($filterStatus == 2) {
	$where[] = 'AND tblpurchase_order.id IN (
					SELECT tblpurchase_order.id 
					FROM tblpurchase_order 
					WHERE tblpurchase_order.amount_paid < tblimport.total
					AND tblpurchase_order.suppliers_id = tblsuppliers.id
					AND DATEDIFF(CURDATE(), tblpurchase_order.date) > tblsuppliers.time_payment
					AND tblsuppliers.time_payment <> 0
				)';
}
$group_by = 'GROUP BY tblsuppliers.id';
$result = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'tblsuppliers.type','( ' . $server_pay . ' + SUM(tblpurchase_order.price_other_expenses)) as price_other_expenses_import'
], '', $group_by, $having);
$output = $result['output'];
$rResult = $result['rResult'];
$aColumns_order = [
	'tblsuppliers.id',
	'tblsuppliers.company',
	'SUM(tbl_orders.cost_delivery) as total_import',
	'SUM(tbl_orders.price_other_expenses_delivery) as amount_paid_import',
	// 'SUM(tbl_orders.price_other_expenses_delivery) as price_other_expenses_import',
	'4',
	'5',
	'6',
	'7',
];
$sIndexColumn_order = 'id';
$sTable_order = 'tblsuppliers';
$where_order = [];
$having_order = 'HAVING (total_import - price_other_expenses_import) > 0';
$filter_order = [];
$join_order = [
	'LEFT JOIN tbl_orders ON tbl_orders.transporter_id=tblsuppliers.id',
];
array_push($where_order, 'AND tbl_orders.status = "approved"');
if (!empty($suppliers_id)) {
	array_push($where_order, 'AND tblsuppliers.id IN(' . trim($suppliers_id, ',') . ')');
}
array_push($where_order, 'AND tblsuppliers.type = 1');
$filterStatus = $this->ci->input->post('filterStatus');
if ($filterStatus == 1) {
	array_push($where_order, 'AND tblsuppliers.id = -1');
} else if ($filterStatus == 2) {
	array_push($where_order, 'AND tblsuppliers.id = -1');
}
$group_by_order = 'GROUP BY tblsuppliers.id';
$result_order = data_tables_init_having($aColumns_order, $sIndexColumn_order, $sTable_order, $join_order, $where_order, [
	'tblsuppliers.type','SUM(tbl_orders.price_other_expenses_delivery) as price_other_expenses_import',
], '', $group_by_order, $having_order);
$output_order = $result_order['output'];
$rResult_order = $result_order['rResult'];
$aColumnsoutsource = [
	'tblsuppliers.id as id',
	'tblsuppliers.company as company',
	'SUM(tbl_outsource.grand_total) as total_import',
	'(COALESCE(SUM(tbl_outsource.amount_paid),0)) as amount_paid_import',
	// '0 as price_other_expenses_import',
	'4',
	'5',
	'6',
	'7',
];
$sIndexColumnoutsource = 'id';
$sTableoutsource = 'tblsuppliers';
$whereoutsource = [];
$havingoutsource = 'HAVING (total_import - amount_paid_import) > 0';
$filteroutsource = [];
$joinoutsource = [
	'LEFT JOIN tbl_outsource ON tbl_outsource.supplier_id=tblsuppliers.id',
];
array_push($whereoutsource, 'AND tbl_outsource.status = "approved"');
if (!empty($suppliers_id)) {
	array_push($whereoutsource, 'AND tblsuppliers.id IN(' . trim($suppliers_id, ',') . ')');
}
array_push($whereoutsource, 'AND tblsuppliers.type = 0');
$filterStatus = $this->ci->input->post('filterStatus');
if ($filterStatus == 1) {
	array_push($whereoutsource, 'AND tblsuppliers.id = -1');
}
$group_byoutsource = 'GROUP BY tblsuppliers.id';
$resultoutsource = data_tables_init_having(
	$aColumnsoutsource,
	$sIndexColumnoutsource,
	$sTableoutsource,
	$joinoutsource,
	$whereoutsource,
	[
		'tblsuppliers.type','0 as price_other_expenses_import'
	],
	'',
	$group_byoutsource,
	$havingoutsource
);
$outputoutsource = $resultoutsource['output'];
$rResultoutsource = $resultoutsource['rResult'];
$j = 0;
$today = date('Y-m-d');
$week30s = strtotime(date("Y-m-d", strtotime($today)) . " -30 day");
$week30 = strftime("%Y-%m-%d", $week30s);
$week60s = strtotime(date("Y-m-d", strtotime($today)) . " -60 day");
$week60 = strftime("%Y-%m-%d", $week60s);
$week90s = strtotime(date("Y-m-d", strtotime($today)) . " -90 day");
$week90 = strftime("%Y-%m-%d", $week90s);
$footer_data = array(
	'debt_total' => 0,
	'debt_30N' => 0,
	'debt_30N60N' => 0,
	'debt_60N' => 0,
);
// $rResult=array_merge($rResult,$rResultoutsource); 
if (empty($rResult)) {
	$rResult = array_merge($rResult, $rResultoutsource);
}
$rResult = array_merge($rResult, $rResult_order);
$output['iTotalRecords'] = $output['iTotalRecords'] + $output_order['iTotalRecords'] + $outputoutsource['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'] + $output_order['iTotalDisplayRecords'] + $outputoutsource['iTotalDisplayRecords'];
foreach ($rResult as $key => $aRow) {
	if ($key == 0) {
		if ($aRow['type'] == 0) {
			$_data_s = 0;
			$row = array(
				"",
				_l('supplier'),
			);
			$row['DT_RowClass'] = 'alert-header bold danger';
			for ($i = 0; $i < count($aColumns)-2; $i++) {
				$row[] = "";
			}
			$output['aaData'][] = $row;
		} else {
			$_data_s = 1;
			$row = array(
				"",
				_l('tnh_transporters'),
			);
			$row['DT_RowClass'] = 'alert-header bold danger';
			for ($i = 0; $i < count($aColumns)-2; $i++) {
				$row[] = "";
			}
			$output['aaData'][] = $row;
		}
	} else {
		if ($aRow['type'] != $_data_s) {
			$_data_s = 1;
			$row = array(
				"",
				_l('tnh_transporters'),
			);
			$row['DT_RowClass'] = 'alert-header bold danger';
			for ($i = 0; $i < count($aColumns)-2; $i++) {
				$row[] = "";
			}
			$output['aaData'][] = $row;
		}
	}
	$row = array();
	$j++;
	//	for ($i = 0; $i < count($aColumns); $i++) {
	//		if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	//			$_data = $aRow[strafter($aColumns[$i], 'as ')];
	//		} else {
	//			$_data = $aRow[$aColumns[$i]];
	//		}

	$row[0] = '<div class="text-center">' . $j . '</div>';
	$row[1] = '<a onclick="suppliert_detail(' . $aRow['id'] . ')">' . $aRow['company'] . '</a>';
	$row[2] = formatNumber($aRow['total_import']);
	if ($aRow['type'] == 0) {
		$row[3] = formatNumber($aRow['amount_paid_import'] - $aRow['price_other_expenses_import']);
	} else {
		$row[3] = formatNumber($aRow['amount_paid_import']);
	}

	// $row[4] = formatNumber($aRow['price_other_expenses_import']);

	if ($aRow['type'] == 0) {
		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblimport.suppliers_id' => $aRow['id'],
			'tblimport.date >=' => $week30,
			'tblimport.date <=' => $today,
			// 'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))"; // AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin['join'] = array(
			// 'tblimport_items,tblimport_items.id_import=tblimport.id,inner',
			// 'tblpurchase_invoice_items,tblpurchase_invoice_items.id_import_item=tblimport_items.id,left',
			// 'tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_invoice_items.purchase_invoice_id,inner',
			'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
		);
		$whereJoin['field'] = 'tblimport.total';
		$subtotal = sum_from_table_join('tblimport', $whereJoin);

		//return 
		// SELECT SUM(tblreturn_suppliers.total)
        //         FROM tblreturn_suppliers 
        //         WHERE tblreturn_suppliers.suppliers_id = tblsuppliers.id AND  tblreturn_suppliers.treatment_methods = 2 and tblreturn_suppliers.treatment_methods = 2),0)
		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblreturn_suppliers.suppliers_id' => $aRow['id'],
			'tblreturn_suppliers.date >=' => $week30,
			'tblreturn_suppliers.date <=' => $today,
		);
		$whereJoin['join'] = array(
		);
		$whereJoin['field'] = 'tblreturn_suppliers.total';
		$subtotal -= sum_from_table_join('tblreturn_suppliers', $whereJoin);
		// 

		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date >=' => $week30,
			'tblpurchase_order.date <=' => $today,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin1['field'] = 'tblpurchase_order.amount_paid';
		$amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

		$whereJoin2 = array();
		$whereJoin2['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date >=' => $week30,
			'tblpurchase_order.date <=' => $today,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0))"; // or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
		$amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

		$whereJoin5 = array();
		$whereJoin5['where'] = array(
			'tbl_services.suppliers_id' => $aRow['id'],
			'tbl_services.status' => 1,
			'tbl_services.date >=' => $week30,
			'tbl_services.date <=' => $today,
		);
		$whereJoin5['join'] = array();
		$whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
		$services = sum_from_table_join_v2('tbl_services', $whereJoin5);

		$footer_data['debt_30N'] += ($subtotal - $amount_paid - $amount_paid_invoice + $services);
		$row[4] = formatNumber($subtotal - $amount_paid - $amount_paid_invoice + $services);


		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblimport.suppliers_id' => $aRow['id'],
			'tblimport.date >=' => $week60,
			'tblimport.date <' => $week30,
			// 'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin['join'] = array();
		$whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))"; // AND tblimport.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin['join'] = array(
			// 'tblimport_items,tblimport_items.id_import=tblimport.id,inner',
			// 'tblpurchase_invoice_items,tblpurchase_invoice_items.id_import_item=tblimport_items.id,left',
			// 'tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_invoice_items.purchase_invoice_id,inner',
			'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
		);
		$whereJoin['field'] = 'tblimport.total';
		$subtotal = sum_from_table_join('tblimport', $whereJoin);


		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblreturn_suppliers.suppliers_id' => $aRow['id'],
			'tblreturn_suppliers.date >=' => $week60,
			'tblreturn_suppliers.date <' => $week30,
		);
		$whereJoin['join'] = array(
		);
		$whereJoin['field'] = 'tblreturn_suppliers.total';
		$subtotal -= sum_from_table_join('tblreturn_suppliers', $whereJoin);

		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date >=' => $week60,
			'tblpurchase_order.date <' => $week30,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin1['field'] = 'tblpurchase_order.amount_paid';
		$amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

		$whereJoin2 = array();
		$whereJoin2['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date >=' => $week60,
			'tblpurchase_order.date <' => $week30,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
		$amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

		$whereJoin5 = array();
		$whereJoin5['where'] = array(
			'tbl_services.suppliers_id' => $aRow['id'],
			'tbl_services.status' => 1,
			'tbl_services.date >=' => $week60,
			'tbl_services.date <=' => $week30,
		);
		$whereJoin5['join'] = array();
		$whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
		$services = sum_from_table_join_v2('tbl_services', $whereJoin5);
		$footer_data['debt_30N60N'] += ($subtotal - $amount_paid - $amount_paid_invoice + $services);
		$row[5] = formatNumber($subtotal - $amount_paid - $amount_paid_invoice + $services);


		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblimport.suppliers_id' => $aRow['id'],
			'tblimport.date <' => $week60,
			// 'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin['join'] = array();
		$whereJoin['where_or'] = "((tblpurchase_order.status_pay != 2))"; // AND tblimport.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin['join'] = array(
			// 'tblimport_items,tblimport_items.id_import=tblimport.id,inner',
			// 'tblpurchase_invoice_items,tblpurchase_invoice_items.id_import_item=tblimport_items.id,left',
			// 'tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_invoice_items.purchase_invoice_id,inner',
			'tblpurchase_order,tblpurchase_order.id=tblimport.id_order,inner'
		);
		$whereJoin['field'] = 'tblimport.total';
		$subtotal = sum_from_table_join('tblimport', $whereJoin);
		//tra hàng
		$whereJoin = array();
		$whereJoin['where'] = array(
			'tblreturn_suppliers.suppliers_id' => $aRow['id'],
			'tblreturn_suppliers.date <' => $week60,
		);
		$whereJoin['join'] = array(
		);
		$whereJoin['field'] = 'tblreturn_suppliers.total';
		$subtotal -= sum_from_table_join('tblreturn_suppliers', $whereJoin);

		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date <' => $week60,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin1['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin1['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin1['field'] = 'tblpurchase_order.amount_paid';
		$amount_paid = sum_from_table_join('tblpurchase_order', $whereJoin1);

		$whereJoin2 = array();
		$whereJoin2['where'] = array(
			'tblpurchase_order.suppliers_id' => $aRow['id'],
			'tblpurchase_order.date <' => $week60,
			'tblpurchase_order.id IN(select id_order from tblimport)',
		);
		$whereJoin2['where_or'] = "((tblpurchase_order.status_pay != 2 AND tblpurchase_order.red_invoice = 0) or (tblpurchase_invoice.status != 2 AND tblpurchase_order.status_pay = 0))";
		$whereJoin2['join'] = array('tblpurchase_invoice,tblpurchase_invoice.id=tblpurchase_order.red_invoice,left');
		$whereJoin2['field'] = 'tblpurchase_order.price_other_expenses';
		$amount_paid_invoice = sum_from_table_join('tblpurchase_order', $whereJoin2);

		$whereJoin5 = array();
		$whereJoin5['where'] = array(
			'tbl_services.suppliers_id' => $aRow['id'],
			'tbl_services.status' => 1,
			'tbl_services.date <' => $week60,
		);
		$whereJoin5['join'] = array();
		$whereJoin5['field'] = '(tbl_services.subtotal - tbl_services.payment)';
		$services = sum_from_table_join_v2('tbl_services', $whereJoin5);
		$footer_data['debt_60N'] += ($subtotal - $amount_paid - $amount_paid_invoice + $services);
		$row[6] = formatNumber($subtotal - $amount_paid - $amount_paid_invoice + $services);
	} else {
		$whereJoin = array();
		$whereJoin['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date >=' => $week30 . ' 00:00:00',
			'tbl_orders.date <=' => $today . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin['join'] = array();
		$whereJoin['field'] = 'cost_delivery';
		$subtotal = sum_from_table_join('tbl_orders', $whereJoin);
		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date >=' => $week30 . ' 00:00:00',
			'tbl_orders.date <=' => $today . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin1['where_or'] = "";
		$whereJoin1['join'] = array();
		$whereJoin1['field'] = 'tbl_orders.price_other_expenses_delivery';
		$amount_paid = sum_from_table_join('tbl_orders', $whereJoin1);
		$row[4] = formatNumber($subtotal - $amount_paid);


		$whereJoin = array();
		$whereJoin['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date >=' => $week60 . ' 00:00:00',
			'tbl_orders.date <=' => $week30 . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin['join'] = array();
		$whereJoin['field'] = 'cost_delivery';
		$subtotal = sum_from_table_join('tbl_orders', $whereJoin);
		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date >=' => $week60 . ' 00:00:00',
			'tbl_orders.date <=' => $week30 . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin1['where_or'] = "";
		$whereJoin1['join'] = array();
		$whereJoin1['field'] = 'tbl_orders.price_other_expenses_delivery';
		$amount_paid = sum_from_table_join('tbl_orders', $whereJoin1);
		$row[5] = formatNumber($subtotal - $amount_paid);


		$whereJoin = array();
		$whereJoin['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date <' => $week60 . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin['join'] = array();
		$whereJoin['field'] = 'cost_delivery';
		$subtotal = sum_from_table_join('tbl_orders', $whereJoin);
		$whereJoin1 = array();
		$whereJoin1['where'] = array(
			'tbl_orders.transporter_id' => $aRow['id'],
			'tbl_orders.date <' => $week60 . ' 23:59:59',
			'tbl_orders.status' => 'approved'
		);
		$whereJoin1['where_or'] = "";
		$whereJoin1['join'] = array();
		$whereJoin1['field'] = 'tbl_orders.price_other_expenses_delivery';
		$amount_paid = sum_from_table_join('tbl_orders', $whereJoin1);
		$row[6] = formatNumber($subtotal - $amount_paid);
	}


	if ($aRow['type'] == 0) {
		$row[7] = formatNumber($aRow['total_import'] - $aRow['amount_paid_import']);
		$footer_data['debt_total'] += $aRow['total_import'] - $aRow['amount_paid_import'];
	} else {
		$row[7] = formatNumber($aRow['total_import'] - $aRow['price_other_expenses_import']);
		$footer_data['debt_total'] += $aRow['total_import'] - $aRow['price_other_expenses_import'];
	}


	//		$row[] = $_data;
	//	}
	$output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
	$footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;
