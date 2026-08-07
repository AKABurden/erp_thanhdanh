<?php init_head(); ?>
<style type="text/css">
    .item-purchase .ui-sortable tr td input {
        width: 80px;
    }

    .select2-choice {
        height: 100% !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(),
                array('id' => 'purchase-form', 'class' => '_transaction_form invoice-form'));
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="additional"></div>
                    <div class="panel-body">
                        <?php
                        $type = '';
                        if (!isset($purchase)) {
                            $type = 'warning';
                        } elseif ($purchase->status == 0) {
                            $type = 'warning';
                        } elseif ($purchase->status == 1) {
                            $type = 'info';
                        } elseif ($purchase->status == 2) {
                            $type = 'success';
                        }

                        ?>
                        <div class="ribbon <?= $type ?>" project-status-ribbon-2="">
                            <?php
                            if (isset($purchase)) {
                                $status = format_purchase_status($purchase->status, '', false);
                            } else {
                                $status = format_purchase_status(-1, '', false);
                            }
                            ?>
                            <span><?= $status ?></span>
                        </div>
                        <?php
                        if (isset($purchase)) { ?>
                            <?php
                        }
                        ?>
                        <h4 class="bold no-margin font-medium">
                            <?php echo _l('ch_purchases_t'); ?>
                        </h4>
                        <hr/>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?= _l('lead_general_info') ?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <label for="number" class="control-label">
                                                    <small class="req text-danger">* </small>
                                                    <?php echo _l('ch_code_p'); ?>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <?php echo(isset($purchase) ? ($purchase->prefix) : get_option('prefix_purchase')); ?></span>
                                                        <?php
                                                        $number = sprintf('%06d',
                                                            ch_getMaxID('id', 'tblpurchases') + 1);
                                                        $value = (isset($purchase) ? ($purchase->code) : $number);
                                                        ?>
                                                        <input type="text" name="number" class="form-control"
                                                               value="<?= $value ?>" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <label for="date" class="control-label">
                                                    <small class="req text-danger">* </small>
                                                    <?php echo _l('ch_date_p'); ?>
                                                </label>
                                            </td>
                                            <td>
                                                <?php $value = (isset($purchase) ? _d($purchase->date) : _d(date('Y-m-d H:i:s'))); ?>
                                                <?php echo render_datetime_input('date', '', $value); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="name" class="control-label">
                                                    <?php echo _l('ch_name_p'); ?>
                                                </label>
                                            </td>
                                            <td>
                                                <?php $value = (isset($purchase) ? $purchase->name_purchase : _l('ch_purchases')); ?>
                                                <?php echo render_input('name', '', $value); ?>
                                            </td>
                                            <td>
                                                <label for="date" class="control-label">
                                                    <small class="req text-danger">* </small>
                                                    <?php echo _l('Ngày về NPL'); ?>
                                                </label>
                                            </td>
                                            <td>
                                                <?php $value = (isset($purchase) ? _d($purchase->delivery_date) : _d(date('Y-m-d H:i:s'))); ?>
                                                <?php echo render_datetime_input('delivery_date', '', $value); ?>
                                            </td>
                                            <td class="hide">
                                                <label for="type_items" class="control-label">
                                                    <?php echo _l('ch_type'); ?>
                                                </label>
                                            </td>
                                            <td class="hide">
                                                <?php echo render_select('type_items', $type_items,
                                                    array('type', 'name'), '', -1); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="reason" class="control-label">
                                                    <?php echo _l('ch_note_t'); ?>
                                                </label>
                                            </td>
                                            <td colspan="3">
                                                <?php $value = (isset($purchase) ? $purchase->explanation : ""); ?>
                                                <?php echo render_textarea('reason', '', $value); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div id="top_search_new" style="width: 100%;">
                                <div>
                                    <input style="float: right; height: 60px" type="search" id="SearchQR" class="form-control"
                                           placeholder="<?php echo _l('Quét mã sản phẩm...'); ?>">
                                </div>
                                <div id="top_search_button" class="top_search_button_scan" style="top: 15px">
                                    <button type="button" class="btn"><i class="fa fa-barcode" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot50">
                            <div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
                                <div class="panel-heading">
                                    <?= lang('tnh_info_items') ?>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive s_table">
                                        <table class="dt-tnh table item-purchases table-bordered table-hover"
                                               style="width: 100%;">
                                            <!-- <table class="table items item-purchases no-mtop dont-responsive-table"> -->
                                            <thead>
                                            <tr>
                                                <th width="120" class="text-center"><?php echo _l('ch_image'); ?><input
                                                            type="hidden" id="itemID" value=""/></th>
                                                <th width="300px" class="text-left"><i class="fa fa-exclamation-circle"
                                                                                     aria-hidden="true"
                                                                                     data-toggle="tooltip"
                                                                                     data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('ch_items_name_t'); ?>
                                                </th>
                                                <th width="100" class="text-center"><?php echo _l('item_unit'); ?></th>
                                                <th width="100"
                                                    class="text-center"><?php echo _l('item_quantity'); ?></th>
                                                <th width="100"
                                                    class="text-center confirm-danger"><?php echo _l('item_quantity_confirm'); ?></th>
                                                <th width="200" class="text-center"><?php echo _l('note'); ?></th>
                                                <th width="50"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $i = 0;
                                            $totalQuantity_approve = 0;
                                            $totalQuantity = 0;
                                            if (isset($purchase) && count($purchase->items) > 0) {
                                                foreach ($purchase->items as $value) {
                                                    ?>
                                                    <tr class="sortable item">
                                                        <td class="avatart text-center"><img
                                                                    style="border-radius: 50%;width: 4em;height: 4em;"
                                                                    src="<?= $value['avatar_1'] ?>"><br>
                                                            <?= format_item_purchases($value['type']) ?><input
                                                                    type="hidden" id="type"
                                                                    name="items[<?php echo $i; ?>][type]"
                                                                    value="<?php echo $value['type']; ?>"/>
                                                            <input type="hidden" id="product_id"
                                                                   name="items[<?php echo $i; ?>][id]"
                                                                   value="<?php echo $value['product_id']; ?>">
                                                        </td>
                                                        <td style="width: 450px"><input type="hidden" class="count"
                                                                   value="<?php echo $i; ?>"/><input type="hidden"
                                                                                                     id="type"
                                                                                                     value="<?php echo $value['type']; ?>"/>
                                                            <!-- <?php echo render_select('custom_item_select_' . $i,
                                                                get_options_search_cbo('items', $value['product_id'],
                                                                    $value['type']), array('id', 'name'), '',
                                                                $value['product_id'], array(), array(), '',
                                                                'custom_item_select'); ?> -->
                                                            <div style="width: 450px">
                                                            <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>"
                                                                   id="custom_item_select_<?= $i ?>"
                                                                   name="custom_item_select_<?= $i ?>"
                                                                   class="custom_item_select"
                                                                   type-id="<?= $value['type'] ?>"
                                                                   data-id="<?= $value['product_id'] ?>"
                                                                   style="width: 100%"><br><br>
                                                            <div class="color"><?= format_item_color($value['product_id'],
                                                                    $value['type']) ?></div>
                                                            </div>
                                                        </td>
                                                        <td><?= $value['unit'] ?></td>
                                                        <td style="width: 100px;"><input style="width: 100px"
                                                                                         class="mainQuantity H_input"
                                                                                         type="text"
                                                                                         name="items[<?php echo $i; ?>][quantity]"
                                                                                         value="<?= $value['quantity'] ?>"/>
                                                        </td>
                                                        <td class="danger" style="width: 100px;"><input
                                                                    style="width: 100px" class="mainQuantityNet H_input"
                                                                    type="text"
                                                                    name="items[<?php echo $i; ?>][quantity_net]"
                                                                    value="<?= $value['quantity_net'] ?>"/></td>
                                                        <td><textarea style="width: 100%;" class="note"
                                                                      name="items[<?php echo $i; ?>][note]"
                                                                      value="<?= $value['note'] ?>"><?= $value['note'] ?></textarea>
                                                        </td>
                                                        <td><a href="#" class="btn btn-danger pull-right"
                                                               onclick="deleteTrItem(this); return false;"><i
                                                                        class="fa fa-times"></i></a></td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                    $totalQuantity += $value['quantity'];
                                                    $totalQuantity_approve += $value['quantity_net'];
                                                }
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <div style="width: 85%" class="pull-left">
                    <table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
                        <tbody>
                        <tr>
                            <td><span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                            </td>
                            <td class="total_quantity_all">
                                <?php echo $totalQuantity ?>
                            </td>
                            <td><span class="bold"><?php echo _l('item_quantity_approve'); ?> :</span>
                            </td>
                            <td class="total_quantity_approve">
                                <?php echo $totalQuantity_approve ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-info pull-right only-save customer-form-submiter">
                    <?php echo _l('submit'); ?>
                </button>
                <a href="<?= admin_url('purchases'); ?>" style="margin-right: 10px;"
                   class="btn btn-default pull-right only-save">
                    <?php echo _l('go_back'); ?>
                </a>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    var token = "<?= $this->security->get_csrf_token_name() ?>";
    var hash = "<?= $this->security->get_csrf_hash() ?>";
    $(function () {
        createTrItemfist();
        var dt = $('.item-purchases').DataTable({
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'fixedHeader': true,
            // scrollY: true,
            // scrollY: '150px',
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function (settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
        });
    });

    function init_ajax_searchs(e, t, a, i) {
        var n = $("body").find(t);
        var h = t;
        if (n.length) {
            var s = {
                ajax: {
                    url: void 0 === i ? admin_url + "misc/get_relation_data" : i,
                    data: function () {
                        var type = $('#type_items').val();
                        var id_order = $('#id_order').val();
                        var t = {
                            [csrfData.token_name]: csrfData.hash
                        };
                        return t.type = e, t.rel_id = "", t.q = "{{{q}}}", t.type_items = type, t.id_order = id_order, void 0 !== a && jQuery.extend(t, a), t
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
                preprocessData: function (e) {
                    for (var t = [], a = e.length, i = 0; i < a; i++) {
                        var n = {
                            value: e[i].id,
                            text: e[i].name,
                            type_items: e[i].type_items
                        };
                        t.push(n)
                    }
                    findItemselect(t, h);
                },
                preserveSelectedPosition: "after",
                preserveSelected: !0
            };
            n.data("empty-title") && (s.locale.emptyTitle = n.data("empty-title")), n.selectpicker().ajaxSelectPicker(s);

        }
    }

    var findItemselect = (data, h) => {
        setTimeout(function () {
            $(h).find('option:gt(0)').remove();
            $(h).selectpicker('refresh');
            var count = data.length;
            var html = '';
            $.each(data, function (key, value) {
                if (key == 0) {
                    html += '<optgroup label="' + value.text + '">';
                } else if (value.value == 'h') {
                    html += '</optgroup>';
                    html += '<optgroup label="' + value.text + '">';
                } else {
                    html += '<option data-id=' + value.type_items + ' value="' + value.value + '">' + value.text + '</option>';
                }
            });
            html += '</optgroup>';
            $(h).html(html);
            $(h).selectpicker('refresh');
            if (count > 3) {
                $(h).parents().find('.status').addClass('hide');
            } else {
                $(h).parents().find('.status').removeClass('hide');
            }
        }, 1);
    };
    $(function () {
        // validate_invoice_form();
        _validate_form($('#purchase-form'), {
            date: "required",
            number: "required"
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
    countrow();

    function countrow() {
        var items = $('table.item-purchases tbody').find('tr.item');
        $.each(items, (index, value) => {
            var type = $(value).find('td:nth-child(1)').find('input#type').val();
            var name_type = '<span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(type) + '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);


        });
    }

    countrow_2();

    function countrow_2() {
        var items = $('table.item-purchases tbody').find('tr.item');
        $.each(items, (index, value) => {
            var ID = $('#custom_item_select_' + index).attr('data-id');
            var type = $('#custom_item_select_' + index).attr('type-id');
            ajaxSelectCallBack($('#custom_item_select_' + index), "<?= admin_url('purchases/SearchItems') ?>", ID, type);
        });
    }

    $(document).on('change', '.custom_item_select', (e) => {
        var currentQuantityInput = $(e.currentTarget);

        var id = $(currentQuantityInput).val();
        if (id == '') {
        } else {
            var type = currentQuantityInput.select2('data').type;
            var id_order = $('#id_order').val();
            $.post(admin_url + 'import/get_items/' + id + '/' + type, {
                [csrfData['token_name']]: csrfData['hash']
            }, function (item) {
                var item = JSON.parse(item);
                createTrItem(item, currentQuantityInput, type);
            });
        }
    });

    function countrowfist() {
        if (!$('table.item-purchases tbody tr.item').find('input[value=hau]').length) {
            createTrItemfist();
        }
    }

    var uniqueArray = <?= $i ?>;
    var createTrItemfist = (item) => {
        var newTr = $('<tr class="sortable item"></tr>');
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var td1 = $('<td class="avatart text-center"><input type="hidden" name="items[' + uniqueArray + '][type]" value="hau" />' + name_type + '</td>');
        var td2 = $('<td><input type="hidden" class="count" value="' + uniqueArray + '" /><div class="form-group "  style="width: 450px">\
								             <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_' + uniqueArray + '" name="custom_item_select_' + uniqueArray + '" style="width: 100%">\
								        <br><br><div class="color"></div></td>');
        var td3 = $('<td class="unit_name text-center"></td>');
        var td4 = $('<td style="width: 100px;"><input style="width: 100px" class="mainQuantity H_input" type="text" name="items[' + uniqueArray + '][quantity]" value="1" /></td>');
        var td5 = $('<td class="danger" style="width: 100px;"><input style="width: 100px" class="mainQuantityNet H_input"  type="text" name="items[' + uniqueArray + '][quantity_net]" value="1" /></td>');
        var td6 = $('<td><textarea style="width: 100%;" class="note" name="items[' + uniqueArray + '][note]"></textarea></td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append('<td class="delete"></td');
        $('table.item-purchases tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        ajaxSelectCallBack($('#custom_item_select_' + uniqueArray), "<?= admin_url('purchases/SearchItems') ?>", 0);

        uniqueArray++;
        getTotalPrice();
        reset_item_select();
    }

    var createTrItem = (item, currentQuantityInput, type) => {
        if (typeof (item) == 'undefined' || item.length == 0) return;
        if (($('table.item-purchases tbody tr').find('input[value=' + item.id + ']#product_id').length > 0) && ($('table.item-purchases tbody tr').find('input[value=' + type + ']#type').length > 0)) {
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng kiểm tra lại!");
            return;
        }
        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;" src="' + item.avatar + '"><br><span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">' + findItem(type) + '</span>	';
        var new_tr = currentQuantityInput.parents('tr');
        var count = new_tr.find('td > input.count').val();
        new_tr.find('td.avatart').html(name_type + '<input type="hidden" id="type" name="items[' + count + '][type]" value="' + type + '" /><input type="hidden" class="id" id="product_id" name="items[' + count + '][id]" value="' + item.id + '" />');
        var unit_name = item.unit_name;
        if (item.unit_name == null) {
            unit_name = '';
        }
        new_tr.find('td.unit_name').html(unit_name);
        new_tr.find('.color').html(item.html)
        new_tr.find('td.delete').html('<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>');
        getTotalPrice();
        countrowfist();
    }
    $(document).on('change', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() == '') {
            currentQuantityInput.val(1);
            currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(1);
        }
        getTotalPrice();
    });
    $(document).on('change', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() == '') {
            currentQuantityInput.val(1);
        }
        getTotalPrice();
    });
    $(document).on('click', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(1);
            currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(1);
        }
        if (currentQuantityInput.val() == '-0') {
            currentQuantityInput.val(1);
            currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(1);
        }
        var totalss = unformat_number(currentQuantityInput.val());
        currentQuantityInput.val(formatNumber(totalss));
        currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(formatNumber(totalss));
        getTotalPrice();
    });
    $(document).on('keyup', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(1);
            currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(1);
        }
        if (currentQuantityInput.val() == '-0') {
            currentQuantityInput.val(1);
            currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(1);
        }
        var totalss = unformat_number(currentQuantityInput.val());
        currentQuantityInput.val(formatNumber(totalss));
        currentQuantityInput.parents('tr').find('input.mainQuantityNet').val(formatNumber(totalss));
        getTotalPrice();
    });
    $(document).on('click', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(1);
        }
        if (currentQuantityInput.val() == '-0') {
            currentQuantityInput.val(1);
        }
        getTotalPrice();
    });
    $(document).on('keyup', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(1);
        }
        if (currentQuantityInput.val() == '-0') {
            currentQuantityInput.val(1);
        }
        getTotalPrice();
    });
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };

    function getTotalPrice() {
        countrow();
        var items = $('table.item-purchases tbody').find('tr.item');
        var totalQuantity_approve = 0;
        var totalQuantity = 0;
        $.each(items, (index, value) => {
            if (!empty($(value).find('#type').val())) {
                totalQuantity += parseFloat($(value).find('td:nth-child(4) > input').val().replace(/\,/g, ''));
                totalQuantity_approve += parseFloat($(value).find('td:nth-child(5) > input').val().replace(/\,/g, ''));
            }
        });
        $('.total_quantity_all').text(totalQuantity);
        $('.total_quantity_approve').text(totalQuantity_approve);
    }

    $('#purchase-form').on('submit', (e) => {
        if ($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');
        }
    });

    function reset_item_select() {
        $('#custom_item_select').html('');
        $('#custom_item_select').selectpicker('refresh');
    }

    $(document).ready(function () {
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "auto");
        })
    });

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
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
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
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
                formatResult: repoFormatSelection_ch,
                formatSelection: repoFormatSelection_ch,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) {
                    return m;
                }
            });
        }
    }


    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr = nStr.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        return nStr;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };

    $('body').on('change', '#SearchQR', function (e) {
        var code = $(this).val();
        if (code) {
            $.ajax({
                url: "<?= base_url() ?>" + 'admin/purchases/SearchQR',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    code: code,
                    csrf_token_name: hash,
                },
            })
                .done(function (data) {
                   if (data.result){
                       alert_float('success',data.message);
                       createItems(data.items);
                   } else {
                       alert_float('danger',data.message);
                   }
                })
                .fail(function () {
                });
        }
        $('#SearchQR').val('');
    })

    function createItems(item){
        if (($('table.item-purchases tbody tr').find('input[value=' + item.id + ']#product_id').length > 0) && ($('table.item-purchases tbody tr').find('input[value=' + item.type + ']#type').length > 0)) {
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng kiểm tra lại!");
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $(`<td class="avatart text-center"><img style="border-radius: 50%;width: 4em;height: 4em;"  src="${item.avatar}"><br><span class="label label-default inline-block customer-group-list pointer" style="border:1px solid #e30000">${findItem(item.type)}</span>
        <input type="hidden" id="type" name="items[${uniqueArray}][type]" value="${item.type}" /><input type="hidden" class="id" id="product_id" name="items[${uniqueArray}][id]" value="${item.id}" />
        </td>`);
        var td2 = $(`<td><input type="hidden" class="count" value="${uniqueArray}" />
        <div class="form-group"  style="width: 450px">
             <input style="width:100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_${uniqueArray}" name="custom_item_select_${uniqueArray}" style="width: 100%" value="">\
        <br><br><div class="color">${item.html}</div></td>`);
        var unit_name = item.unit_name;
        if (item.unit_name == null) {
            unit_name = '';
        }
        var td3 = $(`<td class="unit_name text-center">${unit_name}</td>`);
        var td4 = $(`<td style="width: 100px;"><input style="width: 100px" class="mainQuantity H_input" type="text" name="items[` + uniqueArray + `][quantity]" value="1" /></td>`);
        var td5 = $(`<td class="danger" style="width: 100px;"><input style="width: 100px" class="mainQuantityNet H_input"  type="text" name="items[` + uniqueArray + `][quantity_net]" value="1" /></td>`);
        var td6 = $(`<td><textarea style="width: 100%;" class="note" name="items[` + uniqueArray + `][note]"></textarea></td>`);
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append('<td class="delete"><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-purchases tbody').prepend(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        ajaxSelectCallBack($('#custom_item_select_' + uniqueArray), "<?= admin_url('purchases/SearchItems') ?>", item.id,item.type);
        uniqueArray++;
        getTotalPrice();
        reset_item_select();
    }
</script>