<?php echo form_open('admin/roles/updateStaff/'.$id, array('id'=>'update-staff')); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="hidden" name="role_id" value="<?= $id ?>">
                        <?= lang('Nhân viên', 'staff_id') ?>
                        <select style="width: 100%" id="staff_id" onchange="changeStaff(this)" data-placeholder="<?= lang('Nhân viên') ?>" class="modal-select2 staff_id">
                            <option value=""></option>
                            <?php if(!empty($staff)) {?>
                                <?php foreach ($staff as $key => $value) : ?>
                                    <option value="<?= $value['staffid'] ?>" data-name="<?= $value['firstname'].' '.$value['lastname'] ?>" data-image="<?= $value['image'] ?>"><?= $value['firstname'].' '.$value['lastname'] ?></option>
                                <?php endforeach ?>
                            <?php } ?>
                        </select>
                    </div>
                    <table class="table table_update_staff table-bordered table-cs dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                                <th class="text-center"><?= lang('Nhân viên') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Tác vụ') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0; if (!empty($dtStaffRole)){ ?>
                                <?php foreach ($dtStaffRole as $key => $value){ ?>
                                    <tr>
                                        <td><div class="text-center stt"><?= (++$key) ?></div></td>
                                       <td><div class="td-staff">
                                            <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                            <input type="hidden" name="staff_id[<?= $counter ?>]" class="staff_id" value="<?= $value['staffid'] ?>">
                                            <img  class="staff-profile-image-small" src="<?= $value['image'] ?>">
                                             <?= get_staff_full_name($value['staffid']) ?>
                                           </div></td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                            <?php $counter ++; } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= lang('Cập nhập') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $("#staff_id").select2();
    var counter = <?= !empty($counter) ? $counter : 0 ?>;
    function changeStaff(_this){
        staff_id = $(_this).val();
        image = $('option:selected', _this).attr("data-image");
        name = $('option:selected', _this).attr("data-name");

        tdStt = `<div class="text-center stt"></div>`;
        tdStaff = `<div class="td-staff">
            <input type="hidden" name="counter[]" class="counter" value="${counter}">
            <input type="hidden" name="staff_id[${counter}]" class="staff_id" value="${staff_id}">
            <img  class="staff-profile-image-small" src="${image}">
            ${name}
        </div>`;
        tdAction = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        tr = `<tr>
            <td>${tdStt}</td>
            <td>${tdStaff}</td>
            <td class="text-center">${tdAction}</td>
            </tr>
        `;
        $(".table_update_staff tbody").append(tr);
        counter ++;
        getTotal();
    }

    function removeRow(el)
    {
        $(el).closest('tr').remove();
        getTotal();
    }

    function getTotal(){
        tb = '.table_update_staff tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
        }
    }

    $(function(){
        init_selectpicker();

        appValidateForm($('#update-staff'), {
        }, updateStaff);

        function updateStaff(form) {
            // $('.add').attr('disabled', 'disabled');
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
                        $('.modal-dialog .close').trigger('click');
                        location.reload();
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    console.log("error");
                });
            return false;
        }
    })
</script>