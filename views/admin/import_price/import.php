<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-danger">- Xin vui lòng tải xuống tập tin mẫu ở bên dưới.</p>
                                <a href="<?php echo base_url('uploads/import_price_supplier_v1.xlsx') ?>" target="_blank" class="btn btn-success">Tải Mẫu</a>
                                <hr />
                                <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'import_form')); ?>
                                <?php echo form_hidden('items_import', 'true'); ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php echo render_input('file_excel', 'dt_choose_excel_file', '', 'file'); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_select('suppliers_id', $data_supplier, array('id', 'company'), 'supplier'); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_input('name_price', 'name_set_prices', '', 'text'); ?>
                                    </div>
                                    <div class="col-md-3"><?php echo render_input('year', 'dt_set_year', '', 'number'); ?></div>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                                </div>
                                <?php echo form_close(); ?>
                                <?php if (isset($message)) { ?>
                                    <div class="alert alert-info"><?php echo $message;
                                                                    unset($message); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
    $(function() {
        appValidateForm($('#import_form'), {
            file_excel: {
                required: true,
                extension: "xlsx,xls"
            },
            suppliers_id: "required",
            name_price: "required",
            year: "required",
        });
    });
</script>
</body>

</html>