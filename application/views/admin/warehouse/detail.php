<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $tagest = getTagest($id, 'product'); ?>
<?php $tagestbtp = getTagest($id, 'semi_products'); ?>

<style type="text/css">
    .nav-tabs {
        margin-bottom: 0px;
    }

    .table-warehouse_items tbody tr td {
        vertical-align: middle;
    }

    .table-warehouse_items_deal_products tr td:nth-child(1):not(.hau) {
        vertical-align: middle;
        min-width: 150px;
        white-space: unset;
    }

    .table-warehouse_items_deal_products tr td:nth-child(2):not(.hau) {
        vertical-align: middle;
        min-width: 150px;
        white-space: unset;
    }

    .table-warehouse_items_deal_products tr td:nth-child(3):not(.hau) {
        vertical-align: middle;
        min-width: 150px;
        white-space: unset;
        text-align: center;
    }

    .table-warehouse_items_deal_products tr th:nth-child(2):not(.hau) {
        text-align: center;
    }

    <?php foreach ($tagest as $key => $value) { ?>.table-warehouse_items_deal_products tr td:nth-child(<?= ($key + 4) ?>):not(.hau) {
        vertical-align: middle;
        min-width: 80px;
        white-space: unset;
        text-align: center;
    }

    .table-warehouse_items_deal_products tr th:nth-child(<?= ($key + 4) ?>):not(.hau) {
        text-align: center;
    }

    <?php }
    ?>.table-warehouse_items_deal_products tr td:nth-child(<?= ($key + 5) ?>):not(.hau) {
        vertical-align: middle;
        text-align: right;
        min-width: 150px;
    }

    .table-warehouse_items_deal_products tr td:nth-child(1):not(.hau) {
        vertical-align: middle;
        min-width: 150px;
    }
</style>
<div id="wrapper">
    <?php if (has_permission('warehouse', '', 'create')) { ?>
        <div class="panel_s mbot10 H_scroll" id="H_scroll">
            <div class="panel-body _buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
        </div>
    <?php } ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <p class="bold"><?php echo _l('filter_by'); ?></p>
                        </div>
                        <div class="col-md-3 select_custom_item_select">
                            <div class="form-group ">
                                <label for="custom_item_select" class="control-label"><?php echo _l('item_name'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="custom_item_select" name="custom_item_select" class="custom_item_select" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-md-3 select_custom_item_select">
                            <div class="form-group ">
                                <label for="category_id" class="control-label"><?php echo _l('tnh_category_id'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="category_id" name="category_id" class="category_id" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-md-3 select_warehouse_localtion">
                            <div class="form-group ">
                                <label for="warehouse_localtion" class="control-label"><?php echo _l('warehouse_localtion'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="warehouse_localtion" name="warehouse_localtion" class="warehouse_localtion" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>

                        <!-- <div class="col-md-3">
                            <div class="form-group mbot25">
                                <label for="localtion"><?= _l('warehouse_localtion') ?></label>
                                <select class="selectpicker no-margin" data-width="100%" id="localtion" name="localtion" data-none-selected-text="<?php echo _l('warehouse_localtion'); ?>" data-live-search="true">
                                    <?= $localtion ?>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-md-3">
                            <?= render_input('lot_code', 'Lot') ?>
                        </div>
                        <div class="clearfix"></div>
                        <ul class="nav nav-tabs">
                            <li class="in-title active"><a data-toggle="tab" href="#deal_product" onclick="viewListProducts();" aria-expanded="true"><?= _l('product') ?></a></li>
                            <li class="in-title "><a data-toggle="tab" href="#deal_productsemi" onclick="viewListProductssemi();" aria-expanded="true"><?= _l('semi_products') ?></a></li>
                            <li class="in-title"><a data-toggle="tab" href="#deal_nvl" onclick="viewListMaterials();" aria-expanded="false"><?= _l('tnh_item_materials') ?></a></li>
                            <li class="in-title"><a data-toggle="tab" href="#deal_tools" onclick="viewListTools();" aria-expanded="false"><?= _l('tnh_tools_supplies') ?></a></li>
                        </ul>
                        <?php if ($id == WAREHOUSES_ERRORS) { ?>
                            <div class="text-right"><a class="btn btn-info btn-icon" onclick="create_export()">Tạo phiếu xuất</a></div>
                            <br>
                        <?php } ?>
                        <br>
                        <div class="tab-content">
                            <input type="hidden" id="filterStatus" name="filterStatus" value="product" />
                            <div role="deal_product" class="tab-pane active" id="deal_product">
                                <div class="horizontal-scrollable-tabs">
                                    <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                    <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                    <div class="horizontal-tabs">
                                        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                            <li class="active">
                                                <a class="H_filter_v2 " data-id="1">
                                                    <?= _l('Tồn sẵn') ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="H_filter_v2" data-id="2">
                                                    <?= _l('Tồn chờ giao') ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <input type="hidden" id="filterStatus_v2" name="filterStatus_v2" value="1" />
                                <div class="clearfix"></div>
                                <table class="table table-warehouse_items_deal_products">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('item_code')); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('item_name')); ?></th>
                                            <th colspan="<?= (count($tagest) + 1) ?>" class="text-center"><?php echo ucwords(_l('Tổng SL')); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('ch_price_warehouse')); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center"><?php echo ucwords(_l('Hàng sẵn')); ?></th>
                                            <?php
                                            foreach ($tagest as $key => $value) {
                                            ?>
                                                <th class="text-center"><?php echo $value['name']; ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div role="deal_productsemi" class="tab-pane " id="deal_productsemi">
                                <div class="clearfix"></div>
                                <table class="table table-warehouse_items_deal_productssemi">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('item_code')); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('item_name')); ?></th>
                                            <th colspan="<?= (count($tagestbtp) + 1) ?>" class="text-center"><?php echo ucwords(_l('Tổng SL')); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo ucwords(_l('ch_price_warehouse')); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center"><?php echo ucwords(_l('Hàng sẵn')); ?></th>
                                            <?php
                                            foreach ($tagestbtp as $key => $value) {
                                            ?>
                                                <th class="text-center"><?php echo $value['name']; ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <!-- <div role="tabpanel" class="tab-pane active" id="synthetic">
                                <div class="clearfix"></div>
                                <?php render_datatable_tfoot_ch(array(
                                    _l('item_code'),
                                    _l('Lot'),
                                    _l('ch_items_specification'),
                                    _l('warehouse_localtion'),
                                    _l('quantity_detal'),
                                    _l('ch_date_of_manufacture'),
                                    _l('ch_items_dateed'),
                                    _l('ch_items_date_use'),
                                    _l('tnh_reference_productions_orders_details'),
                                    _l('ch_price_warehouse'),
                                ), 'warehouse_items'); ?>
                            </div> -->
                            <div role="deal_nvl" class="tab-pane" id="deal_nvl">
                                <div class="clearfix"></div>
                                <?php render_datatable(array(
                                    _l('item_code'),
                                    _l('item_name'),
                                    _l('tnh_dvt'),
                                    _l('Lot'),
                                    _l('ch_items_specification'),
                                    _l('warehouse_localtion'),
                                    _l('quantity_detal'),
                                    _l('ch_date_of_manufacture'),
                                    _l('ch_items_dateed'),
                                    _l('ch_items_date_use'),
                                    _l('ch_price_warehouse'),
                                ), 'warehouse_items_deal_nvl'); ?>
                            </div>
                            <div role="deal_tools" class="tab-pane" id="deal_tools">
                                <div class="clearfix"></div>
                                <?php render_datatable(array(
                                    _l('item_code'),
                                    _l('item_name'),
                                    _l('tnh_dvt'),
                                    _l('Lot'),
                                    _l('ch_items_specification'),
                                    _l('warehouse_localtion'),
                                    _l('quantity_detal'),
                                    _l('ch_date_of_manufacture'),
                                    _l('ch_items_dateed'),
                                    _l('ch_items_date_use'),
                                    _l('ch_price_warehouse'),
                                ), 'warehouse_items_deal_tools'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="return_suppliers_data"></div>
<div id="view_adjusted_data"></div>
<div id="view_transfer_data"></div>
<div id="export_different_data"></div>
<script type="text/javascript" src="<?= base_url('assets/js/dataTables.rowsGroup.js') ?>"></script>

<script>
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    $('.H_filter_v2').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus_v2"]').val(value);
        $('input[name="filterStatus_v2"]').change();
    });
    var CustomersServerParams = {
        'category_id': '[name="category_id"]',
        'filterStatus': '[name="filterStatus"]',
        'filterStatus_v2': '[name="filterStatus_v2"]',
        'custom_item_select': '[name="custom_item_select"]',
        'localtion': '[name="warehouse_localtion"]',
        'lot_code': '[name="lot_code"]',
    };
    var tAPIProduct;
    var tAPIMaterials;
    var tAPIProductsemi;
    var tAPITools;
    $(function() {
        var type = $('#type_items').val();
        if (empty(type)) {
            // $('.select_custom_item_select').addClass('hide');
        }
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
        ajaxSelectCallBack($('#category_id'), "<?= admin_url('warehouse/SearchCategory') ?>", 0);
        ajaxSelectCallBack($('#warehouse_localtion'), "<?= admin_url('warehouse/SearchLocaltion') ?>", 0);

        function ajaxSelectCallBack(element, url, id, types = '') {
            if (id > 0) {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    initSelection: function(element, callback) {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: url + '/' + id + '/' + types,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0].children[0]);
                            }
                        });
                    },
                    ajax: {
                        url: url,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: $('#filterStatus').val(),
                                types: types,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return {
                                    results: data.results
                                };
                            } else {
                                return {
                                    results: [{
                                        id: '',
                                        text: 'No Match Found'
                                    }]
                                };
                            }
                        }
                    },
                    formatResult: repoFormatSelection,
                    formatSelection: repoFormatSelection,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            } else {
                $(element).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    ajax: {
                        url: url + '/' + $(element).val(),
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: $('#filterStatus').val(),
                                warehouse: <?= $id ?>,
                                types: types,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return {
                                    results: data.results
                                };
                            } else {
                                return {
                                    results: [{
                                        code_client: '',
                                        id: '',
                                        text: 'No Match Found'
                                    }]
                                };
                            }
                        }
                    },
                    formatResult: repoFormatSelection,
                    formatSelection: repoFormatSelection,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            }
        }
        var base_url = '<?= base_url() ?>';

        function repoFormatSelection(state) {
            if (!state.id) return state.text;
            if (state.code == undefined) {
                return state.text;
            }
            return state.text + ' - ' + '(' + state.code + ')';
        }
        // init_ajax_searchs('items', '#custom_item_select')

        function init_ajax_searchs(e, t, a, i) {
            var n = $("body").find(t);
            if (n.length) {
                var s = {
                    ajax: {
                        url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                        data: function() {
                            var type = $('#type_items').val();

                            var t = {
                                [csrfData.token_name]: csrfData.hash
                            };
                            return t.type = e, t.rel_id = "", t.q = "{{{q}}}", t.type_items = type, void 0 !== a && jQuery.extend(t, a), t
                        }
                    },
                    locale: {
                        emptyTitle: app.lang.search_ajax_empty,
                        statusInitialized: app.lang.search_ajax_initialized,
                        statusSearching: app.lang.search_ajax_searching,
                        statusNoResults: app.lang.not_results_found,
                        searchPlaceholder: app.lang.search_ajax_placeholder,
                        currentlySelected: app.lang.currently_selected
                    },
                    requestDelay: 500,
                    cache: !1,
                    preprocessData: function(e) {
                        for (var t = [], a = e.length, i = 0; i < a; i++) {
                            var n = {
                                value: e[i].id,
                                text: e[i].name
                            };
                            e[i].subtext && (n.data = {
                                subtext: e[i].subtext
                            }), t.push(n)
                        }
                        return t
                    },
                    preserveSelectedPosition: "after",
                    preserveSelected: !0
                };
                n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s)
            }
        }

        // var tAPI = initDataTable_ch('.table-warehouse_items', admin_url + 'warehouse/table_warehouse_items/' + <?= $id ?>, [0], [0], CustomersServerParams, []);
        // var items = initDataTable('.table-warehouse_items_deal_items', admin_url + 'warehouse/warehouse_items_deal_items/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        // var product = initDataTable('.table-warehouse_items_deal_product', admin_url + 'warehouse/warehouse_items_deal_product/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        // var nvl = initDataTable('.table-warehouse_items_deal_nvl', admin_url + 'warehouse/warehouse_items_deal_nvl/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        // var tools = initDataTable('.table-warehouse_items_deal_tools', admin_url + 'warehouse/warehouse_items_deal_tools/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
    });
    $.each(CustomersServerParams, function(filterIndex, filterItem) {
        $('' + filterItem).on('change', function() {
            if ($('.table-warehouse_items_deal_nvl').hasClass('dataTable')) {
                tAPIMaterials.draw('page');
            }
            if ($('.table-warehouse_items_deal_products').hasClass('dataTable')) {
                tAPIProduct.draw('page');
            }
            if ($('.table-warehouse_items_deal_productssemi').hasClass('dataTable')) {
                tAPIProductsemi.draw('page');
            }
            if ($('.table-warehouse_items_deal_tools').hasClass('dataTable')) {
                tAPITools.draw('page');
            }
        });
    });

    function viewListProducts() {
        $('input[name="filterStatus"]').val('product');

        if (!$('.table-warehouse_items_deal_products').hasClass('dataTable')) {
            tAPIProduct = initDataTable('.table-warehouse_items_deal_products', admin_url + 'warehouse/table_warehouse_items_product/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        } else {
            tAPIProduct.draw('page');
        }

    }

    function viewListProductssemi() {
        $('input[name="filterStatus"]').val('product');

        if (!$('.table-warehouse_items_deal_productssemi').hasClass('dataTable')) {
            tAPIProductsemi = initDataTable('.table-warehouse_items_deal_productssemi', admin_url + 'warehouse/table_warehouse_items_productsemi/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        } else {
            tAPIProductsemi.draw('page');
        }

    }

    function viewListMaterials() {
        $('input[name="filterStatus"]').val('nvl');

        if (!$('.table-warehouse_items_deal_nvl').hasClass('dataTable')) {
            tAPIMaterials = initDataTable('.table-warehouse_items_deal_nvl', admin_url + 'warehouse/table_warehouse_items_nvl/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        } else {
            tAPIMaterials.draw('page');
        }

    }

    function viewListTools() {
        $('input[name="filterStatus"]').val('tools');

        if (!$('.table-warehouse_items_deal_tools').hasClass('dataTable')) {
            tAPITools = initDataTable('.table-warehouse_items_deal_tools', admin_url + 'warehouse/table_warehouse_items_tools/' + <?= $id ?>, [0], [0], CustomersServerParams, [0, 'asc']);
        } else {
            tAPITools.draw('page');
        }

    }
    viewListProducts();
    $('.table-warehouse_items').on('draw.dt', function() {
        row_header_v2(this);
    });
    $('.table-warehouse_items').on('draw.dt', function() {
        var paymentReceivedReportsTable = $(this).DataTable();
        var sums = paymentReceivedReportsTable.ajax.json().sums;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
        $(this).find('tfoot td').eq(4).html('<div class="text-center" style="font-size:19px;color:#3c763d;font-weight: bold">' + sums.slt + '</div>');
        $(this).find('tfoot td').eq(9).html('<div class="text-right" >' + sums.gtt + '</div>');
    });

    function row_header_v2(e) {
        var class_tr = $(e).find('.alert-header');
        $.each(class_tr, function(index, value) {
            var data = $(value).find('td').first().html();
            $(value).find('td:eq(2),td:eq(3)').remove();
            $(value).find('td:eq(1)').attr('colspan', 3);
        })
    }
    $('.table-warehouse_items_deal_items').on('draw.dt', function() {
        row_header(this);
    });
    $('.table-warehouse_items_deal_products').on('draw.dt', function() {
        row_header(this);
    });
    $('.table-warehouse_items_deal_nvl').on('draw.dt', function() {
        row_header(this);
    });
    $('.table-warehouse_items_deal_tools').on('draw.dt', function() {
        row_header(this);
    });

    function row_header(e) {
        var class_tr = $(e).find('.alert-header');
        $.each(class_tr, function(index, value) {
            var data = $(value).find('td').first().html();
            $(value).find('td:eq(1),td:eq(2)').remove();
            $(value).find('td:eq(0)').attr('colspan', 3);
        })
    }
    $('#type_items').on('change', function(e) {
        $('#custom_item_select').selectpicker('val', '');
        $('#custom_item_select').selectpicker('refresh');
        var type = $('#type_items').val();
        if (empty(type)) {
            $('.select_custom_item_select').addClass('hide');
        } else {
            $('.select_custom_item_select').removeClass('hide');
        }
    });

    function view_return_suppliers(id = null, edit = false) {
        $('#return_suppliers_data').html('');
        $.get(admin_url + 'return_suppliers/int_return_suppliers_view/' + id).done(function(response) {
            $('#return_suppliers_data').html(response);
            $('#return_suppliers').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew('tblreturn_suppliers', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#return_suppliers', function() {
        $('#return_suppliers_data').html('');
        tAPI.draw('page');
    });

    function view_adjusted(id) {
        $('#view_adjusted_data').html('');
        $.get(admin_url + 'adjusted/adjusted_data/' + id).done(function(response) {
            $('#view_adjusted_data').html(response);
            $('#view_adjusted').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew_ch('tbladjusted', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_adjusted', function() {
        $('#view_adjusted_data').html('');
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

    function view_export_different(id = null) {
        $('#export_different_data').html('');
        $.get(admin_url + 'export_different/int_export_different_view/' + id).done(function(response) {
            $('#export_different_data').html(response);
            $('#view_export_different').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            // changeRowNew('tblexport_different', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_export_different', function() {
        $('#export_different_data').html('');
        tAPI.draw('page');
    });
</script>
<script>
    function create_export() {
        var filterStatus = $('#filterStatus').val();
        var data = {};
        var dataString = '';
        // $.each(row_stranfer, function(index, value) {
        //     data[$(value).data('product')] = $(value).data('pod');
        //     dataString += '&items[' + index + '][product]=' + $(value).data('product');
        //     dataString += '&items[' + index + '][pod]=' + $(value).data('pod');
        //     dataString += '&items[' + index + '][quanliti]=' + $(value).data('quanliti');
        //     dataString += '&items[' + index + '][type]=product';
        // })
        window.open(admin_url + 'export_different/detail_warehouse?report=true&filterStatus=' + filterStatus, '_blank');
    }
</script>
</body>

</html>