<div class="modal fade" id="modal_import_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('IMPORT'); ?> - <?=$dataMain->name?></span>
                </h4>
            </div>
            <?php echo form_open('admin/set_prices/import_data/'.$dataMain->id,array('id'=>'import_form','enctype'=>'multipart/form-data')); ?>
            <div class="modal-body">
                <div class="row">
                    <a href="<?=base_url('uploads/import_set_prices_template.xlsx')?>" class="btn btn-success mleft15">Download Sample</a>
                    <hr />
                    <div class="row mleft5 mright5">
                        <div class="col-md-12">
                            <?php echo render_input('file_import','import_choose_file','','file'); ?>
                        </div>
                        <div class="col-md-12">
                            <div class="radio radio-primary">
                                <input type="radio" name="status" value="1" checked>
                                <label for="single"><?=_l('next_data')?></label>
                            </div>
                            <div class="radio radio-primary">
                                <input type="radio" name="status" value="2">
                                <label for="single"><?=_l('update_data')?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('import'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>