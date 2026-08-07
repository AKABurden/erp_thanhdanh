<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-compose tr td:nth-child(5) {
        min-width: 200px;
        max-width: 200px;
        white-space: unset;
        text-align: center;
    }

    .table-compose tr td:nth-child(21) {
        min-width: 200px;
        max-width: 200px;
        white-space: unset;
        text-align: center;
    }

    <?php for ($i = 2; $i < 14; $i++) { ?>.table-compose tr td:nth-child(<?= $i ?>) {
        min-width: 120px;
        max-width: 150px;
        white-space: unset;
        text-align: center;
    }

    <?php } ?>.table-compose tr td:nth-child(15) {
        min-width: 300px;
        max-width: 300px;
        white-space: unset;
        text-align: center;
    }

    .table-compose tr td:nth-child(16) {
        min-width: 120px;
        max-width: 150px;
        white-space: unset;
        text-align: center;
    }

    .table-compose tr td:nth-child(17) {
        min-width: 100px;
        max-width: 100px;
        white-space: unset;
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if (has_permission('compose', '', 'create')) { ?>
                    <a href="<?= admin_url('compose/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?php echo render_input('search_code', 'Mã PO'); ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="dt-tnh table table-compose table-bordered table-hover mtop0 mbot0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 200px" class="text-center"><?php echo _l('PO'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('Style Number'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('COLOR NAME'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('PRIMARY SIZE'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('UPC/EAN CODE'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('SL'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('Trim card'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('Sample 1'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('Loss'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('1%'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('SL QC'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('Dán nhãn thực tế'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('QC Sample'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('TC'); ?></th>
                                        <th style="width: 300px" class="text-center"><?php echo _l('LAYOUT NO.'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('REMARK#'); ?></th>
                                        <th style="width: 50px" class="text-center"><?php echo _l('ch_option'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>Tổng</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="total_quan"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var tAPI;
    $(function() {
        var CustomersServerParams = {
            // 'filterStatus': '[name="filterStatus"]',
            // 'suppliers_id': '[name="suppliers_id"]',
            'search_code': '[name="search_code"]',
            // 'search_staff': '[name="search_staff[]"]',
            // 'search_id_suppliers': '[name="search_id_suppliers[]"]',
            // 'search_date': '[name="search_date"]',
        };
        tAPI = initDataTableCustom('.table-compose', admin_url + 'compose/table', [0], [0], CustomersServerParams,
            <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>,
            fixedColumns = {
                leftColumns: 1,
                rightColumns: 0
            });
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
    });
    $(document).on('click', '.delete-reminders', function() {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });
    $(document).on('change', '.update_code', function(e) {
        var input = $(this);
        var data = $(this).val();
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        $.post(admin_url + "compose/update_compose", {
            data: data,
            id: id,
            name: name,
            [csrfData['token_name']]: csrfData['hash']
        }, function(data) {
            data = JSON.parse(data);
            tAPI.draw('page');
            alert_float(data.alert_type, data.message);

        })
    });
    $('.table-compose').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var sums = invoiceReportsTable.ajax.json().sums;
        $('.dataTables_scrollFoot').find('tfoot').addClass('bold');
        $('.DTFC_LeftFootWrapper').css("background","#ffff");
        $('.total_quan').text(sums.all);
    });
</script>