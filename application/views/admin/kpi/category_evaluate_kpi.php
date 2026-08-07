<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-category-kpi tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }
    #table-category-kpi tr td:nth-child(5) {
        width: 150px;
        white-space: unset;
        text-align: center;
    }
    .content-text {
        display: block;
        max-height: 35px; /* Hạn chế chiều cao ban đầu */
        overflow: hidden; /* Ẩn nội dung thừa */
        text-overflow: ellipsis; /* Thêm dấu "..." */
        transition: max-height 0.3s ease;
    }
    .title-header{
        font-size: 16px;
        font-weight: 500;
    }
    .table tbody tr:first-child {
        border-top: 1px solid #cedae6 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="col-md-12">
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-8">
                            <div class="clearfix"></div>
                            <div>
                                <table id="table-category_evaluate_kpi" class="table table-hover dataTable dont-responsive-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center" width="50px">STT</th>
                                        <th class="text-center">Tên</th>
                                        <th class="text-center" style="width: 100px">Số điểm từ</th>
                                        <th class="text-center" style="width: 100px">Số điểm đến</th>
                                        <th class="text-center" style="width: 100px">Màu sắc</th>
                                        <th class="text-center" style="width: 200px">Thưởng</th>
                                        <th class="text-center" style="width: 200px">Kỷ luật</th>
                                    </tr>
                                    </thead>
                                    <tbody id="html_category_evaluate_kpi">

                                    </tbody>
                                </table>
                            </div>
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
<script>

    $(document).ready(function() {
        category_evaluate_kpi();
    });

    function updateCategoryEvaluateKpi(_this,category_evaluate_kpi_id,name){
        $.ajax({
            type: 'POST',
            url: admin_url+'kpi/updateCategoryEvaluateKpi',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                'category_evaluate_kpi': category_evaluate_kpi_id,
                'name': name,
                'value': $(_this).val(),
            },
            dataType: "JSON",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                category_evaluate_kpi();
            }
        });
    }

    function category_evaluate_kpi() {
        $.ajax({
            type: 'POST',
            url: admin_url+'kpi/view_category_evaluate_kpi',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
            },
            dataType: "JSON",
            success: function (response) {
                $('tbody#html_category_evaluate_kpi').html(response.html);
            }
        });
    }

</script>