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
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= base_url('admin/job_detail/import') ?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddJobDetail): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/job_detail/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= lang('Mã vị trí', 'role_id_search') ?>
                                        <input type="text" name="role_id_search" id="role_id_search" class="role_id_search modal-select2"
                                               data-placeholder="<?= lang('Mã vị trí') ?>" style="width: 100%;" value=""
                                               title="">
                                    </div>
                                </div>
                            </div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li>
                                            <a class="H_filter" data-id="all">
                                                <?=_l('Tất Cả')?>(<span class="count_status count_all">0</span>)
                                            </a>
                                        </li>
                                        <li class="active">
                                            <a class="H_filter" data-id="1">
                                                <?=_l('Hoạt Động')?>(<span class="count_status count_1">0</span>)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="0">
                                                <?=_l('Không Hoạt Động')?>(<span class="count_status count_0">0</span>)
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" id="filterStatus" name="filterStatus" value="1" />
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-job-detail" class="table dt-tnh table-job-detail" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã công việc') ?></th>
                                        <th class="text-center"><?= lang('Vị trí') ?></th>
                                        <th class="text-center"><?= lang('Version') ?></th>
                                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                                        <th class="text-center"><?= lang('Tiêu đề công việc') ?></th>
                                        <th class="text-center"><?= lang('Mục tiêu') ?></th>
                                        <th class="text-center"><?= lang('Trách nhiệm') ?></th>
                                        <th class="text-center"><?= lang('Phạm vi quyền hạn') ?></th>
                                        <th class="text-center"><?= lang('Yêu cầu công việc') ?></th>
                                        <th class="text-center"><?= lang('Tiêu chuẩn năng lực') ?></th>
                                        <th class="text-center"><?= lang('Ngày ban hành') ?></th>
                                        <th class="text-center"><?= lang('Thời gian hết hạn') ?></th>
                                        <th class="text-center"><?= lang('Ngày ban hành mới nhất') ?></th>
                                        <th class="text-center"><?= lang('Ngày hết hạn') ?></th>
                                        <th class="text-center"><?= lang('Đường dẫn tài liệu') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
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
    ajaxSelectParams('#role_id_search', 'admin/suggest_task/searchRoles', 0, true, true);
    var oTable = '';

    var fnserverparams = {
        'role_id_search': '#role_id_search',
        'filterStatus': '#filterStatus',
    };
    oTable = tnhInitDataTable('#table-job-detail',
        '<?= site_url('admin/job_detail/getJobDetail') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/job_detail/getJobDetail') ?>',
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
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    $('body').on('change', '.onoffswitch-checkbox', function() {
        var url = $(this).attr('data-switch-url-2');
        var status = $(this).prop('checked') ? 1 : 0;
        var id = $(this).attr('data-id');
        $.get(url + '/' + id + '/' + (status ?? 0), function (result) {
            result = JSON.parse(result);
            console.log(result);
            if(result.success) {
                alert_float(result.alert_type, result.message);
            }
            oTable.draw();
        })
    })


    $('.table-job-detail').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var total = invoiceReportsTable.ajax.json().total;
        $('.count_status').text(0);
        $.each(total, function(index, value) {
            $(`.count_${index}`).text(value);
        })
    })
    $(document).on('change',
        '#filterStatus',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    $(document).on('change',
        '#role_id_search',
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