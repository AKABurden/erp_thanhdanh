<?php echo form_open(base_url($link), array('id' => 'export_form', 'enctype' => 'multipart/form-data')); ?>
<style>
    fieldset {
        padding: .35em .625em .75em!important;
        margin: 0 2px!important;
        border: 1px solid #19a9ea!important;
    }
    legend {
        font-size: 15px;
        font-weight: 500;
        width: auto!important;
    }

    ._mtop20 {
        margin-top: 20px!important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/bala.DualSelectList.css"
      xmlns="http://www.w3.org/1999/html">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title"><?= _l('tnh_export_excel') ?></h4>
        </div>
        <input type="hidden" name="export_excel" id="export_excel" class="form-control" value="1">
        <div class="modal-body">
            <select class="" name="cloumns[]" id="cloumns" multiple="multiple" required></select>
            <div id="dualSelectExample"></div>
            <div class="clearfix"></div>
            <fieldset class="_mtop20">
                <legend>Số Lượng Xuất</legend>
                <div class="col-md-6">
                    <?= render_input('limit_start', 'Từ', 0)?>
                </div>
                <div class="col-md-6">
                    <?= render_input('limit_end', 'Đến', 499)?>
                </div>
            </fieldset>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button class="btn btn-primary add" type="submit"><?= lang('excel') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
    <script src="<?= base_url() ?>assets/js/bala.DualSelectList.jquery.js"></script>
	<?php echo form_close(); ?>

    <script>
        $(document).ready(function () {
            var dsl = $('#dualSelectExample').DualSelectList({
                'candidateItems': <?= json_encode($list) ?>,
                'selectionItems': [],
                'idSelect': '#cloumns',
                'css_dsl_panel': 'overflow: auto;height: 400px;',
            });

            appValidateForm($('#export_form'), {
                cloumns: 'required',
            }, exportExcel);

            function exportExcel(form) {
                $('.add').attr('disabled', 'disabled');
                var data = $(form).serialize();
                var url = form.action;
                $.ajax({
                    url: site.base_url + '<?= $link ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                })
                    .done(function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            download(data.filename, data.file);
                            $('.add').removeAttr('disabled', 'disabled');
                        } else {
                            alert_float('danger', data.message);
                            $('.add').removeAttr('disabled', 'disabled');
                        }
                    })
                    .fail(function () {
                        alert_float('danger', 'errors');
                        $('.add').removeAttr('disabled', 'disabled');
                    });
                return false;
            }
        });
    </script>

