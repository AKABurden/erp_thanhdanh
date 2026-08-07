<div class="modal fade" id="modal_maintenance" tabindex="-1" role="dialog">
    <style>
        .text_checklist {
            position: absolute;
            resize: none;
            overflow: hidden;
            left: 25px;
            top: 0;
            font-size: 14px;
            width: 90%;
            border-radius: 3px;
            border: 0;
            outline: 0;
            padding-left: 5px;
        }

        .font-medium-12 {
            font-size: 12px !important;
        }

        .select_height_100 .dropdown-toggle {
            height: 100% !important;
            white-space: inherit;
        }
    </style>
    <div class="modal-dialog modal-lg" style="min-width: 80%;">
		<?php echo form_open(admin_url('maintenance/create_maintenance_stick/' . (!empty($maintenance->id) ? $maintenance->id : '')), ['id' => 'form_maintenance']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? _l($title) : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td style="width: 15%;"><label for="date">Ngày</label></td>
                        <td style="width: 35%;">
							<?= form_input('date', set_value('date') ? set_value('date') : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                        </td>
                        <td style="width: 15%;">
                            <label for="name">Tên phiếu bảo trì</label>
                        </td>
                        <td style="width: 35%;">
							<?= form_input('name', '', 'id="name" class="form-control" placeholder="' . lang('Tên phiếu') . '" required ') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <label for="category_tasks">Mã công việc</label>
                        </td>
                        <td style="width: 35%;">
							<?= render_select('category_tasks', (!empty($category_tasks) ? $category_tasks : []), ['id', 'code', 'content']) ?>
                        </td>
                        <td style="width: 15%;">
                            <label for="id_branch">Chi nhánh</label>
                        </td>
                        <td style="width: 35%;">
							<?= render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <div style="display: flex;">
								<?= lang('Thiết bị máy móc', 'id_maintenance_list') ?>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <select id="id_maintenance" name="id_maintenance" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
                                <?php if(!empty($list_maintenance)) {?>
                                    <?php foreach($list_maintenance as $key => $value) {?>
                                            <option value="<?=$value['id']?>"
                                                    data-note_main="<?= htmlentities($value['note_main']) ?>"
                                                    data-id_machines="<?= $value['id_machines'] ?>"
                                            ><?=$value['name_machines']?> (<?=_d($value['date'])?>)</option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="id_machines" id="id_machines" value="">
                        </td>
<!--                        <td style="width: 15%;">-->
<!--                            <div style="display: flex;">-->
<!--								--><?//= lang('Bộ phận', 'id_maintenance_list') ?>
<!--                            </div>-->
<!--                        </td>-->
<!--                        <td style="width: 35%;" class="select_height_100">-->
<!--                            <select id="id_maintenance_list" name="id_maintenance_list[]" multiple data-actions-box="true" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">-->
<!--                                --><?php //if(!empty($list_maintenance)) {?>
<!--                                    --><?php //foreach($list_maintenance as $key => $value) {?>
<!--                                            <option value="--><?//=$value['id']?><!--"-->
<!--                                                    data-subtext="--><?//= htmlentities($value['note_main']) ?><!--"-->
<!--                                                    data-id_machines="--><?//= $value['id_machines'] ?><!--"-->
<!--                                                    data-id_maintenance="--><?//= $value['id_maintenance'] ?><!--"-->
<!--                                                    data-note_main="--><?//= htmlentities($value['note_main']) ?><!--"-->
<!--                                            >--><?//=$value['name_machines']?><!--/--><?//=$value['name_maintenance']?><!-- (--><?//=_d($value['date'])?><!--)</option>-->
<!--                                    --><?php //} ?>
<!--                                --><?php //} ?>
<!--                            </select>-->
<!--                        </td>-->
                        <td>
                            <div style="display: flex;">
								<?= lang('Tổng số lượng', 'quantity_pcs') ?>
                            </div>
                        </td>
                        <td>
							<?php $value = !empty($maintenance) ? $maintenance->product_in_month : '' ?>
							<?= form_input('quantity_pcs', $value, 'id="quantity_pcs" class="form-control" placeholder="' . lang('Tổng số lượng') . '"') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
                            <div style="display: flex;">
								<?= lang('Loại bảo trì', 'type') ?>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <select id="type" name="type" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
								<?php if(!empty($type)) {?>
									<?php foreach($type as $key => $value) {?>
                                        <option value="<?=$key?>" <?=!empty($maintenance->type) && $maintenance->type == $key ? 'selected' : ''?>><?=$value?></option>
									<?php } ?>
								<?php } ?>
                            </select>

                        </td>
                        <td>
                            <div style="display: flex;">
								<?= lang('Ghi chú cách thức bảo trì', 'note_main') ?>
                            </div>
                        </td>
                        <td>
							<?php $value = !empty($maintenance) ? $maintenance->note_main : '' ?>
                            <textarea type="text" name="note_main" class="form-control" rows="4" placeholder="Ghi chú cách thức bảo trì" aria-invalid="false"><?=$value?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="type_html mtop30"></div>
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
    init_datepicker();
    init_selectpicker('refresh');
    $('#modal_maintenance').modal('show');
    appValidateForm($('#form_maintenance'), {
        date: 'required',
        name: 'required',
        // id_maintenance_list: 'required',
        category_tasks: 'required',
        id_trouble: 'required',
        id_branch: 'required'
    }, ManageMaintenanceTick);

    function ManageMaintenanceTick(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function (data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                if (data.idtask) {
                    init_task_modal(data.idtask);
                    $('#modal_maintenance').modal('hide');
                    $("#task-modal").addClass('show_add');
                }
                if(typeof(TableData) != 'undefined') {
                    TableData.draw('page');
                }
                if(typeof(calendar_selector_pod) != 'undefined') {
                    calendar_selector_pod.fullCalendar('refetchEvents');
                }
            } else {
                $(form).find('button[type="submit"]').removeAttr('disabled');
            }
        })
        .fail(function (err) {
            alert_float('danger', err.responseText);
            $(form).find('button[type="submit"]').removeAttr('disabled');
        });
        return false;
    }

    $('#id_maintenance').change(function () {
        // var id = $(this).val();
        // var data = {id : id};
        // if (typeof (csrfData) !== 'undefined') {
        //     data[csrfData['token_name']] = csrfData['hash'];
        // }
        // $('#id_maintenance_list').html('').selectpicker('refresh');
        //
        // $.post(admin_url + 'maintenance/get_maintenance_to_machines', data, function(result) {
        //     var result = JSON.parse(result);
        //     $.each(result, function(index, value) {
        //         $('#id_maintenance_list').append(`<option value="${value.id}"
        //                                             data-subtext="${value.note_main}"
        //                                             data-id_machines="${value.id_machines}"
        //                                             data-id_maintenance="${value.id_maintenance}"
        //                                             data-note_main="${value.note_main}">${value.name_maintenance} (${value.date})</option>`)
        //     })
        //     $('#id_maintenance_list').selectpicker('refresh');
        // })

        var data = $(this).find('option:selected');
        var note_main = '';
        if($(data).data('note_main')) {
            note_main = $(data).data('note_main');
        }
        $('textarea[name="note_main"]').val(note_main);
        $('#id_machines').val($(data).data('id_machines'));
    })

    $('#type, #id_maintenance').change(function() {
        var type = $('#type').val();
        var id_machines = $('#id_machines').val();
        $.get(admin_url + 'maintenance/get_table_category/' + type + '/' + id_machines, function(html) {
            $('.type_html').html(html);
        })
    })


    // $('#id_maintenance_list').change(function() {
    //     var data = $(this).find('option:selected');
    //     var note_main = '';
    //     $.each(data, function(index, value) {
    //         if($(value).data('note_main')) {
    //             note_main += $(value).data('note_main') + ', ' + "\n";
    //         }
    //     })
    //     $('textarea[name="note_main"]').val(note_main);
    // })

    $('#id_trouble').change(function () {
        var id_trouble = $(this).val();
        $.get(admin_url + 'trouble/get_trouble/' + id_trouble, function (result) {
            result = JSON.parse(result);
            $('.div_material').html('');
            if (result.material) {
                $.each(result.material, function (index, value) {
                    $('.div_material').append(`<div>
                                                <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                    <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                        <input type="checkbox" name="checked[material][${index}]" value="1">
                                                        <label for=""><span class="hide">${value.name}</span></label>
                                                        <textarea class="text_checklist" name="items[material][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                    </div>
                                                </div>
                                            </div>`);
                })
            }
            $('.div_man').html('');
            $.each(result.man, function (index, value) {
                $('.div_man').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[man][]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[man][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })
            $('.div_machine').html('');
            $.each(result.machine, function (index, value) {
                $('.div_machine').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[machine][${index}]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[machine][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })
            $('.div_method').html('');
            $.each(result.method, function (index, value) {
                $('.div_method').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[method][]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[method][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })
            $('.div_procedure').html('');
            $.each(result.procedure, function (index, value) {
                $('.div_procedure').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[procedure][${index}]" value="1" checked>
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[procedure][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })
        })
    })
</script>