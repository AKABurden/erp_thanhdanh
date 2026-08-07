<?php echo form_open('admin/production_smoothing/add_machines/'.$id.'/'.$production_order_id, array('id' => 'add-machines')); ?>
<div class="modal-dialog" style="width: 35%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Thêm máy và thời gian cho công đoạn'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?php echo lang('Máy móc', 'machines_id'); ?>
                        <?php $selected = $machines_id; ?>
                        <?php echo render_select('machines_id', $machines, array('id', 'name'), '',$selected); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php echo lang('Thời gian bắt đầu', 'date_start'); ?>
                    <?php $date_start = !empty($date_start) ? _dt($date_start) : date('d/m/Y H:i'); ?>
                    <?= form_input('date_start', set_value('date_start') ? set_value('date_start') : $date_start, 'id="date_start" class="form-control datetimepicker" placeholder="' . lang('date') . '" ') ?>
                </div>
                <div class="col-md-6 hide">
                    <?php echo lang('Thời gian kết thúc', 'date_end'); ?>
                    <?= form_input('date_end', set_value('date_end') ? set_value('date_end') : date('d/m/Y H:i'), 'id="date_end" class="form-control datetimepicker" placeholder="' . lang('date') . '" ') ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    counter = 0;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var count_errors = 0;
    var order_id = '<?= $id ?>';
</script>
<script>
    $(function(){
        // $('#items').select2();
        init_datepicker();
        init_selectpicker();

        appValidateForm($('#add-machines'), {
            // 'machines_id': 'required',
            // 'date_start': 'required',
            // 'date_end': 'required',
        }, payment);

        function payment(form) {

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
                url : url,
                type : 'POST',
                dataType: 'JSON',
                cache : false,
                contentType : false,
                processData : false,
                data: formData,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTableVs1 != 'undefined' && oTableVs1 != '') {
                            oTableVs1.draw();
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