<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    table.dataTable thead>tr>th {
        border-bottom: 1px solid;
    }
</style>
<input id="date_export_ch" class="hide">
<input id="type_export_ch" class="hide">

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4 border-right">
                                <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('inventory_report'); ?></h4>
                                <hr />
                                <?php if (has_permission('stock_card', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'stock-card-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('ch_stock_card'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <?php if (has_permission('import_export_report', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'warehouse-inventory-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('import_export_report'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'report_all_of_stock'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('dt_report_all_of_stock'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'report_all_of_stock_product'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('dt_report_all_of_stock_product'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'limit_user_date'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Cảnh báo date sử dụng NVl'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'limit_user_date_btp'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Cảnh báo date sử dụng BTP - TP'); ?></a>
                                </p>
                                <hr class="hr-10" />
 
                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'inventory_nvl_hs'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Báo cáo tồn kho NVL hàng sẵn'); ?></a>
                                </p>
                                <hr class="hr-10" />

                               <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'inventory_tp_hs'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Báo cáo tồn kho thành phẩm hàng sẵn'); ?></a>
                                </p>
                                <hr class="hr-10" />

                                <p>
                                    <a href="#" class="font-medium report_all" onclick="init_report(this,'inventory_btp_hs'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Báo cáo tồn kho bán thành phẩm hàng sẵn'); ?></a>
                                </p>
                                <hr class="hr-10" />

                            </div>
                            <div class="col-md-4 border-right">
                                <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('detail_report'); ?></h4>
                                <hr />
                                <!-- <?php if (has_permission('import_report', '', 'view')) { ?> -->
                                <p>
                                    <a href="#" class="font-medium" onclick="init_report(this,'warehouse-import-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Báo cáo chi tiết nhập thành phẩm'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <!-- <?php } ?>
                                <?php if (has_permission('import_report', '', 'view')) { ?> -->
                                <p>
                                    <a href="#" class="font-medium" onclick="init_report(this,'warehouse-import-report_mh'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('Báo cáo chi tiết nhập mua hàng'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <!-- <?php } ?> -->
                                <?php if (has_permission('export_report', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'warehouse-export-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('export_report'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <?php if (has_permission('export_sx_report', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'warehouse-exporting_producion-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('export_sx_report'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <!-- <?php if (has_permission('export_orther_report', '', 'view')) { ?> -->
                                <p>
                                    <a href="#" class="font-medium" onclick="init_report(this,'warehouse-other-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('export_other_report'); ?></a>
                                </p>
                                <hr class="hr-10" />
                                <!-- <?php } ?> -->
                                <?php if (has_permission('transfer_report', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'warehouse-transfer-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('transfer_report'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>
                                <?php if (has_permission('adjusted_report', '', 'view')) { ?>
                                    <p>
                                        <a href="#" class="font-medium" onclick="init_report(this,'warehouse-adjusted-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('adjusted_report'); ?></a>
                                    </p>
                                    <hr class="hr-10" />
                                <?php } ?>

                            </div>
                            <div class="col-md-4">
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="report-time">
                                            <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                                            <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                                                <option value="this_month"><?php echo _l('this_month'); ?></option>
                                                <option value="1"><?php echo _l('last_month'); ?></option>
                                                <option value="this_year"><?php echo _l('this_year'); ?></option>
                                                <option value="last_year"><?php echo _l('last_year'); ?></option>
                                                <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                                                <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                                                <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                                                <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id="date-range" class="hide mbot15">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="typeitems">
                                            <!-- <input type="text" name="type_items" class="hide" id="type_items"> -->
                                            <?php
                                            echo render_select('type_itemss', $type_items, array('type', 'name'), 'Loại mặt hàng', -1, [], [], '', '', false);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="items">
                                            <input type="text" name="type_items" class="hide" id="type_items">
                                            <label for="months-report"><?php echo _l('tnh_items'); ?></label><br />
                                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select" name="custom_item_select" style="width: 100%">
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light-gray border-radius-4">
                                    <div class="p8">
                                        <div class="form-group hide" id="items_inventory">
                                            <input type="text" name="type_items_new" class="hide" id="type_items_new">
                                            <label for="months-report"><?php echo _l('tnh_items'); ?></label><br />
                                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select_inventory" id="custom_item_select_inventory" name="custom_item_select_inventory" style="width: 100%">
                                        </div>
                                    </div>
                                </div>
                                <div id="ch_type_import" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('Loại phiếu nhập'); ?></label>
                                                <select class="selectpicker type_import" id="type_import" name="type_import" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <option value="1"><?= _l('Nhập mới') ?></option>
                                                    <option value="2"><?= _l('Nhập kho thành phẩm') ?></option>
                                                    <option value="3"><?= _l('Nhập gia công') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse_inventory" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group ">
                                                <label for="report-to" class="control-label"><?php echo _l('warehouse'); ?></label>
                                                <select class="selectpicker warehouse_id_inventory" id="warehouse_id_inventory" name="warehouse_id_inventory" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value="-1">Tất cả</option>
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse_array" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group ">
                                                <label for="warehouse_id_array" class="control-label"><?php echo _l('warehouse'); ?></label>
                                                <select class="selectpicker warehouse_id_array" data-actions-box="1" multiple="1" data-live-search="true" id="warehouse_id_array" name="warehouse_id_array[]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group ">
                                                <label for="report-to" class="control-label"><?php echo _l('warehouse'); ?></label>
                                                <select class="selectpicker warehouse_id" id="warehouse_id" name="warehouse_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse_limit_dates" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('warehouse'); ?></label>
                                                <select class="selectpicker warehouse_limit_date" id="warehouse_limit_date" name="warehouse_limit_date" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option></option>
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse_tran_export" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('warehouse_D'); ?></label>
                                                <select class="selectpicker warehouse_id_export" id="warehouse_id_export" name="warehouse_id_export" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option></option>
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="warehouse_tran_import" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('warehouse_N'); ?></label>
                                                <select class="selectpicker warehouse_id_import" id="warehouse_id_import" name="warehouse_id_import" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option></option>
                                                    <?php foreach ($warehouse as $key => $value) {
                                                    ?>
                                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                    <?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ch_type_adjusted" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('adjusted_type'); ?></label>
                                                <select class="selectpicker type_adjusted" id="type_adjusted" name="type_adjusted" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <option value="1"><?= _l('ch_adjustedT') ?></option>
                                                    <option value="2"><?= _l('ch_adjustedG') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ch_status_transfer" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('Xác nhận xuất kho'); ?></label>
                                                <select class="selectpicker status_transfer" id="status_transfer" name="status_transfer" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <option value="1"><?= _l('Chưa xác nhận') ?></option>
                                                    <option value="2"><?= _l('Đã xác nhận') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="category_inventory_warehouse_search" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('Danh mục'); ?></label>
                                                <select class="selectpicker category" id="category_search_new" name="category_search_new[]" data-actions-box="1" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <!-- <?= recursiveCategoryItems() ?> -->
                                                    <?php
                                                    foreach ($CategoryItems as $key => $value) {
                                                    ?>
                                                        <optgroup label="<?= $value['name'] ?>">
                                                            <?php
                                                            foreach ($value['detail'] as $k => $v) {
                                                            ?>
                                                                <option value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </optgroup>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ch_type_limit_date" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="report-to" class="control-label"><?php echo _l('Tình trạng hạn sử dụng'); ?></label>
                                                <select class="selectpicker type_limit_date" id="type_limit_date" name="type_limit_date" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <option value="1"><?= _l('Sắp tới hạn') ?></option>
                                                    <option value="2"><?= _l('Quá hạn') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="ch_type_purchase_products" class="hide ">
                                    <div class="bg-light-gray border-radius-4">
                                        <div class="p8">
                                            <div class="form-group">
                                                <label for="type_purchase_products" class="control-label"><?php echo _l('Loại nhập kho thành phẩm'); ?></label>
                                                <select class="selectpicker type_purchase_products" id="type_purchase_products" name="type_purchase_products" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <option selected value="1"><?= _l('Tồn sẵn') ?></option>
                                                    <option value="2"><?= _l('Theo đơn hàng') ?></option>
                                                    <option value="3"><?= _l('Hàng lỗi') ?></option>
                                                    <option value="4"><?= _l('Giữ trên chuyền') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="report" class="hide">
                            <hr class="hr-panel-heading" />
                            <h3 style="color: #2b6fa2;font-weight: bold;" class="no-mtop text-center title_ch"><?php echo mb_strtoupper(_l('reports_sales_generated_report'), 'UTF-8'); ?></h3>
                            <hr class="hr-panel-heading" />
                            <?php $this->load->view('admin/reports/warehouse/stock_card_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/import_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/import_report_mh'); ?>
                            <?php $this->load->view('admin/reports/warehouse/export_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/exporting_producion_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/export_other_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/transfer_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/adjusted_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/inventory_report'); ?>
                            <?php $this->load->view('admin/reports/warehouse/stock_product'); ?>
                            <?php $this->load->view('admin/reports/warehouse/report_products'); ?>
                            <?php $this->load->view('admin/reports/warehouse/limit_user_date'); ?>
                            <?php $this->load->view('admin/reports/warehouse/limit_user_date_btp'); ?>
                            <?php $this->load->view('admin/reports/warehouse/inventory_nvl_hs'); ?>
                            <?php $this->load->view('admin/reports/warehouse/inventory_tp_hs'); ?>
                            <?php $this->load->view('admin/reports/warehouse/inventory_btp_hs'); ?>
                            <?php $this->load->view('admin/reports/warehouse/report_all', $warehouse); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="viewinventorywarehouse_data"></div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/warehouse/warehouse_js'); ?>
<script>
    $('select[name="months-report"]').selectpicker('val', 'this_month');
    var report_monthss = $('select[name="months-report"]').val();
    var report_froms = $('[name="report-from"]').val();
    var report_tos = $('[name="report-to"]').val();
    var data = {};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
        data['report_months'] = report_monthss;
        data['report_from'] = report_froms;
        data['report_to'] = report_tos;
    }
    var date_text = '';
    $.post(admin_url + 'warehouse/GetDate/', data).done(function(response) {
        date_text = response;
        //insert
        $('#date_export_ch').val(date_text);

    });
</script>
</body>

</html>