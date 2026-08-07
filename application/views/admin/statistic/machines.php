<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #menu {
        width: 0% !important;
    }
    #wrapper {
        margin-left: 0px !important;
    }
    .pre {
        white-space: pre;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('Ngày bắt đầu (Ngày Tạo)', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Ngày kết thúc (Ngày Tạo)', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-staff" class="table dt-tnh table-machines" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Nhóm Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Mã Máy Móc, Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Tên Máy Móc, Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Số Model') ?></th>
                                            <th class="text-center"><?= lang('Xuất Xứ') ?></th>
                                            <th class="text-center"><?= lang('Năm Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Nhà Cung Cấp') ?></th>
                                            <th class="text-center"><?= lang('Hợp Đồng Mua Bán') ?></th>
                                            <th class="text-center"><?= lang('Ngày Ký Kết') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tiếp Nhận Máy') ?></th>
                                            <th class="text-center"><?= lang('Khổ Vận Hành') ?></th>
                                            <th class="text-center"><?= lang('Quy Trình Vận Hành Máy Móc') ?></th>
                                            <th class="text-center"><?= lang('Tiêu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('Định Mức Năng Suất/h/Tháng') ?></th>
                                            <th class="text-center"><?= lang('Định Mức Thời Gian Canh Bài') ?></th>
                                            <th class="text-center"><?= lang('Định Mức NPL Canh Bài') ?></th>
                                            <th class="text-center"><?= lang('Thẻ Theo Dõi Bảo Dưỡng_Ngày') ?></th>
                                            <th class="text-center"><?= lang('Thẻ Theo Dõi Hiệu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Hiệu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hiệu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tái Tục') ?></th>
                                            <th class="text-center"><?= lang('Ngân Sách Hiệu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Thay Size') ?></th>
                                            <th class="text-center"><?= lang('Tổng Giá Trị') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Khấu Hao') ?></th>
                                            <th class="text-center"><?= lang('Ngày Bắt Đầu Khấu Hao') ?></th>
                                            <th class="text-center"><?= lang('Số Tiền Khấu Hao') ?></th>
                                            <th class="text-center"><?= lang('Giá Trị Còn Lại') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Kết Thúc Khấu Hao') ?></th>
                                            <th class="text-center"><?= lang('Đang Hoạt Động') ?></th>
                                            <th class="text-center"><?= lang('Ngưng Sử Dụng') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tạo') ?></th>
                                            <th class="text-center"><?= lang('Ngày Điều Chỉnh') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
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
    var oTable = '';
    var SuppliersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
        'start_date' : '[name="start_date_search"]',
        'end_date' : '[name="end_date_search"]',
    };
    oTable = initDataTableCustom('.table-machines', admin_url + 'statistic/getMachines', [0], [0], SuppliersServerParams,[0, 'desc'], fixedColumns = {leftColumns: 0, rightColumns: 0});

    $(document).on('change', '#end_date_search, #start_date_search', function(event) {
        event.preventDefault();
        oTable.draw();
    });

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/statistic/getMachinesExcel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>