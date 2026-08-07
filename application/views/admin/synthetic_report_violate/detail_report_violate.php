<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
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
                                <div class="col-md-3">
                                    <label for="year_search">Năm</label>
                                    <select class="year_search" id="year_search" name="year_search" style="width: 100%">
                                        <?php foreach (getYear() as $key => $value){ ?>
                                            <option <?= date('Y') == $value ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="room_search">Phòng ban</label>
                                    <select class="room_search" id="room_search" name="room_search" style="width: 100%">
                                        <option value="">Tất cả</option>
                                        <?php foreach ($dtRoom as $key => $value){ ?>
                                            <option  value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic-detail-report-violate" class="table dt-tnh table-synthetic-detail-report-violate" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" ><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Phòng ban') ?></th>
                                        <th class="text-center"><?= lang('Liên quan đến') ?></th>
                                        <th class="text-center"><?= lang('Sự cố') ?></th>
                                        <th class="text-center"><?= lang('Tổng số lỗi') ?></th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                    </tr>
                                    </tfoot>
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
    $("#year_search").select2();
    $("#room_search").select2();
    var oTable = '';

    var fnserverparams = {
        'year_search': '#year_search',
        'room_search': '#room_search',
    };
    oTable = tnhInitDataTable('#table-synthetic-detail-report-violate',
        '<?= site_url('admin/synthetic_report_violate/getSyntheticDetailReportViolate') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:"450px",
            "ajax": {
                "url": '<?= site_url('admin/synthetic_report_violate/getSyntheticDetailReportViolate') ?>',
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
        '#year_search,#room_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

</script>