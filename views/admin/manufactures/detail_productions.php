<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.2') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<style type="text/css">
    .activity-feed .feed-item {
        padding-bottom: 10px;
    }

    #tb-export-materials-warehouses_filter {
        text-align: left;
    }

    .fixed {
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 9999999;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?= $this->load->view('admin/breadcrumb') ?>
        </div>
    </div>
    <div class="content">
        <?php //echo form_open('admin/manufactures/detail_productions', array('id' => 'add-productions-plan')); 
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body p0 p-top-bottom">
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <div class="row-contro">
                                    <div><?= lang('tnh_reference_productions_orders') ?>: </div>
                                    <div class="ml-at t-bold"><?= $production_detail['reference_no_order'] ?></div>
                                </div>
                                <div class="row-contro">
                                    <div><?= lang('tnh_reference_productions_orders_details') ?>: </div>
                                    <div class="ml-at t-bold">
                                        <?php
                                        $barcode = $production_detail['reference_no'];
                                        $barcode = preg_replace('/[\/\(\)]/', '_', $barcode);
                                        ?>
                                        <div><img src="<?= base_url('admin/products/gen_barcode/' . $barcode) ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view" id="leadViewWrapper">
                                <?php if (!empty($order)) : ?>
                                    <div class="row-contro">
                                        <div><?= lang('orders') ?> - <?= lang('tnh_customer') ?>: </div>
                                        <div class="ml-at t-bold text-right"><?= $order['reference_no'] ?> -
                                            <?= $order['company'] ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($business_plan)) : ?>
                                    <div class="row-contro">
                                        <div><?= lang('business_plan') ?>: </div>
                                        <div class="ml-at t-bold text-right"><?= $business_plan['reference_no'] ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="row-contro">
                                    <div><?= lang('tnh_branch') ?>: </div>
                                    <div class="ml-at t-bold text-right"><?= $production_detail['branch_name'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-view mtop15" id="leadViewWrapper">
                                <?php if(!empty($isFinished['is_finished'])): ?>
                                    <div class="panel-finished" style="margin: auto;">
                                        <div class=""><?= lang('tnh_finished_production') ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($isAdmin) : ?>
                            <div class="col-md-12">
                                <?php $totalMa = $totalMaterial - $totalInternal; ?>
                                <div style="height: auto;" class="col-md-4">
                                    <div class="tnh-card tnh-text-white tnh-bg-warning o-hidden h-100" style="height: 80px;">
                                        <div class="tnh-card-body">
                                            <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                                            <div class="tnh-h3">
                                                <?= lang('tnh_self_cost_expenses') ?><br>
                                                <span style="font-size: 12px;" id="tnh-subtotal"><?= formatMoney($totalMa) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: auto;" class="col-md-4">
                                    <div class="tnh-card tnh-text-white tnh-bg-primary o-hidden h-100" style="height: 80px;">
                                        <div class="tnh-card-body">
                                            <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                                            <div class="tnh-h3">
                                                <?= lang('tnh_other_costs') ?><br>
                                                <span id="tnh-subtotal"><?= formatMoney($totalPayslips) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: auto;" class="col-md-4">
                                    <div class="tnh-card tnh-text-white tnh-bg-success o-hidden h-100" style="height: 80px;">
                                        <div class="tnh-card-body">
                                            <img src="<?= base_url('assets/images/circle.svg') ?>" class="tnh-card-img-absolute" alt="circle-image">
                                            <div class="tnh-h3">
                                                <?= lang('tnh_total_cost') ?><br>
                                                <?php
                                                    $totalCost = $totalMa + $totalPayslips;
                                                ?>
                                                <span id="tnh-subtotal"><?= formatMoney($totalMa + $totalPayslips) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#info-detail" aria-controls="info-detail" role="tab" data-toggle="tab"><?= lang('info') ?></a>
                </li>
                <li role="presentation">
                    <a href="#situation_ex_material" onclick="loadTable('situation_ex_material')" aria-controls="situation_ex_material" role="tab" data-toggle="tab"><?= lang('tnh_situation_ex_material') ?></a>
                </li>
                <li role="presentation">
                    <a href="#view-stock" aria-controls="view-stock" role="tab" data-toggle="tab"><?= lang('tnh_exporting_stock_producion') ?><span class="badge menu-badge bg-warning count-stock"></span></a>
                </li>
                <li role="presentation">
                    <a href="#view-purchase-internal" onclick="loadDataTable('view-purchase-internal')" aria-controls="view-purchase-internal" role="tab" data-toggle="tab"><?= lang('purchase_internal') ?><span class="badge menu-badge bg-warning count-purchase-internal"></span></a>

                </li>
                <li role="presentation">
                    <a href="#view-tasks" aria-controls="view-tasks" role="tab" data-toggle="tab"><?= lang('tnh_tasks') ?><span class="badge menu-badge bg-warning count-tasks"></span></a>

                </li>
                <?php if ($isAdmin) : ?>
                    <li role="presentation">
                        <a href="#view-cost-material" aria-controls="view-cost-material" role="tab" data-toggle="tab"><?= lang('tnh_cost_material') ?><span class="badge menu-badge bg-warning count-cost-material"></span></a>
                    </li>
                <?php endif ?>
                <li role="presentation">
                    <a href="#view-history-detail-productions" aria-controls="view-history-detail-productions" role="tab" data-toggle="tab"><?= lang('activity_log_puchases') ?></a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="info-detail">
                    <section>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?= lang('tnh_production_progress') ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="tb-info" class="tnh-table table dataTable" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10%;" class="text-center">
                                                            <?= lang('tnh_images') ?></th>
                                                        <th style="width: 25%;" class="text-center">
                                                            <?= lang('tnh_describe') ?></th>
                                                        <th style="width: 25%;" class="text-center">
                                                            <?= lang('tnh_product_code') ?></th>
                                                        <th style="width: 15%;" class="text-center">
                                                            <?= lang('tnh_type') ?></th>
                                                        <th style="width: 10%;" class="text-center">
                                                            <?= lang('tnh_unit') ?></th>
                                                        <th style="width: 10%;" class="text-center">
                                                            <?= lang('quantity') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?php
                                                            $images = base_url() . 'assets/images/tnh/no_image.png';
                                                            if ($production_detail['images']) {
                                                                $images = base_url() . 'uploads/products/' . $production_detail['images'];
                                                            }
                                                            ?>
                                                            <div class="td-image">
                                                                <div class="preview_image" style="width: auto;">
                                                                    <div class="display-block contract-attachment-wrapper img">
                                                                        <div style="width:45px; margin: auto;">
                                                                            <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                <div class="">
                                                                                    <img src="<?= $images ?>" style="border-radius: 50%">
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center"><?= $production_detail['items_name'] ?>
                                                        </td>
                                                        <td class="text-center"><?= $production_detail['items_code'] ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= lang($production_detail['type_items']) ?></td>
                                                        <td class="text-center"><?= $production_detail['unit_name'] ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= formatNumber($production_detail['quantity']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="td-process">
                                                            <?= $process ?>
                                                            <?php
                                                                $quantity_warehoused = $production_detail['quantity_warehoused'];
                                                                if ($quantity_warehoused > 0) 
                                                                {
                                                                    $priceTT =  $totalCost/$quantity_warehoused;
                                                                    echo '<div style="padding-left: 40px;"><span class="label label-danger">Giá thành tạm tính: '.formatMoney($priceTT).'</span></div>';
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <!-- panel history -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <?= lang('tnh_history_export_material_and_semi_product') ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <table id="tb-export-materials-warehouses" class="table table-hover table-margin">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"><?= lang('tnh_items') ?></th>
                                                    <th class="text-center"><?= lang('quantity') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"><?= lang('tnh_history_waerhousing_product') ?></h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="tnh-table table dataTable table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 150px;"><?= lang('tnh_warehouses') ?></th>
                                                    <th class="text-center"><?= lang('tnh_position') ?></th>
                                                    <th class="text-center"><?= lang('quantity') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="">
                                                <?php foreach ($warehousing as $key => $value) : ?>
                                                    <tr>
                                                        <td>
                                                            <div class="bold"><?= _d($value['date'], true) ?></div>
                                                            <div class="bold"><?= $value['reference_no'] ?></div>
                                                            <div><?= $value['warehouse_name'] ?></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div><?= $value['location_name'] ?></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div><?= formatNumber($value['quantity']) ?></div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                            <tfoot class="hide">
                                                <tr class="bold">
                                                    <td colspan="2" class="text-center"><?= lang('tnh_grand_total') ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= formatNumber($production_detail['quantity_warehoused']) ?>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 mtop10">
                                <div class="" id="comment-detail">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><?= lang('comment_string') ?></h3>
                                        </div>
                                        <div class="panel-body">
                                            <?php include_once(APPPATH . 'views/admin/feedback/order_production_details/feedback.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div role="tabpanel" class="tab-pane" id="situation_ex_material">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div id="stacked_materials">
                                    
                                </div>
                                <!-- <canvas id="chartMaterials" style="width: 100% !important; height: 200px !important;"></canvas> -->
                                <div class="card-footer small Updated text-muted"><?= lang('tnh_update') ?> <?= _dt(date('Y-m-d H:i:s')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mtop5">
                        <table id="tb-materials" class="table dt-tnh dont-responsive-table mtop0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center"><?= lang('image') ?></th>
                                    <th class="text-center"><?= lang('name') ?> - <?= lang('code') ?></th>
                                    <th class="text-center"><?= lang('tnh_unit') ?></th>
                                    <th class="text-center"><?= lang('tnh_plan') ?></th>
                                    <th class="text-center"><?= lang('tnh_exported') ?></th>
                                    <th class="text-center"><?= lang('tnh_rest') ?></th>
                                    <th class="text-center"><?= lang('tnh_recall') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot class="bold">
                                <tr>
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
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
                <div role="tabpanel" class="tab-pane" id="view-stock">
                    <section>
                        <div class="">
                            <table id="tb-stock" class="table dt-tnh table-hover  table-condensed" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center"><?= lang('id') ?></th>
                                        <th class="text-center"><?= lang('date') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_stock') ?></th>
                                        <th class="text-center"><?= lang('tnh_export_name') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                        <th class="text-center"><?= lang('status') ?></th>
                                        <th class="text-center"><?= lang('ch_warehoues_app') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <div role="tabpanel" class="tab-pane" id="view-purchase-internal">
                    <section>
                        <div class="">
                            <table id="tb-purchase-internal" class="table dt-tnh table-hover  table-condensed" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_purchase_internal') ?></th>
                                        <th><?= lang('tnh_enter_name') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('tnh_material_code') ?></th>
                                        <th><?= lang('tnh_material_name') ?></th>
                                        <th><?= lang('tnh_unit') ?></th>
                                        <th><?= lang('tnh_location_warehouse') ?></th>
                                        <th><?= lang('quantity') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('note') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>
                </div>
                <div role="tabpanel" class="tab-pane" id="view-tasks">
                    <section>
                        <div class="mbot10">
                            <a href="#" onclick="new_task('tasks/task//?typePOD=pod&podId=<?= $id ?>'); return false;" class="btn btn-info"><i class="fa fa-plus"></i> <?= lang('tnh_add_task') ?></a>
                        </div>
                        <div class="">
                            <table id="tb-tasks" class="table dt-tnh table-hover  table-condensed" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('tasks_dt_name') ?></th>
                                        <th><?= lang('tnh_shift_work') ?></th>
                                        <th><?= lang('task_status') ?></th>
                                        <th><?= lang('tasks_dt_datestart') ?></th>
                                        <th><?= lang('task_duedate') ?></th>
                                        <th><?= lang('task_assigned') ?></th>
                                        <th><?= lang('tags') ?></th>
                                        <th><?= lang('tasks_list_priority') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <?php if ($isAdmin) : ?>
                    <div role="tabpanel" class="tab-pane" id="view-cost-material">
                        <section>
                            <table id="tb-cost-material" class="table dt-tnh table-hover table-condensed dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('tnh_material_code') ?></th>
                                        <th><?= lang('tnh_material_name') ?></th>
                                        <th><?= lang('tnh_unit') ?></th>
                                        <th><?= lang('Số lượng xuất') ?></th>
                                        <th><?= lang('Số thu hồi') ?></th>
                                        <th><?= lang('CP xuất NVL') ?></th>
                                        <th><?= lang('CP thu hồi') ?></th>
                                        <th><?= lang('tnh_total_amount') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </section>
                    </div>
                <?php endif ?>
                <div role="tabpanel" class="tab-pane" id="view-history-detail-productions">
                    <section>
                        <div class="table-responsive">
                            <table id="tb-history-detail" class="table dt-tnh table-hover  table-condensed" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('type') ?></th>
                                        <th><?= lang('tnh_content') ?></th>
                                        <th><?= lang('tnh_staff_action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <?php //echo form_close(); 
        ?>
    </div>
</div>

<input type="hidden" name="pod_id" id="pod_id" class="form-control" value="<?= $id ?>">
<input type="hidden" name="pois_id" id="pois_id" class="form-control" value="<?= $production_detail['productions_orders_item_id'] ?>">

<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script src="<?= js('hightcharts/highcharts.js') ?>"></script>
<script src="<?= js('hightcharts/exporting.js') ?>"></script>
<script src="<?= js('hightcharts/export-data.js') ?>"></script>
<script src="<?= js('hightcharts/accessibility.js') ?>"></script>

<style type="text/css">
    .group.group-start {
        font-weight: bold;
        background: #ddd;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?= css('daterangepicker.css') ?>" />
<script type="text/javascript">
    var csrf_token_name = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    var fnserverparams = {};
    var dtMaterial = '';
    var dtSuggest = '';
    var dtStock = '';
    var dateCreated = '<?= $production_detail['date_created'] ?>';
    var actionsUrl = '<?= $this->input->get('actions') ?>';
</script>
<script type="text/javascript">
    $(document).ready(function() {
        if (actionsUrl) {
            animationElement('#' + actionsUrl, 0);
        }
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        dtSuggest = tnhDatatable(
            '#tb-suggest', {
                'order': [
                    [2, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // scrollY: true,
                // scrollX: true,
                responsive: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getSuggestExportingDetail/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                    st = aData[7];
                    type = aData[8];
                    if (st != "un_approved" || type == 3) {
                        $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                    }

                    if (st != "un_approved") {
                        $(nRow).find('.tnh-delete').addClass('tnh-disabled');
                    }

                    // if (type == 3) {
                    // 	$(nRow).find('.tnh-delete').addClass('tnh-disabled');
                    // }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();

                    iTotalRecords = json.iTotalRecords;
                    $('.count-suggest').html(iTotalRecords);
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var info = this.api().page.info();
                    $('.count-suggest').html(info.recordsTotal);
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center'
                    },
                    {
                        "targets": 1,
                        "name": 'id',
                        'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2,
                        "name": 'date',
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a class="tnh-modal" title="<?= lang('view') ?>" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/manufactures/view_suggest_exporting/') ?>' +
                                row[1] + '">' + data + '</a>';
                        },
                        "targets": 3,
                        "name": 'reference_no'
                    },
                    {
                        "render": function(data, type, row) {
                            type = row[8];
                            label = 'success';
                            typeText = '<?= lang('tnh_tbom') ?>';
                            if (type == 3) {
                                label = 'primary';
                                typeText = '<?= lang('tnh_additional') ?>';
                            }
                            return '<div class="mbot5">' + data + '</div><div class="label label-' +
                                label + '">' + typeText + '</div>';
                        },
                        "targets": 4,
                        "name": 'export_name'
                    },
                    {
                        "targets": 5,
                        "name": 'note'
                    },
                    {
                        "targets": 6,
                        "name": 'created_by'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data == "un_approved") {
                                str =
                                    '<span class="label label-danger"><?= lang('un_approved') ?></span>';
                            } else if (data == "approved") {
                                str =
                                    '<span class="label label-success"><?= lang('approved') ?></span>';
                            }
                            return str;
                        },
                        "targets": 7,
                        "name": 'status'
                    },
                    {
                        "targets": 8,
                        "name": 'type',
                        'visible': false
                    },
                    {
                        "targets": 9,
                        "name": 'actions',
                        'searchable': false,
                        'sortable': false,
                        'width': '80px',
                    },
                ]
            }
        );
        $(document).on('click', '#tb-suggest_wrapper .btn-dt-reload', function(event) {
            dtSuggest.draw('page');
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        dtStock = tnhDatatable(
            '#tb-stock', {
                'order': [
                    [2, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                responsive: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getExportStockByDetailId/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                    st = aData[6];
                    if (st != "un_approved") {
                        $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();

                    iTotalRecords = json.iTotalRecords;
                    $('.count-stock').html(iTotalRecords);
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var info = this.api().page.info();
                    $('.count-stock').html(info.recordsTotal);
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center'
                    },
                    {
                        "targets": 1,
                        "name": 'id',
                        'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2,
                        "name": 'date',
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a class="tnh-modal" title="<?= lang('view') ?>" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/stock/view_exporting_production/') ?>' +
                                row[1] + '">' + data + '</a>';
                        },
                        "targets": 3,
                        "name": 'reference_stock'
                    },
                    {
                        "targets": 4,
                        "name": 'export_name'
                    },
                    {
                        "targets": 5,
                        "name": 'note'
                    },
                    {
                        "targets": 6,
                        "name": 'created_by'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data == "un_approved_stock") {
                                str =
                                    '<span class="label label-danger"><?= lang('un_approved_stock') ?></span>';
                            } else if (data == "approved") {
                                str =
                                    '<span class="label label-success"><?= lang('approved') ?></span>';
                            }
                            return '<div class="text-center">' + str + '</div>';
                        },
                        "targets": 7,
                        "name": 'status'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data > 0) {
                                str = '<div class="label label-primary"><?= lang('tnh_approved_ws_stock') ?></div>';
                            } else {
                                str = '<div class="label label-danger"><?= lang('tnh_un_approved_ws_stock') ?></div>';
                            }
                            return '<div class="text-center">' + str + '</div>';
                        },
                        "targets": 8,
                        "name": 'status_warehouse'
                    },
                ]
            }
        );
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        dtPurchaseInternal = tnhDatatable(
            '#tb-purchase-internal', {
                'order': [
                    [2, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // "processing": true,
                scrollY: true,
                scrollX: true,
                // responsive: true,
                // autoWidth: true,
                fixedColumns: {
                    leftColumns: 4,
                    rightColumns: 0
                },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getPurchaseInternalByDetailProductions/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();

                    iTotalRecords = json.iTotalRecords;
                    $('.count-purchase-internal').html(iTotalRecords);
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var info = this.api().page.info();
                    $('.count-purchase-internal').html(info.recordsTotal);

                    var quantity = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity += intVal(aaData[i][10]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[9].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(quantity) +
                        '</div>';
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center'
                    },
                    {
                        "targets": 1,
                        "name": 'id',
                        'visible': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2,
                        "name": 'date',
                        'searchable': false,
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 3,
                        "name": 'reference_no',
                        'width': '140px'
                    },
                    {
                        "targets": 4,
                        "name": 'enter_name',
                        'width': '100px'
                    },
                    {
                        "targets": 5,
                        "name": 'warehouse_name',
                        'width': '150px'
                    },
                    {
                        "targets": 6,
                        "name": 'item_code',
                        'width': '120px'
                    },
                    {
                        "targets": 7,
                        "name": 'item_name',
                        'width': '120px'
                    },
                    {
                        "targets": 8,
                        "name": 'unit_name',
                        'width': '80px'
                    },
                    {
                        "targets": 9,
                        "name": 'location_name',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 10,
                        "name": 'quantity',
                        'width': '100px'
                    },
                    {
                        "targets": 11,
                        "name": 'created_by',
                        'width': '130px'
                    },
                    {
                        "targets": 12,
                        "name": 'note_item',
                        'width': '100px'
                    },
                ]
            }
        );
        $(document).on('click', '#tb-purchase-internal_wrapper .btn-dt-reload', function(event) {
            dtPurchaseInternal.draw('page');
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        dtTableHistoryDetail = tnhDatatable(
            '#tb-history-detail', {
                'order': [
                    [1, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getHistoryProductionsDetail/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {},
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 1,
                        "name": 'date',
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == "productions_orders_details") {
                                return '<?= lang('productions_orders_details') ?>'
                            } else if (data == "suggest_exporting") {
                                return '<?= lang('suggest_exporting') ?>'
                            } else if (data == "check_quality") {
                                return '<?= lang('check_quality') ?>'
                            } else if (data == "purchase_products") {
                                return '<?= lang('purchase_products') ?>'
                            } else if (data == "exporting_producion") {
                                return '<?= lang('exporting_producion') ?>'
                            } else if (data == "purchase_internal") {
                                return '<?= lang('purchase_internal') ?>'
                            } else {
                                return data;
                            }
                        },
                        "targets": 2,
                        "name": 'type'
                    },
                    {
                        "targets": 3,
                        "name": 'content'
                    },
                    {
                        "targets": 4,
                        "name": 'created_by'
                    }
                ]
            }
        );
        $(document).on('click', '#tb-history-detail_wrapper .btn-dt-reload', function(event) {
            dtTableHistoryDetail.draw('page');
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        dtTasks = tnhDatatable(
            '#tb-tasks', {
                'order': [
                    [1, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getTasksPOD/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();

                    iTotalRecords = json.iTotalRecords;
                    $('.count-tasks').html(iTotalRecords);
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var info = this.api().page.info();
                    $('.count-tasks').html(info.recordsTotal);
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data) {
                                arrData = data.split('__');
                                return '<a href="' + site.base_url + 'admin/tasks/view/' + arrData[1] +
                                    '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' +
                                    arrData[1] + '); return false;">' + arrData[0] + '</a>';
                            }
                            return data;
                        },
                        "targets": 1,
                        "name": 'task_name'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + data + '</div>';
                        },
                        "targets": 2,
                        "name": 'name_shift_work'
                    },
                    {
                        "targets": 3,
                        "name": 'task_status'
                    },
                    {
                        "targets": 4,
                        "name": 'task_startdate',
                        'searchable': false
                    },
                    {
                        "targets": 5,
                        "name": 'task_duedate'
                    },
                    {
                        "targets": 6,
                        "name": 'task_assignees'
                    },
                    {
                        "targets": 7,
                        "name": 'task_tags'
                    },
                    {
                        "targets": 8,
                        "name": 'task_priority'
                    },
                ]
            }
        );
        $(document).on('click', '#tb-tasks_wrapper .btn-dt-reload', function(event) {
            dtTasks.draw();
        });


    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        dtTableCostMaterial = tnhDatatable(
            '#tb-cost-material', {
                'order': [
                    [1, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getCostMaterial/' . $id) ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "drawCallback": function(settings, nRow) {},
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_export = 0,
                        quantity_recovered = 0,
                        cpnvl = 0,
                        cpth = 0,
                        total_amount = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_export += intVal(aaData[i][4]);
                        quantity_recovered += intVal(aaData[i][5]);
                        cpnvl += intVal(aaData[i][6]);
                        cpth += intVal(aaData[i][7]);
                        total_amount += intVal(aaData[i][8]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[4].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(
                        quantity_export) + '</div>';
                    nCells[5].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(
                        quantity_recovered) + '</div>';
                    nCells[6].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(cpnvl) +
                        '</div>';
                    nCells[7].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(cpth) + '</div>';
                    nCells[8].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(total_amount) +
                        '</div>';

                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '45px',
                        'className': 'text-center',
                        'searchable': false,
                        'sortable': false
                    },
                    {
                        "targets": 1,
                        "name": 'item_code'
                    },
                    {
                        "targets": 2,
                        "name": 'item_name'
                    },
                    {
                        "targets": 3,
                        "name": 'name_unit',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 4,
                        "name": 'quantity_export',
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 5,
                        "name": 'quantity_recovered',
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 6,
                        "name": 'cpnvl',
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'cpth',
                        "searchable": false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'total_amount',
                        "searchable": false
                    }
                ]
            }
        );
        $(document).on('click', '#tb-cost-material_wrapper .btn-dt-reload', function(event) {
            dtTableCostMaterial.draw('page');
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'input[name="tabset"]', function(event) {
            tab = $(this).attr('id');
            localStorage.setItem("tab", tab);
        });
        if (localStorage.getItem("tab")) {
            $('#' + localStorage.getItem("tab")).trigger('click');
        }
    });
</script>
<script type="text/javascript">
    var i = 0;
    var employees = <?= json_encode($employees) ?>;

    function getEmployees(selected_id = 0) {
        var options = '<option></option>';
        $.each(employees, function(index, el) {
            selected = selected_id == el.staffid ? 'selected' : '';
            options += '<option ' + selected + ' value="' + el.staffid + '">' + el.fullname + '</option>';
        });
        return options;
    }
</script>

<script type="text/javascript">
    //purchase internal
    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        if (item_id) {
            console.log(data);
            tr = $(sl).closest('tr');
            name = data.name;
            unit_name = data.unit_name;
            unit_id = data.unit_id;
            price_import = data.price_import;

            tr.find('.price').val(price_import);
            tr.find('.td-total-internal').val(price_import);
            tr.find('.unit_id').val(unit_id);
            tr.find('.td-item-name').html(name);
            tr.find('.td-unit').html(unit_name);
            $(row).find('select.locations').html(locations);

            lastrow = $('#tb-items tbody tr')[$('#tb-items tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url + 'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url + 'assets/images/tnh/no_image.png');
        }
        totalPurchaseInternal();
    });

    $(document).on('change', '.quantity-internal, .price-internal', function(event) {
        event.preventDefault();
        totalPurchaseInternal();
    });

    $(document).on('click', '.remove-row', function(event) {
        event.preventDefault();
        dt.row($(this).parents('tr')).remove().draw();
        totalPurchaseInternal();
    });

    $(document).on('change', '#productions_orders_search, #items_search, #start_date_search, #end_date_search', function() {
        oTable.draw();
    });
</script>

<script>
    function agreeProcess(_this, c_pois_id, c_status = 1, type = 0, _actions = '') {
        if (type == 0) {
            var cTitle = '<?= lang('Bạn có muốn duyệt hoàn thành giai đoạn này ?') ?>';
            if (c_status == 0) {
                cTitle = '<?= lang('Bạn có muốn bỏ duyệt hoàn thành giai đoạn này ?') ?>';
            }
            bootbox.confirm({
                message: cTitle,
                buttons: {
                    confirm: {
                        label: '<?= lang('tnh_update') ?>',
                        className: 'btn-primary'
                    },
                    cancel: {
                        label: '<?= lang('close') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result == true) {
                        $.ajax({
                            type: "POST",
                            url: site.base_url + 'admin/manufactures/agreeProcess',
                            data: {
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                pois_id: c_pois_id,
                                status: c_status,
                                pod_id: '<?= $id ?>',
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.result == 1) {
                                    alert_float('success', response.message);
                                } else if (response.result == 2) {
                                    alert_float('danger', response.message);
                                    $.ajax({
                                            url: site.base_url + 'admin/manufactures/notiPurchaseProducts',
                                            type: 'POST',
                                            dataType: 'html',
                                            data: {
                                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                                pod_id: '<?= $id ?>',
                                                result: response.result,
                                                pois_id: c_pois_id,
                                                cqi_id: 0
                                            },
                                        })
                                        .done(function(data) {
                                            $('#tnhModal').html(data);
                                        })
                                        .fail(function() {
                                            console.log("error");
                                        });
                                    $('#tnhModal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                } else if (response.result == 0) {
                                    alert_float('danger', response.message);
                                }
                                tbExportMaterials.draw();
                                $('.td-process').html(response.process);
                            }
                        });
                    }
                }
            });
        } else {
            if (type == "begins_production") {
                var cTitle = '<?= lang('Bạn có muốn bắt đầu sản xuất giai đoạn này ?') ?>';
                if (c_status == 0) {}
                bootbox.confirm({
                    message: cTitle,
                    buttons: {
                        confirm: {
                            label: '<?= lang('tnh_update') ?>',
                            className: 'btn-primary'
                        },
                        cancel: {
                            label: '<?= lang('close') ?>',
                            className: 'btn-danger'
                        }
                    },
                    callback: function(result) {
                        if (result == true) {
                            $.ajax({
                                type: "POST",
                                url: site.base_url + 'admin/manufactures/agreeProcess',
                                data: {
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                    pois_id: c_pois_id,
                                    status: c_status,
                                    type: type,
                                    pod_id: '<?= $id ?>',
                                },
                                dataType: "json",
                                success: function(response) {
                                    if (response.result == 1) {
                                        alert_float('success', response.message);
                                    } else if (response.result == 2) {} else if (response.result == 0) {
                                        alert_float('danger', response.message);
                                    }
                                    // tbExportMaterials.draw();
                                    $('.td-process').html(response.process);
                                }
                            });
                        }
                    }
                });
            } else if (type == 1) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/handlingSemiProduct',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            pod_id: '<?= $id ?>',
                            type: type,
                            pois_id: c_pois_id,
                            actions: _actions
                        },
                    })
                    .done(function(data) {
                        $('#tnhModal').html(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
                $('#tnhModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            } else if (type == 2) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/handlingProducts',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            pod_id: '<?= $id ?>',
                            type: type,
                            pois_id: c_pois_id,
                            actions: _actions
                        },
                    })
                    .done(function(data) {
                        $('#tnhModal').html(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
                $('#tnhModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            } else if (type == 3) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/handlingProductsStep',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            pod_id: '<?= $id ?>',
                            type: type,
                            pois_id: c_pois_id,
                            actions: _actions
                        },
                    })
                    .done(function(data) {
                        $('#tnhModal').html(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
                $('#tnhModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            } else if (type == 6) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures_temp/handlingPrepareMaterials',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            pod_id: '<?= $id ?>',
                            type: type,
                            pois_id: c_pois_id,
                            actions: _actions
                        },
                    })
                    .done(function(data) {
                        $('#tnhModal').html(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
                $('#tnhModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
        }
    }

    function agreeProcessRemake(_this, c_cqi_id, c_status, type, c_pois_id, c_quantity = 0, c_cqis_id = 0) {
        if (type == 0 || c_status == 0) {
            var cTitle = '<?= lang('Bạn có muốn duyệt hoàn thành giai đoạn sản xuất lại này ?') ?>';
            if (c_status == 0) {
                cTitle = '<?= lang('Bạn có muốn bỏ duyệt hoàn thành giai đoạn sản xuất lại này ?') ?>';
            }
            bootbox.confirm({
                message: cTitle,
                buttons: {
                    confirm: {
                        label: '<?= lang('tnh_update') ?>',
                        className: 'btn-primary'
                    },
                    cancel: {
                        label: '<?= lang('close') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result == true) {
                        $.ajax({
                            type: "POST",
                            url: site.base_url + 'admin/manufactures/agreeProcessRemake',
                            data: {
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                cqi_id: c_cqi_id,
                                status: c_status,
                                pois_id: c_pois_id,
                                type: type,
                                cqis_id: c_cqis_id,
                                pod_id: '<?= $id ?>',
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.result == 1) {
                                    alert_float('success', response.message);
                                } else if (response.result == 2) {
                                    alert_float('danger', response.message);
                                    $.ajax({
                                        url: site.base_url + 'admin/manufactures/notiPurchaseProducts',
                                        type: 'POST',
                                        dataType: 'html',
                                        data: {
                                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                                            pod_id: '<?= $id ?>',
                                            result: response.result,
                                            pois_id: c_pois_id,
                                            cqi_id: c_cqi_id
                                        },
                                    })
                                    .done(function(data) {
                                        $('#tnhModal').html(data);
                                    })
                                    .fail(function() {
                                        console.log("error");
                                    });
                                    $('#tnhModal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                } else if (response.result == 0) {
                                    alert_float('danger', response.message);
                                }
                                tbExportMaterials.draw();
                                $('.td-process').html(response.process);
                            }
                        });
                    }
                }
            });
        } else if (type == 1) {
            $.ajax({
                    url: site.base_url + 'admin/manufactures/handlingSemiProduct',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        pod_id: '<?= $id ?>',
                        type: type,
                        pois_id: c_pois_id,
                        cqi_id: c_cqi_id,
                        quantity: c_quantity,
                        cqis_id: c_cqis_id,
                        actions: 'qc'
                    },
                })
                .done(function(data) {
                    $('#tnhModal').html(data);
                })
                .fail(function() {
                    console.log("error");
                });
            $('#tnhModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else if (type == 2) {
            $.ajax({
                    url: site.base_url + 'admin/manufactures/handlingProducts',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        pod_id: '<?= $id ?>',
                        type: type,
                        pois_id: c_pois_id,
                        cqi_id: c_cqi_id,
                        quantity: c_quantity,
                        cqis_id: c_cqis_id,
                        actions: 'qc'
                    },
                })
                .done(function(data) {
                    $('#tnhModal').html(data);
                })
                .fail(function() {
                    console.log("error");
                });
            $('#tnhModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        } else if (type == 3) {
            $.ajax({
                    url: site.base_url + 'admin/manufactures/handlingProductsStep',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        pod_id: '<?= $id ?>',
                        type: type,
                        pois_id: c_pois_id,
                        cqi_id: c_cqi_id,
                        quantity: c_quantity,
                        cqis_id: c_cqis_id,
                        actions: 'qc'
                    },
                })
                .done(function(data) {
                    $('#tnhModal').html(data);
                })
                .fail(function() {
                    console.log("error");
                });
            $('#tnhModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    }
</script>


<?php include_once(APPPATH . 'views/admin/feedback/order_production_details/feedback_js.php'); ?>
<script type="text/javascript">
    <?php if (!empty($bot)) { ?>
        $(document).ready(function() {
            animationElement('#comment-detail', 300);
        });
        // $(function() {
        // 	setTimeout(function(e) {
        // 		window.scrollTo(0, document.body.scrollHeight);
        // 	}, 1500)
        // })
    <?php } ?>
</script>

<script>
    function loadDataTable(type_table) {
        if (type_table == "view-purchase-internal") {
            dtPurchaseInternal.draw();
        }
    }

    var tbExportMaterials = '';
    var paramsExportMaterials = {
        pod_id: '#pod_id',
        pois_id: '#pois_id'
    };
    $(document).ready(function() {
        tbExportMaterials = tnhInitDataTable('#tb-export-materials-warehouses',
            '<?= site_url('admin/manufactures/getExportMaterialsWarehouses') ?>', {
                'order': [
                    [1, 'desc']
                ],
                // 'fixedHeader': {
                //     header: true,
                // },
                dom: "<'row'><'row'<'col-md-7'lB><'col-md-12'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
                'responsive': true,
                "ajax": {
                    "url": '<?= site_url('admin/manufactures/getExportMaterialsWarehouses') ?>',
                    "type": "POST",
                    "data": function(d) {
                        if (typeof(csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in paramsExportMaterials) {
                            d[key] = $(paramsExportMaterials[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function(json) {
                        return json.aaData;
                    }
                },
                "columnDefs": [{
                        "targets": 0,
                        'width': '150px'
                    },
                    {
                        "targets": 1,
                        'width': '80px'
                    },
                ]
            });
    });
    $('.modal').on('scroll', function() {
        var sticky = $('table.table-items-check > thead'),
            scroll = $(window).scrollTop();
        var scrollModal = $('#tnhModal').scrollTop();
        if ((scrollModal + 200) >= (scroll)) {
            sticky.addClass('fixed');
            sticky.css({
                top: (scrollModal - 318),
            });
        } else {
            sticky.removeClass('fixed');
        }
    });
</script>

<script>
    var tbMaterials = '';
    var fnserverparamsMaterials = {
        pod_id: '#pod_id',
        pois_id: '#pois_id'
    };
    $(document).ready(function() {
        tbMaterials = tnhInitDataTable('#tb-materials', '<?= site_url('admin/manufactures/getMaterialDetailProductions') ?>', {
            // 'order': [
            //     [1, 'desc']
            // ],
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getMaterialDetailProductions') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsMaterials) {
                        d[key] = $(fnserverparamsMaterials[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    $('#tb-materials tfoot tr td:nth-child(4)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantity)}</div>`);
                    $('#tb-materials tfoot tr td:nth-child(5)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityExport)}</div>`);
                    $('#tb-materials tfoot tr td:nth-child(6)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityRest)}</div>`);
                    $('#tb-materials tfoot tr td:nth-child(7)').html(`<div class="text-center">${tnhFormatNumber(json.totalQuantityReCall)}</div>`);
                    init_chart_materials();
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
                if (data[0] === 'group') {
                    $('td:eq(0)', row).attr('colspan', 7);
                    $('td:eq(1)', row).css('display', 'none');
                    $('td:eq(2)', row).css('display', 'none');
                    $('td:eq(3)', row).css('display', 'none');
                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');
                    this.api().cell($('td:eq(0)', row)).data(data[1]);
                    $(row).addClass('bg-group bold');
                }
            },
        });
    });
</script>
<script>
    function loadTable(cType) {
        if (cType == 'situation_ex_material') {
            tbMaterials.draw();
        }
    }

    var chartMaterials;
    function init_chart_materials() {

        pageMaterials = tbMaterials.page.info();
        start = pageMaterials.start;
        end = pageMaterials.end;
        dataString = {
            pod_id: $('#pod_id').val(),
            pois_id: $('#pois_id').val(),
            start: start,
            end: end,
            [csrfData['token_name']]: csrfData['hash']
        };

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/init_chart_materials',
            data: dataString,
            dataType: "json",
            success: function(response) {
                chartMaterials = Highcharts.chart('stacked_materials', {
                    style: {
                        fontFamily: 'Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif'
                    },
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<?= lang('tnh_situation_ex_material') ?>'
                    },
                    xAxis: {
                        categories: response.categories
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '<?= lang('quantity') ?>'
                        },
                        stackLabels: {
                            enabled: false,
                            style: {
                                fontWeight: 'bold',
                                color: ( // theme
                                    Highcharts.defaultOptions.title.style &&
                                    Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                            }
                        }
                    },
                    legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}'
                    },
                    // <br/>Total: {point.stackTotal}
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [
                        {
                            name: '<?= lang('tnh_plan') ?>',
                            data: response.dataQuantity,
                            color: '#008000',
                        }, 
                        {
                            name: '<?= lang('tnh_exported') ?>',
                            data: response.dataQuantityExport,
                            color: '#ff6f00',
                        }, 
                        {
                            name: '<?= lang('tnh_recall') ?>',
                            data: response.dataQuantityRecall,
                            color: '#337ab7',
                        }
                    ]
                });
            }
        });
    }

    $(document).ready(function() {
    });
</script>