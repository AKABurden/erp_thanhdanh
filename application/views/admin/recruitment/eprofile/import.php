<?php echo form_open('admin/recruitment/importEprofile/', array('id' => 'importExcel')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?? '' ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <a target="_blank"
                       href="<?= base_url('uploads/import_c/import_ho_so_ung_vien.xlsx?vs=1.2') ?>">Download
                        file mẫu</a>
                </div>
                <div class="col-md-12">
                    <div class="fileinput fileinput-new mtop10 mbot10" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12 mbot20">
                            <span>File excel</span>
                            <input type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import"
                                   required="">
                        </span>
                    </div>
                    <div class="show-errors text-danger"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= lang('Import') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function () {
        appValidateForm($('#importExcel'), {
            file: 'required',
        }, handling);

        function handling(form) {
            // $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(form.find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
                .done(function (data) {
                    if (data.success) {
                        alert_float('success', data.message);
                        if (!data.errors) {
                            $('.modal-dialog .close').trigger('click');
                        }
                        $('.show-errors').html(data.errors);
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        if (typeof tAPI != 'undefined') {
                            tAPI.draw();
                        }
                    } else {
                        $('.show-errors').html(data.errors);
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function () {
                    console.log("error");
                });
            return false;
        }
    })
</script>