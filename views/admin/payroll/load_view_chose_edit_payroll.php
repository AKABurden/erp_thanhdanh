<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('Chọn sửa bảng lương'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('year', 'year') ?>
                        <select name="year" id="year_edit" class="" require data-placeholder="<?= lang('year') ?>"
                            style="width: 100%;" style="width: 100%;">
                            <?php if (!empty(getYear())) : ?>
                            <?php foreach (getYear() as $key => $value) : ?>
                            <option <?= date('Y') == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?>
                            </option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('month', 'month') ?>
                        <select name="month" id="month_edit" class="" require data-placeholder="<?= lang('month') ?>"
                            style="width: 100%;" style="width: 100%;">
                            <?php if (!empty(getMonth())) : ?>
                            <?php foreach (getMonth() as $key => $value) : ?>
                            <option <?= date('m') == $key ? 'selected' : '' ?> value="<?= $key ?>">
                                <?= $value ?>
                            </option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Chi nhánh', 'branch_edit') ?>
                        <select name="branch_edit" id="branch_edit" class="branch_edit" required data-placeholder="<?= lang('Chi nhánh') ?>"
                                style="width: 100%;" style="width: 100%;">
                            <option></option>
                            <?php if (!empty(getListBranch())) : ?>
                                <?php foreach (getListBranch() as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>">
                                        <?= $value['name'] ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="button" class="btn btn-primary add edit_payroll"><?= _l('Sửa') ?></button>
        </div>
    </div>
</div>
<script>
$(function() {
    $('#month_edit').select2();
    $('#year_edit').select2();
    $('#branch_edit').select2();
    $(".edit_payroll").click(function() {
        month_edit = $("#month_edit").val();
        year_edit = $("#year_edit").val();
        branch_edit = $("#branch_edit").val();
        if (!month_edit || !year_edit || !branch_edit) {
            bootbox.alert('Xin vui lòng chọn năm ,tháng,chi nhánh');
            return;
        }
        if (month_edit && year_edit && branch_edit) {
            window.open(site.base_url + 'admin/payroll/editPayroll?month=' + month_edit + '&year=' +
                year_edit +'&branch='+branch_edit, "_blank");
        }
    });
})
</script>