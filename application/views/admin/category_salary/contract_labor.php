<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-category-permission tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }

    #table-category-permission tr td:nth-child(5) {
        width: 150px;
        white-space: unset;
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if ($this->perAddContractLabor): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/category_salary/detail_contract_labor') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                    <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                    <a href="<?=admin_url('category_salary/import_contract_labor')?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                        Import Excel
                    </a>
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-contract-labor" class="table dt-tnh table-contract-labor" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Mã NV') ?></th>
                                            <th class="text-center"><?= lang('Tên NV') ?></th>
                                            <th class="text-center"><?= lang('Loại Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Mức Lương Cơ Bản') ?></th>
                                            <th class="text-center"><?= lang('Mức Lương Vị Trí') ?></th>
                                            <th class="text-center"><?= lang('Ngày Thử Việc') ?></th>
                                            <th class="text-center"><?= lang('Mức Ký HĐ') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hiệu Lực') ?></th>
                                            <th class="text-center"><?= lang('Ngày Hết Hiệu Lực') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tái Ký') ?></th>
                                            <th class="text-center"><?= lang('Trạng thái') ?></th>
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

    var fnserverparams = {};
    oTable = tnhInitDataTable('#table-contract-labor',
        '<?= site_url('admin/category_salary/getContractLabor') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/category_salary/getContractLabor') ?>',
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
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var currentDate = new Date();
                currentDate.setHours(0, 0, 0, 0);
                // Kiểm tra cột ngày tái ký (index 11)
                if (aData[11] && aData[11] !== '' && aData[11] !== '-') {
                    // Parse date in DD/MM/YYYY or DD-MM-YYYY format
                    var dateParts = aData[11].split(/[\/\-]/);
                    var renewDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                    renewDate.setHours(0, 0, 0, 0);
                    console.log('Renew Date:', renewDate);
                    if (!isNaN(renewDate.getTime()) && renewDate < currentDate) {
                        $(nRow).css('background-color', '#ffcccc');
                        // $(nRow).css('color', '#cc0000');
                    }
                }
                return nRow;
            },
            "createdRow": function(row, data, index) {},
            "columnDefs": [],
        });


    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    // Handle status change
    $(document).on('click', '#agree, #reject', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var status = $(this).attr('value');
        $('.po').popover('hide');
        $.ajax({
            url: '<?= site_url('admin/category_salary/change_status_contract_labor') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: status,
                [csrfData.token_name]: csrfData.hash
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    oTable.draw(false);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', '<?= lang('Có lỗi xảy ra, vui lòng thử lại') ?>');
            }
        });
    });


    function exportExcel() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/category_salary/export_contract_labor',
            data: {
                csrf_token_name: hash,
                export_excel: 1,
            },
            dataType: "json",
            success: function (response) {
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