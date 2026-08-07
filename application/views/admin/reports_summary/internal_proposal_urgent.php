<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #menu {
        width: 0% !important;
    }
    #wrapper {
        margin-left: 0px !important;
    }
    .text-danger_v2 {
        color: red!important;
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
                                               style="width: 100%;" value="<?=_d(date('Y-m-01'))?>">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="<?=_d(date('Y-m-t'))?>">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-device" class="table dt-tnh table-internal_proposal_urgent" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Người Xác Nhận Gấp Khẩn') ?></th>
                                            <th class="text-center"><?= lang('Loại Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Mã Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Giờ Lập Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Giờ Duyệt Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Giờ Duyệt Thực Thi') ?></th>
                                            <th class="text-center"><?= lang('Hoàn Thành Đề Xuất') ?></th>
                                            <th class="text-center"><?= lang('Kiểm Tra Hoàn Thành') ?></th>
                                            <th class="text-center"><?= lang('Tra Soát Hoàn Thành') ?></th>
                                            <th class="text-center"><?= lang('Mã BCKPH') ?></th>
                                            <th class="text-center"><?= lang('Xử Lý Vi Phạm') ?></th>
                                            <th class="text-center"><?= lang('Qui Trình Phòng Ngừa') ?></th>
                                            <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
                                            <th class="text-center"><?= lang('Ngày Điều Chỉnh Foso') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Thời Gian Tái Vi Phạm') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Ngày Tái Vi Phạm') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Chi Phí Thiệt Hại') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Hình Thức Kỷ Luật') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Ngày Áp Dụng Kỷ Luật') ?></th>
                                            <th class="text-center text-danger_v2"><?= lang('Ngày Hoàn Thành Lưu Trữ') ?></th>
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
    oTable = initDataTableCustom('.table-internal_proposal_urgent', admin_url+'reports_summary/getInternalProposalUrgent', [4,5,6,7,8,9,10,11,12], [4,5,6,7,8,9,10,11,12], CustomersServerParams,[0, 'desc'], fixedColumns = {leftColumns: 0, rightColumns: 0});

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
            url: site.base_url + 'admin/reports_summary/getInternalProposalUrgent',
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