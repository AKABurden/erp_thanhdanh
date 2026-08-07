<?php echo form_open_multipart('admin/contract_appendix/detail/'.$id, array('id'=>'detail_contract_appendix')); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Mã phụ lục', 'code') ?> <span class="text-danger">*</span>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('Mã phụ lục').'" id="code" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Tên phụ lục', 'name') ?> <span class="text-danger">*</span>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('Tên phụ lục').'" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Hợp đồng lao động', 'contract_labor_id') ?> <span class="text-danger">*</span>
                        <input type="hidden" name="contract_labor_id" id="contract_labor_id" value="<?= (!empty($dtData) ? $dtData['contract_labor_id'] : '') ?>" class="form-control" required />
                        <input type="text" id="contract_labor_search" class="modal-select2" style="width: 100%;" placeholder="<?= lang('Chọn hợp đồng lao động') ?>" style="width: 100%;" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Lương cơ bản mới', 'salary') ?>
                        <?php echo form_input('salary', (isset($_POST['salary']) ? $_POST['salary'] : (!empty($dtData) ? formatMoney($dtData['salary']) : 0)), ' id="salary" class="form-control input-tip number-format"'); ?>
                        <small class="text-muted">Lương cơ bản sau khi duyệt</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('Lương vị trí mới', 'salary_position') ?>
                        <?php echo form_input('salary_position', (isset($_POST['salary_position']) ? $_POST['salary_position'] : (!empty($dtData) ? formatMoney($dtData['salary_position']) : 0)), ' id="salary_position" class="form-control input-tip number-format"'); ?>
                        <small class="text-muted">Lương vị trí sau khi duyệt</small>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày tái ký', 'date_sign') ?>
                        <input type="text" name="date_sign" class="date_sign datepicker form-control" autocomplete="off" id="date_sign" value="<?= (!empty($dtData['date_sign']) ? _dhau($dtData['date_sign']) : '') ?>">
                        <small class="text-muted">Ngày tái ký hợp đồng (nếu có)</small>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('File đính kèm', 'file') ?>
                        <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <small class="text-muted">Cho phép: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Tối đa 10MB)</small>
                        <?php if (!empty($dtData['file_path'])): ?>
                            <div class="mtop10">
                                <a href="<?= base_url($dtData['file_path']) ?>" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fa fa-download"></i> Tải file hiện tại
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ghi chú', 'note') ?>
                        <textarea name="note" id="note" class="form-control" rows="3" placeholder="<?= lang('Ghi chú') ?>"><?= (isset($_POST['note']) ? $_POST['note'] : (!empty($dtData) ? $dtData['note'] : '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        init_datepicker();
        
        // Khởi tạo select2 ajax cho hợp đồng lao động bằng hàm ajaxSelectCallBack
        ajaxSelectCallBack('#contract_labor_search', 'admin/contract_appendix/get_contract_labor_ajax', <?= (!empty($dtData['contract_labor_id']) ? $dtData['contract_labor_id'] : 0) ?>);
        
        // Khi chọn hoặc thay đổi giá trị, cập nhật vào input hidden
        $("#contract_labor_search").on('change', function (e) {
            var selectedValue = $(this).val();
            $("#contract_labor_id").val(selectedValue || '');
        });
        
        appValidateForm($('#detail_contract_appendix'), {
            code: 'required',
            name: 'required',
            contract_labor_id: 'required',
        }, handling);

        function handling(form) {
            $('.add').attr('disabled', 'disabled');
            var formData = new FormData(form);
            
            $.ajax({
                url: form.action,
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                processData: false,
                contentType: false,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'Có lỗi xảy ra');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>
