<div class="modal fade" id="modal_category_tasks" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('category_tasks/modal/' . (!empty($category_tasks->id) ? $category_tasks->id : '')), ['id' => 'form_category_tasks']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? _l($title) : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->code : '' ?>
                    <?php echo render_input('code', 'Mã', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->time : '' ?>
                    <?php echo render_input('time', 'Định mức (Phút)', $value) ?>
                </div>
                <div class="col-md-12 hide">
                    <?php $value = !empty($category_tasks->departments) ? explode(',', $category_tasks->departments) : []; ?>
                    <?php //echo render_select('departments[]', (!empty($departments) ? $departments : []), ['departmentid', 'name'], 'Phòng ban', $value, ['data-actions-box' => true, 'multiple' => true])
                    ?>
                    <?php echo render_select('departments[]', (!empty($departments) ? $departments : []), ['departmentid', 'name'], 'Phòng ban', $value, ['data-actions-box' => false, 'multiple' => false, 'onchange' => 'changeDepartments(this)']) ?>
                </div>
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <? //= lang('Chức vụ cấp 1', 'role_id_1') 
                        ?>
                        <select name="role_id_1" id="role_id_1" data-live-search="true" data-none-selected-text="<? //= lang('Chức vụ cấp 1') 
                                                                                                                    ?>" class="form-control selectpicker">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <? //= lang('Chức vụ cấp 2', 'role_id_2') 
                        ?>
                        <select name="role_id_2" id="role_id_2" data-live-search="true" data-none-selected-text="<? //= lang('Chức vụ cấp 2') 
                                                                                                                    ?>" class="form-control selectpicker">
                            <option value=""></option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <? //= lang('Loại CV', 'type') 
                        ?>
                        <select name="type" id="type" data-live-search="true" data-none-selected-text="<? //= lang('Loại CV') 
                                                                                                        ?>" class="form-control selectpicker">
                            <option value=""></option>
                            <?php
                            //$getTypeCategoryTasks = getTypeCategoryTasks();
                            ?>
                            <?php //if(!empty($getTypeCategoryTasks)): 
                            ?>
                                <?php //oreach($getTypeCategoryTasks as $key => $value): 
                                ?>
                                    <option value="<? //= $key 
                                                    ?>"><? //= $value 
                                                        ?></option>
                                <?php //endforeach; 
                                ?>
                            <?php //endif; 
                            ?>
                        </select>
                    </div>
                </div> -->
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? _dt($category_tasks->date_approve) : '' ?>
                    <?php echo render_datetime_input('date_approve', 'Ngày Ban Hành', $value) ?>
                </div>
                <div class="col-md-12">
                    <?php $active = !empty($category_tasks) ? $category_tasks->active : '0'; ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="radio radio-primary">
                                <input type="radio" value="0" id="check_active_0" name="active" <?=$active == 0 ? 'checked' : ''?>>
                                <label for="check_active_0">Chưa sử dụng</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="radio radio-primary">
                                <input type="radio" value="1" id="check_active_1" name="active" <?=$active == 1 ? 'checked' : ''?>>
                                <label for="check_active_1">Sử dụng</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="radio radio-primary">
                                <input type="radio" value="2" id="check_active_2" name="active" <?=$active == 2 ? 'checked' : ''?>>
                                <label for="check_active_2">Ngừng sử dụng</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->content : '' ?>
                    <?php echo render_textarea('content', 'Tên công việc', $value) ?>
                </div>
                <div class="col-md-12 mbot20">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_procedure">Quy Trình</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_procedure" class="tab-pane fade in active">
                            <table id="tb-process-category" class="table table-hover table-cs dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center open" style="width: 50px;">
                                         
                                        </th>
                                        <th>Quy trình<span class="text-danger">*</span></th>
                                        <th>KPI +</th>
                                        <th>KPI -</th>
                                        <th class="hide">Công đoạn bàn giao<span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($category_tasks->process)) {
                                        $i = 0;
                                    ?>
                                        <?php foreach ($category_tasks->process as $key => $value) { ?>
                                            <tr>
                                                <td class="stt text-center"><?= ($key + 1) ?></td>
                                                <td>
                                                    <input type="text" name="process[<?= $i ?>][name]" class="form-control process" value="<?= $value['name'] ?>" placeholder="Quy trình">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="process[<?= $i ?>][kpi_plus]" class="form-control" value="<?= isset($value['kpi_plus']) ? $value['kpi_plus'] : 0 ?>" placeholder="KPI +">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="process[<?= $i ?>][kpi_minus]" class="form-control" value="<?= isset($value['kpi_minus']) ? $value['kpi_minus'] : 0 ?>" placeholder="KPI -">
                                                </td>
                                                <td class="hide">
                                                    <?php
                                                    echo render_select('process[' . $i . '][stages]', $stages, array('id', 'name'), '', $value['stages']);
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $i++;
                                        } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    init_selectpicker();
    init_datepicker();
    $('#modal_category_tasks').modal('show');
    appValidateForm($('#form_category_tasks'), {
        code: 'required'
    }, manage_category_tasks);

    function manage_category_tasks(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#modal_category_tasks').modal('hide');
            }
            alert_float(response.alert_type, response.message);
            $('.table-category_tasks').DataTable().ajax.reload();
        }).fail(function(data) {
            var error = JSON.parse(data.responseText);
            alert_float('danger', error.message);
        });
        return false;
    }
    var i = '<?= !empty($i) ? $i : 0 ?>';

    function addProcessCategory() {
        var Tr = $(`<tr></tr>`);
        var tdSTT = $(`<td class="stt text-center"></td>`);
        var tdProcess = $(`<td><input type="text" name="process[${i}][name]" class="form-control process" placeholder="Quy trình"></td>`);
        var tdKpiPlus = $(`<td><input type="number" step="0.01" name="process[${i}][kpi_plus]" class="form-control" value="0" placeholder="KPI +"></td>`);
        var tdKpiMinus = $(`<td><input type="number" step="0.01" name="process[${i}][kpi_minus]" class="form-control" value="0" placeholder="KPI -"></td>`);
        var tdStages = $(`<td>
        <select class="selectpicker stages" name="process[${i}][stages]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <option></option>
            <?php foreach ($stages as $t) { ?>
                <option value="<?php echo $t['id']; ?>"> <?= $t['name'] ?> </option>
            <?php } ?>
        </select></td>`);
        var tdRemove = $(`<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>`);
        Tr.append(tdSTT);
        Tr.append(tdProcess);
        Tr.append(tdKpiPlus);
        Tr.append(tdKpiMinus);
        Tr.append(tdStages);
        Tr.append(tdRemove);
        i++;
        $('#tb-process-category').find('tbody').append(Tr);
        orderStt($('#tb-process-category'));
        $('.stages').selectpicker('refresh');
    }

    function orderStt(table) {
        var list_stt = $(table).find('tr').find('.stt');
        $.each(list_stt, function(index, value) {
            $(value).html((index + 1));
        })
    }

    function removeProcess(_this) {
        $(_this).parents('tr').remove();
        orderStt($('#tb-process-category'));
    }

    function changeDepartments(_this) {
        var department_id = $(_this).val();
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['department_id'] = department_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/category_tasks/getRoleParent',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                dtRoles1 = response.roles;
                opRole1 = '';
                $.each(dtRoles1, function(index, value) {
                    opRole1 += `<option value="${index}">${value}</option>`;
                });
            }
        });
    }
</script>