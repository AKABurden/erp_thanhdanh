<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title">Import bảng giá theo khách hàng (bằng file excel)</span>
                <div class="col-md-6 mtop5 pull-right">
                    <div class="text-right">
                        <a style="float: right;margin-top: 7px;font-size: 16px;padding-left: 5px;" href="<?=admin_url('import_price_group')?>"> Quay lại</a>
                        <a class="pull-right" href="<?=admin_url('import_price_group')?>">
                            <svg data-toggle="tooltip" data-placement="left" title="Quay lại" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" version="1.1" width="30" height="30" x="0" y="0" viewBox="0 0 438.483 438.483" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                <circle r="219.2415" cx="219.2415" cy="219.2415" fill="#0E5DAB" shape="circle"></circle>
                                <g transform="matrix(0.7,0,0,0.7,65.77247292995469,65.77260289192205)">
                                    <g xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path d="M431.168,230.762c-23.552-75.776-98.304-127.488-187.904-129.024V13.162c0-4.096-3.584-7.68-7.68-7.68    c-1.536,0-3.072,0.512-4.608,1.536L3.136,171.882c-3.584,2.56-4.096,7.168-1.536,10.752c0.512,0.512,1.024,1.024,1.536,1.536    l227.84,163.84c3.584,2.56,8.192,1.536,10.752-1.536c1.024-1.536,1.536-3.072,1.536-4.608v-88.064    c55.296,0,101.888,26.112,118.272,65.536c13.824,33.792,2.56,70.144-30.208,100.352c-3.072,3.072-3.584,7.68-0.512,10.752    c1.536,1.536,3.584,2.56,5.632,2.56h6.144c1.536,0,3.072-0.512,4.096-1.536C421.952,381.802,454.208,304.49,431.168,230.762z" fill="white" data-original="white"></path>
                                        </g>
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                    <g xmlns="http://www.w3.org/2000/svg">
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content" style="min-height: 650px">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-danger">- Xin vui lòng tải xuống tập tin mẫu ở bên dưới.</p>
                                <a href="<?php echo base_url('uploads/template/import_price_group.xlsx?vs=1.2') ?>" target="_blank" class="btn btn-success">Tải Mẫu import</a>
                                <hr />
                                <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'import_form')); ?>
                                <?php echo form_hidden('items_import', 'true'); ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php echo render_input('file_excel', 'dt_choose_excel_file', '', 'file'); ?>
                                    </div>
                                    <div class="col-md-3">
										<?= lang('customers', 'customers') ?>
                                        <input type="text" name="client" data-placeholder="<?= lang('customers') ?>" id="client" class="client" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_input('name_price', 'name_set_prices', '', 'text'); ?>
                                    </div>
<!--                                    <div class="col-md-3">--><?php //echo render_input('year', 'dt_set_year', '', 'number'); ?><!--</div>-->
                                </div>
                                <div class="clearfix"></div>
                                <hr />
                                <div class="form-group">
                                    <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                                </div>
                                <?php echo form_close(); ?>
                                <?php if (isset($message)) { ?>
                                    <div class="alert alert-info"><?php echo $message; unset($message); ?></div>
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
        ajaxSelectParams('#client', 'admin/clients/searchOnlyCustomers', 0, true, true);
        $('.action-menu').trigger('click');
        appValidateForm($('#import_form'), {
            file_excel: {
                required: true,
                extension: "xlsx,xls"
            },
            client: "required",
            name_price: "required",
            year: "required",
        });
    });
</script>
</body>

</html>