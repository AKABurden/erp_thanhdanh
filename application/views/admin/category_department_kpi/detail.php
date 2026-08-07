<div class="modal-dialog modal-md">
    <?php echo form_open(admin_url('category_department_kpi/detail/' . $id),
        ['id' => 'category_department_kpi']); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
           <div class="row">
               <div class="col-md-12">
                   <div class="form-group">
                       <label for="department_id"><?= lang('Phòng ban') ?></label>
                       <select name="department_id" id="department_id" data-placeholder="<?= lang('Phòng ban') ?>" class="department_id modal-select2"  style="width: 100%">
                           <option value=""></option>
                           <?php if(!empty($dtDepartment)) {?>
                               <?php foreach ($dtDepartment as $key => $value) : ?>
                                   <option <?= !empty($dtData) && $dtData['department_id'] == $value['departmentid'] ? 'selected' : '' ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                               <?php endforeach ?>
                           <?php } ?>
                       </select>
                   </div>
                   <div class="form-group">
                       <label for="name"><?= lang('Danh mục đánh giá') ?></label>
                       <textarea name="name" class="name form-control" rows="4" id="name"><?= !empty($dtData) ? $dtData['name'] : '' ?></textarea>
                   </div>
               </div>
           </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    init_datepicker();
    init_selectpicker('refresh');
    $("#department_id").select2();
    appValidateForm($('#category_department_kpi'), {
        department_id: 'required',
        name: 'required',
    }, detail);

    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function(data) {
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
        }).fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }

</script>