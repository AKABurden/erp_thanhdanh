<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('collect_categories/add'), array('id' => 'collect_categories-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('collect_categories'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="additional"></div>
                    <div class="hide">
                        <input type="" id="collect_categories_id" name="collect_categories_id" class="form-control" autocomplete="off" value="<?php echo !empty($id) ? $id : '' ?>">
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('code', 'colcat_code', (!empty($code) ? $code : ''), '', array('autocomplete' => 'off')); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('name', 'colcat_name', (!empty($name) ? $name : ''), '', array('autocomplete' => 'off')); ?>
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <?php
                                $type_selected[0] = '';
                                $type_selected[1] = '';
                                if (!empty($type)) {
                                    switch ($type) {
                                        case 0:
                                            $type_selected[0] = 'selected';
                                            break;
                                        case 1:
                                            $type_selected[1] = 'selected';
                                            break;
                                    }
                                }
                            ?>
                            <?= lang('type', 'type') ?>
                            <select name="type" class="form-control selectpicker" data-none-selected-text="<?= lang('type') ?>" data-placeholder="<?= lang('type') ?>">
                                <option value="0"></option>
                                <option value="1" <?php echo $type_selected[0] ?>><?= lang('tnh_cpncsx') ?></option>
                                <option value="2" <?php echo $type_selected[1] ?>><?= lang('tnh_cpsxc') ?></option>
                            </select>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <?php echo render_select('costs_parent', $parent, array('id', 'name'), 'ch_chose_parent', (!empty($costs_parent) ? $costs_parent : '')); ?>
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
            code: 'required',
            name: 'required'
        }, manage_costs);

        function manage_costs(form) {
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