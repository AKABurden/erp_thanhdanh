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
                                <h3 class="panel-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo _l('sales_reports'); ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <?php if ($this->perViewOrdersOfQuotes): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'orders_of_quotes')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_report_orders_of_quotes'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewDeliverySchedules): ?>
                                        <div class="col-md-3 hide">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'delivery_schedules')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_report_delivery_schedules'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'detail_delivery')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Chi tiết giao hàng'); ?>
                                        </a>
                                    </div>
                                    <?php if ($this->perViewSalesOfOrder): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'sales_of_order')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_sales_of_order'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewNearestSellingPrice): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'nearest_selling_price')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_nearest_selling_price'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewReturnedGoods): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'returned_goods')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_report_returned_goods'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewOrderStatus): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'order_status')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_report_order_status'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewSalesAnalysis): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'sales_analysis')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('sales_analysis'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewSellingDiary): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'selling_diary')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('selling_diary'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($this->perViewSellingDiary): ?>
                                        <div class="col-md-3">
                                            <a href="#" class="font-medium" onclick="loadReport(this, 'sales_report')">
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <?= _l('tnh_sales_report'); ?>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-heading"/>
                        <div class="main-report">
                            <?php $this->load->view('admin/reports/sales/report_chart'); ?>
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
    $(document).on('change', 'input[name="report-from"]', (e)=>{
        var val = $('input[name="report-from"]').val();
        var report_to_val = $('input[name="report-to"]').val();
        if (val != '') {
            if (report_to_val != '') {
             init_dashboard_report();
             get_total_limit();
           }
           $('input[name="report-to"]').attr('disabled', false);
        } else {
           $('input[name="report-to"]').attr('disabled', true);
        }
    });
    $('input[name="report-to"]').on('change', function() {
        var val = $('input[name="report-to"]').val();
        if (val != '') {
            init_dashboard_report();
            get_total_limit();
        }
    });
    $('select[name="search_id_staff[]"]').on('change', function() {
        var val = $('select[name="search_id_staff[]"]').val();
            init_dashboard_report();
            get_total_limit();
    });
    $('[name="customers_ch"]').on('change', function() {
            init_dashboard_report();
            get_total_limit();
    });
    function loadReport(el, view) {
        $.ajax({
            url: '<?= base_url('admin/reports/loadSalesReport') ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                view: view,
                'start_date' : '<?= $start_date ?>',
                'end_date' : '<?= $end_date ?>',
                'search' : '<?= $search ?>',
                'customers' : '<?= $customers ?>',
                'orders' : '<?= $orders ?>',
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
    if ($search == "delivery_schedules") {
        echo "loadReport(this, 'delivery_schedules')";
    }
    ?>
    ajaxSelectParams('#customers_ch', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
</script>