<div class="modal-dialog modal-lg" style="min-width: 80%;">
    <?php echo form_open(admin_url('suggest_check/detail/' . $id),
        ['id' => 'suggest_check']); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <table class="tnh-tb table-bordered table-hover">
                <tbody>
                <tr>
                    <td style="width: 15%;">
                        <?= lang('dt_reference_suggest', 'reference_no') ?>
                    </td>
                    <td style="width: 35%;">
                        <div class="form-group">
                            <input type="text" name="reference_no" class="form-control" id="reference_no"
                                   value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly=""
                                   aria-invalid="false">
                        </div>
                    </td>
                    <td style="width: 15%;">
                        <?= lang('date', 'date') ?>
                    </td>
                    <td style="width: 35%;">
                        <?= form_input('date',
                            set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Mã công việc', 'id_category_task') ?></td>
                    <td colspan="1">
                        <select name="id_category_task" id="id_category_task" class="id_category_task"
                                data-placeholder="<?= lang('Mã công việc') ?>" style="width: 100%;">
                            <option value=""></option>
							<?php if (!empty($category_task)) { ?>
								<?php foreach ($category_task as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['id_category_task'] == $value['id'] ? 'selected' : '') : '' ?>
                                            value="<?= $value['id'] ?>" ><?= $value['code'] ?> <?=!empty($value['content']) ? ('('.$value['content'].')') : ''?></option>
								<?php } ?>
							<?php } ?>
                        </select>
                    </td>
                    
                    <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                    <td colspan="1">
                        <?php
                            $branchs = getListBranch();
                        ?>
                        <select name="branch_id" id="branch_id" class="branch_id"
                                data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($branchs)) { ?>
                                <?php foreach ($branchs as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?>
                                        value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Thiết Bị/Khu Vực', 'machines_id') ?></td>
                    <td colspan="1">
                        <input type="text" id="machines_id" class="machines_id"
                               data-placeholder="<?= lang('Mã thiết bị') ?>" style="width: 100%;"
                               value=""
                               title="">
                        <div class="machines_append mtop10"></div>
                    </td>
                    <td><?= lang('Ghi chú', 'note') ?></td>
                    <td colspan="1">
                        <textarea name="note" id="note" class="form-control note" cols="3" rows="4"><?= !empty($dtData) ? $dtData['note'] : '' ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Phiếu yêu cầu bảo dưỡng', 'suggest_maintenance_id') ?></td>
                    <td colspan="1">
                        <select name="suggest_maintenance_id" id="suggest_maintenance_id" class="suggest_maintenance_id c_select2" data-placeholder="Phiếu yêu cầu bảo dưỡng" style="width: 100%;" title="Phiếu yêu cầu bảo dưỡng">
                            <option value=""></option>
                            <?php if(!empty($suggest_maintenance_id)) {
                                foreach($suggest_maintenance_id as $key => $value) {?>
                                    <option <?= !empty($dtData) ? ($dtData['suggest_maintenance_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['reference_no'] ?></option>
                                <?php }?>
                            <?php } ?>
                        </select>
                    </td>
                    <td></td>
                    <td colspan="1"></td>
                </tr>
                </tbody>
            </table>
            <fieldset class="mtop10">
                <legend>Thao tác</legend>
                <div class="row">
                    <div class="col-md-3">
                        <?= lang('Chọn nhanh người kiểm tra', 'staff_check_manager') ?>
                        <select class="staff_check_manager" id="staff_check_manager"  style="width: 100%;"  data-placeholder="<?= lang('Chọn nhanh người kiểm tra') ?>">
                            <option></option>
                            <?php
                                if (!empty($employees)) {
                                    foreach ($employees as $kk => $vv) {
                                        echo '<option value="' . $vv['staffid'] . '">' . $vv['fullname'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('Kết Quả', 'staff_check_manager') ?>
                        <select class="result_id_manager" id="result_id_manager"  style="width: 100%;"  data-placeholder="<?= lang('Chọn nhanh người kiểm tra') ?>">
                            <option></option>
                            <?php
								if (!empty($dtResult)) {
									foreach ($dtResult as $kk => $vv) {
										echo '<option  value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
									}
								}
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('Quản lý khu vực thiết bị', 'staff_manager_manager') ?>
                        <select class="staff_manager_manager" id="staff_manager_manager"  style="width: 100%;"  data-placeholder="<?= lang('Chọn nhanh quản lý khu vực thiết bị') ?>">
                            <option></option>
                            <?php
								if (!empty($employees)) {
									foreach ($employees as $kk => $vv) {
										echo '<option value="' . $vv['staffid'] . '">' . $vv['fullname'] . '</option>';
									}
								}
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-warning mtop25" type="button" onclick="AppendEvent()">Áp dụng</button>
                    </div>
                    <div class="clearfix"></div>
                    <hr/>
                </div>
            </fieldset>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-maintenance-machines" class="table dataTable">
                        <thead>
                        <tr>
                            <th class="text-center">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox"  id="check_all">
                                    <label for="check_all"></label>
                                </div>
                            </th>
                            <th class="text-center"><?= lang('STT') ?></th>
                            <th class="text-center"><?= lang('Danh mục kiểm tra') ?></th>
                            <th class="text-center"><?= lang('Quy định 5S') ?></th>
                            <th class="text-center"><?= lang('Hình Ảnh') ?></th>
                            <th class="text-center"><?= lang('Người kiểm tra') ?></th>
                            <th class="text-center" style="width: 120px"><?= lang('Kết quả') ?></th>
                            <th class="text-center"><?= lang('Đánh giá') ?></th>
                            <th class="text-center"><?= lang('Quản Lý Khu Vực/Thiết Bị') ?></th>
                            <th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 0;
                        if (!empty($dtItems)) { ?>
                            <?php foreach ($dtItems as $kItem => $Item) { ?>
                                <?php $rowID = $Item['item_type'] . '_' . $Item['item_id'];?>
                                <tr class="bg-danger not-tr <?=$rowID?>">
                                    <td>
                                        <div class="checkbox checkbox-primary mleft10">
                                            <input type="checkbox" class="check_items_all" id="check_items_all_<?=$rowID?>" data-class="rowID_<?=$rowID?>">
                                            <label for="check_items_all_<?=$rowID?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center event-plus show" data-class="rowID_<?=$rowID?>"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                                    <td colspan="7" class="row_items_title">
                                        <b><?=$Item['item_type'] == 'machines' ? 'Thiết Bị' : 'Máy Móc'?>:</b>  <?=$Item['name']?>
                                    </td>
                                    <td class="text-center td-remove-item"><a class="text-danger" onclick="removeItems(this, '<?=$rowID?>')"><i class="fa fa-remove"></i></a><a></a></td>
                                </tr>
								<?php foreach($Item['detail'] as $key => $value) {?>
                                    <?php
                                        $optionResult = '<option></option>';
                                        if (!empty($dtResult)) {
                                            foreach ($dtResult as $kk => $vv) {
                                                $optionResult .= '<option ' . ($vv['id'] == $value['result_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                            }
                                        }
                                        $optionStaffCheck = '<option></option>';
                                        if (!empty($employees)) {
                                            foreach ($employees as $kk => $vv) {
                                                $optionStaffCheck .= '<option ' . ($vv['staffid'] == $value['staff_check'] ? 'selected' : '') . ' value="' . $vv['staffid'] . '">' . $vv['fullname'] . '</option>';
                                            }
                                        }
                                        $optionStaff = '<option></option>';
                                        if (!empty($employees)) {
                                            foreach ($employees as $kk => $vv) {
                                                $optionStaff .= '<option ' . ($vv['staffid'] == $value['staff_manager'] ? 'selected' : '') . ' value="' . $vv['staffid'] . '">' . $vv['fullname'] . '</option>';
                                            }
                                        }
                                    ?>
                                    
                                    <tr class="rowID_<?=$rowID?> is_<?=$rowID?>_<?= $value['machines_maintenance_id'] ?>">
                                        <td>
                                            <div class="checkbox checkbox-primary mleft10">
                                                <input type="checkbox" class="check_items" id="check_items_<?= $counter ?>">
                                                <label for="check_items_<?= $counter ?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center"><?= (++$kItem) ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <input type="hidden" class="counter" name="counter[]"
                                                       value="<?= $counter ?>">
                                                <input type="hidden" class="machines_maintenance_id"
                                                       name="machines_maintenance_id[<?= $counter ?>]"
                                                       value="<?= $value['machines_maintenance_id'] ?>">
                                                <input type="hidden" class="suggest_check_item_id"
                                                       name="suggest_check_item_id[<?= $counter ?>]"
                                                       value="<?= $value['id'] ?>">
                                                <input type="hidden" class="item_type" name="item_type[<?= $counter ?>]" value="<?= $value['item_type'] ?>">
                                                <input type="hidden" class="item_id" name="item_id[<?= $counter ?>]" value="<?= $value['item_id'] ?>">
                                                <?= $value['name_machines_maintenance'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <textarea type="text" name="regulation_5s[<?= $counter ?>]" class="regulation_5s form-control"><?= $value['regulation_5s'] ?></textarea>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?= ViewHtmlImagesDt((!empty($value['img']) ? $value['img'] : ''))?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="staff_check" id="staff_check_<?= $counter ?>" name="staff_check[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Người kiểm tra') ?>">
                                                    <?= $optionStaffCheck ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="result_id" id="result_id_<?= $counter ?>"
                                                        name="result_id[<?= $counter ?>]" style="width: 100%;"
                                                        data-placeholder="<?= lang('Kết quả') ?>">
                                                    <?= $optionResult ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <textarea type="text" name="evaluate[<?= $counter ?>]" class="evaluate form-control"><?= $value['evaluate'] ?></textarea>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="staff_manager" id="staff_manager_<?= $counter ?>" name="staff_manager[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('Nhân viên') ?>">
                                                    <?= $optionStaff ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php $counter++;
                                } ?>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
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
    $('select.suggest_maintenance_id ').select2();
    ajaxSelectParams('#machines_id', 'admin/suggest_check/search_suggest_repalce', '', true, true);
    $("#branch_id").select2();
    $("#id_category_task").select2();
    var dtResult = <?= !empty($dtResult) ? json_encode($dtResult) : '{}' ?>;
    var dtStaff = <?= !empty($employees) ? json_encode($employees) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;
    $(`#staff_check_manager`).select2();
    $(`#result_id_manager`).select2();
    $(`#staff_manager_manager`).select2();
    // $('#check_all').change(function(){
    //     if($(this).prop('checked')) {
    //         $('input.check_items').prop('checked', true);
    //     }
    //     else {
    //         $('input.check_items').prop('checked', false);
    //     }
    // })
    getRowTitle();
    function getRowTitle() {
        var rowTitle = $('.row_items_title');
        $('.machines_append').html('');
        $.each(rowTitle, function(index, value) {
            var aRemove = $(value).parents('tr').find('.td-remove-item').html();
            $('.machines_append').append(`<div class="inline-block label mleft5 mtop5" style="background: #dddada;color: black;">${$(value).text()} ${aRemove}</div>`)
        })
    }
    $('#check_all').change(function(){
        if($(this).prop('checked')) {
            $('input.check_items_all').prop('checked', true).trigger('change');
        }
        else {
            $('input.check_items_all').prop('checked', false).trigger('change');
        }
    })

    $('#suggest_check').on('change', '.check_items_all', function () {
        var TrClass = $(this).attr('data-class');
        if($(this).prop('checked')) {
            // $('input.check_items').prop('checked', true);
            $(`.${TrClass}`).find('input.check_items').prop('checked', true);
        }
        else {
            $(`.${TrClass}`).find('input.check_items').prop('checked', false);
        }
    })
    
    
    function AppendEvent() {
        if($('input.check_items:checked').length == 0) {
            alert_float('danger', 'Không có dữ liệu được chọn');
            return false;
        }
        if(confirm('Dữ liệu được chọn sẽ được áp dụng')) {
            var staff_check_manager = $('#staff_check_manager').val();
            if (staff_check_manager) {
                var list_check_items = $('input.check_items:checked');
                $.each(list_check_items, function (index, value) {
                    $(value).parents('tr').find('select.staff_check').val(staff_check_manager);
                })
                $('select.staff_check').select2();
                $('#staff_check_manager').val('').select2();
            }
            var result_id_manager = $('#result_id_manager').val();
            if (result_id_manager) {
                var list_check_items = $('input.check_items:checked');
                $.each(list_check_items, function (index, value) {
                    $(value).parents('tr').find('select.result_id').val(result_id_manager);
                })
                $('select.result_id').select2();
                $('#result_id_manager').val('').select2();
            }
            var staff_manager_manager = $('#staff_manager_manager').val();
            if (staff_manager_manager) {
                var list_check_items = $('input.check_items:checked');
                $.each(list_check_items, function (index, value) {
                    $(value).parents('tr').find('select.staff_manager').val(staff_manager_manager);
                })
                $('select.staff_manager').select2();
                $('#staff_manager_manager').val('').select2();
            }
        }
    }
    //
    // $('#staff_check_manager').change(function() {
    //     if(confirm('Bạn có chắc muốn thay đổi nhân viên đã chọn?')) {
    //         var staff_check_manager = $(this).val();
    //         var list_check_items = $('input.check_items:checked');
    //         $.each(list_check_items, function (index, value) {
    //             $(value).parents('tr').find('select.staff_check').val(staff_check_manager).select2();
    //         })
    //     }
    //     else {
    //         $('#staff_check_manager').val('').select2();
    //     }
    // })
    //
    // $('#result_id_manager').change(function() {
    //     if(confirm('Bạn có chắc muốn thay đổi nhân viên đã chọn?')) {
    //         var result_id_manager = $(this).val();
    //         var list_check_items = $('input.check_items:checked');
    //         $.each(list_check_items, function (index, value) {
    //             $(value).parents('tr').find('select.result_id').val(result_id_manager).select2();
    //         })
    //     }
    //     else {
    //         $('#result_id_manager').val('').select2();
    //     }
    // })
    // $('#staff_manager_manager').change(function() {
    //     if(confirm('Bạn có chắc muốn thay đổi nhân viên đã chọn?')) {
    //         var staff_manager_manager = $(this).val();
    //         var list_check_items = $('input.check_items:checked');
    //         $.each(list_check_items, function (index, value) {
    //             $(value).parents('tr').find('select.staff_manager').val(staff_manager_manager).select2();
    //         })
    //     }
    //     else {
    //         $('#staff_manager_manager').val('').select2();
    //     }
    // })
    
    $('#suggest_check').on('click', '.event-plus', function() {
        var TrClass = $(this).attr('data-class');
        if($(this).hasClass('show')) {
            $(this).removeClass('show')
            $(this).find('i').removeClass('fa-minus-square-o');
            $(this).find('i').addClass('fa-plus-square-o');
            $(`.${TrClass}`).addClass('hide');
        }
        else {
            $(this).addClass('show');
            $(this).find('i').removeClass('fa-plus-square-o');
            $(this).find('i').addClass('fa-minus-square-o');
            $(`.${TrClass}`).removeClass('hide');
        }
    })

    $("#machines_id").change(function () {
        string_id = $(this).val();
        // $("#tb-maintenance-machines").find('tbody').html('');
        if (string_id) {
            $.ajax({
                url: site.base_url + 'admin/suggest_check/getMaintenaceMachinesDetail',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    string_id: string_id,
                },
            }).done(function (data) {
                    if (data.dtMaintenaceMachines.length > 0) {
                        $.each(data.dtMaintenaceMachines, function (k, v) {
                            loadItem(v, data.dtMaintenace);
                        });
                        getRowTitle();
                    }
                    else {
                        alert_float('danger', 'Thiết bị/Khu vực không có danh mục kiểm tra 5S');
                    }
                    $("#machines_id").val('');
                    ajaxSelectParams('#machines_id', 'admin/suggest_check/search_suggest_repalce', '', true, true);
                })
                .fail(function () {
                    console.log("error");
                });
        }
    })

    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            $(`#result_id_${i}`).select2({
                allowClear: true
            });
            $(`#staff_check_${i}`).select2();
            $(`#staff_manager_${i}`).select2();
        }
    }

    function optionResult(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtResult, function (index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function optionStaff(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtStaff, function (index, el) {
            selected = selected_id == el.staffid ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.staffid + '">' + el.fullname + '</option>';
        });
        return option;
    }
    function removeItems(_this, _class) {
        $(`.${_class}`).remove();
        $(`.rowID_${_class}`).remove();
        getRowTitle();
    }

    function loadItem(item, item_main = {}) {
        if($(`.${item_main.type}_${item_main.id}`).length == 0) {
            var trMain = ` <tr class="bg-danger ${item_main.type}_${item_main.id} not-tr">
                             <td>
                                <div class="checkbox checkbox-primary mleft10">
                                    <input type="checkbox" class="check_items_all" id="check_items_all_${item_main.type}_${item_main.id}" data-class="rowID_${item_main.type}_${item_main.id}">
                                    <label for="check_items_all_${item_main.type}_${item_main.id}"></label>
                                </div>
                            </td>
                            <td class="text-center event-plus show" data-class="rowID_${item_main.type}_${item_main.id}"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                            <td colspan="7" class="row_items_title">
                                ${item_main.type == 'cleaning' ? '<b>Khu Vực:</b> ' : (item_main.type == 'machines' ? '<b>Thiết Bị:</b> ' : '')} ${item_main.name}
                            </td>
                            <td class="text-center td-remove-item"><a class="text-danger" onclick="removeItems(this, '${item_main.type}_${item_main.id}')"><i class="fa fa-remove"></i></a></td>
                        </tr>`;
            $("#tb-maintenance-machines").find('tbody').append(trMain);
        }
        if($(`.is_${item_main.type}_${item_main.id}_${item.id}`).length > 0) {
            return true;
        }
        
        var tdStt = `<div class="text-center stt"></div>`;
        var tdName = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="hidden" class="machines_maintenance_id" name="machines_maintenance_id[${counter}]" value="${item.id}">
            <input type="hidden" class="item_type" name="item_type[${counter}]" value="${item_main.type}">
            <input type="hidden" class="item_id" name="item_id[${counter}]" value="${item_main.id}">
            ${item.name}
        </div>`;
        var tdImage = `<?=ViewHtmlImagesDt('')?>`;
        var tdStaffCheck = `<div>
             <select class="staff_check" id="staff_check_${counter}" name="staff_check[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Người kiểm tra') ?>">
                ${optionStaff()}
            </select>
        </div>`;
        var tdResult = `<div>
             <select class="result_id" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        var tdEvaluate = `<div xmlns="http://www.w3.org/1999/html">
            <textarea type="text" name="evaluate[${counter}]" class="evaluate form-control" ></textarea>
        </div>`;
        var tdRegulation = `<div>
                            <textarea name="regulation_5s[${counter}]" class="regulation_5s form-control">${item.note}</textarea>
                        </div>`;
        var tdStaffManager = `<div>
             <select class="staff_manager" id="staff_manager_${counter}" name="staff_manager[${counter}]" style="width: 100%;" data-placeholder="<?= lang('Nhân viên') ?>">
                ${optionStaff()}
            </select>
        </div>`;
        var trItem = $(`<tr class="rowID_${item_main.type}_${item_main.id} is_${item_main.type}_${item_main.id}_${item.id}"></tr>`);
        trItem.append(`<td class="text-center">
                            <div class="checkbox checkbox-primary mleft10">
                                <input type="checkbox" class="check_items" id="check_items_${counter}">
                                <label for="check_items_${counter}"></label>
                            </div>
                        </td>`)
        trItem.append(`<td class="text-center stt">${tdStt}</td>`)
        trItem.append(`<td>${tdName}</td>`)
        trItem.append(`<td>${tdRegulation}</td>`)
        trItem.append(`<td>${tdImage}</td>`)
        trItem.append(`<td>${tdStaffCheck}</td>`)
        trItem.append(`<td>${tdResult}</td>`)
        trItem.append(`<td>${tdEvaluate}</td>`)
        trItem.append(`<td>${tdStaffManager}</td>`)
        if(item.img) {
            $(trItem).find('.preview_image').find('img').attr('src', item.img);
            $(trItem).find('.preview_image').find('a').prop('href', item.img);
        }
        trItem.append(`<td></td>`)

        if($(`.${item_main.type}_${item_main.id}`).length > 0) {
            $(`.${item_main.type}_${item_main.id}`).after(trItem);
        }
        else {
            $("#tb-maintenance-machines").find('tbody').append(trItem);
        }
        $(`#result_id_${counter}`).select2({
            allowClear: true
        });
        $(`#staff_check_${counter}`).select2();
        $(`#staff_check_${counter}`).attr('required', 'required');
        $(`#staff_manager_${counter}`).select2();
        init_datepicker();
        counter++;
        getTotal();
    }

    function getTotal() {
        tb = '#tb-maintenance-machines tbody tr:not(".not-tr")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
        }
    }

    appValidateForm($('#suggest_check'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        area_id: 'required',
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
        }).done(function (data) {
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
        }).fail(function () {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
</script>