<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo $title; ?></span>
                </h4>
            </div>
            <?php echo form_open(admin_url('handover_item/add'), array('id' => 'add_form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div id="additional"></div>
                    <div class="hide">
                        <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo !empty($id) ? $id : '' ?>">
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('name', 'handover_item_name', (!empty($name) ? $name : ''), 'text', array('autocomplete' => 'off')); ?>
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
            name: 'required'
        }, manage_modal);

        function manage_modal(form) {
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
                $('#add_modal').modal('hide');
            });
            return false;
        }
    });
</script>