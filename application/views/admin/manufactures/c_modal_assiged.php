<div class="modal fade" id="modal_assiged_promotion" role="dialog">
    <?php echo form_open('admin/manufactures/changeStaffAssignDetail/' . $id_promotion, array('id' => 'form_modal_assiged_promotion')); ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title in-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="staff_assign" class="control-label in-title"><?=_l('Nhân viên')?></label>
                        <select id="staff_assign" name="staff_assign[]" class="selectpicker"  data-width="100%" multiple="true" data-none-selected-text="<?=_l('dropdown_non_selected_tex')?>" data-live-search="true" tabindex="-98">
                            <?php if(!empty($staff)) :?>
                                <?php foreach ($staff as $key => $value) { ?>
                                        <?php $selected = ''?>
                                        <?php foreach($staff_productions as $k => $v) {
                                            if($v == $value['staffid']) $selected = 'selected';
                                        }?>
                                    <option <?=$selected?> value="<?= $value['staffid'] ?>"><?= $value['firstname'] . ' ' . $value['lastname'] ?></option>
                                <?php } ?>
                            <?php endif;?>
                        </select>
                        <input type="hidden" name="type" value="save"/>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info"><?= _l('submit') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    $('#modal_assiged_promotion').modal('show');
    init_selectpicker();
    appValidateForm($('#form_modal_assiged_promotion'), {
    }, manage_assiged_promotion);

    function manage_assiged_promotion(form) {
        //var button = $('#modal_products').find('button[type="submit"]');
        //button.button({
        //    loadingText: '<?//= _l('cong_please_wait') ?>//'
        //});
        //button.button('loading');
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if(typeof(oTable) != 'undefined') {
                    oTable.draw('page');
                }
                $('#modal_assiged_promotion').modal('hide');
            }
            alert_float(response.alert_type, response.message);
        }).always(function() {
            // button.button('reset')
        });
        return false;
    }
</script>