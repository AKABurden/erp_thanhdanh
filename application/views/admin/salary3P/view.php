<style>
    div.form-group > label{
        font-weight: 500;
    }
</style>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã vị trí', 'role_id') ?>
                            <div><?= !empty($dtData) ? $dtData['code_role'] : '' ?></div>
                        </div>
                    </div>
                     <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã cấp bậc vai trò', 'role_level_id') ?>
                            <div><?= !empty($dtData) ? $dtData['code_role_level'] : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã danh mục thâm niên', 'grade_id') ?>
                            <div><?= !empty($dtData) ? $dtData['code_grade'] : '' ?><span> (<?= $dtData['seniority_from_month'] .'-'. $dtData['seniority_to_month'] ?> tháng)</span></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('code', 'code') ?>
                            <div><?= !empty($dtData) ? $dtData['code'] : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Version', 'version') ?>
                            <div><?= !empty($dtData) ? $dtData['version'] : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày hiệu lực', 'effective_from') ?>
                            <div><?= (!empty($dtData) ? _dhau($dtData['effective_from']) : '') ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày hết hiệu lực', 'effective_to') ?>
                            <div><?= (!empty($dtData) ? _dhau($dtData['effective_to']) : '') ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('note', 'note') ?>
                            <div><?= !empty($dtData) ? $dtData['note'] : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Hệ số', 'coef') ?>
                            <div><?= !empty($dtData) ? $dtData['coef'] : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P1', 'salary_p1') ?>
                            <div><?= !empty($dtData) ? formatMoney($dtData['salary_p1']) : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P2', 'salary_p2') ?>
                            <div><?= !empty($dtData) ? formatMoney($dtData['salary_p2']) : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Lương P3', 'salary_p3') ?>
                            <div><?= !empty($dtData) ? formatMoney($dtData['salary_p3']) : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Phụ cấp P3', 'allowed_p3') ?>
                            <div><?= !empty($dtData) ? formatMoney($dtData['allowed_p3']) : '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ghi chú phụ cấp P3', 'allowed_p3_note') ?>
                            <div><?= !empty($dtData) ? $dtData['allowed_p3_note'] : '' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
        </div>
    </div>
</div>