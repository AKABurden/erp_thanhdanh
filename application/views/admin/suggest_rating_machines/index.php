<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-suggest-rating-process tr th:nth-child(1) {
        width: 40px;
    }
    #table-suggest-rating-machines tr th:nth-child(2) {
        width: 100px;
    }
    #table-suggest-rating-machines tr th:nth-child(7) {
        width: 120px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <?php if ($this->preAddSuggestRatingMachines): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_rating_machines/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                <?php endif ?>
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
                            <div class="table-responsive">
                                <table id="table-suggest-rating-machines" class="table dt-tnh table-suggest-rating-machines-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Mã Thiết Bị/Công Việc') ?></th>
                                        <th class="text-center"><?= lang('Tên Thiết Bị/Công Việc') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Năng Suất/Tháng') ?></th>
                                        <th class="text-center"><?= lang('Tiêu Chuẩn') ?></th>
                                        <th class="text-center"><?= lang('Phương Pháp Kiểm') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Năng Suất/h') ?></th>
                                        <th class="text-center"><?= lang('Ngày Bắt Đầu Bảo Trì') ?></th>
                                        <th class="text-center"><?= lang('Khổ Vận Hành') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Chuẩn Bị (Giờ))') ?></th>
                                        <th class="text-center"><?= lang('Thông số kỹ thuật)') ?></th>
                                        <th class="text-center"><?= lang('Quy Trình') ?></th>
                                        <th class="text-center"><?= lang('Bộ Phận Máy Móc') ?></th>
                                        <th class="text-center"><?= lang('Số Ngày Cần Bảo Trì') ?></th>
                                        <th class="text-center"><?= lang('Ghi Chú Cách Thức Bảo Trì') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Thời Gian Duyệt Màu') ?></th>
                                        <th class="text-center"><?= lang('Nhóm Công Đoạn') ?></th>
                                        <th class="text-center"><?= lang('NPL canh bài') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái Duyệt') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái Hoành Thành') ?></th>
                                        <th class="text-center"><?= lang('Nội Dung Đánh Giá') ?></th>
                                        <th class="text-center"><?= lang('Kết Quả') ?></th>
                                        <th class="text-center"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                                        <th class="text-center"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
                                        <th class="text-center"><?= lang('Người Tạo') ?></th>
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

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    oTable = tnhInitDataTable('#table-suggest-rating-machines',
        '<?= site_url('admin/suggest_rating_machines/getSuggestRatingMachines') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/suggest_rating_machines/getSuggestRatingMachines') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });


    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function agree(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/suggest_rating_machines/agree',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });

    }

     function agreeFinish(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/suggest_rating_machines/agreeFinish',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });

    }

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_rating_machines/exportExcel',
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