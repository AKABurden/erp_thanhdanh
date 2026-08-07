<div class="modal fade" id="add_template_modal" tabindex="-1" role="dialog">
    <?php echo form_open(admin_url('sms/AddTemplate'),array('class'=>'template-form','autocomplete'=>'off')); ?>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button group="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">
                        <span class="edit-title">
                            <?php echo !empty($template) ? _l('cong_update_template_sms') : _l('cong_add_template_sms'); ?>
                        </span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="id" name="id" value="<?=!empty($template->id) ? $template->id : ''?>">
                            <div class="form-group" app-field-wrapper="name">
                                <label for="name" class="control-label">Tên mẫu Template</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?=!empty($template->name) ? $template->name : ''?>">
                            </div>
                            <div class="form-group" app-field-wrapper="content">
                                <label for="content" class="control-label">Nội dung Template</label>
                                <textarea id="content" name="content" class="form-control" rows="6"><?=!empty($template->content) ? trim($template->content) : ''?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button group="submit" class="btn btn-info">
                        <?php echo _l('submit'); ?>
                    </button>
                    <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>