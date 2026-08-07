<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-inventory img {
        height: 20px;
        width: 20px;
    }

    .table-inventory thead tr th {
        text-align: center;
    }

    .table-inventory tr td:nth-child(2) {
        min-width: 95px;
        white-space: unset;
        text-align: center;
    }

    .table-inventory tr td:nth-child(1) {
        min-width: 110px;
        white-space: unset;
        text-align: center;

    }

    .table-inventory tr td:nth-child(3) {
        min-width: 125px;
        white-space: unset;
        text-align: center;
    }

    .table-inventory tr td:nth-child(4) {
        min-width: 125px;
        white-space: unset;
        text-align: center;

    }

    .table-inventory tr td:nth-child(5) {
        min-width: 120px;
        white-space: unset;
    }

    .table-inventory tr td:nth-child(6) {
        min-width: 100px;
        white-space: unset;
    }

    .table-inventory tr td:nth-child(7) {
        min-width: 250px;
        white-space: unset;
    }

    .table-inventory tbody tr td:nth-child(8) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-inventory tbody .dropdown {
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if (has_permission('inventory', '', 'create')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?= admin_url('inventory/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?></a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" aria-expanded="true" style="">
                    <div class="col-md-6">
                        <div class="form-group" id="items">
                            <input type="text" name="type_items" class="hide" id="type_items">
                            <label for="months-report"><?php echo _l('tnh_items'); ?></label><br />
                            <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select" name="custom_item_select" style="width: 100%">
                        </div>
                    </div>
                </div>
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
                                            <?= _l('leads_all') ?> (<span class="all">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('ch_confirm_22') ?> (<span class="status0">0</span>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('dont_approve') ?> (<span class="status1">0</span>)
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
                            _l('warehouse'),
                            _l('ch_status'),
                            _l('ch_note'),
                            _l('ch_option'),
                        );
                        render_datatable($table_data, 'inventory');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div class="modal fade in" id="view_adjusted_quantity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('test_quantyti_time '); ?><span class="date_time"></span></span>
                </h4>
            </div>
            <?php
            echo form_open(admin_url('inventory/update_submit'), array('id' => 'inventory-form', 'class' => '_transaction_form invoice-form'));
            ?>
            <input type="text" id="id_inventory" class="hide" name="id_inventory" value="">
            <div class="modal-body">
                <div class="row">
                    <div class="panel-body">
                        <div class="table-responsive" style="height: 400px;">
                            <table class="dt-tnh table item-inventory table-bordered table-hover mtop0 mbot0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 100px"></th>
                                        <th style="width: 300px" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('item_unit'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('quantity_change'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('ch_quantity_time'); ?></th>
                                        <th style="width: 100px" class="text-center"><?php echo _l('ch_difference'); ?></th>
                                        <th style="width: 200px" class="text-center"><?php echo _l('ch_handling'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info  form-submitersss">
                        <?php echo _l('Áp dụng và lưu'); ?>
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="view_inventory_data"></div>
<div id="view_adjusted_data"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $(function() {
        var dt = $('.item-purchases').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
    });
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
            'type_items': '[name="type_items"]',
            'custom_item_select': '[name="custom_item_select"]',
        };
        tAPI = initDataTableCustom('.table-inventory', admin_url + 'inventory/table', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1, 'desc'))); ?>, fixedColumns = {
            leftColumns: 2,
            rightColumns: 1
        });
        // var tAPI = initDataTable('.table-inventory', admin_url+'inventory/table', [0], [0], CustomersServerParams,[0, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                tAPI.draw('page');
            });
        });
        $('.table-inventory').on('draw.dt', function() {
            get_total_limit();
        });
    });
    var itemList = <?php echo json_encode($type_items); ?>;
    var findItem = (type) => {
        var itemResult;
        $.each(itemList, (index, value) => {
            if (value.type == type) {
                itemResult = value.name;
                return false;
            }
        });
        return itemResult;
    };

    function var_status(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>inventory/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success == true) {
                        tAPI.draw('page');
                        alert_float(response.alert_type, response.message);
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

    function view_inventory(id) {
        $('#view_inventory_data').html('');
        $.get(admin_url + 'inventory/inventory_data/' + id).done(function(response) {
            $('#view_inventory_data').html(response);
            $('#view_inventory').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew_ch('tblinventory', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_inventory', function() {
        $('#view_inventory_data').html('');
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
                url: "<?= admin_url() ?>inventory/confirm_warehous",
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

    function get_total_limit() {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>inventory/count_all/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $('.all').html(data.all);
                $('.status0').html(data.status0);
                $('.status1').html(data.status1);
            }
        });
    }

    function create_adjusted(id) {
        dataString = {
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>inventory/test_quantity_times/" + id,
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                if (empty(data.item)) {
                    // window.open('<?= admin_url('adjusted/create/') ?>'+id);
                    window.location.href = '<?= admin_url('adjusted/create/') ?>' + id;
                } else {
                    $('table.item-inventory tbody').html('');
                    $('.date_time').html(data.inventory.date);
                    $('#id_inventory').val(data.inventory.id);

                    $.each(data.item, function(i, v) {
                        var newTr = $('<tr class="sortable item"></tr>');
                        var td1 = $('<td class="text-center"><img style="border-radius: 50%;width: 2em;height: 2em;" src="' + v.items.avatar_1 + '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(v.type) + '</span><input class="hide id"  name="items[' + i + '][id]" value="' + v.id + '" />' +
                            '<input class="hide localtion" name="items[' + i + '][localtion]" value="' + v.localtion + '" /><input class="hide type" name="items[' + i + '][type]" value="' + v.type + '" /></td>');
                        var td2 = $('<td class="dragger">' + v.items.name + '<br>' + v.name_localtion + '</td>');
                        var td3 = $('<td>' + v.items.unit_name + '</td>');
                        var td4 = $('<td><input readonly style="width:100px"  class="mainQuantity H_input" type="number" name="items[' + i + '][quantity]" value="' + v.get_quantity_time + '" /></td>');
                        var td5 = $('<td><input style="width:100px" id="mainQuantityNet_' + i + '" class="mainQuantityNet H_input" type="number" name="items[' + i + '][quantity_net]" value="' + v.quantity_net + '" /></td>');
                        var td6 = $('<td><input readonly style="width:100px" class="mainQuantityDiff H_input" type="number" name="items[' + i + '][quantity_diff]" value="" /></td>');

                        var td7 = $('<td><input class="handling" type="hidden" name="items[' + i + '][handling]" value=""></td>');


                        newTr.append(td1);
                        newTr.append(td2);
                        newTr.append(td3);
                        newTr.append(td4);
                        newTr.append(td5);
                        newTr.append(td6);
                        newTr.append(td7);
                        $('table.item-inventory tbody').append(newTr);
                        $('#mainQuantityNet_' + i).change();
                    })
                    var items = $('table.item-inventory tbody tr');

                    $('#view_adjusted_quantity').modal('show');
                }
            }
        });
    }
    $(document).on('change', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        quantity = $(e.currentTarget).parents('tr').find('input.mainQuantity');
        quantityDiff = $(e.currentTarget).parents('tr').find('input.mainQuantityDiff');
        handlingInput = $(e.currentTarget).parents('tr').find('input.handling');
        handlingTd = handlingInput.parent();
        var diff = Number(currentQuantityInput.val()) - Number(quantity.val());
        var handling = '';
        if (diff > 0) handling = '<?= _l('ch_handling_up') ?> ' + Math.abs(diff);
        if (diff < 0) handling = '<?= _l('ch_handling_down') ?> ' + Math.abs(diff);
        handlingTd.text(handling);
        handlingInput.val(handling);
        handlingTd.append(handlingInput);
        quantityDiff.val(Number(currentQuantityInput.val()) - Number(quantity.val()));
    });
    appValidateForm($('#inventory-form'), {}, manage_inventory);

    function manage_inventory(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                tAPI.draw('page');
                $('#view_adjusted_quantity').modal('hide');
                create_adjusted(response.id);
            }

        })
        return false;
    }

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

    $('#custom_item_select').on('change', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() != '') {
            var type = currentQuantityInput.select2('data').type;
            $('#type_items').val(type);
        } else {
            $('#type_items').val('');
        }
        $('#type_items').change();
    });

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
                            type: $('#type_items').val(),
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
                            type: -1,
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

        return state.text + ' - ' + '(' + state.code + ')';
    }
    ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('inventory/SearchItems_new') ?>", 0);
</script>