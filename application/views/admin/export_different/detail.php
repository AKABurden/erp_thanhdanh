<?php init_head(); ?>
<style type="text/css">
    .item-items .ui-sortable tr td input {
        width: 80px;
    }

    .select2-choice {
        height: auto !important;
        ;
        min-height: 35px !important;
        ;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'import-form', 'class' => '_transaction_form invoice-form'));
            if (isset($invoice)) {
                echo form_hidden('isedit');
            }
            ?>
            <input type="text" class="hide" id="id_return" value="<?= (isset($items) ? $items->id : '') ?>">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="additional"></div>
                    <div class="panel-body">
                        <?php
                        $type = '';
                        if (!isset($items))
                            $type = 'warning';
                        elseif ($items->status == 0)
                            $type = 'warning';
                        elseif ($items->status == 1)
                            $type = 'info';
                        elseif ($items->status == 2)
                            $type = 'success';

                        ?>
                        <div class="ribbon <?= $type ?>" project-status-ribbon-2="">
                            <?php
                            if (isset($items)) {
                                $status = format_import_status($items->status, '', false);
                            } else {
                                $status = format_import_status(-1, '', false);
                            }
                            ?>
                            <span><?= $status ?></span>
                        </div>
                        <?php if (isset($items)) { ?>
                        <?php } ?>
                        <h4 class="bold no-margin font-medium">
                            <?php echo $title; ?>
                        </h4>
                        <hr />
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="alert alert-warning text-center total_debt hide"></div>
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?= _l('lead_general_info') ?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>
                                                <td style="width: 15%">
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_code_p'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 35%">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <?php echo (isset($items) ? ($items->prefix) : get_option('prefix_export_different')); ?>-</span>
                                                            <?php
                                                            $number = sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1);
                                                            $value = (isset($items) ? ($items->code) : $number);
                                                            ?>
                                                            <input type="text" name="number" class="form-control" value="<?= $value ?>" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 15%">
                                                    <label for="date" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_date_p'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 35%">
                                                    <?php $value = (isset($items) ? _d($items->date) : _d(date('Y-m-d'))); ?>
                                                    <?php echo render_date_input('date', '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('ch_type'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo render_select('type_items', $type_items, array('type', 'name'), '', -1);
                                                    ?>
                                                </td>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_type_objects'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <?php $list_object = array(
                                                        array(
                                                            'id' => 1,
                                                            'name' => _l('ch_IN_client')
                                                        ),
                                                        array(
                                                            'id' => 2,
                                                            'name' => _l('ch_IN_suppliers')
                                                        ),
                                                        array(
                                                            'id' => 3,
                                                            'name' => _l('ch_IN_staff')
                                                        ),
                                                        array(
                                                            'id' => 4,
                                                            'name' => _l('ch_IN_other')
                                                        ),
                                                    ); ?>
                                                    <?php $value = (isset($items) ? $items->object : ''); ?>
                                                    <?php echo render_select('object', $list_object, array('id', 'name'), '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= lang('Chi nhánh', 'id_branch') ?></td>
                                                <td>
                                                    <?php $branchs = getListBranch();
                                                    $branch_id = 0;
                                                    $branch_id = (isset($items) ? $items->id_branch : 0);
                                                    ?>
                                                    <select name="id_branch" id="id_branch" class="id_branch  " required="required" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                                        <option value=""></option>
                                                        <?php if (!empty($branchs)) { ?>
                                                            <?php foreach ($branchs as $key => $value) { ?>
                                                                <option <?= $branch_id == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                <td>
                                                    <label for="type_items" class="control-label">
                                                        <?php echo _l('cong_object'); ?>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="append_id_object">
                                                        <input data-placeholder="<?= _l('ch_list_objects') ?>" name="id_object" style="width: 100%" id="id_object">
                                                    </div>
                                                    <div class="ch_list_object hide">
                                                        <div class="form-group id">
                                                            <input type="text" id="object_text" name="object_text" class="form-control object_text" value="<?= (!empty($items) ? $items->object_text : '') ?>">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="hide">
                                                    <?= lang('Lệnh sản xuất tổng', 'po_id') ?>
                                                </td>
                                                <td class="hide">
                                                    <input type="text" name="po_id" data-placeholder="<?= lang('Lệnh sản xuất tổng') ?>" id="po_id" class="po_id" style="width: 100%;" value="<?= (!empty($items) ? $items->po_id : '') ?>">
                                                </td>
                                                <td>
                                                    <?= lang('Loại', 'type_po') ?>
                                                </td>
                                                <td>
                                                    <select name="type_po" id="type_po" data-none-selected-text="<?= lang('Loại') ?>" class="form-control selectpicker">
                                                        <option value=""></option>
                                                        <option <?= (!empty($items) && $items->type_po == 1 ? 'selected' : '') ?> value="1"><?= lang('Xuất khuôn bể') ?></option>
                                                        <option <?= (!empty($items) && $items->type_po == 2 ? 'selected' : '') ?> value="2"><?= lang('Xuất kẽm') ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="note" class="control-label">
                                                        <?php echo _l('ch_note_t'); ?>
                                                    </label>
                                                </td>
                                                <td colspan="3">
                                                    <?php $value = (isset($items) ? $items->note : ""); ?>
                                                    <?php echo render_textarea('note', '', $value, array('rows' => 3)); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot50">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <?= lang('tnh_info_items') ?>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table style="table-layout: fixed;" class="dt-tnh table item-import table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px;" class="center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                                    <th style="width: 200px;" class="text-center">
                                                        <?php echo _l('ch_items_name_t'); ?></th>
                                                    <th style="width: 200px;" class="text-center">
                                                        <?php echo _l('warehouse'); ?></th>
                                                    <th style="width: 250px;" class="text-center hide">
                                                        <?php echo _l('warehouse_localtion'); ?></th>
                                                    <th style="width: 160px;" class="text-center">
                                                        <?php echo lang('Lệnh sản xuất tổng'); ?></th>
                                                    <th style="width: 100px;" class="text-center">
                                                        <?php echo _l('item_unit'); ?></th>
                                                    <th style="width: 100px;" class="text-center">
                                                        <?php echo _l('ch_warehouse_reports'); ?></th>
                                                    <th style="width: 100px;" class="text-center">
                                                        <?php echo _l('quantity'); ?></th>
                                                    <th style="width: 100px;" class="text-center">
                                                        <?php echo _l('cong_price_thinh'); ?></th>
                                                    <th style="width: 100px;" class="text-center">
                                                        <?php echo _l('invoice_total'); ?></th>
                                                    <th style="width: 150px;" class="text-center">
                                                        <?php echo _l('note'); ?></th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 0;
                                                $totalQuantity_approve = 0;
                                                $totalQuantity = 0;
                                                if (isset($items) && count($items->items) > 0) {
                                                    foreach ($items->items as $value) {
                                                ?>
                                                        <tr class="sortable item">
                                                            <td class="dragger avatart" style="text-align: center;"><img style="border-radius: 50%;width: 4em;height: 4em;" src="<?= (!empty($value['avatar']) ? (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : (file_exists('uploads/tools_supplies/' . $value['avatar']) ? base_url('uploads/tools_supplies/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg')) ?>"><br><input class="hidden" id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" />
                                                                <div id="type_name"></div>
                                                                <input type="hidden" class="id" name="items[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
                                                            </td>
                                                            <td><input type="hidden" id="type" name="items[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>" /><input type="hidden" class="count" value="<?php echo $i; ?>" />
                                                                <!-- <?php echo render_select('custom_item_select_' . $i, get_options_search_cbo('items', $value['product_id'], $value['type']), array('id', 'name'), '', $value['product_id']); ?> -->
                                                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="custom_item_select_<?= $i ?>" style="width: 100%;" name="custom_item_select_<?= $i ?>" class="custom_item_select" type-id="<?= $value['type'] ?>" data-id="<?= $value['product_id'] ?>" style="width: 100%"><br><br>
                                                                <div class="color">
                                                                    <?= format_item_color($value['product_id'], $value['type']) ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group" style="width: 100%">
                                                                    <select style="width: 100%;" data-id="<?= $value['id_warehouse_items'] ?>" class="warehouses_id" id="warehouses_id_<?= $i ?>" name="items[<?= $i ?>][warehouses_id]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                        <option></option>
                                                                        <?php foreach ($warehouse as $k => $v) {
                                                                        ?>
                                                                            <option <?= ($value['warehouses_id'] == $v['id']) ? 'selected' : '' ?> value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
                                                                        <?php
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="hide">
                                                                <div class="form-group" style="width: 100%">
                                                                    <select style="width: 100%;" data-id="<?= $value['localtion_warehouses_id'] ?>" class="localtion_warehouses_id" id="localtion_warehouses_id_<?= $i ?>" name="items[<?= $i ?>][localtion_warehouses_id]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="items[<?= $i ?>][po_id]" id="po_id_<?= $i ?>" placeholder="<?= lang('Lệnh sản xuất tổng') ?>" class="" value="<?= $value['po_id'] ?>" style="width: 100%; min-width: 150px;">
                                                            </td>
                                                            <td>
                                                                <?= $value['unit_name_stock'] ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="mainQuantity_supp_text">
                                                                    <?= formatNumber($value['quantity_warehoue']) ?></div><input class="mainQuantity_supp height_auto hide" type="number" name="items[<?= $i ?>][mainQuantity_supp]" value="<?= formatNumber($value['quantity_warehoue']) ?>" />
                                                            </td>
                                                            <td>
                                                                <input class="mainQuantityNet H_input height_auto" type="text" name="items[<?= $i ?>][quantity_net]" value="<?= formatNumber($value['quantity_net']) ?>" />
                                                            </td>
                                                            <td>
                                                                <input onkeyup="formatNumBerKeyUpCus(this)" class="height_auto H_input align_right price" type="text" name="items[<?= $i ?>][price]" value="<?= number_format($value['price']) ?>" />
                                                            </td>
                                                            <td class="align_right amount"><?= number_format($value['amount']) ?>
                                                            </td>
                                                            <td><textarea style="width: 100%;" class="note" name="items[<?php echo $i; ?>][note]" value="<?= $value['note'] ?>"><?= $value['note'] ?></textarea>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;">
                                                                    <i class="fa fa-times"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function () {
                                                                ajaxSelectParamsCallback('#po_id_<?= $i ?>', 'admin/manufactures/searchProductionsOrders', $('#po_id_<?= $i ?>').val(), false, true);
                                                            });
                                                        </script>
                                                <?php
                                                        $i++;
                                                        // $totalQuantity+=$value['quantity'];
                                                        // $totalQuantity_approve+=$value['quantity_net'];
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
                <div style="width: 85%" class=" pull-left">
                    <table class="table tnh-tb noMargin table-color_sum dont-responsive-table">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="bold"><?php echo _l('item_quantity_all'); ?> :</span>
                                </td>
                                <td class="total_quantity_all">
                                    <?php echo formatNumber($totalQuantity) ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('item_quantity_approve'); ?> :</span>
                                </td>
                                <td class="total_quantity_approve">
                                    <?php echo formatNumber($totalQuantity_approve) ?>
                                </td>
                                <td>
                                    <span class="bold"><?php echo _l('total_price'); ?> :</span>
                                </td>
                                <td class="total_price">
                                    0
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-info pull-right only-save customer-form-submiter">
                    <?php echo _l('submit'); ?>
                </a>
                <a style="margin-right: 10px;" href="<?= admin_url('return_suppliers') ?>" class="btn btn-default pull-right">
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
    $('.customer-form-submiter').on('click', (e) => {
        if ($('input.error').length) {
            e.preventDefault();
            alert('<?= _l('ch_invalid_value') ?>');
            return;
        }
        var a = confirm("<?= _l('ch_you_want_update') ?>");
        if (a === false) {
            e.preventDefault();
        } else {
            $('#import-form').submit();
        }
    });

    function add_validate_form() {
        _validate_form($('#import-form'), {
            date: "required",
            suppliers_id: "required",
            number: "required",
            warehouse_id: "required",
            object: "required",
            id_object: "required",
            object_text: "required",
            id_branch: "required",
            localtion_warehouses_id: "required"
        });
    }
    $(function() {
        $("#id_branch").select2();
        var warehouse_id = $('#warehouse_id').val();
        if (warehouse_id != '') {
            if (!$('table.item-import tbody tr.item').find('input[value=hau]').length) {
                createTrItemfist();
            }
        }
        // validate_invoice_form();
        var dt = $('.item-import').DataTable({
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
        // hauhau
        add_validate_form();

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
    appendtype();

    function appendtype() {
        var items = $('table.item-import tbody').find('tr.item');
        $.each(items, (index, value) => {
            var type = $(value).find('td:nth-child(2)').find('input#type').val();
            var name_type =
                '<span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +
                findItem(type) + '</span>';
            $(value).find('td:nth-child(1)').find('div#type_name').html(name_type);
            // init_ajax_searchs('items','#custom_item_select_'+index);
            var ID = $('#custom_item_select_' + index).attr('data-id');
            var type = $('#custom_item_select_' + index).attr('type-id');
            ajaxSelectCallBack($('#custom_item_select_' + index), "<?= admin_url('purchases/SearchItems') ?>", ID,
                type);

            $('#localtion_warehouses_id_' + index).select2();
            loadLocaltion_warehouses_id(index,ID);
            // $('#warehouses_id_' + index).select2();
            // loadLocaltion_warehouses_ch(index);

        });
    }

    function countrow() {
        if (!$('table.item-import tbody tr.item').find('input[value=hau]').length) {
            createTrItemfist();
        }
    }
    $(document).on('change', '.custom_item_select', (e) => {
        var warehouse_id = $('#warehouse_id').val();
        var currentQuantityInput = $(e.currentTarget);
        var suppliers_id = $('#suppliers_id').val();
        var id = $(currentQuantityInput).val();
        if (id == '') {} else {
            var type = currentQuantityInput.select2('data').type;
            $.post(admin_url + 'return_suppliers/get_items/' + id + '/' + type + '/' + suppliers_id, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(item) {
                var item = JSON.parse(item);
                createTrItem(item, currentQuantityInput, type);
            });
        }
    });
    var uniqueArray = <?= $i ?>;
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    var createTrItem = (item, currentQuantityInput, type) => {
        if (typeof(item) == 'undefined' || item.length == 0) return;
        // if (($('table.item-import tbody tr').find('input[value=' + item.id + '].id ').length > 0) && ($(
        //         'table.item-import tbody tr').find('input[value=' + type + ']#type ').length > 0)) {
        //     alert_float('danger', '<?= _l('ch_exsit_items_rfq') ?>');
        //     return;
        // }

        var name_type = '<img style="border-radius: 50%;width: 4em;height: 4em;" src="' + item.avatar +
            '"><br><span class="label label-default mleft5 inline-block customer-group-list pointer" style="border:1px solid #e30000">' +
            findItem(type) + '</span>';
        var new_tr = currentQuantityInput.parents('tr');
        var count = new_tr.find('td > input.count').val();
        new_tr.find('.color').html(item.color);
        new_tr.find('.mainQuantity_supp_text').html(item.quantity_warehoue);
        new_tr.find('td > input.mainQuantity_supp').val(item.quantity_warehoue);
        new_tr.find('td.avatart').html(name_type + '<input type="hidden" id="type" name="items[' + count +
            '][type]" value="' + type + '" /><input type="hidden" class="id" name="items[' + count +
            '][id]" value="' + item.id + '" />');
        var unit_name = item.unit_name_stock;
        if (item.unit_name_stock == null) {
            unit_name = '';
        }
        new_tr.find('td.unit_name').html(unit_name);
        new_tr.find('td.delete').html(
            '<a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a>'
        );
        currentQuantityInput.parent().parent().find('td:nth-child(4)').find('select.localtion_warehouses_id').trigger(
            'change');
        loadLocaltion_warehouses_id(count);

        countrow();
    }
    var createTrItemfist = () => {
        if ($('.dataTables_empty').length) {
            $('.dataTables_empty').parents('tr').remove();
        }
        var name_type =
            '<img style="border-radius: 50%;width: 4em;height: 4em;"  src="<?= base_url('assets/images/preview-not-available.jpg') ?>">';
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="dragger avatart" style="text-align: center;">' + name_type +
            '<input type="hidden" name="items[' + uniqueArray +
            '][type]" class="type" id="type" value="hau" /></td>');

        var td2 = $('<td><input type="hidden" class="count" value="' + uniqueArray +
            '" />\
        	<input style="width: 100%;" data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" class="custom_item_select" id="custom_item_select_' +
            uniqueArray +
            '" name="custom_item_select_' + uniqueArray + '" style="width: 100%">\
								        <br><br><div class="color"></div></td>');
        var td12 = $('<td><div class="form-group " style="width: 100%">\
								             <select class="warehouses_id" id="warehouses_id_' + uniqueArray + '" name="items[' + uniqueArray + '][warehouses_id]" style="width: 100%;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             </select>\
								        </div></td>');
        var td3 = $('<td class="hide"><div class="form-group " style="width: 100%">\
								             <select class="localtion_warehouses_id" id="localtion_warehouses_id_' + uniqueArray +
            '" name="items[' + uniqueArray + '][localtion_warehouses_id]" style="width: 100%;" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">\
								             </select>\
								        </div></td>');
        var td4 = $('<td class="unit_name"></td>');

        var tdPO = $(`<td class="td-po">
            <input type="text" name="items[${uniqueArray}][po_id]" id="po_id_${uniqueArray}" data-placeholder="Lệnh sản xuất tổng" class="" style="width: 100%; min-width: 150px;" value="">
        </td>`);

        var td5 = $(
            '<td class="text-center"><div class="mainQuantity_text"></div><input class="mainQuantity height_auto hide" type="text" name="items[' +
            uniqueArray + '][quantity]" value="1" /></td>');
        var td6 = $('<td><input class="mainQuantityNet H_input height_auto"  type="text" name="items[' + uniqueArray +
            '][quantity_net]" value="1" /></td>');
        var td7 = $(
            '<td ><input onkeyup="formatNumBerKeyUpCus(this)" class="height_auto H_input align_right price" type="text" name="items[' +
            uniqueArray + '][price]" value="0" /></td>');

        var td9 = $('<td class="align_right amount">0</td>');
        var td10 = $('<td><textarea style="width: 100%;" class="note" name="items[' + uniqueArray +
            '][note]"></textarea></td>');
        var td11 = $('<td class="delete"></td>');
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td12);
        newTr.append(td3);
        newTr.append(tdPO);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        $('table.item-import tbody').append(newTr);
        newTr.find('.selectpicker').selectpicker('refresh');
        // init_ajax_searchs('items','#custom_item_select_'+uniqueArray);
        $('#localtion_warehouses_id_' + uniqueArray).select2();
        $('#warehouses_id_' + uniqueArray).select2();
        ajaxSelectCallBack($('#custom_item_select_' + uniqueArray), "<?= admin_url('purchases/SearchItems') ?>", 0);
        ajaxSelectParamsCallback($('#po_id_' + uniqueArray), "<?= 'admin/manufactures/searchProductionsOrders' ?>", 0, false, true);

        uniqueArray++;
        getTotalPrice();
        reset_item_select();
    }
    $(document).on('change', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
        }
        mainQuantity = currentQuantityInput.parents('tr').find('.mainQuantity').val();
        if (parseInt(currentQuantityInput.val()) > mainQuantity) {
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', '<?= _l('ch_limit_items') ?>');
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
                .tooltip('show'));
            currentQuantityInput.addClass('error');
            currentQuantityInput.focus();
        } else {
            currentQuantityInput.attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
            currentQuantityInput.attr("style", "width: 100px;");
            currentQuantityInput.removeClass('error');
            currentQuantityInput.focus();
        }
        getTotalPrice();
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
        }
        mainQuantity = currentQuantityInput.parents('tr').find('.mainQuantity').val();
        if (parseInt(currentQuantityInput.val()) > mainQuantity) {
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', '<?= _l('ch_limit_items') ?>');
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
                .tooltip('show'));
            currentQuantityInput.addClass('error');
            currentQuantityInput.focus();
        } else {

            currentQuantityInput.attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
            currentQuantityInput.attr("style", "width: 100px;");
            currentQuantityInput.removeClass('error');
            currentQuantityInput.focus();
        }
        getTotalPrice();
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() == '') {
            currentQuantityInput.val(0);
            currentQuantityInput.parents('tr').find('.mainQuantityNet').val(0);
        }
    });
    $(document).on('keyup', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
            currentQuantityInput.parents('tr').find('.mainQuantityNet').val(0);
        } else {
            currentQuantityInput.parents('tr').find('.mainQuantityNet').val(currentQuantityInput.val());
        }
        getTotalPrice();
    });
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice();
    };

    $(document).on('click', '.mainQuantity', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
            currentQuantityInput.parents('tr').find('.mainQuantityNet').val(0);
        } else {
            currentQuantityInput.parents('tr').find('.mainQuantityNet').val(currentQuantityInput.val());
        }
        getTotalPrice();
    });
    $(document).on('click', '.mainQuantityNet', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        if (currentQuantityInput.val() < 0) {
            currentQuantityInput.val(0);
        }
        mainQuantity = currentQuantityInput.parents('tr').find('.mainQuantity').val();
        if (parseInt(currentQuantityInput.val()) > mainQuantity) {
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', '<?= _l('ch_limit_items') ?>');
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(() => $(this).tooltip('show')).hover(() => $(this)
                .tooltip('show'));
            currentQuantityInput.addClass('error');
            currentQuantityInput.focus();
        } else {
            currentQuantityInput.attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
            currentQuantityInput.attr("style", "width: 100px;");
            currentQuantityInput.removeClass('error');
            currentQuantityInput.focus();
        }
        getTotalPrice();
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.price', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);
        var current_row = currentInput.parents('tr');

        let mainQuantityNet = unformat_number(current_row.find('.mainQuantityNet').val());
        let price = unformat_number(current_row.find('.price').val());

        var total = mainQuantityNet * price;
        current_row.find('.amount').text(formatNumber(total));
        getTotalPrice();
    };

    function getTotalPrice() {
        var items = $('table.item-import tbody').find('tr.item');
        var totalQuantity = 0;
        var totalQuantityNet = 0;
        var totalPrice = 0;
        $.each(items, (index, value) => {
            if ($(value).find('#type').val() != "hau") {
                totalQuantityNet += parseFloat($(value).find('.mainQuantityNet').val().replace(/\,/g, ''));
                totalPrice += parseFloat($(value).find('.amount').text().replace(/\,/g, ''));
            }
        });
        $('.total_quantity_all').text(tnhFormatNumber(totalQuantity));
        $('.total_quantity_approve').text(tnhFormatNumber(totalQuantityNet));
        $('.total_price').text(formatNumber(totalPrice));
    }
    $('#items-form').on('submit', (e) => {
        if ($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', '<?= _l('ch_invalid_value') ?>');
        }
    });

    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.replace(/[^\-\d\.]/g, '');
        }
        return _number;
    };

    function reset_item_select() {
        $('#custom_item_select').html('');
        $('#custom_item_select').selectpicker('refresh');
    }
    var button_create = () => {
        var warehouse_id = $('#warehouse_id').val();
        if ((warehouse_id != '')) {
            if (!$('table.item-import tbody tr.item').find('input[value=hau]').length) {
                createTrItemfist();
            }
        } else {
            alert_float('warning', '<?= _l('alert_warehouse') ?>');
            return;
        }
    }

    function ajaxSelectCallBack_2(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
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
                            type: $('#suppliers_id').val(),
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
                formatResult: repoFormatSelection_v2,
                formatSelection: repoFormatSelection_v2,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
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
                    data: function(term, page) {
                        return {
                            type: $('#suppliers_id').val(),
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
                formatResult: repoFormatSelection_v2,
                formatSelection: repoFormatSelection_v2,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    ajaxSelectCallBack_2($('#id_order'), "<?= admin_url('return_suppliers/SearchOrder') ?>", 0);

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            var suppliers_id = $('#suppliers_id').val();
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: false,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types + '/' + suppliers_id,
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
                            suppliers_id: $('#suppliers_id').val(),
                            type: $('#type_items').val(),
                            types: $('#id_order').val(),
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
                allowClear: false,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            suppliers_id: $('#suppliers_id').val(),
                            type: $('#type_items').val(),
                            types: $('#id_order').val(),
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

    function repoFormatSelection_v2(state) {
        if (!state.id) return state.text;

        return state.text;
    }
    $(document).on('change', '.warehouses_id', (e) => {
        var id_return = $('#id_return').val();
        var currentQuantityInput = $(e.currentTarget);
        var items = currentQuantityInput.parent().parent().parent().find('td:nth-child(1)').find('input.id').val();
        var type = currentQuantityInput.parent().parent().parent().find('td:nth-child(1)').find('input#type').val();

        var warehouse_id = currentQuantityInput.parent().parent().parent().find('td:nth-child(3)').find(
            'select.warehouses_id').val();

        var id = $(currentQuantityInput).val();
        if (id == '') {
            currentQuantityInput.parent().parent().parent().find('td:nth-child(6)').find('input').val(0);
            currentQuantityInput.parent().parent().parent().find('td:nth-child(6)').find('div.mainQuantity_text')
                .html(tnhFormatNumber(0));
            currentQuantityInput.parent().parent().parent().find('td:nth-child(7)').find('input').change();
        } else {
            $.post(admin_url + 'export_different/get_quantity/' + items + '/' + id + '/' +
                type + '/' + id_return, {
                    [csrfData['token_name']]: csrfData['hash']
                },
                function(item) {
                    var item = JSON.parse(item);
                    currentQuantityInput.parent().parent().parent().find('td:nth-child(7)').find('input').val(
                        item);
                    currentQuantityInput.parent().parent().parent().find('td:nth-child(7)').find(
                        'div.mainQuantity_text').html(tnhFormatNumber(item));
                    currentQuantityInput.parent().parent().parent().find('td:nth-child(8)').find('input')
                        .change();
                });
        }
    });
    $("#object").change(function() {
        $('#id_object').selectpicker('refresh');
        $('#id_object').prop('required', false);
        $('.ch_list_object').addClass('hide');
        $('.append_id_object').addClass('hide');
        var id = $('#object').val();
        var id_object_id = 0;
        <?php
        if (!empty($items)) { ?>
            id_object_id = <?= $items->id_object; ?>;
        <?php
        }
        ?>
        if (id == 1) {
            var html =
                '<div class="form-group id ">\
                    <input data-placeholder="Khách hàng" name="id_object" class="id_object" style="width: 100%" value="' +
                id_object_id + '" id="id_object">\
                </div>';
            $('.append_id_object').removeClass('hide');
            $('#object_text').val(1);
            $('#id_object').prop('required', true);
            ajaxSelectCallBack_3($('#id_object'), "<?= admin_url('other_payslips/SearchClient') ?>", id_object_id);
            add_validate_form();
            $('#id_object').trigger('change');
        }
        else if (id == 2) {
            var html = '<div class="form-group id ">\
                    <input data-placeholder="Nhà cung cấp" name="id_object" style="width: 100%" value="' +
                id_object_id + '" id="id_object">\
                </div>';
            // $('.append_id_object').html(html);
            $('.append_id_object').removeClass('hide');
            $('#object_text').val(1);
            $('#id_object').prop('required', true);
            ajaxSelectCallBack_3($('#id_object'), "<?= admin_url('other_payslips/SearchClient') ?>", id_object_id);
            add_validate_form();
            $('#id_object').trigger('change');
        }
        else if (id == 3) {
            var html = '<div class="form-group id ">\
                    <input data-placeholder="Nhân viên" name="id_object" style="width: 100%" value="' + id_object_id + '" id="id_object">\
                </div>';
            $('.append_id_object').removeClass('hide');
            $('#object_text').val(1);
            // $('.append_id_object').html(html);
            $('#id_object').prop('required', true);
            ajaxSelectCallBack_3($('#id_object'), "<?= admin_url('other_payslips/SearchClient') ?>", id_object_id);
            add_validate_form();
            $('#id_object').trigger('change');

        } else if (id == 4) {

            var html1 = '<div class="form-group id">\
                    <input type="text" id="object_text" name="object_text" class="form-control object_text" value="<?= (!empty($items) ? $items->object_text : '') ?>">\
            </div>';
            // $('.append_id_object').html(html1);
            $('.vouchers_ids').find('button').addClass('no-drop-v2');
            $('.ch_list_object').removeClass('hide');
            $('#id_object').val('');
            if ($('#object_text').val() == 1) {
                $('#object_text').val('');
            }
            add_validate_form();
        }
    });
    <?php
    if (!empty($items)) { ?>
        $('#object').change();
    <?php
    }
    ?>

    function ajaxSelectCallBack_3(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + $('#object').val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#object').val(),
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
                formatResult: repoFormatSelection_3,
                formatSelection: repoFormatSelection_3,
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
                            type: $('#object').val(),
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
                formatResult: repoFormatSelection_3,
                formatSelection: repoFormatSelection_3,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    $(function(e) {
        <?php
        if (empty($items)) { ?>
            ajaxSelectCallBack_3($('#id_object'), "<?= admin_url('other_payslips/SearchClient') ?>", 0);
        <?php
        }
        ?>
    })

    function repoFormatSelection_3(state) {
        var id = $('#objects').val();
        if (id == 3) {
            return state.text;
        }
        return '[' + state.code_client + '] ' + state.text;
    }
    // $(document).on('change', '.warehouses_id', function(e) {
    //     var currentQuantityInput = $(e.currentTarget);
    //     // var items = currentQuantityInput.parent().parent().parent().find('td:nth-child(1)').find('input.id').val();
    //     loadLocaltion_warehouses(currentQuantityInput);
    // });

    function loadLocaltion_warehouses_ch(id) {
        var localtion_warehouses = $('#localtion_warehouses_id_' + id);
        var warehouse_id = localtion_warehouses.parent().parent().parent().find('td:nth-child(3)').find(
            'select.warehouses_id').val();
        var checked = localtion_warehouses.attr('data-id');
        var items = $('#custom_item_select_' + id).val();
        localtion_warehouses.attr('required', true);
        localtion_warehouses.find('option:gt(0)').remove();
        if (localtion_warehouses.length) {

            $.post(admin_url + "warehouse/list_localtion", {
                items: items,
                warehouse: warehouse_id,
                checked: checked,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                console.log(data);
                localtion_warehouses.html(data).find('option').attr('disabled', 'disabled').parents(
                    '#localtion_warehouses_id_' + id).find('option[child="1"]').removeAttr('disabled');
                localtion_warehouses.find('option:nth-child(1)').removeAttr('disabled');
                localtion_warehouses.select2('val', checked);
                localtion_warehouses.trigger('change');
            })
        }
    }

    function loadLocaltion_warehouses(currentQuantityInput) {
        var warehouse = currentQuantityInput.val();
        var localtion_id = currentQuantityInput.parent().parent().parent().find('td:nth-child(4)').find(
            'select.localtion_warehouses_id');
        localtion_id.select2();
        localtion_id.find('option:gt(0)').remove();
        if (localtion_id.length) {
            $.post(admin_url + "warehouse/list_localtion", {
                warehouse: warehouse,
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                localtion_id.html(data).find('option').attr('disabled', 'disabled').parents(localtion_id).find(
                    'option[child="1"]').removeAttr('disabled');
                localtion_id.find('option:nth-child(1)').removeAttr('disabled');
                localtion_id.select2('val', '');
                localtion_id.trigger('change');
            })
        }
        // $('#custom_item_select').select2('val','');
    }

    function loadLocaltion_warehouses_id(id, value = '') {
        var localtion_warehouses = $('#warehouses_id_' + id);
        var checked = localtion_warehouses.attr('data-id');

        var id_product = $('#custom_item_select_' + id).val();
        var type = $('#custom_item_select_' + id).select2('data').type;

        if (empty(id_product)) {
            id_product = $(value).find('td:nth-child(1)').find('input.id').val();
        }
        $('#warehouses_id_' + id).attr('required', true);
        // $('#warehouses_id_' + id).select2();
        $.post(admin_url + "warehouse/list_localtion_product_transfer", {
            [csrfData['token_name']]: csrfData['hash'],
            id_product: id_product,
            checked: checked,
            type: type
        }, function(data) {
            $('#warehouses_id_' + id).html(data);
            $('#warehouses_id_' + id).val(checked).trigger('change');
            $('#warehouses_id_' + id).select2({
                formatResult: repoFormatHtml,
                formatSelection: repoFormatHtml,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        });
    }

    function repoFormatHtml(item) {
        var originalOption = item.element;
        if ($(originalOption).data('check') == 1) {
            return "<b>" + $(originalOption).data('text') + "</b>"
        }
        if ($(originalOption).data('type') == 'nvl' || $(originalOption).data('type') == 'product') {
            return "<b>" + $(originalOption).data('text') + "</b>" +
                "<span style='font-style: italic'><br><?= _l('Lot') ?>: </span>" + $(originalOption).data('lot') +
                "<span style='font-style: italic'><br><?= _l('ch_date_of_manufacture') ?>: </span>" + $(originalOption).data('date_sx') +
                "<span style='font-style: italic'><br><?= _l('ch_items_dateed') ?>: </span>" + $(originalOption).data('date_sd')
        } else {
            return "<b>" + $(originalOption).data('text') + "</b>" +
                "<span style='font-style: italic'><br><?= _l('Lot') ?>: </span>" + $(originalOption).data('lot')
        }
    }
</script>
<script>
    $(document).ready(function () {
        ajaxSelectParamsCallback('#po_id', 'admin/manufactures/searchProductionsOrders', $('#po_id').val(), false, true);
    });
</script>