<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transfer_bussiness extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->isAdmin = is_admin();
    }

    public function index(){
        $data = [];
        $data['title'] = lang('Giữ kho trên chuyền');
        $data['branch'] = getListBranch();
        $this->load->view('admin/transfer_bussiness/index', $data);
    }

    public function getTranferBusiness(){
        $product_search = $this->input->post('product_search');
        $orders_search = $this->input->post('orders_search');
        $business_search = $this->input->post('business_search');
        $branch_search = $this->input->post('branch_search');
        $productions_orders_search = $this->input->post('productions_orders_search');

        $arrBranch = get_branch_staff();
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_order = "(
            SELECT
                tbl_tranfer_business_item.tranfer_business_id as tranfer_business_id,
                GROUP_CONCAT(DISTINCT tbl_orders.reference_no SEPARATOR '<br>') as reference_no
            FROM tbl_tranfer_business_item
            JOIN tbl_orders ON tbl_orders.id = tbl_tranfer_business_item.order_id
            GROUP BY tbl_tranfer_business_item.tranfer_business_id
        ) tb_order";

        $aColumns = [
            'tbl_tranfer_business.id as id',
            'tbl_tranfer_business.date as date',
            'tbl_tranfer_business.reference_no as reference_no',
            'tb_order.reference_no as reference_no_order',
            'tbl_tranfer_business.created_by as created_by',
            'tbl_tranfer_business.status as status',
            'tbl_tranfer_business.note as note',
            '"" as reference_po',
            '1 as actions'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_tranfer_business';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN '.$tb_order.' ON tb_order.tranfer_business_id = tbl_tranfer_business.id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_tranfer_business.branch_id'
        ];

        if (!empty($productions_orders_search)) {
            array_push($where, ' AND exists (
                SELECT 1
                FROM tbl_tranfer_business_item
                INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"
                WHERE tbl_tranfer_business_item.tranfer_business_id = tbl_tranfer_business.id AND tbl_productions_orders_items.productions_orders_id = '.$productions_orders_search.'
            )');
        }

        if (!empty($branch_search)){
            array_push(
                $where,
                'AND tbl_tranfer_business.branch_id = '.$branch_search.''
            );
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                array_push(
                    $where,
                    'AND tbl_tranfer_business.branch_id IN ('.$coverStrBranch.')'
                );
            } else {
                array_push(
                    $where,
                    'AND tbl_tranfer_business.id = 0'
                );
            }
        }

        if (!empty($product_search)){
            $product_search = explode('__',$product_search);
            array_push(
                $where,
                'AND EXISTS(
                    SELECT tbl_tranfer_business_item.tranfer_business_id
                    FROM tbl_tranfer_business_item
                    WHERE tbl_tranfer_business_item.tranfer_business_id = tbl_tranfer_business.id
                    AND tbl_tranfer_business_item.item_id = '.$product_search[0].'
                )'
            );
        }

        if (!empty($orders_search)){
            array_push(
                $where,
                'AND EXISTS(
                    SELECT tbl_tranfer_business_item.tranfer_business_id
                    FROM tbl_tranfer_business_item
                    WHERE tbl_tranfer_business_item.tranfer_business_id = tbl_tranfer_business.id
                    AND tbl_tranfer_business_item.order_id = '.$orders_search.'
                )'
            );
        }

        if (!empty($business_search)){
            array_push(
                $where,
                'AND EXISTS(
                    SELECT tbl_tranfer_business_item.tranfer_business_id
                    FROM tbl_tranfer_business_item
                    WHERE tbl_tranfer_business_item.tranfer_business_id = tbl_tranfer_business.id
                    AND tbl_tranfer_business_item.id_business_plan = '.$business_search.'
                )'
            );
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_tranfer_business.date_status as date_status, 
            tbl_tranfer_business.date_created as date_created, 
            tblbranch.name as branch_name, 
            tbl_tranfer_business.staff_status as staff_status'
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];
            $reference_no = $aRow['reference_no'];
            $date = $aRow['date'];
            $created_by = $aRow['created_by'];
            $status = $aRow['status'];
            $staff_status = $aRow['staff_status'];
            $note = $aRow['note'];
            $row = array();

            $strBranch = '';
            if (!empty($aRow['branch_name'])){
                $strBranch = '<div style="font-style: italic">'.$aRow['branch_name'].'</div>';
            }

            $row[0] = '<div class="text-center">'.(++$key).'</div>';
            $row[1] ='<div class="text-left">'._dhau($date).'</div>';
            $row[2] ='<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/transfer_bussiness/view_tranfer_bussiness/' . $id) . '" data-toggle="modal" data-target="#myModal">'.$reference_no.'</a>'.$strBranch.'</div>';
            $row[3] = $aRow['reference_no_order'];
            $htmlCreated = staff_profile_image($aRow['created_by'],
                    array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => ' Vào lúc: ' . _dt($aRow['date_created'])
                    )) . get_staff_full_name($aRow['created_by']) . '<br>';
            $row[4] ='<div class="text-left">'.$htmlCreated.'</div>';
            $htmlStatus = '';
            $htmlStatusStaff = '';
            if ($status == 0){
                $htmlStatus = '<div class="label label-danger">Chưa duyệt</div>';
            } else {
                $htmlStatus = '<div class="label label-success">Đã duyệt</div>';
                $htmlStatusStaff = '<div>Người duyệt : '.get_staff_full_name($staff_status).'</div>';
            }
            $row[5] = '<div class="text-center">'.$htmlStatus.$htmlStatusStaff.'</div>';
            $row[6] = '<div class="text-left">'.$note.'</div>';

            $po = $this->site_model->getPOTranferBusinessItem($id);
            $row[7] = '<div class="text-left">'.$po['reference_no_po'].'</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/transfer_bussiness/view_tranfer_bussiness/' . $id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('Phiếu') . '</a>';

            $print = '<a href="' . base_url('admin/transfer_bussiness/print_pdf/' . $id) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('phiếu') . '</a>';
            $delete ='<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/transfer_bussiness/deleteTransferToPlanProducts/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('Phiếu') . '</a>';


//            $delete = '';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $print . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[8] = $actions;
            $output['aaData'][] = $row;

        }
        echo json_encode($output);
    }

    public function view_tranfer_bussiness($id){

        $transfer_bussiness = get_table_where('tbl_tranfer_business',['id' => $id],'','row_array');

        $this->db->select('
            tbl_orders.reference_no as reference_no_order,
            tbl_business_plan.reference_no as reference_no_business,
            tbl_tranfer_business_item.quantity as quantity,
            tbl_tranfer_business_item.item_id as item_id,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_business_plan_items.items_code as items_code,
            tbl_business_plan_items.items_name as items_name,
        ');
        $this->db->from('tbl_tranfer_business_item');
        $this->db->join('tbl_products','tbl_products.id = tbl_tranfer_business_item.item_id');
        $this->db->join('tbl_business_plan','tbl_business_plan.id = tbl_tranfer_business_item.id_business_plan');
        $this->db->join('tbl_business_plan_items','tbl_business_plan_items.id = tbl_tranfer_business_item.business_plan_item_id');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_tranfer_business_item.order_id');
        $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$id);
        $items = $this->db->get()->result_array();


        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $reference_no_order = $value['reference_no_order'];
                $reference_no_business = $value['reference_no_business'];
                $items_id = $value['item_id'];
                $images = '';
                $info = null;
                $info = $this->products_model->rowProduct($items_id);
                 $unit = $this->unit_model->rowUnit($info['unit_id']);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }

                if (!empty($value['image_product'])) {
                    $images = base_url('download/preview_image?path=uploads/products/' . $value['id'] . '/' . $value['image_product']);
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }


                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdCode = '<td class="text-left">' . $value['code'] . '</td>';
                $tdName = '<td class="text-left">' . $value['name'] . '</td>';
                $tdOrder = '<td>' . $reference_no_order . '</td>';
                $tdKHKD = '<td>' . $reference_no_business . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdOrder . '
                    ' . $tdKHKD . '
                    ' . $tdQuantity . '
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;
        $data['transfer_bussiness'] = $transfer_bussiness;
        $this->load->view('admin/transfer_bussiness/view_transfer_bussiness', $data);
    }

    public function deleteTransferToPlanProducts($transfer_id) {

        if (!$this->isAdmin){
            $data['result'] = 0;
            $data['message'] = lang('Không có quyền xóa');
            echo json_encode($data);die();
        }
        $data['result'] = 0;
        $data['message'] = lang('fail');

//        $transfer_id = $this->input->post('transfer_id');


        $this->db->from('tbltransfer_warehouse_detail');
        $this->db->where('tbltransfer_warehouse_detail.tranfer_business_id',$transfer_id);
        $test_quantity_new = $this->db->count_all_results();
        if (!empty($test_quantity_new)){
            $data['result'] = 0;
            $data['message'] = lang('Đã có giữ kho không thể xóa');
            echo json_encode($data);die();
        }

        $this->db->from('tbl_tranfer_business_item');
        $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$transfer_id);
        $this->db->where('EXISTS (
                SELECT tbl_deliveries.order_id
                FROM tbl_deliveries
                WHERE tbl_deliveries.order_id = tbl_tranfer_business_item.order_id
            )');
        $test_quantity = $this->db->get()->result_array();
        if (!empty($test_quantity)){
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo giao hàng');
            echo json_encode($data);die();
        }

        $tranfer_business = get_table_where('tbl_tranfer_business',['id' => $transfer_id],'','row_array');

        $item_transfers = get_table_where('tbl_tranfer_business_item', ['tranfer_business_id' => $transfer_id], '', 'result_array');
        $success = false;
        if ($transfer_id){
            $this->db->where('tbl_tranfer_business.id',$transfer_id);
            $success = $this->db->delete('tbl_tranfer_business');

            $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$transfer_id);
            $this->db->delete('tbl_tranfer_business_item');

            insertActivityLog([
                'type_parent_obj' => 'tranfer_business',
                'table_obj' => 'tbl_tranfer_business',
                'id_obj' => $transfer_id,
                'name_obj' => $tranfer_business['reference_no'],
                'content' => lang('Xóa giữ kho trên chuyền').' ['.$tranfer_business['reference_no'].']',
                'actions' => 'delete',
            ]);

            //tính lại transfer trong products
            if (!empty($item_transfers)) {
                $arrItemId = [];
                foreach ($item_transfers as $item_transfer) {
                    $arrItemId[] = $item_transfer['item_id'];
                }

                if (!empty($arrItemId)) {
                    totalTransferBusinessItem($arrItemId);
                }
            }

            if ($success){
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function print_pdf($id){
        ob_end_clean();
        ob_start();
        stylePdf();
        $day = date( 'd');
        $month = date('m');
        $year = date('Y');
        $staff = get_staff_full_name();
        $transfer_bussiness = get_table_where('tbl_tranfer_business',['id' => $id],'','row');
        $this->db->select('
            tbl_orders.reference_no as reference_no_order,
            tbl_business_plan.reference_no as reference_no_business,
            tbl_tranfer_business_item.quantity as quantity,
            tbl_tranfer_business_item.item_id as item_id,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_business_plan_items.items_code as items_code,
            tbl_business_plan_items.items_name as items_name,
            tbl_tranfer_business_item.business_plan_item_id as business_plan_item_id
        ');
        $this->db->from('tbl_tranfer_business_item');
        $this->db->join('tbl_products','tbl_products.id = tbl_tranfer_business_item.item_id');
        $this->db->join('tbl_business_plan','tbl_business_plan.id = tbl_tranfer_business_item.id_business_plan');
        $this->db->join('tbl_business_plan_items','tbl_business_plan_items.id = tbl_tranfer_business_item.business_plan_item_id');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_tranfer_business_item.order_id');
        $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$id);
        $items = $this->db->get()->result_array();
        
        $bodyItems = '';
        if (!empty($items)) {
            $totalQuantity = 0;
            foreach ($items as $key => $value) {
                $reference_no_order = $value['reference_no_order'];
                $reference_no_business = $value['reference_no_business'];
                $items_id = $value['item_id'];
                $images = '';
                $info = null;
                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }

                if (!empty($value['image_product'])) {
                    $images = base_url('download/preview_image?path=uploads/products/' . $value['id'] . '/' . $value['image_product']);
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $po = $this->site_model->getPOTranferBusinessItemByItem($value['business_plan_item_id']);
                $reference_no_po = $po['reference_no_po'];

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdCode = '<td class="text-left">' . $value['code'] . '</td>';
                $tdName = '<td class="text-left">' . $value['name'] . '</td>';
                $tdOrder = '<td>' . $reference_no_order . '</td>';
                $tdKHKD = '<td>' . $reference_no_business . '</td>';
                $tdPO = '<td>' . $reference_no_po . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $totalQuantity += $value['quantity'];

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdOrder . '
                    ' . $tdKHKD . '
                    ' . $tdPO . '
                    ' . $tdQuantity . '
                </tr>';
            }
        }
        echo '
        <h1 class="text-center uppercase">' . lang('PHIẾU GIỮ KHO TRÊN CHUYỀN') . '</h1>

        <div class="text-left">
            <span><span class="bold">' . _l('Mã phiếu') . ':</span> ' . $transfer_bussiness->reference_no . '</span><br>
            <span><span class="bold">' . _l('date') . ':</span> ' . _d($transfer_bussiness->date) . '</span><br>
            <span><span class="bold">' . _l('note') . ':</span> ' . $transfer_bussiness->note . '</span><br>
        </div>

		<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
			<tr nobr="true" style="background-color: #ddd;">
				<td class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</td>
				<td class="bold text-center" style="width: 18%;">' . _l('Mã TP') . '</td>
				<td class="bold text-center" style="width: 22%;">' . _l('Tên TP') . '</td>
				<td class="bold text-center" style="width: 17%;">' . _l('Đơn hàng') . '</td>
				<td class="bold text-center" style="width: 17%;">' . _l('KHKD') . '</td>
				<td class="bold text-center" style="width: 16%;">' . _l('Số LSX') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Số lượng') . '</td>
			</tr>
			'.$bodyItems.'
            <tr>
                <td class="bold text-center" colspan="2">' . _l('tnh_grand_total') . '</td>
				<td class="bold text-center" >' . _l('') . '</td>
				<td class="bold text-center" >' . _l('') . '</td>
				<td class="bold text-center" >' . _l('') . '</td>
				<td class="bold text-center" >' . _l('') . '</td>
				<td class="bold text-center" >' . $totalQuantity . '</td>
            </tr>
		</table>
		<br><br>
            <table style="width: 100%">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td class="text-center">
                        <span class="bold">Người Lập</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                        <br>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Duyệt</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                        <br>
                    </td>
                </tr>
            </table>
		';
        $content = ob_get_contents();

        ob_end_clean();
        $data['content'] = $content;
        // $data['barcode'] = '';
        // $data['type'] = 'L';
        // $data['img'] = '';
        // $data['pageCustome'] = 'orders_detail';
        $pdf = @print_pdf_tnh_new($data);
        $type = 'I';
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function searchBusiness(){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_business_plan.id as id, 
            tbl_business_plan.reference_no as text
        ', false);
        $this->db->from('tbl_business_plan');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_business_plan.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $business_plan = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('business_plan'), 'children' => $business_plan];
        echo json_encode($data);
    }
}