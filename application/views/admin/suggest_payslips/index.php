<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if ($this->preAddSuggestPayslips) : ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_payslips/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                                        <?= lang('Phiếu đề xuất nội bộ', 'internal_proposal_search') ?>
                                        <input type="text" name="internal_proposal_search" autocomplete="off" placeholder="<?= lang('Phiếu đề xuất nội bộ') ?>" id="internal_proposal_search" class="internal_proposal_search form-control" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                        <?php foreach($option_suggest_payslips as $key => $value): ?>
                                            <li role="presentation" class="<?= $key == 0 ? 'active' : '' ?>">
                                                <a href="#tab-<?= $value['id'] ?>" aria-controls="<?= $value['id'] ?>" role="tab" value="<?= $value['id'] ?>" data-toggle="tab"><?= $value['name'] ?>(<span class="count-<?= $value['id'] ?>">0</span>)</a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="0">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-suggest-payslips" class="table dt-tnh table-suggest-payslips-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                            <th class="text-center"><?= lang('Ngày') ?></th>
                                            <th class="text-center"><?= lang('Người lập phiếu') ?></th>
                                            <th class="text-center"><?= lang('Nhà cung cấp') ?></th>
                                            <th class="text-center"><?= lang('Người Tạo') ?></th>
                                            <th class="text-center"><?= lang('Tổng tiền') ?></th>
                                            <th class="text-center"><?= lang('Trạng thái') ?></th>
                                            <th class="text-center"><?= lang('Phiếu đề xuất nội bộ') ?></th>
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
        status_table: '#status_table',
        internal_proposal_search: '#internal_proposal_search'
    };
    oTable = tnhInitDataTable('#table-suggest-payslips',
        '<?= site_url('admin/suggest_payslips/getSuggestPayslips') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/suggest_payslips/getSuggestPayslips') ?>',
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
            "columnDefs": [
                {
                    "targets": 8,
                    "sortable": false,
                    "searchable": false,
                },
            ],
        });

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>suggest_payslips/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        oTable.draw();
                        alert_float(response.alert_type, response.message);
                    }
                }
            });
            return false;
        }
    }
    $(document).on('change',
        '#end_date_search,#start_date_search, #internal_proposal_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
            countSuggestPayslips();
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
            url: site.base_url + 'admin/suggest_payslips/agree',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });

    }

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_payslips/exportExcel',
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

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });

    function countSuggestPayslips() {
        var dataPOST = {};

        if (typeof(csrfData) !== 'undefined') {
            dataPOST[csrfData['token_name']] = csrfData['hash'];
        }

        for (var key in fnserverparams) {
            dataPOST[key] = $(fnserverparams[key]).val();
        }

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/suggest_payslips/countSuggestPayslips',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                $.each(response?.option_suggest_payslips, function (index, value) { 
                    $(`.count-${value.id}`).html(tnhFormatNumber(value.count));
                });
            }
        });
    }

    $(document).ready(function () {
        countSuggestPayslips();
    });
</script>