<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=2.5') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a href="<?= base_url('admin/manufactures/statistical_planning') ?>" class="btn btn-info mright5 pull-right H_action_button hide">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('tnh_statistical_planning'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= lang('customers', 'customer_search') ?>
                            <input type="text" name="customer_search" id="customer_search" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= lang('tnh_product_name_code', 'products_search') ?>
                            <input type="text" name="products_text_search" placeholder="<?= lang('tnh_product_name_code') ?>" id="products_text_search" class="form-control products_text_search" value="">
                            <input type="text" name="products_search" id="products_search" class="hide" style="width: 100%;" value="" data-placeholder="<?= lang('products') ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('tnh_category_product', 'category_product_search') ?>
                            <select name="category_product_search[]" id="category_product_search" data-none-selected-text="<?= lang('tnh_category_product') ?>" class="form-control category_product_search selectpicker" data-live-search="true" multiple="true" data-actions-box="true" required="required">
                                <?php if (!empty($category_products)) : ?>
                                    <?php foreach ($category_products as $key => $value) : ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <?php
                    $date = date("Y-m-d");
                    ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('tnh_start_date_delivery_expected', 'start_date_search') ?>
                            <input type="text" name="start_date_search" placeholder="<?= lang('tnh_start_date_delivery_expected') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="<?= _d(minusMonth($date, 6)) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('tnh_end_date_delivery_expected', 'end_date_search') ?>
                            <input type="text" name="end_date_search" placeholder="<?= lang('tnh_end_date_delivery_expected') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="<?= _d(plusMonth($date, 12)) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= lang('tnh_type_orders', 'type_orders_search') ?>
                            <select name="type_orders_search[]" id="type_orders_search" data-none-selected-text="<?= lang('tnh_type_orders') ?>" class="form-control type_orders_search selectpicker" data-live-search="true" multiple="true" data-actions-box="true" required="required">
                                <option value=""></option>
                                <?php foreach ($type_orders as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_date_order"
                                   class="control-label"><?= _l('Ngày mở đơn') ?></label>
                            <div class="input-group">
                                <input type="text" id="search_date_order" placeholder="<?= _l('Ngày mở đơn') ?>" name="search_date_order" class="form-control search_date_order" aria-invalid="false">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3 hide">
                    <div class="flex-center" style="margin-top: 10px;">
                        <div class="radio radio-primary mright10">
                            <input type="radio" name="type_view_search" id="type_view_search-1" value="1" checked>
                            <label for="type_view_search-1"><?= lang('Xem tổng') ?></label>
                        </div>
                        <div class="radio radio-primary" style="margin-top: 10px;">
                            <input type="radio" name="type_view_search" id="type_view_search-2" value="2">
                            <label for="type_view_search-2"><?= lang('Xem theo khách hàng') ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" onclick="filterManufactures()" style="margin-top: 10px;" class="btn btn-default btn-primary"><span class="fa fa-filter"></span> <?= lang('filter') ?></button>
                    <button type="button" onclick="clickAddManufactures()" style="margin-top: 10px;" class="btn btn-default btn-warning"><span class="fa fa-exchange"></span> <?= lang('Lập kế hoạch NVL') ?></button>
                    <a href="#" onclick="exportExcelPlan(); return false;" style="margin-top: 10px;" class="btn btn-info">
                        <i class="fa fa-download" aria-hidden="true"></i> Xuất excel
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="view-show-list-manufactures">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<div id="show-form-detail-1"></div>

<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        orders_search: '#orders_search',
        business_plan_search: '#business_plan_search',
        type_orders_search: '#type_orders_search',
        search_date_order: '#search_date_order',
        start_date: '#start_date',
        end_date: '#end_date'
    };
    var oTable = '';
</script>
<script type="text/javascript">
    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        ajaxSelectParams('#products_search', 'admin/products/searchProductsSelect2', 0, true, true);
    });
</script>
<script>
    var customer_search = '';
    var start_date_search = '';
    var end_date_search = '';
    var products_search = '';
    var category_product_search = '';
    var type_view_search = '';
    var products_text_search = '';

    function filterManufactures() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        customer_search = $('#customer_search').val();
        products_search = $('#products_search').val();
        category_product_search = $('#category_product_search').val();
        type_orders_search = $('#type_orders_search').val();
        search_date_order = $('#search_date_order').val();
        type_view_search = $('input[name="type_view_search"]:checked').val();
        products_text_search = $('#products_text_search').val();


        if (!start_date_search || !end_date_search) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            return;
        }

        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['start_date_search'] = start_date_search;
        dataPOST['end_date_search'] = end_date_search;
        dataPOST['customer_search'] = customer_search;
        dataPOST['products_search'] = products_search;
        dataPOST['category_product_search'] = category_product_search;
        dataPOST['type_view_search'] = type_view_search;
        dataPOST['products_text_search'] = products_text_search;
        dataPOST['type_orders_search'] = type_orders_search;
        dataPOST['search_date_order'] = search_date_order;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/filterManufactures',
            data: dataPOST,
            dataType: "html",
            success: function(response) {
                $('.view-show-list-manufactures').html(response);
            }
        });
    }

    function convertManufactures() {
        pId = '';
        iLength = $('input[name="product_id[]"]').length;
        if (iLength) {
            $.each($('input[name="product_id[]"]'), function(index, value) {
                product_id = $(value).prop('checked');
                if (product_id) {
                    product_id = $(value).val();
                    pId += product_id + ',';
                }
            });
            if (pId) {
                pId = pId.substring(0, pId.length - 1);
            }
        }

        if (!pId) {
            alert_float('danger', 'Vui lòng chọn mặt hàng để chuyển qua kế hoạch');
            return;
        }
        linkManufactures = site.base_url + 'admin/manufactures/add_productions_plan?p_id=' + pId + '&cs_id=' + customer_search + '&start_date=' + start_date_search + '&end_date=' + end_date_search;

        var url = site.base_url + 'admin/manufactures/add_productions_plan';
        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="p_id" value="${pId}">`;
        inputs += `<input type="hidden" name="cs_id" value="${customer_search}">`;
        inputs += `<input type="hidden" name="start_date" value="${start_date_search}">`;
        inputs += `<input type="hidden" name="end_date" value="${end_date_search}">`;
        $("#show-form-detail-1").append('<form action="' + url + '" method="post" id="poster-detail-1">' + inputs + '</form>');
        $("#poster-detail-1").submit();
        // window.open(linkManufactures, '_blank');
    }

    function showDetailManufactures(_this, c_product_id) {
        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['start_date_search'] = start_date_search;
        dataPOST['end_date_search'] = end_date_search;
        dataPOST['customer_search'] = customer_search;
        dataPOST['product_id'] = c_product_id;
        dataPOST['products_search'] = products_search;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/showDetailManufactures',
            data: dataPOST,
            dataType: "html",
            success: function(response) {
                $('#tnhModal').html(response);
            }
        });
        $('#tnhModal').modal({
            backdrop: 'static',
            keyboard: true
        });
    }

    function showDetailManufacturesKeep(_this, c_product_id) {
        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['start_date_search'] = start_date_search;
        dataPOST['end_date_search'] = end_date_search;
        dataPOST['customer_search'] = customer_search;
        dataPOST['product_id'] = c_product_id;
        dataPOST['products_search'] = products_search;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/showDetailManufacturesKeep',
            data: dataPOST,
            dataType: "html",
            success: function(response) {
                $('#tnhModal').html(response);
            }
        });
        $('#tnhModal').modal({
            backdrop: 'static',
            keyboard: true
        });
    }

    function showDetailManufacturesWarehouses(_this, c_product_id) {
        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['start_date_search'] = start_date_search;
        dataPOST['end_date_search'] = end_date_search;
        dataPOST['customer_search'] = customer_search;
        dataPOST['product_id'] = c_product_id;
        dataPOST['products_search'] = products_search;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/showDetailManufacturesWarehouses',
            data: dataPOST,
            dataType: "html",
            success: function(response) {
                $('#tnhModal').html(response);
            }
        });
        $('#tnhModal').modal({
            backdrop: 'static',
            keyboard: true
        });
    }

    $(document).ready(function() {
        filterManufactures();
        search_daterangepicker_date_order();
    });
</script>
<script>
    var arrProductId = {};
    function clickAddManufactures() {
        var curProduct = [];
        $.each(arrProductId, function (index, value) { 
            if (value == 1) {
                curProduct.push(index);
            }
        });

        if (curProduct.length == 0) {
            alert_float('danger', 'Vui lòng chọn mặt hàng để lập kế hoạch NVL');
            return;
        }

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['product_id'] = curProduct;
        dataPOST['customer_search_manufactures'] = $('#customer_search_manufactures').val();
        dataPOST['start_date_search_manufactures'] = $('#start_date_search_manufactures').val();
        dataPOST['end_date_search_manufactures'] = $('#end_date_search_manufactures').val();
        dataPOST['products_search_manufactures'] = $('#products_search_manufactures').val();
        dataPOST['category_product_search_manufactures'] = $('#category_product_search_manufactures').val();
        dataPOST['type_view_search_manufactures'] = $('#type_view_search_manufactures').val();
        dataPOST['type_orders_search_manufactures'] = $('#type_orders_search_manufactures').val();
        dataPOST['search_date_order_manufactures'] = $('#search_date_order_manufactures').val();

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures_temp/modalDetailPlan',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.modal-select2').select2('close');
                $('#tnhModal').html(response);
                $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            }
        });
    }

    var search_daterangepicker_date_order = () => {
        $('input[name="search_date_order"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            isInvalidDate: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": lang_daterangepicker.applyLabel,
                "cancelLabel": lang_daterangepicker.cancelLabel,
                "fromLabel": lang_daterangepicker.fromLabel,
                "toLabel": lang_daterangepicker.toLabel,
                "customRangeLabel": lang_daterangepicker.customRangeLabel,
                "daysOfWeek": lang_daterangepicker.daysOfWeek,
                "monthNames": lang_daterangepicker.monthNames
            },
        }, function(start, end, label) {});
        $('input[name="search_date_order"]').val('').datepicker("refresh");
        $('input[name="search_date_order"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $("#search_date_order").trigger("change");
        });
        $('input[name="search_date_order"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#search_date_order").trigger("change");
        });
    };

    function exportExcelPlan() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        customer_search = $('#customer_search').val();
        products_search = $('#products_search').val();
        category_product_search = $('#category_product_search').val();
        type_orders_search = $('#type_orders_search').val();
        search_date_order = $('#search_date_order').val();
        type_view_search = $('input[name="type_view_search"]:checked').val();
        products_text_search = $('#products_text_search').val();


        if (!start_date_search || !end_date_search) {
            alert_float('danger', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            return;
        }

        var dataPOST = {};
        dataPOST[token] = hash;
        dataPOST['start_date_search'] = start_date_search;
        dataPOST['end_date_search'] = end_date_search;
        dataPOST['customer_search'] = customer_search;
        dataPOST['products_search'] = products_search;
        dataPOST['category_product_search'] = category_product_search;
        dataPOST['type_view_search'] = type_view_search;
        dataPOST['products_text_search'] = products_text_search;
        dataPOST['type_orders_search'] = type_orders_search;
        dataPOST['search_date_order'] = search_date_order;
        dataPOST['export_excel'] = 1;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/exportExcelPlan',
            data: dataPOST,
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
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>