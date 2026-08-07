<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-suggest-rating-process tr th:nth-child(1) {
        width: 40px;
    }

    #table-moderation-request_client_complaints tr th:nth-child(2) {
        width: 110px;
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
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-2">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <div class="table-responsive">
                                    <table id="table-moderation-request_client_complaints" class="table dt-tnh table-moderation-request_client_complaints-new" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><?= lang('STT') ?></th>
                                                <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                                <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                                <th class="text-center"><?= lang('Tên Brand') ?></th>
                                                <th class="text-center"><?= lang('Khách Hàng') ?></th>
                                                <th class="text-center"><?= lang('Người Khiếu Nại') ?></th>
                                                <th class="text-center"><?= lang('Nhóm Khiếu Nại') ?></th>
                                                <th class="text-center"><?= lang('Chi Tiết Khiếu Nại') ?></th>
                                                <th class="text-center"><?= lang('Người Tiếp Nhận (TD)') ?></th>
                                                <th class="text-center"><?= lang('Định Mức Thời Gian') ?></th>
                                                <th class="text-center"><?= lang('Nguyên Nhân') ?></th>
                                                <th class="text-center"><?= lang('Quy Trình Xử Lý') ?></th>
                                                <th class="text-center"><?= lang('Kết Quả') ?></th>
                                                <th class="text-center"><?= lang('Kết Quả') ?></th>
                                                <th class="text-center"><?= lang('Quy Trình Phòng Ngừa') ?></th>
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

    oTable = tnhInitDataTable('#table-moderation-request_client_complaints',
    '<?= site_url('admin/moderation_request_client_complaints/getModerationRequestClientComplaints') ?>', {
        'order': [
            [0, 'desc']
        ],
        'fixedHeader': {
            header: true,
        },
        "ajax": {
            "url": '<?= site_url('admin/moderation_request_client_complaints/getModerationRequestClientComplaints') ?>',
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


    $(document).on('change', '#end_date_search,#start_date_search',function(event) {
        event.preventDefault();
        oTable.draw();
    });
</script>