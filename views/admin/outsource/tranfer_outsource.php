<?php echo form_open('admin/outsource/exportOutsource/' . $id, array('id' => 'add-transfer-outsource')); ?>
<style>
    .bootstrap-select .filter-option .text-muted {
        display: none;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 85%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Xuất NVL/BTP gia công'); ?> (<?= $outsource['reference_no'] ?>)</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Nhà cung cấp', '') ?>
                        <p><?php $supplier = get_table_where(
                                'tblsuppliers',
                                ['id' => $outsource['supplier_id']],
                                '',
                                'row_array'
                            );
                            echo $supplier['company'];
                            ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Ngày', 'date') ?>
                        <?= form_input(
                            'date',
                            set_value('date') ? set_value('date') : date('d/m/Y H:i:s'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '"'
                        ) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea(
                            'note',
                            (isset($_POST['note']) ? $_POST['note'] : $outsource['note']),
                            'placeholder="' . lang('note') . '" id="note" class="form-control input-tip" style="height: 50px;"'
                        ); ?>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>

                        <div class="tab-panels">
                            <section id="marzen" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="tb-tranfer-outsource" class="dt-tnh tnh-table table-bordered table-hover dont-responsive-table dataTable" style="width: 100%">
                                        <thead>
                                            <th class="text-center" style="width: 30px;">
                                                <a class="btn btn-info btn-icon add-row"><i class="fa fa-plus"></i></a>
                                            </th>
                                            <th style="width: 150px;"><?= lang('tnh_item_code') ?></th>
                                            <th style="width: 50px;text-align: center"><?= lang('Loại') ?></th>
                                            <th style="width: 120px;"><?= lang('Kho xuất') ?></th>
                                            <th style="width: 80px;"><?= lang('quantity') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                            <th style="width: 50px;"><?= lang('actions') ?></th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            //tnh
                                            $arrItems = [];
                                            $this->db->select('
                                                tbl_outsource_items.pod_id as pod_id, 
                                                tbl_outsource_items.id_stage as id_stage,
                                                SUM(tbl_outsource_items.quantity) as quantity,
                                            ', false);
                                            $this->db->from('tbl_outsource');
                                            $this->db->join('tbl_outsource_items', 'tbl_outsource_items.outsource_id = tbl_outsource.id');
                                            $this->db->where('tbl_outsource.id', $id);
                                            $this->db->group_by('tbl_outsource_items.pod_id, tbl_outsource_items.id_stage');
                                            $items = $this->db->get()->result_array();
                                            if (!empty($items)) {
                                                foreach ($items as $key => $value) {
                                                    $pod_id = $value['pod_id'];
                                                    $quantity_products = $value['quantity'];

                                                    $this->db->select('
                                                        tbl_productions_orders_details.productions_orders_id as po_id,
                                                        tbl_productions_orders_details.productions_orders_item_id as poi_id,
                                                        tbl_productions_orders_items.plan_id as plan_id
                                                    ');
                                                    $this->db->from('tbl_productions_orders_details');
                                                    $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                                                    $this->db->where('tbl_productions_orders_details.id', $pod_id);
                                                    $pod = $this->db->get()->row_array();
                                                    if (!empty($pod)) {
                                                        $poi_id = $pod['poi_id'];
                                                        $po_id = $pod['po_id'];
                                                        $this->db->select('
                                                        tbl_productions_orders_items_sub.id as id, tbl_productions_orders_items_sub.quantity_single
                                                        ');
                                                        $this->db->from('tbl_productions_orders_items_sub');
                                                        $this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $poi_id);
                                                        $this->db->where('tbl_productions_orders_items_sub.parent_id', 0);
                                                        $element = $this->db->get()->result_array();
                                                        if (!empty($element)) {
                                                            foreach ($element as $k => $val) {
                                                                $quantity_el = $quantity_products * $val['quantity_single'];
                                                                $this->db->select('
                                                                tbl_productions_orders_items_sub.id as id, tbl_productions_orders_items_sub.quantity_single,
                                                                tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
                                                                tbl_productions_orders_items_sub.type as type,
                                                                tbl_productions_orders_items_sub.item_code as item_code,
                                                                tbl_productions_orders_items_sub.item_name as item_name,
                                                                tbl_productions_orders_items_sub.item_id as item_id,
                                                                ');
                                                                $this->db->from('tbl_productions_orders_items_sub');
                                                                $this->db->where('tbl_productions_orders_items_sub.parent_id', $val['id']);
                                                                $bom = $this->db->get()->result_array();
                                                                if (!empty($bom)) {
                                                                    foreach ($bom as $kB => $vB) {
                                                                        $cIndex = $vB['type'].'__'.$vB['item_id'];
                                                                        $quantity = $quantity_el * $vB['quantity_single'];
                                                                        $quantityPrimary = 0;
                                                                        if (!empty($vB['quantity_exchange'])) {
                                                                            $quantityPrimary = $quantity/$vB['quantity_exchange'];
                                                                        }

                                                                        if (empty($arrItems[$cIndex])) {
                                                                            $arrItems[$cIndex] = [
                                                                                'item_code' => $vB['item_code'],
                                                                                'item_name' => $vB['item_name'],
                                                                                'item_id' => $vB['item_id'],
                                                                                'item_type' => $vB['type'],
                                                                                'plan_id' => $pod['plan_id'],
                                                                                'quantityPrimary' => $quantityPrimary,
                                                                            ];
                                                                        } else {
                                                                            $arrItems[$cIndex]['quantityPrimary'] = $arrItems[$cIndex]['quantityPrimary'] + $quantityPrimary;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                }
                                            }
                                            //end tnh
                                            ?>
                                            <?php if(!empty($arrItems)): ?>
                                                <?php 
                                                    $stt = 0; 
                                                    $counter = 0; 
                                                ?>
                                                <?php foreach($arrItems as $key => $value): ?>
                                                    <?php
                                                        $item_type = $value['item_type'];
                                                        $item_id = $value['item_id'];
                                                        $production_plan_id = $value['plan_id'];
                                                        $warehouses = $this->manufactures_model->searchW($item_type, $item_id, $production_plan_id);
                                                        $items_material_id = $item_type.'__'.$item_id;
                                                        // echo '<pre>';
                                                        // print_r($warehouses);
                                                        // echo '</pre>';
                                                    ?>
                                                    <tr>
                                                        <td class="text-center td-number"><?= ++$stt ?></td>
                                                        <td>
                                                            <input type="hidden" name="counter_material[]" id="input" class="form-control" value="<?= $counter ?>">
                                                            <input type="hidden" class="check_item" value="0">
                                                            <input type="hidden" class="checkOption" value="0">
                                                            <input type="text" name="items_material_id[]" id="items_material_id_<?= $counter ?>" class="items_material_id hide" style="width: 100%;" data-placeholder="" value="<?= $items_material_id ?>">
                                                            <?= $value['item_name'].'('.$value['item_code'].')' ?>
                                                            <div style="margin-top:10px" class="show_detail"></div>
                                                        </td>
                                                        <td class="td-type-material text-center">
                                                            <?php if($value['item_type'] == "materials"): ?>
                                                                <div class="label label-primary">Nguyên vật liệu</div>
                                                            <?php else: ?>
                                                                <div class="label label-warning">Bán thành phẩm</div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-left td-warehouses">
                                                            <select style="width: 100%" name="locations[]" data-placeholder="<?= lang('choose') ?>" id="locations_<?= $counter ?>" class="locations modal-select2" style="width: 100px;">
                                                                <option value=""></option>
                                                                <?php if(!empty($warehouses)): ?>
                                                                    <?php foreach($warehouses as $kW => $vWW): ?>
                                                                        <?php foreach($vWW as $kWW => $vW): ?>
                                                                        <optgroup label="<?= $vW['text'] ?>">
                                                                        <?php if(!empty($vW['children'])): ?>
                                                                            <?php foreach($vW['children'] as $kC => $vC): ?>
                                                                                <option <?= $production_plan_id == $vC['productions_plan_id'] ? 'selected' : '' ?> data-quantity="<?= $vC['product_quantity'] ?>" value="<?= $vC['id'] ?>"><?= $vC['location_name'] . ' - ' . formatNumber($vC['product_quantity']) ?></option>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                        </optgroup>
                                                                        <?php endforeach; ?>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </td>
                                                        <td class="td-quantity-material"><input type="text" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material number-format" style="width: 100%;" value="<?= formatNumber($value['quantityPrimary']) ?>"><div class="text-danger show-error"></td>
                                                        <td class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></td>
                                                        <td class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></td>
                                                    </tr>
                                                    <?php $counter++; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <input type="hidden" name="outsource_id" value="<?= $id ?>">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        counter = <?= $counter ?>;
        var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
        var count_errors = 0;
        var outsource_id = '<?= $id ?>';
        var locationTo = <?= !empty($locationWarehouseTo) ? json_encode($locationWarehouseTo) : '{}' ?>;
        var data_optionLocationTo = `<?= !empty($select_optionLocationTo) ? $select_optionLocationTo : '' ?>`;
        var items = <?= !empty($items) ? json_encode($items) : '{}' ?>;
        var lang_outsource = <?= json_encode([
                                    'tnh_please_chosen_customer' => lang('tnh_please_chosen_customer'),
                                    'tnh_expected_date' => lang('tnh_expected_date'),
                                    'tnh_quantity_outsource_less' => lang('tnh_quantity_outsource_less'),
                                    'tnh_do_you_load_bom' => lang('tnh_do_you_load_bom'),
                                ]) ?>;
        var dtMaterial = '';
    </script>
    <script>
        function getLocations(listLocation, selected_id = 0) {
            var option = '<option value="0"></option>';
            $.each(listLocation, function(index, el) {
                // selected = selected_id == el.id ? 'selected' : '';
                selected = (index == 0) ? 'selected' : '';
                option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
            });
            return option;
        }

        function formatss(item) {
            var originalOption = item.element;
            if ($(originalOption).data('check') == 2) {
                return "<b>" + $(originalOption).data('text') + "</b>" +
                    "<br><span style='font-style: italic'>Chiều dài: </span>" + $(originalOption).data('framework') +
                    "<br><span style='font-style: italic'>Số kg: </span>" + $(originalOption).data('weight');
            } else {
                return "<b>" + $(originalOption).data('text') + "</b>"
            }
        }

        function totalMaterial() {
            tbMaterial = '#tb-tranfer-outsource tbody tr';
            var nMaterial = $(tbMaterial).length;
            var stt = 0;
            count_errors = 0;

            for (ii = 0; ii < nMaterial; ii++) {
                stt++;
                elementMaterial = $(tbMaterial)[ii];
                $(elementMaterial).find('.td-number').html(stt);

                quantityMaterial = intVal($(elementMaterial).find('.quantity_material').val());
                priceMaterial = intVal($(elementMaterial).find('.price_material').val());
                qtyWarehouse = $(elementMaterial).find('.price_material').val();
                qtyWarehouse = intVal($(elementMaterial).find('select.locations').select2().find(":selected").data(
                    'quantity'));
                if (quantityMaterial > qtyWarehouse) {
                    $(elementMaterial).find('.show-error').html('Số lượng xuất phải nhỏ hơn ' + qtyWarehouse);
                    count_errors++;
                } else {
                    $(elementMaterial).find('.show-error').html('');
                }
                amountMaterial = quantityMaterial * priceMaterial;

                $(elementMaterial).find('.td-amount-material').html(tnhFormatMoney(amountMaterial));
            }
        }


        function totalImportOutsource() {
            tb = '#tb-import-outsource tbody tr:not("[class^=not-tr]")';
            var n = $(tb).length;
            var stt = 0;
            count_errors = 0;
            var totalQuantity = 0;
            var grandTotal = 0;
            for (ii = 0; ii < n; ii++) {
                stt++;
                element = $(tb)[ii];
                $(element).find('.td-number').html(stt);
                quantity = intVal($(element).find('.quantity').val());
                price_outsource = intVal($(element).find('.price').val());
                amount_outsource = price_outsource * quantity;
                $(element).find('.td-amount').html(tnhFormatMoney(amount_outsource));
                // if (quantity_outsource > quantity_max) {
                //     $(element).find('.show-error-item').html('<?= lang('tnh_quantity_import_less') ?>' + ' ' +
                //         quantity_max);
                //     count_errors++;
                // } else {
                //     $(element).find('.show-error-item').html('');
                // }
                // totalQuantity += quantity_outsource;
                // grandTotal += amount_outsource;
            }
            if (n > 0) {
                $('#warehouse_to').select2('readonly', true);
            } else {
                $('#warehouse_to').select2('readonly', false);
            }
            $('.th-total-quantity').html(tnhFormatNumber(totalQuantity));
            $('.th-grand-total').html(tnhFormatMoney(grandTotal));
        }

        function removeRow(el) {
            $(el).closest('tr').remove();
            totalImportOutsource();
        }

        function refershTable() {
            bootbox.confirm({
                message: lang_core['tnh_you_are_referesh'],
                buttons: {
                    confirm: {
                        label: lang_core['yes'],
                        className: 'btn-success'
                    },
                    cancel: {
                        label: lang_core['no'],
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        $('#tb-import-outsource tbody').html('');
                    }
                }
            });
        }

        if (!empty(items)) {
            $.each(items, function(k, v) {
                var trItem = createdRowOutsourceItem(v, counter);
                $('#tb-import-outsource').append(trItem);
                $('select.location_from').select2({
                    formatSelection: formatss,
                    formatResult: formatss,
                    escapeMarkup: function(m) {
                        return m;
                    },
                });
                $('select.location_to').select2();
                <?php if (!empty($location_warehouse)) : ?>
                    $('select.location_to').val('<?= $location_warehouse ?>').trigger('change');
                <?php endif; ?>
                counter++;
            });
            totalImportOutsource();
        }

        function createdRowOutsourceItem(outsource_item, counter) {
            trItem = '';
            if (outsource_item) {
                var tdNumber = $(`<td class="text-center td-number"></td>`);
                var tdCode = $('<td class="td-code">' +
                    '<input type="hidden" name="items[' + counter + '][counter]" id="counter[' + counter +
                    ']" class="form-control counter " value="' + counter + '">' +
                    '<input type="hidden" name="items[' + counter + '][id_items]" id="id_items[' + counter +
                    ']" class="form-control id_items" value="' + outsource_item.item_id + '">' +
                    '<input type="hidden" name="items[' + counter + '][type_item]" id="type[' + counter +
                    ']" class="form-control type" value="' + outsource_item.type_item + '">' +
                    outsource_item.item_code +
                    '</td>');
                var tdName = $(`<td class="td-name">${outsource_item.item_name}</td>`);

                var tdLocationFrom = $(`<td class="td-location-from"></td>`);
                var selectLocationFrom = $(
                    `<select name="items[${counter}][location_from]" id="location_from" style="width: 100%;" class="location_from modal-select2" placeholder="Kho chuyển"></select>`
                );
                if (outsource_item.warehouses_from) {
                    selectLocationFrom.append(outsource_item.warehouses_from);
                }
                tdLocationFrom.append(selectLocationFrom);

                var tdLocationTo = $(`<td class="td-location-to"></td>`);
                var selectLocationTo = $(
                    `<select name="items[${counter}][location_to]" id="location_to" style="width: 100%;" class="location_to modal-select2" placeholder="Kho nhận"></select>`
                );
                selectLocationTo.append(data_optionLocationTo);
                tdLocationTo.append(selectLocationTo);


                var tdQuantity = $(
                    `<td class="td-quantity text-center"><input type="hidden" name="items[${counter}][quantity]" value="${outsource_item.quantity}" class="quantity" >${tnhFormatNumber(outsource_item.quantity)}</td>`
                );

                var tdPrice = $(
                    `<td class="td-price"><input type="text" onchange="totalImportOutsource()" name="items[${counter}][price]" id="price" class="form-control price money-format" value="${tnhFormatMoney(outsource_item.price)}"></td>`
                );
                var tdAmount = $(`<td class="td-amount text-right">${tnhFormatMoney(outsource_item.amount)}</td>`);
                tdAmount.append(
                    `<input type="hidden" name="items[${counter}][amount]" value="${outsource_item.amount}" class=amount >`
                );
                var note = outsource_item.note_item != null ? outsource_item.note_item : '';
                var tdNote = $(
                    `<td class="td-note"><textarea name="items[${counter}][note_item]" id="note_item[]" class="form-control" rows="3">${note}</textarea></td>`
                );
                var tdActions = $(
                    `<td class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></i></td>`
                );

                var trItem = $('<tr data-outsource-item-id="' + outsource_item.id + '"></tr>');
                trItem.append(tdNumber);
                trItem.append(tdCode);
                trItem.append(tdName);
                trItem.append(tdLocationFrom);
                trItem.append(tdLocationTo);
                trItem.append(tdQuantity);
                trItem.append(tdPrice);
                trItem.append(tdAmount);
                trItem.append(tdNote);
                // trItem.append(tdActions);
            }
            return trItem;

        }

        $(function() {
            $('#items').select2();
            init_datepicker();
            init_selectpicker();
            $('#staff_admin').select2();
            $('#warehouse_from').select2();
            $('#warehouse_from').select2('readonly', true);
            $('#warehouse_to').select2();
            $("#id_order").trigger('click');
            $('#warehouses').selectpicker('val', '');


            appValidateForm($('#add-transfer-outsource'), {}, convert);

            function convert(form) {
                if (count_errors > 0) {
                    alert_float('danger', '<?= lang('tnh_check_quantity_outsource') ?>');
                    return;
                }
                $('.add').attr('disabled', 'disabled');
                var url = form.action;
                // var data = $(form).serialize();
                var form = $(form),
                    formData = new FormData(),
                    formParams = form.serializeArray();

                $.each(form.find('input[type="file"]'), function(i, tag) {
                    $.each($(tag)[0].files, function(i, file) {
                        formData.append(tag.name, file);
                    });
                });

                $.each(formParams, function(i, val) {
                    formData.append(val.name, val.value);
                });

                $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'JSON',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined' && oTable != '') {
                                oTable.draw();
                            }
                            $('.modal-dialog .close').trigger('click');
                        } else {
                            alert_float('danger', data.message);
                            $('.add').removeAttr('disabled', 'disabled');
                        }
                    })
                    .fail(function() {
                        alert_float('danger', 'error');
                        $('.add').removeAttr('disabled', 'disabled');
                    });
                return false;
            }
        });

        function refershItemsTable() {
            bootbox.confirm({
                message: lang_core['tnh_you_are_referesh'],
                buttons: {
                    confirm: {
                        label: lang_core['yes'],
                        className: 'btn-success'
                    },
                    cancel: {
                        label: lang_core['no'],
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        $("#tb-tranfer-outsource").find('tbody tr').remove();
                    }
                }
            });
        }

        function removeRowMaterial(el) {
            var current = $(el).parent().parent();
            $(el).parent().parent().remove();
            totalMaterial();
        }



        function createMaterial(items) {
            id = items.split('__');
            // elTr = $('.items_material_id_check[value="' + "materials__" + id[0] + '"]').closest('tr');
            // if (elTr.length > 0) {
            // alert_float('warning', 'Mặt hàng đã tồn tại !');
            // return;
            // } else {
            tdNumber = $('<td class="text-center td-number"></td>');
            tdItem = $('<td><input type="hidden" name="counter_material[]" id="input" class="form-control" value="' +
                counter + '">\
               <input type="hidden" class="order_id" name="order_id[]" value=" 0" />\
               <input type="hidden" class="warehouse_id" name="warehouse_id[]" value=" 0" />\
               <input type="hidden" name="size_metarial[]" class="size_metarial" value="0">\
                <input type="hidden" name="size_id[]" class="size_id" value="0">\
               <input type="hidden" class="items_material_id_check" name="items_material_id_check[]" value=" 0" />\
               <input type="hidden" name="items_material_id_v2[]" class="items_material_id_v2" value="0">\
               <input type="text" name="items_material_id[]" id="items_material_id_' + counter +
                '" class="items_material_id" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value="">\
                <div style="margin-top:10px" class="show_detail"></div>\
                </td>');
            tdImage = $('<td class="td-image">' +
                '<div class="preview_image" style="width: auto;">' +
                '<div class="display-block contract-attachment-wrapper img">' +
                '<div style="width:45px;">' +
                '<a href="' + site.base_url +
                'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">' +
                '<div class="">' +
                '<img src="' + site.base_url + 'assets/images/tnh/no_image.png" style="border-radius: 50%">' +
                '</div>' +
                '</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</td>');
            tdWarehouse = $(
                '<td class="text-left"><div class="td-item-name-warehouse"></div><select name="locations[]" data-placeholder="' +
                lang_core['choose'] + '" id="locations_' + counter +
                '" class="locations modal-select2" style="width: 200px;"></select></td>');
            tdItemName = $('<td class="td-item-name-material"></td>');
            tdUnit = $('<td class="td-unit-material text-center"></td>');
            tdLocation = $('<td class="td-location"><select name="locations[]" data-placeholder="' + lang_core['choose'] +
                '" id="locations" class="locations" style="width: 180px;"></select></td>');
            tdQuantity = $(
                '<td class="td-quantity-material"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material" style="width: 100%;" value="1"></td>'
            );
            tdPrice = $(
                '<td class="td-price-material"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price_material[]" id="price_material[]" class="form-control price_material" value="0"></td>'
            );
            tdAmount = $('<td class="td-amount-material text-right"></td>');
            tdNote = $(
                '<td class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></td>'
            );
            tdActions = $(
                '<td class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></td>'
            );

            var trItem = $('<tr></tr>');
            trItem.append(tdNumber);
            trItem.append(tdItem);
            trItem.append(tdWarehouse);
            trItem.append(tdItemName);
            trItem.append(tdUnit);
            trItem.append(tdQuantity);
            trItem.append(tdNote);
            trItem.append(tdActions);
            $('table#tb-tranfer-outsource tbody').append(trItem);
            // }

            warehouses = $("#warehouses").val();
            if (id[2] == 'nvl') {
                ajaxSelectCallBack_chose($('#items_material_id_' + counter), 'admin/outsource/SearchItems', "materials__" +
                    id[0], '', 'nvl', id[1], warehouses);
            } else {
                ajaxSelectCallBack_chose($('#items_material_id_' + counter), 'admin/outsource/SearchItems',
                    "semi_products__" + id[0], '', 'product', id[1], warehouses, id[3], id[4]);
            }
            $('#items_material_id_' + counter).trigger('change');
            $('#locations_' + counter).select2({
                'allowClear': true
            });
            totalMaterial();
            counter++;

        }

        $('.add-row').on('click', function(event) {
            event.preventDefault();

            tdNumber = $('<td class="text-center td-number"></td>');
            tdItem = $(
                '<td><input type="hidden" name="counter_material[]" id="input" class="form-control" value="' +
                counter + '">\
              <input type="hidden" class="check_item" value="0">\
              <input type="hidden" class="checkOption" value="0">\
            <input type="text" name="items_material_id[]" id="items_material_id_' + counter +
                '" class="items_material_id" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value="">\
           <div style="margin-top:10px" class="show_detail"></div>\
            </td>');
            tdImage = $('<td class="td-image">' +
                '<div class="preview_image" style="width: auto;">' +
                '<div class="display-block contract-attachment-wrapper img">' +
                '<div style="width:45px;">' +
                '<a href="' + site.base_url +
                'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">' +
                '<div class="">' +
                '<img src="' + site.base_url + 'assets/images/tnh/no_image.png" style="border-radius: 50%">' +
                '</div>' +
                '</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</td>');
            tdWarehouse = $(
                '<td class="text-left td-warehouses"><select style="width: 100%" name="locations[]" data-placeholder="' +
                lang_core['choose'] + '" id="locations_' + counter +
                '" class="locations modal-select2" style="width: 100px;"></select></td>');
            tdItemName = $('<td class="td-item-name-material"></td>');
            tdUnit = $('<td class="td-unit-material text-center"></td>');
            tdType = $('<td class="td-type-material text-center"></td>');
            tdQuantity = $(
                '<td class="td-quantity-material"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material" style="width: 100%;" value="0"><div class="text-danger show-error"></td>'
            );
            tdPrice = $(
                '<td class="td-price-material"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price_material[]" id="price_material[]" class="form-control price_material" value="0"></td>'
            );
            tdAmount = $('<td class="td-amount-material text-right"></td>');
            tdNote = $(
                '<td class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></td>'
            );
            tdActions = $(
                '<td class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></td>'
            );

            var trItem = $('<tr></tr>');
            trItem.append(tdNumber);
            trItem.append(tdItem);
            trItem.append(tdType);
            trItem.append(tdWarehouse);
            // trItem.append(tdItemName);
            // trItem.append(tdUnit);
            trItem.append(tdQuantity);
            trItem.append(tdNote);
            trItem.append(tdActions);
            $('table#tb-tranfer-outsource tbody').append(trItem);
            ajaxSelectCallBack($('#items_material_id_' + counter), 'admin/outsource/SearchItems', 0);
            $('#locations_' + counter).select2({
                'allowClear': true
            });
            totalMaterial();
            counter++;
        });

        function ajaxSelectCallBack_chose(element, url, id = '', types = '', type_chose = '', id_order = '', warehouses =
            '', size = '', type_semi_product = '') {
            if (id) {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    initSelection: function(element, callback) {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: site.base_url + url + '/' + id + '/' + type_chose + '/' + id_order +
                                '/' + warehouses + '/' + size + '/' + type_semi_product,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0].children[0]);
                            }
                        });
                    },
                    ajax: {
                        url: site.base_url + url,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: type_chose,
                                factory: $('#factory').val(),
                                id_order: $('#id_ordeadd').val(),
                                warehouses: $('#warehouses').val(),
                                type_outsource: $('#type_outsource').val(),
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
                    // formatSelection: repoFormatSelection,
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
                        url: site.base_url + url + '/' + $(element).val() + '/' + type_chose,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                type: type_chose,
                                factory: $('#factory').val(),
                                id_order: $('#id_order').val(),
                                warehouses: $('#warehouses').val(),
                                type_outsource: $('#type_outsource').val(),
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
                    // formatSelection: repoFormatSelection,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            }
        }

        function ajaxSelectCallBack(element, url, id, types = '', id_order = '') {
            if (id > 0) {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: true,
                    initSelection: function(element, callback) {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: site.base_url + url + '/' + id,
                            dataType: "json",
                            success: function(data) {
                                callback(data.results[0]);
                            }
                        });
                    },
                    ajax: {
                        url: site.base_url + url,
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
                                        id: '',
                                        text: 'No Match Found'
                                    }]
                                };
                            }
                        }
                    },
                    formatResult: repoFormatSelection,
                    // formatSelection: repoFormatSelection,
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
                        url: site.base_url + url + '/' + $(element).val(),
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
                    // formatSelection: repoFormatSelection,
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            }
        }

        function repoFormatSelection(state) {
            if (!state.id) return state.text;
            if (state.img) {
                var img = '<img class="img_option" src="' + site.base_url + state.img + '"/> ';
            } else {
                var img = '<img class="img_option" src="' + site.base_url + 'download/preview_image"/> ';
            }
            if (state.type == 'nvl') {
                var tr = '' +
                    '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
                    '<div>Đơn vị : ' + state.unit_name + '</div>' +
                    '<div class="label label-primary">Nguyên vật liệu</div>' +
                    '';
                tableSelect = tr;
                return tableSelect;
            } else if (state.type == 'semi_products') {
                var tr = '' +
                    '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
                    '<div>Đơn vị : ' + state.unit_name + '</div>' +
                    '<div class="label label-warning">Bán thành phẩm</div>' +
                    '';
                tableSelect = tr;
                return tableSelect;
            } else {
                var tr = '' + img + state.text + '';
                tableSelect = tr;
                return tableSelect;
            }
        }

        function getWarehousesLocation(counter, item_id, type_item) {
            optionWh = '';
            $.ajax({
                    url: site.base_url + 'admin/outsource/getWarehousesLocation',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        csrf_token_name: hash,
                        item_id: item_id,
                        type_item: type_item,
                    },
                })
                .done(function(data) {
                    if (data) {
                        optionWh = data.option;
                    } else {
                        optionWh = '';
                    }
                    tr = $('#locations_' + counter + '').closest('tr');

                    if (data.checkOption > 0) {
                        $('#locations_' + counter + '').html(optionWh);
                        $('#locations_' + counter + '').select2();
                        $('#locations_' + counter + '').val(data.check).trigger('change');
                    } else {
                        html = `<div class="text-center H_border">
                            <a style="background: linear-gradient(#fff,#fff) padding-box, linear-gradient(to right, #226faa 0%, #2989d8 37%, #72c0d3 100%) border-box;border: 1px solid red; border-radius: 35px;
    display: inline-block;padding: 7px 20px;color: red;" class="btn btn-danger">Tồn Kho Hết</a>
                            <input type="hidden" name="locations[]" value="0">
                        </div>`;
                        tr.find('.td-warehouses').html(html);
                    }
                })
                .fail(function() {
                    console.log("error");
                });
        }
        // $('.add-row').click();
    </script>
    <script>
        $(document).ready(function () {
            $('select.locations').select2();
            totalMaterial();
        });
    </script>