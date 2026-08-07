<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('view') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('start_date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _d($costing['start_date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('end_date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _d($costing['end_date']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('name') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $costing['name'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Chi phí nguyên vật liệu trực tiếp') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= formatMoney($costing['direct_material']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Chi phí nhân công trực tiếp') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= formatMoney($costing['direct_labor_costing']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('Chi phí sản xuất chung') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= formatMoney($costing['general_costing']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#giavonmathang" aria-controls="giavonmathang" role="tab" data-toggle="tab"><?= lang('Giá vốn mặt hàng') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#nhapthanhpham" aria-controls="nhapthanhpham" role="tab" data-toggle="tab">Nhập thành phẩm</a>
                            </li>
                            <li role="presentation">
                                <a href="#chiphinvltructiep" aria-controls="chiphinvltructiep" role="tab" data-toggle="tab">Chi phí nguyên vật liệu</a>
                            </li>
                            <li role="presentation">
                                <a href="#chiphinhancongtructiep" aria-controls="chiphinhancongtructiep" role="tab" data-toggle="tab">Chi phí nhân công trực tiếp</a>
                            </li>
                            <li role="presentation">
                                <a href="#chiphisanxuatchung" aria-controls="chiphisanxuatchung" role="tab" data-toggle="tab">Chi phí sản xuất chung</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="giavonmathang">
                                <div class="">
                                    <table id="table-items-costing" class="table table-hover dont-responsive-table" style="max-height: 400px !important;">
                                        <thead>
                                            <tr>
                                                <th style="width: 30px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                                <th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
                                                <th style="width: 200px;"><?= lang('tnh_product_name') ?></th>
                                                <th style="width: 100px;"><?= lang('tnh_quantity_finished') ?></th>
                                                <th style="width: 150px;"><?= lang('Chi phí NVL MH') ?></th>
                                                <th style="width: 150px;"><?= lang('Chi phí SX chung MH') ?></th>
                                                <th style="width: 180px;"><?= lang('Chi phí nhân công trực tiếp MH') ?></th>
                                                <th style="width: 170px;"><?= lang('Chi phí NVL dở dang đầu kỳ') ?></th>
                                                <th style="width: 170px;"><?= lang('Chi phí NVL dở dang cuối kỳ') ?></th>
                                                <th style="width: 150px;"><?= lang('Giá thành đơn vị') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= $value['item_code'] ?></td>
                                                    <td><?= $value['item_name'] ?></td>
                                                    <td class="text-center"><?= formatNumber($value['soLuongHT']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['chiPhiNVLMatHang']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['chiPhiSXMatHang']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['chiPhiNCTTMatHang']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['chiPhiNVLDoDangDK']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['chiPhiNVLDoDangCK']) ?></td>
                                                    <td class="text-center"><?= formatMoney($value['giaThanhDonVi']) ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="">
                                                <td colspan="3"><?= lang('tnh_grand_total') ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="nhapthanhpham">
                                <?php
                                $arrPodId = [];
                                $this->db->select("
                                        tbl_purchase_products.id as id,
                                        tbl_purchase_products.date as date,
                                        tbl_purchase_products.reference_no as reference_no,
                                        tbl_purchase_product_items.item_code as item_code,
                                        tbl_purchase_product_items.item_name as item_name,
                                        tbl_purchase_product_items.quantity as quantity,
                                        tbl_purchase_products.productions_orders_details_id as pod_id,
                                    ", false);
                                $this->db->from('tbl_purchase_products');
                                $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                                $this->db->where('tbl_purchase_products.warehouseman_id >', 0);
                                $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', $costing['start_date']);
                                $this->db->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', $costing['end_date']);
                                $purchaseProducts = $this->db->get()->result_array();
                                ?>

                                <table class="table table-hover tb-nhapthanhpham dataTable tb-datatable">
                                    <thead>
                                        <tr>
                                            <th><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('Số nhập kho') ?></th>
                                            <th><?= lang('Mặt hàng') ?></th>
                                            <th><?= lang('quantity') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($purchaseProducts)) : ?>
                                            <?php foreach ($purchaseProducts as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= _dt($value['date']) ?></td>
                                                    <td><a class="tnh-modal2" href="<?= base_url('admin/stock/view_purchase_product/'.$value['id']) ?>" data-toggle="modal" data-target="#myModal2"><?= $value['reference_no'] ?></a></td>
                                                    <td><?= $value['item_name'] ?>(<?= $value['item_code'] ?>)</td>
                                                    <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                                </tr>
                                                <?php
                                                    $pod_id = $value['pod_id'];
                                                    if (!in_array($pod_id, $arrPodId)) {
                                                        array_push($arrPodId, $pod_id);
                                                    }
                                                ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <td></td>
                                            <td><?= lang('tnh_grand_total') ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="chiphinvltructiep">
                                <?php
                                    $strPodId = implode(',', $arrPodId);
                                    if (empty($strPodId)) $strPodId = 0;
                                    $tbMaterialCost = "
                                        SELECT
                                            'suggest_exporting' as type,
                                            tbl_suggest_exporting.id as id,
                                            tbl_suggest_exporting.date_convert_stock as date,
                                            tbl_suggest_exporting.reference_stock as reference_no,
                                            tbl_suggest_exporting.grand_total as grand_total
                                        FROM tbl_suggest_exporting
                                        WHERE tbl_suggest_exporting.warehouseman_id > 0 AND tbl_suggest_exporting.productions_orders_details_id IN (".$strPodId.")
                            
                                        UNION ALL
                            
                                        SELECT
                                            'purchase_internal' as type,
                                            tbl_purchase_internal.id as id,
                                            tbl_purchase_internal.date as date,
                                            tbl_purchase_internal.reference_no as reference_no,
                                            tbl_purchase_internal.grand_total as grand_total
                                        FROM tbl_purchase_internal
                                        WHERE tbl_purchase_internal.warehouseman_id > 0 AND tbl_purchase_internal.pod_id IN (".$strPodId.")
                                        
                                        ORDER BY date ASC
                                    ";
                                    $materalCost = $this->db->query($tbMaterialCost)->result_array();
                                ?>
                                <table class="table table-hover tb-chiphinvltructiep dataTable tb-datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('tnh_type') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('tnh_reference_export_warehouses') ?>/<?= lang('tnh_reference_purchase_internal') ?></th>
                                            <th><?= lang('Tiền xuất kho') ?></th>
                                            <th><?= lang('Tiền thu hồi') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($materalCost)) : ?>
                                            <?php foreach ($materalCost as $key => $value) : ?>
                                                <?php
                                                    $objectId = $value['id'];
                                                    $href = "";
                                                    if ($value['type'] == "suggest_exporting") {
                                                        $href = base_url('admin/stock/view_exporting_production/'.$objectId);
                                                    } else if ($value['type'] == "purchase_internal") {
                                                        $href = base_url('admin/stock/view_purchase_internal/'.$objectId);
                                                    }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td style="width: 100px;">
                                                        <?= $value['type'] == "suggest_exporting" ? '<span class="label label-primary">'.lang('Xuất kho sản xuất').'</span>' : '<span class="label label-danger">'.lang('Thu hồi NVL').'</span>' ?>
                                                    </td>
                                                    <td><?= _dt($value['date']) ?></td>
                                                    <td>
                                                        <a class="tnh-modal2" href="<?= $href ?>" data-toggle="modal" data-target="#myModal2"><?= $value['reference_no'] ?></a>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= $value['type'] == "suggest_exporting" ? formatMoney($value['grand_total']) : 0 ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= $value['type'] == "suggest_exporting" ? 0 : formatMoney($value['grand_total']) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <td></td>
                                            <td><?= lang('tnh_grand_total') ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="chiphinhancongtructiep">
                                <?php
                                    $this->db->select("
                                        tblother_payslips.id as id,
                                        tblother_payslips.date as date,
                                        CONCAT(tblother_payslips.prefix, '', tblother_payslips.code) as reference_no,
                                        tblcosts.name as name_costs,
                                        tblother_payslips.total as total,
                                    ", false);
                                    $this->db->from('tblother_payslips');
                                    $this->db->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');
                                    $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $costing['start_date']);
                                    $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $costing['end_date']);
                                    $this->db->where('tblcosts.type', 1);
                                    $getNhanCongTT = $this->db->get()->result_array();
                                ?>
                                <table class="table table-hover tb-chiphinhancongtructiep dataTable tb-datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('Số chi phí') ?></th>
                                            <th><?= lang('Tên chi phí') ?></th>
                                            <th><?= lang('tnh_total_amount') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($getNhanCongTT)) : ?>
                                            <?php foreach ($getNhanCongTT as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= _dt($value['date']) ?></td>
                                                    <td>
                                                        <a href="#" onclick="view_other_payslips(<?= $value['id'] ?>); return false;"><?= $value['reference_no'] ?></a>
                                                    </td>
                                                    <td><?= $value['name_costs'] ?></td>
                                                    <td class="text-right">
                                                        <?= formatMoney($value['total']) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <td></td>
                                            <td><?= lang('tnh_grand_total') ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="chiphisanxuatchung">
                                <?php
                                    $this->db->select("
                                        tblother_payslips.id as id,
                                        tblother_payslips.date as date,
                                        CONCAT(tblother_payslips.prefix, '', tblother_payslips.code) as reference_no,
                                        tblcosts.name as name_costs,
                                        tblother_payslips.total as total,
                                    ", false);
                                    $this->db->from('tblother_payslips');
                                    $this->db->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');
                                    $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") >=', $costing['start_date']);
                                    $this->db->where('DATE_FORMAT(tblother_payslips.date, "%Y-%m-%d") <=', $costing['end_date']);
                                    $this->db->where('tblcosts.type', 2);
                                    $getNhanCongTT = $this->db->get()->result_array();
                                ?>
                                <table class="table table-hover tb-chiphisanxuatchung dataTable tb-datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('Số chi phí') ?></th>
                                            <th><?= lang('Tên chi phí') ?></th>
                                            <th><?= lang('tnh_total_amount') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($getNhanCongTT)) : ?>
                                            <?php foreach ($getNhanCongTT as $key => $value) : ?>
                                                <tr>
                                                    <td class="text-center"><?= ++$key ?></td>
                                                    <td><?= _dt($value['date']) ?></td>
                                                    <td>
                                                        <a href="#" onclick="view_other_payslips(<?= $value['id'] ?>); return false;"><?= $value['reference_no'] ?></a>
                                                    </td>
                                                    <td><?= $value['name_costs'] ?></td>
                                                    <td class="text-right">
                                                        <?= formatMoney($value['total']) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bold">
                                            <td></td>
                                            <td><?= lang('tnh_grand_total') ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($costing['date_created']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtItemsCosting = $('#table-items-costing').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('Giá vốn mặt hàng') ?>',
                // extend: 'excelHtml5',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
            }, ],
            // scrollY: '300px',
            scrollX: true,
            fixedColumns: {
                leftColumns: 3,
                rightColumns: 0
            },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageSoLuongHT = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(3).footer()).html('<div class="text-center">' + tnhFormatNumber(pageSoLuongHT) + '</div>');

                pageChiPhiNVL = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-center">' + tnhFormatMoney(pageChiPhiNVL) + '</div>');

                pageChiPhiSXChung = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html('<div class="text-center">' + tnhFormatMoney(pageChiPhiSXChung) + '</div>');

                pageChiPhiNCTT = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(6).footer()).html('<div class="text-center">' + tnhFormatMoney(pageChiPhiNCTT) + '</div>');

                pageChiPhiDDDK = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(7).footer()).html('<div class="text-center">' + tnhFormatMoney(pageChiPhiDDDK) + '</div>');

                pageChiPhiDDCK = api
                    .column(8, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(8).footer()).html('<div class="text-center">' + tnhFormatMoney(pageChiPhiDDCK) + '</div>');

                pageGiaThanhDonVi = api
                    .column(9, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(9).footer()).html('<div class="text-center">' + tnhFormatMoney(pageGiaThanhDonVi) + '</div>');
            }
        });

        var dtNhapThanhPham = $('.tb-nhapthanhpham').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('Nhập thành phẩm') ?>',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
            }, ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageTotalQuantity = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-center">' + tnhFormatNumber(pageTotalQuantity) + '</div>');
            }
        });

        var dtChiPhiNVL = $('.tb-chiphinvltructiep').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('Chi phí NVL') ?>',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
            }, ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                pageGrandTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        // b = $($(b)[0]).val();
                        return intVal(a) + intVal(b);
                    }, 0);
                
                pageGrandTotalThuHoi = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        // b = $($(b)[0]).val();
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-right">' + tnhFormatMoney(pageGrandTotal) + '</div>');
                $(api.column(5).footer()).html('<div class="text-right">' + tnhFormatMoney(pageGrandTotalThuHoi) + '</div>');
            }
        });

        var dtChiPhiNhanCongTT = $('.tb-chiphinhancongtructiep').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('Chi phí nhân công') ?>',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
            }, ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageGrandTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-right">' + tnhFormatMoney(pageGrandTotal) + '</div>');
            }
        });

        var dtChiPhiSanXuatChung = $('.tb-chiphisanxuatchung').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [{
                text: 'Excel',
                title: '<?= lang('Chi phí sản xuất chung') ?>',
                // autoFilter: true,
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                },
            }, ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                pageGrandTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(4).footer()).html('<div class="text-right">' + tnhFormatMoney(pageGrandTotal) + '</div>');
            }
        });
    });
</script>