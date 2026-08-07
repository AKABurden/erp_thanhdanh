<div class="modal fade" id="view_modal_data_inspection_criteria_all" role="dialog">
    <div class="modal-dialog" style="min-width: 10%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? $title : ''; ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/tasks/add_task_process_all/', array('id' => 'task_all')); ?>
            <div class="modal-body">
                <input class="hide" id="array_task" name="array_task" value="<?= $array_task ?>">
                <input class="hide" id="procedure_tasks" name="procedure_tasks" value="<?= $procedure_tasks ?>">
                <input class="hide" id="category_tasks_search" name="category_tasks_search" value="<?= $category_tasks_search ?>">
                <input class="hide" id="date_start" name="date_start" value="<?= $date_start ?>">
                <input class="hide" id="date_end" name="date_end" value="<?= $date_end ?>">
                <input class="hide" id="date_start_end" name="date_start_end" value="<?= $date_start_end ?>">
                <input class="hide" id="date_end_end" name="date_end_end" value="<?= $date_end_end ?>">
                <div class="row">
                    <div class="col-md-12">
                        <table id="tb-handling-products-stages" class="table dataTable tb-handling-products-stages">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Công Việc</th>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Duyệt</th>
                                    <th class="text-center" style="width:23%;">Quy Chuẩn Kiểm Soát Hoàn Thành</th>
                                    <th class="text-center" style="width:15%;">
                                        <div class="checkbox mass_select_all_wrap">
                                            <input onclick="validate()" type="checkbox" id="tb-handling-products-stages-check" class="tb-handling-products-stages-check" data-to-table="tb-handling-products-stages">
                                            <label>Có</label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category_hand_over as $key => $value) { ?>
                                    <?php
                                    $isCheck = '';
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]" value="<?= $value['id_category_tasks_process'] ?>">
                                            <?= $value['name'] ?>
                                        </td>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]" value="<?= $value['id_category_tasks_process'] ?>">
                                            <?= $value['approval_standards'] ?>
                                        </td>
                                        <td>
                                            <input type="hidden" <?= $isCheck ?> name="inspection_criteria_id[<?= $key ?>]" value="<?= $value['id_category_tasks_process'] ?>">
                                            <?= $value['completion_control_standards'] ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" <?= $isCheck ?> class="tb-handling-products-stages-child" name="isCheck[<?= $value['id_category_tasks_process'] ?>]">
                                                <label for="isCheck"></label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary add add-finished-stages"><?= _l('save') ?></button>
                <div class="checkbox checkbox-danger pull-right hide">
                    <input type="checkbox" class="save_create_task" id="save_create_task" value="1">
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(function() {
        appValidateForm($('#task_all'), {
            receiver: "required",
        }, saveHandlingProductsAll);

        function saveHandlingProductsAll(form) {
            $('.add').attr('disabled', 'disabled');
            // var data = $(form).serialize();
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function(i, val) {
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
                .done(function(data) {
                    if (data.success) {
                        if (data.id_delivery_records && $('#save_create_task').prop('checked')) {
                            window.open(data.href, '_blank');
                        }
                        $('.add').removeAttr('disabled', 'disabled');
                        alert_float(data.alert_type, data.message);
                        if (typeof _table_api != 'undefined' && _table_api != '') {
                            _table_api.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                        return false;
                    }

                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof _table_api != 'undefined' && _table_api != '') {
                            _table_api.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
        <?php if (empty($category_hand_over) && empty($is)) { ?>
            $('.add-finished-stages').click();
        <?php } ?>
    })
    init_selectpicker();
</script>