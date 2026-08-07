<?php echo form_open('admin/manufactures/add_delivery/' . $id, array('id' => 'add-delivery')); ?>
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_add_delivery'); ?> <?= $productions_orders['reference_no'] ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y h:i:s')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('staff_admin', 'staff_admin') ?>
                        <select name="staff_admin" id="staff_admin" data-placeholder="<?= lang('staff_admin') ?>" style="width: 100%;" class="modal-select2" required="required">
                            <option value=""></option>
                            <?php foreach ($staff as $key => $value) : ?>
                                <option <?= get_option('default_staff_orders') == $value['staffid'] ? 'selected' : '' ?> value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="' . lang('note') . '" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-panels">
                        <div class="table-responsive">
                            <table id="tb-convert-delivery" class="dt-tnh dataTable tnh-table table-bordered table-hover dont-responsive-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th style="width: 80px;" class="text-center"><?= lang('tnh_images') ?></th>
                                        <th style="width: 120px;" class="text-center"><?= lang('tnh_item_code') ?></th>
                                        <th style="width: 100px;" class="text-center"><?= lang('Số đơn hàng') ?></th>
                                        <th style="width: 180px;" class="text-center"><?= lang('tnh_warehouses') ?><span class="text-danger">*</span></th>
                                        <th style="width: 100px;" class="text-center"><?= lang('quantity_import') ?></th>
                                        <th style="width: 100px;" class="text-center"><?= lang('quantity_had_delivery') ?></th>
                                        <th style="width: 100px;" class="text-center"><?= lang('quantity_delivery') ?></th>
                                        <th style="width: 120px;" class="text-center"><?= lang('note') ?></th>
                                        <th style="width: 50px;" class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $this->db->select('
                                            tbl_productions_orders_items.id as poi_id,
                                            tbl_products.code as item_code,
                                            tbl_products.name as item_name,
                                            tbl_products.images as images,
                                            tbl_products.id as item_id,
                                            tbl_productions_orders_details.quantity_warehoused as quantity_warehoused,
                                            tbl_productions_orders_details.quantity_delivery as quantity_delivery,
                                            tbl_orders.reference_no as reference_no,
                                            tbl_orders.customer_id as customer_id,
                                            tbl_productions_orders_details.id as pod_id,
                                            tbl_orders.id as order_id,
                                        ');
                                    $this->db->from('tbl_productions_orders_items');
                                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                                    $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
                                    $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id');

                                    $this->db->where('tbl_productions_orders_items.productions_orders_id', $id);
                                    $this->db->where('(tbl_productions_orders_details.quantity_warehoused - tbl_productions_orders_details.quantity_delivery) > 0');
                                    $this->db->where('tbl_productions_orders_details.object_type', 'orders');
                                    $this->db->order_by('tbl_orders.id ASC');
                                    $items = $this->db->get()->result_array();
                                    ?>
                                    <?php if (!empty($items)) : ?>
                                        <?php $counter = 0; ?>
                                        <?php foreach ($items as $key => $value) : ?>
                                            <?php
                                            $poi_id = $value['poi_id'];
                                            $order_id = $value['order_id'];
                                            $pod_id = $value['pod_id'];
                                            $item_id = $value['item_id'];
                                            $images = $value['images'];
                                            if (!empty($images)) {
                                                $images = base_url('uploads/products/' . $images);
                                            } else {
                                                $images = base_url('assets/images/tnh/no_image.png');
                                            }

                                            $this->db->select('tblwarehouse.id, tblwarehouse.name');
                                            $this->db->from('tblwarehouse');
                                            $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                                            $warehouses = $this->db->get()->result_array();

                                            $tdImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>';
                                            ?>
                                            <tr>
                                                <td class="td-numbers text-center"><?= ++$key ?></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="items[<?= $order_id ?>][poi_id][<?= $counter ?>]" class="form-control" value="<?= $poi_id ?>">
                                                    <?= $tdImages ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= $value['item_name'] ?>(<?= $value['item_code'] ?>)
                                                </td>
                                                <td class="text-center">
                                                    <?= $value['reference_no'] ?>
                                                </td>
                                                <td>
                                                    <select data-placeholder="<?= lang('tnh_warehouses') ?>" name="items[<?= $order_id ?>][warehouse_id][<?= $counter ?>]" class="modal-select2 warehouse_id" style="width: 100%;">
                                                        <option value=""></option>
                                                        <?php if(!empty($warehouses)): ?>
                                                            <?php foreach($warehouses as $kW => $vW): ?>
                                                            <?php
                                                                $this->db->select('
                                                                    tbllocaltion_warehouses.id as location_id,
                                                                    tbllocaltion_warehouses.name as name_location,
                                                                    tblwarehouse_items.product_quantity as product_quantity
                                                                ', false);
                                                                $this->db->from('tblwarehouse_items');
                                                                $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                                                                $this->db->where('tblwarehouse_items.warehouse_id', $vW['id']);
                                                                $this->db->where('tbllocaltion_warehouses.pod_id', $pod_id);
                                                                $this->db->where('tbllocaltion_warehouses.stage_id', 0);
                                                                $this->db->where('tblwarehouse_items.type_items', 'product');
                                                                $this->db->where('tblwarehouse_items.id_items', $item_id);
                                                                $this->db->where('tblwarehouse_items.product_quantity >', 0);
                                                                $location = $this->db->get()->result_array();
                                                            ?>
                                                            <?php if(!empty($location)): ?>
                                                                <optgroup label="<?= $vW['name'] ?>">
                                                                <?php foreach($location as $kL => $vL): ?>
                                                                    <option <?= $kL == 0 ? 'selected' : '' ?> data-quantity="<?= $vL['product_quantity'] ?>" value="<?= $vW['id'].'__'.$vL['location_id'] ?>"><?= $vL['name_location'] .' - SL:'.formatNumber($vL['product_quantity']) ?></option>
                                                                <?php endforeach; ?>
                                                                </optgroup>
                                                            <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <?= formatNumber($value['quantity_warehoused']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= formatNumber($value['quantity_delivery']) ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $quantity = $value['quantity_warehoused'] - $value['quantity_delivery'];
                                                    if ($quantity < 0) $quantity = 0;
                                                    ?>
                                                    <input type="text" onchange="totalDeliveries()" name="items[<?= $order_id ?>][quantity][<?= $counter ?>]" class="form-control" value="<?= formatNumber($quantity) ?>">
                                                </td>
                                                <td class="td-note">
                                                    <textarea name="items[<?= $order_id ?>][note_item][<?= $counter ?>]" class="form-control" rows="3"></textarea>
                                                </td>
                                                <td class="td-actions text-center">
                                                    <a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove text-danger remove-row"></i>
                                                </td>
                                            </tr>
                                            <?php $counter++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <input type="hidden" name="order_id_save" id="order_id_save" class="form-control order_id_save" value="<?= $id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script>
    var count_errors = 0;
    function totalDeliveries() {
        tb = '#tb-convert-delivery tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arr_id = [];
        arr_info = [];
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.td-number').html(stt);
            quantity_delivery = intVal($(element).find('.quantity_delivery').val());
            quantity = intVal($(element).find('.div-quantity').html());
            quantity_had_delivery = intVal($(element).find('.td-quantity-had-delivery').html());
            quantity_max = quantity - quantity_had_delivery;
            order_current_item_id = $(element).find('.order_item_id').val();

            index = jQuery.inArray(order_current_item_id, arr_id);
            if (index !== -1) {
                arr_info[index].quantity_delivery = parseFloat(arr_info[index].quantity_delivery) + parseFloat(quantity_delivery);
            } else {
                arr_id.push(order_current_item_id);
                object = {
                    "quantity": quantity,
                    "quantity_had_delivery": quantity_had_delivery,
                    "quantity_delivery": quantity_delivery
                };
                arr_info.push(object);
            }
        }

        if (arr_id) {
            $.each(arr_id, function(index, el) {
                quantity = arr_info[index].quantity;
                quantity_had_delivery = arr_info[index].quantity_had_delivery;
                quantity_delivery = arr_info[index].quantity_delivery;
                quantity_max = quantity - quantity_had_delivery;
                trCur = $('.order_item_id[value="' + el + '"]').closest('tr');
                if (quantity_delivery > quantity_max) {
                    trCur.find('.show-error-item').html(lang_orders['tnh_quantity_delivery_less'] + ' ' + quantity_max);
                    count_errors++;
                } else {
                    trCur.find('.show-error-item').html('');
                }
            });
        }
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        totalDeliveries();
    }

    $(function() {
        $('select.warehouse_id').select2();
        init_datepicker();
        $('#staff_admin').select2({
            allowClear: true
        });

        appValidateForm($('#add-delivery'), {
            'date': 'required',
            'staff_admin': 'required'
        }, add_delivery);

        function add_delivery(form) {
            if (count_errors > 0) {
                alert_float('danger', lang_orders['tnh_check_quantity_delivery']);
                return;
            }
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            // $.each(form.find('input[type="file"]'), function(i, tag) {
            //     $.each($(tag)[0].files, function(i, file) {
            //         formData.append(tag.name, file);
            //     });
            // });

            // $.each(formParams, function(i, val) {
            //     formData.append(val.name, val.value);
            // });

            $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    // cache: false,
                    // contentType: false,
                    // processData: false,
                    // data: formData,
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        formData: form.serialize()
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw(false);
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
    })
</script>