<?php echo form_open('admin/orders/choose_order_manu/' . $id, array('id' => 'choose-order-manu')); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_choose_order_manu') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('Đơn hàng') ?>:</b>
                    <span><?= $order['reference_no'] ?></span>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_orders', 'orders_choose') ?>
                        <select name="orders_choose[]" id="orders_choose" data-none-selected-text="<?= lang('tnh_orders') ?>" class="form-control" data-live-search="true" data-actions-box="true" multiple>
                            <option value=""></option>
                            <?php
                                $this->db->select('
                                    tbl_orders.id as id,
                                    tbl_orders.reference_no as reference_no,
                                ', false);
                                $this->db->from('tbl_orders_relationship');
                                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_orders_relationship.object_id');
                                $this->db->where('tbl_orders_relationship.type_relationship', 2);
                                $this->db->where('tbl_orders_relationship.order_id', $id);
                                $orders = $this->db->get()->result_array();
                                if (!empty($orders)) {
                                    foreach ($orders as $key => $value) {
                                        echo '<option selected value="'.$value['id'].'">'.$value['reference_no'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('productions_orders', 'productions_orders_choose') ?>
                        <select name="productions_orders_choose[]" id="productions_orders_choose" class="form-control" data-none-selected-text="<?= lang('productions_orders') ?>" data-live-search="true" data-actions-box="true" multiple>
                            <option value=""></option>
                            <?php
                                $this->db->select('
                                    tbl_productions_orders.id as id,
                                    tbl_productions_orders.reference_no as reference_no,
                                ', false);
                                $this->db->from('tbl_orders_relationship');
                                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_orders_relationship.object_id');
                                $this->db->where('tbl_orders_relationship.type_relationship', 1);
                                $this->db->where('tbl_orders_relationship.order_id', $id);
                                $orders_relationship = $this->db->get()->result_array();
                                if (!empty($orders_relationship)) {
                                    foreach ($orders_relationship as $key => $value) {
                                        echo '<option selected value="'.$value['id'].'">'.$value['reference_no'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add-choose-order-manu"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        selectAjax($('select#orders_choose'), false, 'admin/orders/searchOrdersPicker');
        selectAjax($('select#productions_orders_choose'), false, 'admin/orders/searchProductionsOrdersPicker');

        appValidateForm($('#choose-order-manu'), {
        }, handlingChooseQuotes);

        function handlingChooseQuotes(form) {
            $('.add-choose-order-manu').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw(false);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add-choose-order-manu').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    $('.add-choose-order-manu').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })
</script>