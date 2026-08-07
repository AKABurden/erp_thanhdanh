<div class="modal fade" id="import_price_group_add_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg no-modal-header">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title">
                        <?= !empty($title) ? $title : '' ?>
                    </span>
                </h4>
            </div>
			<?php echo form_open('admin/import_price_group/add_modal_child/' . (!empty($group_price_discount) ? $group_price_discount->id : ''), array('id' => 'modal_add')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12  pull-left">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <div class="form-group" app-field-wrapper="price">
                                        <label for="price" class="control-label">Tên bảng giá</label>
                                        <div class="form-control"><?=$clients->company?></div>
                                    </div>
                                </div>
								<?php $value = !empty($group_price_discount) ? $group_price_discount->discount : '' ?>
								<?= render_input('discount', 'Chiết khấu (%)', $value) ?>

								<?php $value = !empty($group_price_discount) ? $group_price_discount->group_price_id : $group_price_id ?>
								<?= form_hidden('group_price_id', $value) ?>

								<?php $value = !empty($group_price_discount) ? $group_price_discount->client : $id_client ?>
								<?= form_hidden('client', $value) ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?= _l('Lưu') ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('Đóng') ?></button>
                </div>
            </div>
			<?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        init_selectpicker();
        init_datepicker();
        $('#import_price_group_add_modal').modal('show');
        appValidateForm($('#modal_add'), {
            customers_groups_add: 'required',
            discount: 'required'
        }, manage_price_group_add);
        function manage_price_group_add(form) {
            var button = $('#import_price_group_add_modal').find('button[type="submit"]');
            button.button({loadingText: '<?=_l('cong_please_wait')?>'});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.success == true) {
                    if ($.fn.DataTable.isDataTable('.table-import_price_group')) {
                        $('.table-import_price_group').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                }
                $('#import_price_group_add_modal').modal('hide');
            }).always(function () {
                button.button('reset')
            });
            return false;
        }
    })
</script>
