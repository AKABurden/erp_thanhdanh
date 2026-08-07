<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-contract-appendix tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }

    #table-contract-appendix tr td:nth-child(9) {
        width: 200px;
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
                <?php if (has_permission('contract_appendix', '', 'create')): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/contract_appendix/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-contract-appendix" class="table dt-tnh table-contract-appendix" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Phụ Lục') ?></th>
                                            <th class="text-center"><?= lang('Tên Phụ Lục') ?></th>
                                            <th class="text-center"><?= lang('Mã Hợp Đồng') ?></th>
                                            <th class="text-center"><?= lang('Tên NV') ?></th>
                                            <th class="text-center"><?= lang('Lương Cơ Bản Mới') ?></th>
                                            <th class="text-center"><?= lang('Lương Vị Trí Mới') ?></th>
                                            <th class="text-center"><?= lang('File Đính Kèm') ?></th>
                                            <th class="text-center"><?= lang('Ngày Tạo') ?></th>
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
    oTable = tnhInitDataTable('#table-contract-appendix',
        '<?= site_url('admin/contract_appendix/getContractAppendix') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/contract_appendix/getContractAppendix') ?>',
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
            url: '<?= site_url('admin/contract_appendix/change_status') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: status,
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    oTable.draw();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra');
            }
        });
    });
</script>
