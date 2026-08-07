<?php echo form_open('admin/category_regulations/detail_role_level/'.$id, array('id'=>'step-salary')); ?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã cấp bậc vai trò', 'code') ?>
                            <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('Mã cấp bậc vai trò').'" id="code" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tên cấp bậc vai trò', 'name') ?>
                            <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('Tên cấp bậc vai trò').'" id="name" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Bằng cấp tối thiều', 'min_degree') ?>
                            <?php echo form_input('min_degree', (isset($_POST['min_degree']) ? $_POST['min_degree'] : (!empty($dtData) ? $dtData['min_degree'] : 0)), 'placeholder="'.lang('Bằng cấp tối thiều').'" id="min_degree" class="form-control number-format  input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tiếng anh tối thiểu', 'min_english') ?>
                            <?php echo form_input('min_english', (isset($_POST['min_english']) ? $_POST['min_english'] : (!empty($dtData) ? ($dtData['min_english']) : 0)), 'placeholder="'.lang('Tiếng anh tối thiểu').'" id="min_english" class="form-control number-format  min_english input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tiếng trung tối thiểu', 'min_chinese') ?>
                            <?php echo form_input('min_chinese', (isset($_POST['min_chinese']) ? $_POST['min_chinese'] : (!empty($dtData) ? ($dtData['min_chinese']) : 0)), 'placeholder="'.lang('Tiếng trung tối thiểu').'" id="min_chinese" class="form-control min_chinese number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Số năm kinh nghiệm tối thiểu', 'min_years_exp') ?>
                            <?php echo form_input('min_years_exp', (isset($_POST['min_years_exp']) ? $_POST['min_years_exp'] : (!empty($dtData) ? ($dtData['min_years_exp']) : 0)), 'placeholder="'.lang('Số năm kinh nghiệm tối thiểu').'" id="min_years_exp" class="form-control min_years_exp number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Kỹ năng IT', 'min_it_skill') ?>
                            <?php echo form_input('min_it_skill', (isset($_POST['min_it_skill']) ? $_POST['min_it_skill'] : (!empty($dtData) ? ($dtData['min_it_skill']) : 0)), 'placeholder="'.lang('Kỹ năng IT').'" id="min_it_skill" class="form-control min_it_skill number-format input-tip"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Điểm sàn giá trị cốt lõi', 'pass_gtcl') ?>
                            <?php echo form_input('pass_gtcl', (isset($_POST['pass_gtcl']) ? $_POST['pass_gtcl'] : (!empty($dtData) ? $dtData['pass_gtcl'] : 0)), 'placeholder="'.lang('Điểm sàn giá trị cốt lõi').'" id="pass_gtcl" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Điểm sàn kỹ năng tuân thủ', 'pass_tc') ?>
                            <?php echo form_input('pass_tc', (isset($_POST['pass_tc']) ? $_POST['pass_tc'] : (!empty($dtData) ? $dtData['pass_tc'] : 0)), 'placeholder="'.lang('Điểm sàn kỹ năng tổ chức').'" id="pass_tc" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Điểm tổng tối thiểu (Đạt)', 'pass_total') ?>
                            <?php echo form_input('pass_total', (isset($_POST['pass_total']) ? $_POST['pass_total'] : (!empty($dtData) ? $dtData['pass_total'] : 0)), 'placeholder="'.lang('Điểm tổng tối thiểu (Đạt)').'" id="pass_total" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Trọng số giá trị cốt lõi', 'w_gtcl') ?>
                            <?php echo form_input('w_gtcl', (isset($_POST['w_gtcl']) ? $_POST['w_gtcl'] : (!empty($dtData) ? $dtData['w_gtcl'] : 0)), 'placeholder="'.lang('Trọng số giá trị cốt lõi').'" id="w_gtcl" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Trọng số kỹ năng tuân thủ', 'w_tc') ?>
                            <?php echo form_input('w_tc', (isset($_POST['w_tc']) ? $_POST['w_tc'] : (!empty($dtData) ? $dtData['w_tc'] : 0)), 'placeholder="'.lang('Trọng số kỹ năng tổ chức').'" id="w_tc" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Trọng số chuyên môn', 'w_cm') ?>
                            <?php echo form_input('w_cm', (isset($_POST['w_cm']) ? $_POST['w_cm'] : (!empty($dtData) ? $dtData['w_cm'] : 0)), 'placeholder="'.lang('Trọng số chuyên môn').'" id="w_cm" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Trọng số kỹ năng mềm', 'w_sk') ?>
                            <?php echo form_input('w_sk', (isset($_POST['w_sk']) ? $_POST['w_sk'] : (!empty($dtData) ? $dtData['w_sk'] : 0)), 'placeholder="'.lang('Trọng số kỹ năng mềm').'" id="w_sk" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Trọng số tư duy', 'w_td') ?>
                            <?php echo form_input('w_td', (isset($_POST['w_td']) ? $_POST['w_td'] : (!empty($dtData) ? $dtData['w_td'] : 0)), 'placeholder="'.lang('Trọng số thái độ').'" id="w_td" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('CEO duyệt', 'is_ceo_required') ?>
                            <select class="is_ceo_required" name="is_ceo_required" data-placeholder="<?= lang('CEO duyệt') ?>" id="is_ceo_required" style="width: 100%">
                                <option></option>
                                <option <?= !empty($dtData) ? ($dtData['is_ceo_required'] == 0 ? 'selected' : '') : '' ?> value="0">Không</option>
                                <option <?= !empty($dtData) ? ($dtData['is_ceo_required'] == 1 ? 'selected' : '') : '' ?> value="1">Có</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Điểm tối thiếu CEO yêu cầu', 'ceo_pass_score') ?>
                            <?php echo form_input('ceo_pass_score', (isset($_POST['ceo_pass_score']) ? $_POST['ceo_pass_score'] : (!empty($dtData) ? $dtData['ceo_pass_score'] : 0)), 'placeholder="'.lang('Điểm tối thiếu CEO yêu cầu').'" id="ceo_pass_score" class="form-control number-format input-tip"'); ?>
                        </div>
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
        $("#is_ceo_required").select2({
        });
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            code: 'required',
            name: 'required',
        }, handling);

        function handling(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var data = $(form).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
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
                    $('.add').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })

</script>