<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .main-report h2 {
        color: #2b6fa2;
        font-size: 23px;
    }

    .fixedHeader-locked {
        display: none;
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
                                <h3 class="panel-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo $title; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'quotes_sample')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Báo giá phát triển mẫu/Chi tiết phát triển mẫu'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'total_quotes')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Tổng Số Báo Giá/Chi tiết báo giá'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'quotes_pass')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Báo giá đạt'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'quotes_fail')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Báo giá không đạt'); ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'total_request_sample')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Tổng số YC phát triển mẫu'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'total_sample_pass')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Tổng số mẫu đạt'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'total_sample_fail')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Tổng số mẫu không đạt'); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="#" class="font-medium" onclick="loadReport(this, 'total_sample_orders')">
                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            <?= _l('Tổng số mẫu đánh lần 2 3'); ?>
                                        </a>
                                    </div>
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
            url: '<?= base_url('admin/report_quotes/loadReport') ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                view: view,
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

    $(document).ready(function () {
        <?php if(!empty($_GET['type'])): ?>
            loadReport(this, '<?= $_GET['type'] ?>');
        <?php endif; ?>
    });
</script>