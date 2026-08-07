<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #menu {
        width: 0% !important;
    }
    #wrapper {
        margin-left: 0px !important;
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
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-device" class="table dt-tnh table-device" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Yêu cầu bảo dưỡng') ?></th><!-- tbl_suggest_maintenance -->
                                            <th class="text-center"><?= lang('Nhóm Thiết Bị Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Mã Thiết Bị Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Tên Thiết Bị Sản Xuất') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu VS5S') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Dầu') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Nhớt') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Điện') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Cơ') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Khí Nén') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Dàn Lạnh') ?></th>
                                            <th class="text-center"><?= lang('Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Hiệu Chuẩn') ?></th>
                                            <th class="text-center"><?= lang('BCKPH') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Ngưng Máy') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Yêu Cầu Sửa Chữa') ?></th>
                                            <th class="text-center"><?= lang('Mã Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Phiếu Chi') ?></th>
                                            <th class="text-center"><?= lang('Số Tiền') ?></th>
                                            <th class="text-center"><?= lang('VAT') ?></th>
                                            <th class="text-center"><?= lang('Thành Tiền') ?></th>
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
    var CustomersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
        'start_date' : '[name="start_date_search"]',
        'end_date' : '[name="end_date_search"]',
    };
    oTable = initDataTableCustom('.table-device', admin_url+'reports_summary/getDevice', [0], [0], CustomersServerParams,[0, 'desc'], fixedColumns = {leftColumns: 0, rightColumns: 0});

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_summary/getDeviceExcel',
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