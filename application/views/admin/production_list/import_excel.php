<?php echo form_open('admin/production_list/import_excel', array('id' => 'import_moderation_plan')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Import update'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('note', 'note') ?>:
                    <div class="text-danger">
                        <div>
                            - Vui lòng xuất excel để làm tập tin mẫu.<br>
                            - Không thay đổi thứ tự các cột trong file Excel.</br>
                            - Trường dữ liệu phải dưới dạng text không dùng công thức hay định dạng.</br>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="mbot10">
                        <?= render_date_input('actual_date', 'Ngày Bắt đầu/Kết thúc Thực tế') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mbot10">
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
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {
        bs_input_file();
        init_datepicker();

        appValidateForm($('#import_moderation_plan'), {
                text_file: {
                    required: true,
                    extension: "xlsx,xls"
                },
                actual_date: 'required',
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
                    url: site.base_url + 'admin/production_list/import_excel',
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
                        $('.close').click();
                        oTable.draw();
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

    // $(function() {
    //     datepicker_init();
    //     $('#parent_id').select2({
    //         allowClear: true
    //     });
    //     appValidateForm($('#add-category'), {
    //         code: 'required',
    //         name: 'required'
    //     }, addCategory);

    //     function addCategory(form) {
    //         $('.add').attr('disabled', 'disabled');
    //         tinymce.get('note').save();
    //         var data = $(form).serialize();
    //         var url = form.action;
    //         $.ajax({
    //                 url: site.base_url + 'admin/products/add_category',
    //                 type: 'POST',
    //                 dataType: 'JSON',
    //                 data: data,
    //             })
    //             .done(function(data) {
    //                 if (data.result) {
    //                     alert_float('success', data.message);
    //                     if (typeof oTable != 'undefined') {
    //                         oTable.draw();
    //                     }
    //                     $('.modal-dialog .close').trigger('click');
    //                 } else {
    //                     alert_float('danger', data.message);
    //                     $('.add').removeAttr('disabled', 'disabled');
    //                 }
    //             })
    //             .fail(function() {
    //                 alert_float('danger', 'error');
    //                 $('.add').removeAttr('disabled', 'disabled');
    //             });
    //         return false;
    //     }
    //     init_editor('textarea[name="note"]');
    // })
</script>