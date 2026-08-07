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
                                <table id="table-device" class="table dt-tnh table-human" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Nhân Viên') ?></th>
                                            <th class="text-center"><?= lang('Chi Nhánh') ?></th>
                                            <th class="text-center"><?= lang('Phòng Ban') ?></th>
<!--                                            <th class="text-center">--><?php //= lang('Số Lượng Nhân Viên') ?><!--</th>-->
                                            <th class="text-center"><?= lang('Tổng Số Giờ Công') ?></th>
                                            <th class="text-center"><?= lang('Phép') ?></th>
                                            <th class="text-center"><?= lang('Vi Phạm') ?></th>
                                            <th class="text-center"><?= lang('Khen Thưởng') ?></th>
                                            <th class="text-center"><?= lang('Kỷ Luật') ?></th>
                                            <th class="text-center"><?= lang('Tăng Ca') ?></th>
                                            <th class="text-center"><?= lang('Lương Căn Bản') ?></th>
                                            <th class="text-center"><?= lang('Lương Năng Lực') ?></th>
                                            <th class="text-center"><?= lang('Lương Thực') ?></th>
                                            <th class="text-center"><?= lang('BHXH') ?></th>
                                            <th class="text-center"><?= lang('BHYT') ?></th>
                                            <th class="text-center"><?= lang('Phúc Lợi') ?></th>
                                            <th class="text-center"><?= lang('Công Đoàn') ?></th>
                                            <th class="text-center"><?= lang('Thuế TNCN') ?></th>
                                            <th class="text-center"><?= lang('Đang Hoạt Động') ?></th>
                                            <th class="text-center"><?= lang('Ngừng Việc') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
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
    oTable = initDataTableCustom('.table-human', admin_url+'reports_summary/getHuman', [0], [0], CustomersServerParams,[0, 'desc'], fixedColumns = {leftColumns: 0, rightColumns: 0});

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
            url: site.base_url + 'admin/reports_summary/getHumanExcel',
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