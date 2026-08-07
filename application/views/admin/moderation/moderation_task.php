<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-moderation-task tr th:nth-child(6) {
        width: 50px;
    }

    #table-moderation-task tr th:nth-child(2) {
        width: 90px;
    }

    #table-moderation-task tr th:nth-child(3) {
        width: 60px;
    }

    #table-moderation-task tr th:nth-child(4) {
        width: 60px;
    }

    #table-moderation-task tr th:nth-child(10) {
        width: 90px;
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
                                    <div class="col-md-3">
                                        <?php echo render_select('room_task[]', !empty($room) ? $room : [], ['id', 'name', 'code'], 'Phòng', '', ['multiple' => true]) ?>
                                    </div>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation-task" class="table dt-tnh table-moderation-task-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Số Phiếu Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Mã Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Tên Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tạo') ?></th>
                                            <th class="text-center"><?= lang('Phiếu yêu câu') ?></th>
                                            <th class="text-center"><?= lang('Phòng ban') ?></th>
                                            <th class="text-center"><?= lang('Chi Tiết Công Việc') ?></th>
                                            <th class="text-center"><?= lang('Quy Trình') ?></th>
                                            <th class="text-center"><?= lang('Quy Định') ?></th>
                                            <th class="text-center"><?= lang('Ngày Bắt Đầu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hoàn Thành') ?></th>
                                            <th class="text-center"><?= lang('Hạn Chót') ?></th>
                                            <th class="text-center"><?= lang('Người Giao Việc') ?></th>
                                            <th class="text-center"><?= lang('Người Được Phân Công') ?></th>
                                            <th class="text-center"><?= lang('Mức Độ Ưu Tiên') ?></th>
                                            <th class="text-center"><?= lang('Trạng Thái') ?></th>
                                            <th class="text-center"><?= lang('Kết Quả') ?></th>
                                            <th class="text-center"><?= lang('Định mức thời gian') ?></th>
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
        room_task: '[name="room_task[]"]',
    };
    oTable = tnhInitDataTable('#table-moderation-task',
        '<?= site_url('admin/moderation/getModerationTask') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/moderation/getModerationTask') ?>',
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
            "createdRow": function(row, data, index) {},
            "columnDefs": [],
        });

    $(document).on('change',
        '#end_date_search,#start_date_search,[name="room_task[]"]',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });


    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        room_task = $('[name="room_task[]"]').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation/exportExcelModerationTask',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                room_task: room_task,
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