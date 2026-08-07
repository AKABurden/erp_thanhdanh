<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <?php echo form_open('admin/products/import_stage', array('id' => 'import-stages')); ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?= lang('note', 'note') ?>:
                        <div class="text-danger">
                            <div>
                                - <?= lang('tnh_please_download_template_sample') ?>:
                                <a href="<?= base_url('file/products/import_stages.xlsx?vs=1.4') ?>" title=""><?= lang('tnh_file_sample') ?></a>
                            </div>
                            <div>
                                - Vui lòng nhập các trường * bắt buộc và các trường mã phải có dự liệu trước trong phần mềm.</br>
                                - Không thay đổi thứ tự các cột trong file Excel.</br>
                                - Bắt đầu lưu dự liệu từ dòng thứ 3 trong file Excel.</br>
                                - Trường dự liệu phải dưới dạng text không dùng công thức hay định dạng.</br>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />
                        <!-- <div class="row">
                            <div class="col-md-2">
                                <div class="radio radio-info">
                                    <input type="radio" name="actions" id="actions_1" value="add" checked="checked">
                                    <label for="actions_1"><?//= lang('add') ?></label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio radio-info">
                                    <input type="radio" name="actions" id="actions_2" value="update">
                                    <label for="actions_2"><?//= lang('update') ?></label>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-md-4 mbot10">
                                <div class="">
                                    <div class="input-group input-file" name="file">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-choose" style="height: 36px;" type="button"><?= lang('file') ?></button>
                                        </span>
                                        <input type="text" name="text_file" class="form-control" placeholder='<?= lang('choose') ?>' />
                                        <span class="input-group-btn">
                                            <button class="btn btn-warning btn-reset" style="height: 36px;" type="button"><?= lang('tnh_reset') ?></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success add mtop5" name="save" value="1"><?= lang('save') ?></button>
                            </div>
                        </div>
                        <div class="show-errors">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        bs_input_file();

    });

    $(document).ready(function() {
        appValidateForm($('#import-stages'), {
                text_file: {
                    required: true,
                    extension: "xlsx,xls"
                },
            },
            importStages, {
                text_file: '<?= lang('tnh_please_choose_excel') ?>'
            }
        );

        function importStages(form) {
            $('.add').attr('disabled', 'disabled');
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
            //
            var url = form.action;
            $.ajax({
                    url: site.base_url + 'admin/products/import_stage',
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    console.log(data);
                    $('.add').removeAttr('disabled', 'disabled');
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                    $('.show-errors').html(data.errors);
                    // if (typeof data.errors != "undefined" && data.errors) {
                    //     $('.show-alert').show();
                    //     $('.show-errors').html(data.errors);
                    // }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    });
</script>