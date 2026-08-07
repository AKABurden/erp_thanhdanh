<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .main-report h2 {
        color: #2b6fa2;
        font-size: 23px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo _l('productions_reports'); ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php if ($this->perViewMaterialNorms): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'material_norms')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_of_material_norms'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewUsageMaterial): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'usage_material')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_the_usage_material'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewProductionDetailed): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'production_detailed')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_production_detailed'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewGeneralProduction): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'general_production_new')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_general_production'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
<!--                                    --><?php //if ($this->perViewUseMlAcProductionOrders): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'order_progress')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('Báo cáo tình trạng đơn hàng'); ?>
                                            </a>
                                        </div>
<!--                                    --><?php //endif ?>
<!--                                    --><?php //if ($this->perViewUseMlAcProductionOrders): ?>
                                        <div class="col-md-3 hide">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'productions_orders')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_productions_orders'); ?>
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'report_over_production')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('Báo cáo sản xuất thừa'); ?>
                                            </a>
                                        </div>
<!--                                    --><?php //endif ?>
                                    <!-- <?php //if ($this->perViewUseMlAcProductionOrders): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'productions_orders_detail')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= ''//_l('report_productions_orders_detail'); ?>
                                            </a>
                                        </div>
                                    <?php //endif ?> -->
                                    <!-- <?php //if ($this->perViewUseMlAcProductionOrders): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'productions_products')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= ''//_l('report_productions_orders_detail'); ?>
                                            </a>
                                        </div>
                                    <?php //endif; ?> 
                                    <?php //if ($this->perViewUseMlAcProductionOrders): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'productions_products_category')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= ''//_l('report_productions_orders_detail_1'); ?>
                                            </a>
                                        </div>
                                    <?php //endif; ?>  -->
                                    <?php if (has_permission('manufactures_productions_orders', '', 'view')): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'product_error')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_product_error'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (has_permission('manufactures_productions_orders', '', 'view')): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'complete_stage')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('report_complete_stage'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-heading"/>
                        <div class="main-report">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript">

    function loadReport(el, view) {
        $.ajax({
            url: '<?= base_url('admin/reports/loadReport') ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                view: view,
                'start_date' : '<?= $start_date ?>',
                'end_date' : '<?= $end_date ?>',
                'search' : '<?= $search ?>',
                "<?= $this->security->get_csrf_token_name() ?>" : "<?= $this->security->get_csrf_hash() ?>"
            },
        })
        .done(function(data) {
            $('.main-report').html(data);
            $('head title').html($('.text-center.uppercase').find('h2').text());
        })
        .fail(function() {
            console.log("error");
        });
    }

    <?php
    if ($search == "situation_order_execution") {
        echo "loadReport(this, 'situation_order_execution')";
    }
    ?>

</script>