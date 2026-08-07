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
                <?php if ($this->preAddSuggestPaidHolidays): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_paid_holidays/detail') ?>" class="btn btn-info H_action_button">
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
                            <div class="">
                                <table id="table-suggest-paid-holiday" class="table dt-tnh table-suggest-paid-holiday-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"></th>
                                        <th class="text-center"><?= lang('Mã Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập ĐX Phép') ?></th>
                                        <th class="text-center"><?= lang('Người Đề Xuất') ?></th>
                                        <th class="text-center"><?= lang('Mã Vị Trí') ?></th>
                                        <th class="text-center"><?= lang('Quy Định') ?></th>
                                        <th class="text-center"><?= lang('Người Tạo') ?></th>
                                        <th class="text-center"><?= lang('Người Duyệt') ?></th>
                                        <th class="text-center"><?= lang('Báo Cáo Không Phù Hợp') ?></th>
                                        <th class="text-center"><?= lang('Người Tiếp Nhận Tạm Thời') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                        <th class="text-center"><?= lang('info') ?></th>

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
    oTable = tnhInitDataTable('#table-suggest-paid-holiday',
        '<?= site_url('admin/suggest_paid_holidays/getSuggestPaidHolidays') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/suggest_paid_holidays/getSuggestPaidHolidays') ?>',
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
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 11,
                    'visible': false,
                },
            ],
        });


    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });


    function loadInfoData(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[11];
    }

    $('#table-suggest-paid-holiday tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadInfoData(row.data())).show();
            tr.addClass('shown');
        }
    });

    $('body').on('click', '#agree_child', function () {
        var id = $(this).data('id');
        var status = $(this).attr('value');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        if (status == 2) {
            $(".note_approve_task").removeClass('hide');
            $(".note_approve_task").attr('required', true);
            $('.approve_task_popover').find('a.not_approve').addClass('active');

            $('.approve_task_popover').find('a.approve').removeClass('active');

            $(".po-save").removeClass('hide');
            $(".po-save").removeAttr('disabled', 'disabled');
            $(".label-note").removeClass('hide');
            $(".po-save").click(function() {
                if ($(".note_approve_task").val() == '') {
                    alert('Vui lòng nhập ghi chú');
                    return;
                }
                note = $(".note_approve_task").val();
                if (note != '') {
                    $.ajax({
                        url: site.base_url + 'admin/suggest_paid_holidays/update_status_child',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            id: id,
                            status: status,
                            note: note,
                        },
                    })
                        .done(function(data) {
                            if (data.result) {
                                alert_float('success', data.message);
                                oTable.draw('page');
                                setTimeout(function () {
                                    $(`#rows-child-${data.id}`).click();
                                }, 300);
                            } else {
                                alert_float('danger', data.message);
                            }
                            $('.po').popover('hide');
                        })
                        .fail(function(data) {
                            alert_float('danger', 'errors');
                            $(index).removeAttr('disabled');
                        })
                }
            });
            return;
        } else {
            $(".label-note").addClass('hide');
            $(".note_approve_task").addClass('hide');
            $(".note_approve_task").attr('required', false);
            $(".po-save").addClass('hide');
            $.post(admin_url + 'suggest_paid_holidays/update_status_child', data, function (result) {
                result = JSON.parse(result);
                if (result.result) {
                    oTable.draw('page');
                    alert_float('success', result.message);
                    $('.popover').closest('div.popover').popover('hide');
                    setTimeout(function () {
                        $(`#rows-child-${result.id}`).click();
                    }, 300);
                } else {
                    alert_float('danger', result.message);
                }
            })
        }
    })

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_paid_holidays/exportExcel',
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