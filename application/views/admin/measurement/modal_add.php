<div class="modal fade" id="modal_add" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('measurement/add'), array('id' => 'collect_categories-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('measurement'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="additional"></div>
                    <div class="hide">
                        <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo !empty($id) ? $id : '' ?>">
                    </div>
                    <div class="col-md-12">
                        <?= render_select('type', $type_list, array('id', 'name'), 'measurement_type', (!empty($type_selected) ? $type_selected : '')); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('value', 'measurement_value', (!empty($value) ? $value : ''), 'text', array('autocomplete' => 'off', 'onkeyup' => 'formatNumberOnkeyup(this)')); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(function() {
        _validate_form($('form'), {
            type: 'required',
            value: 'required'
        }, manage);

        function manage(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                // location.reload();
                oTable.draw();
                $('#modal_add').modal('hide');
            });
            return false;
        }
    });

    function formatNumberOnkeyup(id_input) {
        // key = "";
        // money = $(id_input).val().replace(/[^\-\d\.]/g, '');
        // a = money.split(".");
        // $.each(a, function (index, value) {
        //     key = key + value;
        // });
        // $(id_input).val(formatNumber(key, '.', ','));
        // vl = $(id_input).val().replace(/-/gi, '');
        vl = $(id_input).val().replace(/[^\-\d\.]/g, '');
        vl = vl.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        $(id_input).val(vl)
    }
</script>