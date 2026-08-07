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
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic-report-violate" class="table dt-tnh table-synthetic-report-violate" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Phòng ban') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 1') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 2') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 3') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 4') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 5') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 6') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 7') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 8') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 9') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 10') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 11') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Tháng 12') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo không phù hợp') ?></th>
                                        <th class="text-center"><?= lang('Báo cáo vi phạm') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                       <tr>
                                           <td colspan="2" class="text-center bold">Tổng cộng</td>
                                           <?php  foreach (getMonth() as $k => $v){ if (empty($v)){continue;} ?>
                                               <td class="text-center bold foot_<?= $v ?>"></td>
                                               <td class="text-center bold foot_violate_<?= $v ?>"></td>
                                           <?php } ?>
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
    var oTable = '';

    var fnserverparams = {
        'year_search': '#year_search'
    };
    oTable = tnhInitDataTable('#table-synthetic-report-violate',
        '<?= site_url('admin/synthetic_report_violate/getSyntheticReportViolate') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:"450px",
            "ajax": {
                "url": '<?= site_url('admin/synthetic_report_violate/getSyntheticReportViolate') ?>',
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
                    $.each(json.footData,function (k,v){
                        $('.table-synthetic-report-violate tfoot tr td.' + k).html(tnhFormatNumber(v));
                    })
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $(document).on('change',
        '#year_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        $role_id_search = $('#role_id_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/job_detail/exportExcel',
            data: {
                csrf_token_name: hash,
                role_id_search: $role_id_search,
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