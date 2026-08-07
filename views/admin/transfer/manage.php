<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-transfer img {
        height: 20px;
        width: 20px;
    }

    .table-transfer thead tr th {
        text-align: center;
    }

    .table-transfer tr td:nth-child(2) {
        min-width: 95px;
        white-space: unset;
        text-align: center;
    }

    .table-transfer tr td:nth-child(1) {
        min-width: 110px;
        white-space: unset;
        text-align: center;

    }

    .table-transfer tr td:nth-child(3) {
        min-width: 125px;
        white-space: unset;
        text-align: center;
    }

    .table-transfer tr td:nth-child(4) {
        min-width: 125px;
        white-space: unset;
        text-align: center;

    }

    .table-transfer tr td:nth-child(5) {
        min-width: 120px;
        white-space: unset;
    }

    .table-transfer tr td:nth-child(6) {
        min-width: 120px;
        white-space: unset;
    }

    .table-transfer tr td:nth-child(7) {
        min-width: 120px;
        white-space: unset;
    }

    .table-transfer tr td:nth-child(8) {
        min-width: 150px;
        white-space: unset;
    }

    .table-transfer tbody tr td:nth-child(9) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-transfer tbody .dropdown {
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if (has_permission('transfer', '', 'create')) { ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= admin_url('transfer/detail') ?>" class="btn btn-info test H_action_button">
                            <?php echo _l('create_add_new'); ?></a>
                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <?= lang('Phiếu chuyển kho', 'transfer_search') ?>
                <input type="text" name="transfer_search" id="transfer_search" style="width: 100%;" data-placeholder="<?= lang('Phiếu chuyển kho') ?>" value="">
            </div>
            <div class="col-md-3">
                <?= lang('tnh_reference_orders', 'orders_search') ?>
                <input type="text" name="orders_search" id="orders_search" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
            </div>
            <div class="col-md-3">
                <?= lang('Kế hoạch nguyên phụ liệu', 'productions_capacity_id_search') ?>
                <input type="text" name="productions_capacity_id_search" id="productions_capacity_id_search" style="width: 100%;" data-placeholder="<?= lang('Kế hoạch nguyên phụ liệu') ?>" value="">
            </div>
            <div class="col-md-3">
                <?= lang('productions_orders', 'productions_orders_search') ?>
                <input type="text" name="productions_orders_search" id="productions_orders_search" class="productions_orders_search" value="" style="width: 100%;" data-placeholder="<?= lang('productions_orders') ?>">
            </div>
            <div class="col-md-3">
                <?= render_date_input('date_start', 'Từ ngày') ?>
            </div>
            <div class="col-md-3">
                <?= render_date_input('date_end', 'Đến ngày') ?>
            </div>
            <div class="col-md-3" style="margin-bottom: 10px">
                <label for="purchase_product_search" class="control-label"><?= _l('Nhập kho thành phẩm') ?></label>
                <input type="text" name="purchase_product_search" id="purchase_product_search" class="purchase_product_search" style="width: 100%;" data-placeholder="Nhập kho thành phẩm" value="">
            </div>
            <div class="col-md-3" style="margin-bottom: 10px">
                <label for="tranfer_business_search" class="control-label"><?= _l('Giữ kho (trên chuyền)') ?></label>
                <input type="text" name="tranfer_business_search" id="tranfer_business_search" class="tranfer_business_search" style="width: 100%;" data-placeholder="Giữ kho (trên chuyền)" value="">
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                            <?= _l('leads_all') ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="H_filter" data-id="nvl_sx">
                                            <?= _l('Giữ kho NVL SX') ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="H_filter" data-id="tp">
                                            <?= _l('Giữ kho thành phẩm') ?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="H_filter" data-id="export_warehouse">
                                            <?= _l('Đã xác nhận xuất kho') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <div class="clearfix mtop20"></div>
                        <?php $table_data = array(
                            _l('ch_date_p'),
                            _l('ch_code_p'),
                            _l('ch_catestaff_create'),
                            _l('leads_dt_status'),
                            // _l('ch_warehouse_do'),
                            // _l('ch_warehouse_to'),
                            _l('ch_warehoues_app'),
                            _l('Xác nhận xuất kho'),
                            _l('ch_note'),
                            _l('ch_option'),
                        );
                        render_datatable($table_data, 'transfer');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="view_transfer_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
    ajaxSelectParams('#productions_capacity_id_search', 'admin/transfer/searchproductions', 0, true, true);
    ajaxSelectParams('#transfer_search', 'admin/transfer/searchtransfer', 0, true, true);
    ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
    ajaxSelectParams('#purchase_product_search', 'admin/transfer/searchPurchaseProduct', 0, true, true);
    ajaxSelectParams('#tranfer_business_search', 'admin/transfer/searchTranferBusiness', 0, true, true);
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var tAPI;
    $(function() {

        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
            'orders_search': '[name="orders_search"]',
            'productions_capacity_id_search': '[name="productions_capacity_id_search"]',
            'date_start': '[name="date_start"]',
            'date_end': '[name="date_end"]',
            'productions_orders_search': '[name="productions_orders_search"]',
            'transfer_search': '[name="transfer_search"]',
            'purchase_product_search': '[name="purchase_product_search"]',
            'tranfer_business_search': '[name="tranfer_business_search"]',
        };
        tAPI = initDataTableCustom('.table-transfer', admin_url + 'transfer/table', [0], [0], CustomersServerParams, [], fixedColumns = {
            leftColumns: 2,
            rightColumns: 1
        });
        // var tAPI = initDataTable('.table-transfer', admin_url+'transfer/table', [0], [0], CustomersServerParams,[0, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
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
                url: "<?= admin_url() ?>transfer/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        tAPI.draw('page');
                        alert_float('success', response.message);
                    }
                }
            });
            return false;
        }
    }

    $(document).on('click', '.delete-remind', function() {
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

    function view_transfer(id) {
        $('#view_transfer_data').html('');
        $.get(admin_url + 'transfer/transfer_data/' + id).done(function(response) {
            $('#view_transfer_data').html(response);
            $('#view_transfer').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    $('body').on('hidden.bs.modal', '#view_transfer', function() {
        $('#view_transfer_data').html('');
    });

    function confirm_warehous(id, warehouseman_id) {
        {
            dataString = {
                id: id,
                warehouseman_id: warehouseman_id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>transfer/confirm_warehous",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    tAPI.draw('page');
                    alert_float(response.alert_type, response.message);
                }
            });
            return false;
        }
    }

    function changeStatusActive(id, status) {
        dataString = {
            id: id,
            status: status,
            [csrfData['token_name']]: csrfData['hash']
        };
        $.ajax({
            type: "post",
            url: "<?= admin_url() ?>transfer/change_status_active_transfer",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
        return false;
    }
</script>