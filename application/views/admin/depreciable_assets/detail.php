<div class="modal fade" id="depreciable_assets_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <?php echo form_open(admin_url('depreciable_assets/detail/' . (!empty($depreciable_assets) ? $depreciable_assets->id : '')), array('id' => 'depreciable_assets-form')); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="book-title"><?= !empty($title) ? $title : '' ?></span></h4>
                </div>
                <div class="modal-body">
                    <?php $value = !empty($depreciable_assets) ? $depreciable_assets->id_machines : ''?>
                   <?=render_select('id_machines', (!empty($list_machines) ? $list_machines : []), ['id', 'name', 'code'], 'Thiết Bị', $value)?>

                    <?php $value = !empty($depreciable_assets) ? $depreciable_assets->name_short : ''?>
                    <?=render_input('name_short', 'Tên Riêng Của Thiết Bị', $value, 'text')?>

                    <?php $value = !empty($depreciable_assets) ? _dC($depreciable_assets->date_depreciation) : ''?>
                    <?=render_date_input('date_depreciation', 'Ngày Bắt Đầu Tính Khấu Hao', $value)?>

                    <?php $value = !empty($depreciable_assets) ? $depreciable_assets->depreciation_period : ''?>
                    <?=render_input('depreciation_period', 'Thời Gian Khấu Hao (Tháng)', $value, 'number')?>

                    <?php $value = !empty($depreciable_assets) ? number_format_data($depreciable_assets->asset_value) : ''?>
                    <?=render_input('asset_value', 'Giá Trị Tài Sản (VNĐ)', $value, 'text', [], [], '','number-format')?>


                    <?php $value = !empty($depreciable_assets) ? $depreciable_assets->note : ''?>
                    <?=render_textarea('note', 'Ghi Chú', $value)?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" id="submit"><?= _l('submit') ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function (){
        init_selectpicker();
        init_datepicker();
        $('#depreciable_assets_modal').modal('show');
    })
    $(function () {
        appValidateForm($('#depreciable_assets-form'), {
            id_machines: 'required',
            date_depreciation: 'required',
            depreciation_period: 'required',
            asset_value: 'required',
        }, manageFrom);

        function manageFrom(form) {
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });
            var button = $(form).find('button[type="submit"]');
            button.button({loadingText: 'please wait...'});
            button.button('loading');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            }).done(function (response) {
                alert_float(response.alert_type, response.message);
                if (response.success == true) {
                    if(typeof tAPI != 'undefined') {
                        tAPI.draw(false);
                    }
                    $('#depreciable_assets_modal').modal('hide');
                }
            })
            .always(function() {
                button.button('reset');
            })
            .fail(function () {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
                button.button('reset');
            });
            return false;
        }
    });
</script>