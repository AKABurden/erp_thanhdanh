<?php echo form_open_multipart('admin/kpi/handling_criteria/' . $id, array('id' => 'add-handling_criteria', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4" style="border-right: 1px solid #cedae6;">
                    <div class="form-group">
                        <?= lang('start_date', 'start_date') ?>
                        <input type="text" name="start_date" autocomplete="off" id="start_date" required class="form-control start_date datepicker" value="<?= !empty($kpi_criteria) ? _d($kpi_criteria['start_date']) : date('d/m/Y') ?>">
                    </div>
                    <div class="form-group">
                        <?= lang('end_date', 'end_date') ?>
                        <input type="text" name="end_date" autocomplete="off" id="end_date" required class="form-control end_date datepicker" value="<?= !empty($kpi_criteria) ? _d($kpi_criteria['end_date']) : date('d/m/Y') ?>">
                    </div>
                    <div class="form-group">
                        <?= lang('Mã KPI', 'code_criteria') ?>
                        <?php echo form_input('code_criteria', (isset($_POST['code_criteria']) ? $_POST['code_criteria'] : (!empty($kpi_criteria) ? $kpi_criteria['code_criteria'] : '')), 'placeholder="' . lang('Mã tiêu chí') . '" id="code_criteria" required class="form-control input-tip"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('tnh_criteria', 'criteria') ?>
                        <?php echo form_input('criteria', (isset($_POST['criteria']) ? $_POST['criteria'] : (!empty($kpi_criteria) ? $kpi_criteria['criteria'] : '')), 'placeholder="' . lang('tnh_criteria') . '" id="criteria" required class="form-control input-tip"'); ?>
                    </div>
                    <div class="form-group">
                        <?php
                            $type = 1;
                            if (!empty($kpi_criteria)) $type = $kpi_criteria['type'];
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="radio radio-primary">
                                    <input type="radio" name="type" class="type_criteria" id="c_staff" value="1" <?= $type == 1 ? 'checked="checked"' : '' ?> >
                                    <label for="c_staff"><?= lang('staff') ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="radio radio-primary">
                                    <input type="radio" name="type" class="type_criteria" id="c_department" value="2" <?= $type == 2 ? 'checked="checked"' : '' ?>>
                                    <label for="c_department"><?= lang('department') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox checkbox-danger hide">
                            <input type="checkbox" <?= !empty($kpi_criteria) && ($kpi_criteria['behavior_discipline']) ? 'checked' : '' ?> name="behavior_discipline" id="behavior_discipline" value="1">
                            <label for="behavior_discipline"><?= lang('tnh_behavior_discipline') ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <?//= lang('staff', 'staff') ?>
                        <label for="staff"><?= lang('staff') ?>/<?= lang('department') ?></label>
                        <div class="div-staff" <?= $type == 1 ? '' : 'style="display: none;"' ?>>
                            <select name="staff" id="staff" class="form-control staff selectpicker" data-live-search="true" data-none-selected-text="<?= lang('staff') ?>">
                                <option value=""></option>
                                <?php if (!empty($staffs)) : ?>
                                    <?php foreach ($staffs as $key => $value) : ?>
                                        <?php
                                        if ($value['active'] == 0 && (!empty($kpi_criteria) && $kpi_criteria['staff'] != $value['staffid'])) {
                                            continue;
                                        }
                                        ?>
                                        <option <?= (!empty($kpi_criteria) && $kpi_criteria['type'] == 1 && $kpi_criteria['staff'] == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="div-department" <?= $type == 2 ? '' : 'style="display: none;"' ?>>
                            <select name="_department" id="department" data-none-selected-text="<?= lang('department') ?>" data-actions-box="true" data-live-search="true" class="form-control selectpicker">
                                <?php if(!empty($departments)): 
                                ?>
                                    <?php foreach($departments as $key => $value): ?>
                                        <option <?= (!empty($kpi_criteria) && $kpi_criteria['type'] == 2 && $kpi_criteria['staff'] == $value['departmentid'] ? 'selected' : '') ?> value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= lang('tnh_unit', 'unit') ?>
                        <?php echo form_input('unit', (isset($_POST['unit']) ? $_POST['unit'] : (!empty($kpi_criteria) ? $kpi_criteria['unit'] : '')), 'placeholder="' . lang('tnh_unit') . '" id="unit" class="form-control input-tip"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('tnh_target', 'target') ?>
                        <?php echo form_input('target', (isset($_POST['target']) ? $_POST['target'] : (!empty($kpi_criteria) ? $kpi_criteria['target'] : '')), 'placeholder="' . lang('tnh_target') . '" id="target" class="form-control input-tip"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang('tnh_weight_number', 'weight_number') ?>
                        <input type="number" name="weight_number" id="weight_number" class="form-control" value="<?= (!empty($kpi_criteria) ? $kpi_criteria['weight_number'] : 1) ?>" min="1" max="4" step="1" required="required" title="">
                    </div>
                    <!-- <div class="form-group">
                        <? //= lang('department', 'department') 
                        ?>
                        <?php
                        // $arrCriteriaDepartment = [];
                        // if (!empty($kpi_criteria)) {
                        //     $kpi_criteria_department = $this->kpi_model->getKpiCriteriaDepartment($kpi_criteria['id']);
                        //     if (!empty($kpi_criteria_department)) {
                        //         foreach ($kpi_criteria_department as $key => $value) {
                        //             $arrCriteriaDepartment[] = $value['department_id'];
                        //         }
                        //     }
                        // }
                        ?>
                        <select name="department[]" id="department" data-none-selected-text="<? //= lang('department') 
                                                                                                ?>" multiple="true" data-actions-box="true" data-live-search="true" class="form-control selectpicker" required="required">
                            <?php //if(!empty($departments)): 
                            ?>
                                <?php //foreach($departments as $key => $value): 
                                ?>
                                    <option <? //= (!empty($arrCriteriaDepartment) && in_array($value['departmentid'], $arrCriteriaDepartment) ? 'selected' : '') 
                                            ?> value="<? //= $value['departmentid'] 
                                                                                                                                                                            ?>"><? //= $value['name'] 
                                                                                                                                                                                                            ?></option>
                                <?php //endforeach; 
                                ?>
                            <?php //endif; 
                            ?>
                        </select>
                    </div> -->
                    <!-- <div class="form-group">
                        <? //= lang('role', 'role') 
                        ?>
                        <?php
                        // if (!empty($kpi_criteria)) {
                        //     $kpi_criteria_role = $this->kpi_model->getKpiCriteriaRoles($kpi_criteria['id']);
                        //     $arrCriteriaRole = [];
                        //     if (!empty($kpi_criteria_role)) {
                        //         foreach ($kpi_criteria_role as $key => $value) {
                        //             $arrCriteriaRole[] = $value['role_id'];
                        //         }
                        //     }
                        // }
                        ?>

                        <?php
                        //$roles = $this->kpi_model->getRoleDepartment($arrCriteriaDepartment);
                        ?>

                        <select name="role[]" id="role" data-none-selected-text="<? //= lang('role') 
                                                                                    ?>" multiple="true" data-actions-box="true" data-live-search="true" class="form-control selectpicker" required="required">
                            <?php //if(!empty($roles)): 
                            ?>
                                <?php //foreach($roles as $key => $value): 
                                ?>
                                    <option <? //= (!empty($arrCriteriaRole) && in_array($value['roleid'], $arrCriteriaRole) ? 'selected' : '') 
                                            ?> value="<? //= $value['roleid'] 
                                                                                                                                                            ?>"><? //= $value['name'] 
                                                                                                                                                                                    ?></option>
                                <?php //endforeach; 
                                ?>
                            <?php //endif; 
                            ?>
                        </select>
                    </div> -->
                </div>
                <?php
                $calRecipe = calRecipe();
                ?>
                <div class="col-md-8">
                    <table class="table table-hover dataTable">
                        <thead>
                            <tr>
                                <th style="width: 130px;" class="text-center"><?= lang('name') ?></th>
                                <th style="width: 130px;" class="text-center"><?= lang('tnh_recipe') ?></th>
                                <th class="text-center"><?= lang('tnh_measure') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1.<?= lang('tnh_not_reached') ?><br>(1 điểm)</td>
                                <td>
                                    <select name="not_reached" id="not_reached" data-none-selected-text="<?= lang('tnh_recipe') ?>" class="form-control selectpicker recipe">
                                        <option></option>
                                        <?php if (!empty($calRecipe)) : ?>
                                            <?php foreach ($calRecipe as $key => $value) : ?>
                                                <option <?= (!empty($kpi_criteria) && $kpi_criteria['not_reached'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="flex-center">
                                        <input type="text" name="not_reached_from" id="not_reached_from" class="form-control not_reached_from" value="<?= (!empty($kpi_criteria) ? $kpi_criteria['not_reached_from'] : '') ?>">
                                        <!-- <input type="number" name="not_reached_from" id="not_reached_from" class="form-control not_reached_from" value="<?//= (!empty($kpi_criteria) ? $kpi_criteria['not_reached_from'] : '') ?>" min="0" step="1"> -->
                                        <!-- <span class="span-cs-hide span-not_reached_to <?//= (empty($kpi_criteria) || $kpi_criteria['not_reached'] != 4 ? 'hide' : '') ?>" style="padding: 5px;"> - </span>
                                        <input type="number" name="not_reached_to" id="not_reached_to" class="form-control input-cs-hide not_reached_to <?//= (empty($kpi_criteria) || $kpi_criteria['not_reached'] != 4 ? 'hide' : '') ?>" value="<?//= (!empty($kpi_criteria) ? $kpi_criteria['not_reached_to'] : 0) ?>" min="0" step="1"> -->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2.<?= lang('tnh_need_keep_trying') ?><br>(2 điểm)</td>
                                <td>
                                    <select name="need_keep_trying" id="need_keep_trying" data-none-selected-text="<?= lang('tnh_recipe') ?>" class="form-control selectpicker recipe">
                                        <option></option>
                                        <?php if (!empty($calRecipe)) : ?>
                                            <?php foreach ($calRecipe as $key => $value) : ?>
                                                <option <?= (!empty($kpi_criteria) && $kpi_criteria['need_keep_trying'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="flex-center">
                                        <input type="text" name="need_keep_trying_from" id="need_keep_trying_from" class="form-control need_keep_trying_from" value="<?= (!empty($kpi_criteria) ? $kpi_criteria['need_keep_trying_from'] : '') ?>">
                                        <!-- <span class="span-cs-hide span-need_keep_trying_to <?//= (empty($kpi_criteria) || $kpi_criteria['need_keep_trying'] != 4 ? 'hide' : '') ?>" style="padding: 5px;"> - </span>
                                        <input type="number" name="need_keep_trying_to" id="need_keep_trying_to" class="form-control input-cs-hide need_keep_trying_to <?//= (empty($kpi_criteria) || $kpi_criteria['need_keep_trying'] != 4 ? 'hide' : '') ?>" value="<?//= (!empty($kpi_criteria) ? $kpi_criteria['need_keep_trying_to'] : 0) ?>" min="0" step="1"> -->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3.<?= lang('tnh_obtain') ?><br>(3 điểm)</td>
                                <td>
                                    <select name="obtain" id="obtain" data-none-selected-text="<?= lang('tnh_recipe') ?>" class="form-control selectpicker recipe">
                                        <option></option>
                                        <?php if (!empty($calRecipe)) : ?>
                                            <?php foreach ($calRecipe as $key => $value) : ?>
                                                <option <?= (!empty($kpi_criteria) && $kpi_criteria['obtain'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="flex-center">
                                        <input type="text" name="obtain_from" id="obtain_from" class="form-control obtain_from" value="<?= (!empty($kpi_criteria) ? $kpi_criteria['obtain_from'] : '') ?>">
                                        <!-- <span class="span-cs-hide span-obtain_to <?//= (empty($kpi_criteria) || $kpi_criteria['obtain'] != 4 ? 'hide' : '') ?>" style="padding: 5px;"> - </span>
                                        <input type="number" name="obtain_to" id="obtain_to" class="form-control input-cs-hide obtain_to <?//= (empty($kpi_criteria) || $kpi_criteria['obtain'] != 4 ? 'hide' : '') ?>" value="<?//= (!empty($kpi_criteria) ? $kpi_criteria['obtain_to'] : 0) ?>" min="0" step="1"> -->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4.<?= lang('tnh_pass') ?><br>(4 điểm)</td>
                                <td>
                                    <select name="pass" id="pass" data-none-selected-text="<?= lang('tnh_recipe') ?>" class="form-control selectpicker recipe">
                                        <option></option>
                                        <?php if (!empty($calRecipe)) : ?>
                                            <?php foreach ($calRecipe as $key => $value) : ?>
                                                <option <?= (!empty($kpi_criteria) && $kpi_criteria['pass'] == $key ? 'selected' : '') ?> value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="flex-center">
                                        <input type="text" name="pass_from" id="pass_from" class="form-control pass_from" value="<?= (!empty($kpi_criteria) ? $kpi_criteria['pass_from'] : '') ?>">
                                        <!-- <span class="span-cs-hide span-pass_to <?//= (empty($kpi_criteria) || $kpi_criteria['pass'] != 4 ? 'hide' : '') ?>" style="padding: 5px;"> - </span>
                                        <input type="number" name="pass_to" id="pass_to" class="form-control pass_to input-cs-hide <?//= (empty($kpi_criteria) || $kpi_criteria['pass'] != 4 ? 'hide' : '') ?>" value="<?//= (!empty($kpi_criteria) ? $kpi_criteria['pass_to'] : 0) ?>" min="0" step="1"> -->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php $value = !empty($kpi_criteria['note_criteria']) ? $kpi_criteria['note_criteria'] : '' ?>
                                    <?php echo render_textarea('note_criteria', 'Ghi chú', $value) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    function behaviorDiscipline(_is_show) {
        behavior_discipline = $('#behavior_discipline').prop('checked');
        if (behavior_discipline) {
            if (!_is_show) {
                $('select#staff').val('');
                $('select#staff').selectpicker('refresh');
                
                $('select#department').val('');
                $('select#department').selectpicker('refresh');
            }


            $('.dropdown.staff').css({
                'pointer-events': 'none',
                'opacity': '0.8'
            });

            $('.dropdown#department').css({
                'pointer-events': 'none',
                'opacity': '0.8'
            });
        } else {
            if (!_is_show) {
                $('select#staff').val('');
                $('select#staff').selectpicker('refresh');

                $('select#department').val('');
                $('select#department').selectpicker('refresh');
            }

            $('.dropdown.staff').css({
                'pointer-events': '',
                'opacity': ''
            });
            
            $('.dropdown#department').css({
                'pointer-events': '',
                'opacity': ''
            });
        }
    }

    $(function() {
        init_selectpicker();

        $('.type_criteria').change(function(event) {
            is_check = $(this).prop('checked');
            if (is_check) {
                type_criteria = $(this).val();
                if (type_criteria == 1) {
                    $('.div-staff').show();
                    $('.div-department').hide();
                } else if (type_criteria == 2) {
                    $('.div-staff').hide();
                    $('.div-department').show();
                }
            }
        });

        appValidateForm($('#add-handling_criteria'), {
            code_criteria: 'required',
            criteria: 'required',
            // staff: 'required',
            start_date: 'required',
            end_date: 'required',
            // department: 'required',
            // role: 'required',
        }, handlingCriteria);

        $('select.recipe').change(function(event) {
            cTrH = $(this).closest('tr');
            recipe = $(this).val();
            if (recipe == 4) {
                cTrH.find('.span-cs-hide').removeClass('hide');
                cTrH.find('.input-cs-hide').removeClass('hide');
            } else {
                cTrH.find('.span-cs-hide').addClass('hide');
                cTrH.find('.input-cs-hide').addClass('hide');
            }
        });

        $('#behavior_discipline').change(function(event) {
            behaviorDiscipline();
        });
        behaviorDiscipline(1);

        // $('select#department').change(function(event) {
        //     department_id = $(this).val();
        //     var dataPOST = {};
        //     dataPOST[csrfData['token_name']] = csrfData['hash'];
        //     dataPOST['department_id'] = department_id;
        //     $.ajax({
        //         type: "POST",
        //         url: site.base_url + 'admin/kpi/changeRole',
        //         data: dataPOST,
        //         dataType: "json",
        //         success: function(response) {
        //             optionRole = '';
        //             $.each(response.department, function(index, value) {
        //                 optionRole += '<option value="' + value.roleid + '">' + value.name + '</option>';
        //             });
        //             $('select#role').html(optionRole);
        //             $('select#role').selectpicker('refresh');
        //         }
        //     });
        // });

        function handlingCriteria(form) {
            $('.add').attr('disabled', 'disabled');
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

            var url = form.action;
            $.ajax({
                    url: site.base_url + 'admin/kpi/handling_criteria/<?= $id ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
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
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>