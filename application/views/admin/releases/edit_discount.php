<?php echo form_open('admin/releases/edit_discount/' . $id, array('id' => 'edit-discount')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Cập nhật chiết khấu'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($delivery['date']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_deliveries') ?>: </div>
                            <div class="ml-at t-bold"><?= ($delivery['reference_no']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mbot10">
                    <table class="table table-hover dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                <th class="text-center" style="width: 120px;"><?= lang('tnh_item_code') ?></th>
                                <th class="text-center" style="width: 180px;"><?= lang('tnh_item_name') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('quantity') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('price') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('amount') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('tnh_discount_direct') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($items)): ?>
                                <?php foreach($items as $key => $value): ?>
                                    <?php
                                        $delivery_item_id = $value['id'];
                                        $type_item = $value['type_item'];
                                        $items_id = $value['item_id'];
                                        $info = null;
                                        if ($type_item == "products") {
                                            $info = $this->products_model->rowProduct($items_id);
                                            $unit = $this->unit_model->rowUnit($value['unit_id']);
                                            if (!empty($info['images'])) {
                                                $images = base_url('uploads/products/' . $info['images']);
                                            }
                                        } elseif ($type_item == "items") {
                                            $info = $this->items_model->rowItems($items_id);
                                            $unit = $this->unit_model->rowUnit($info['unit']);
                                            if (!empty($info['avatar'])) {
                                                $images = base_url($info['avatar']);
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= ++$key ?></td>
                                        <td class="text-center"><?= $info['code'] ?? '' ?></td>
                                        <td class="text-center"><?= $info['name'] ?? '' ?></td>
                                        <td class="text-center"><?= formatNumber($value['quantity']) ?></td>
                                        <td class="text-center"><?= formatMoney($value['price']) ?></td>
                                        <td class="text-right"><?= formatMoney($value['amount']) ?></td>
                                        <td class="text-right">
                                            <input type="text" name="itemsCur[<?= $delivery_item_id ?>][discount_direct_amount_item]" placeholder="<?= lang('tnh_discount_direct') ?>" class="form-control money-format" value="<?= formatNumber($value['discount_direct_amount_item'] ?? 0) ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 mbot10">
                    <div class="from-group">
                        <?= lang('Chi phí công thêm', 'additional_costs') ?>
                        <input type="text" name="additional_costs" id="additional_costs" class="form-control additional_costs money-format" value="<?= formatMoney($delivery['additional_costs'] ?? 0) ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add" data-type="1"><?= _l('save') ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>
<script>
    $(function() {
        appValidateForm($('#edit-discount'), {
        }, editDiscount);

        function editDiscount(form) {
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
    })
</script>