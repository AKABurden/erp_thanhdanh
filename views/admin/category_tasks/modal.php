<div class="modal fade" id="modal_category_tasks" tabindex="-1" role="dialog">
    <div class="modal-dialog">
		<?php echo form_open(admin_url('category_tasks/modal/'.(!empty($category_tasks->id) ? $category_tasks->id : '')), ['id' => 'form_category_tasks']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? _l($title) : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->code : ''?>
                    <?php echo render_input('code', 'Mã', $value)?>
                </div>
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->time : ''?>
                    <?php echo render_input('time', 'Định mức (Phút)', $value)?>
                </div>
                <div class="col-md-12 hide">
                    <?php $value = !empty($category_tasks->departments) ? explode(',', $category_tasks->departments) : [];?>
                    <?php //echo render_select('departments[]', (!empty($departments) ? $departments : []), ['departmentid', 'name'], 'Phòng ban', $value, ['data-actions-box' => true, 'multiple' => true])?>
                    <?php echo render_select('departments[]', (!empty($departments) ? $departments : []), ['departmentid', 'name'], 'Phòng ban', $value, ['data-actions-box' => false, 'multiple' => false, 'onchange' => 'changeDepartments(this)'])?>
                </div>
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <?//= lang('Chức vụ cấp 1', 'role_id_1') ?>
                        <select name="role_id_1" id="role_id_1" data-live-search="true" data-none-selected-text="<?//= lang('Chức vụ cấp 1') ?>" class="form-control selectpicker">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?//= lang('Chức vụ cấp 2', 'role_id_2') ?>
                        <select name="role_id_2" id="role_id_2" data-live-search="true" data-none-selected-text="<?//= lang('Chức vụ cấp 2') ?>" class="form-control selectpicker">
                            <option value=""></option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <?//= lang('Loại CV', 'type') ?>
                        <select name="type" id="type" data-live-search="true" data-none-selected-text="<?//= lang('Loại CV') ?>" class="form-control selectpicker">
                            <option value=""></option>
                            <?php
                                //$getTypeCategoryTasks = getTypeCategoryTasks();
                            ?>
                            <?php //if(!empty($getTypeCategoryTasks)): ?>
                                <?php //oreach($getTypeCategoryTasks as $key => $value): ?>
                                    <option value="<?//= $key ?>"><?//= $value ?></option>
                                <?php //endforeach; ?>
                            <?php //endif; ?>
                        </select>
                    </div>
                </div> -->
                <div class="col-md-12">
                    <?php $value = !empty($category_tasks) ? $category_tasks->content : ''?>
                    <?php echo render_textarea('content', 'Nội dung', $value)?>
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
                                        <a class="hover-svg dropdown-toggle add-row" onclick="addProcessCategory()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="true">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Quy trình<span class="text-danger">*</span></th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($category_tasks->process)) {?>
                                        <?php foreach($category_tasks->process as $key => $value) {?>
                                            <tr>
                                                <td class="stt text-center"><?=($key + 1)?></td>
                                                <td>
                                                    <input type="text" name="process[]" class="form-control process" value="<?=$value['name']?>" placeholder="Quy trình">
                                                </td>
                                                <td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>
                                            </tr>
                                        <?php } ?>
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
    $('#modal_category_tasks').modal('show');
    appValidateForm($('#form_category_tasks'),{
        code: 'required'
    }, manage_category_tasks);
    function manage_category_tasks(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                $('#modal_category_tasks').modal('hide');
            }
            alert_float(response.alert_type, response.message);
            $('.table-category_tasks').DataTable().ajax.reload();
        }).fail(function(data){
            var error = JSON.parse(data.responseText);
            alert_float('danger',error.message);
        });
        return false;
    }

    function addProcessCategory() {
        var Tr = $(`<tr></tr>`);
        var tdSTT = $(`<td class="stt text-center"></td>`);
        var tdProcess = $(`<td><input type="text" name="process[]" class="form-control process" placeholder="Quy trình"></td>`);
        var tdRemove = $(`<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>`);
        Tr.append(tdSTT);
        Tr.append(tdProcess);
        Tr.append(tdRemove);
        $('#tb-process-category').find('tbody').append(Tr);
        orderStt($('#tb-process-category'));
    }

    function orderStt(table) {
        var list_stt = $(table).find('tr').find('.stt');
        $.each(list_stt, function (index, value) {
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
            url: site.base_url+'admin/category_tasks/getRoleParent',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                dtRoles1 = response.roles;
                opRole1 = '';
                $.each(dtRoles1, function (index, value) { 
                    opRole1+= `<option value="${index}">${value}</option>`;
                });
            }
        });
    }
</script>