
<div id="modal_add_stage_quote_customer" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
			<?php echo form_open(admin_url('quote_stage/add_stage_quote_customer/' . $id), array('id' => 'modal_add_stage_quote_customer-form')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?=!empty($title) ? $title : ''?></h4>
            </div>
            <div class="modal-body">
                <?php $value = !empty($list_client) ? $list_client : []?>
                <?php echo render_select('list_client[]', (!empty($data_client) ? $data_client : []), ['userid', 'company_short', 'zcode'], 'Khách hàng', $value, ['multiple' => true, 'data-actions-box' => true])?>
                <table class="table" id="table_add_stage_quote_customer">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody> </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <button type="submit" class="btn btn-info">Lưu</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
			<?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    init_selectpicker();
    $('#modal_add_stage_quote_customer').modal('show');
    appValidateForm($('#modal_add_stage_quote_customer-form'), {
    }, manageStage);
    function manageStage(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function (data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                $('#modal_add_stage_quote_customer').modal('hide');
                if(tAPI) {
                    tAPI.ajax.reload();
                }
            }
        })
        .fail(function (err) {
            alert_float('danger', err.responseText);
        });
        return false;
    }

    $('select[name="list_client[]"]').change(function(e) {
        var data = $(this).find('option:selected');
        $('#table_add_stage_quote_customer').find('tbody').html('');
        $.each(data, function(index, value) {
            $('#table_add_stage_quote_customer').find('tbody').append(`<tr><td>${$(value).text()}</td><td><a class="btn btn-danger btn-icon removeTrTable" data-id="${$(value).val()}"><i class="fa fa-remove"></i></a></td></tr>`);
        })
    })
    $(function(e) {
        $('select[name="list_client[]"]').trigger('change');
    })

    $('body').on('click', '.removeTrTable', function() {
        var id = $(this).data('id');
        $(this).parents('tr').remove();
        $(`select[name="list_client[]"]`).find(`option[value="${id}"]`).removeAttr('selected');
        $(`select[name="list_client[]"]`).selectpicker('refresh');
    })
</script>