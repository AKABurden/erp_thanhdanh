<?php echo form_open('admin/salary/loadModalOvertime/', array('id' => 'created_overtime')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="filed" class="control-label bold"><?= lang('Tiêu chí') ?></label>
                    <div class="form-group">
                        <select name="filed[]" id="filed"  class="selectpicker form-control" multiple
                                title='<?php echo _l('tiêu chí'); ?>'
                                data-live-search="true"
                            <option value=""></option>
                            <?php
                            $arrSelect = [];
                            if (!empty($checkBusinessFeeDetail)){
                                if (!empty($checkBusinessFeeDetail['holiday'])){
                                    array_push($arrSelect,1);
                                }
                                if (!empty($checkBusinessFeeDetail['go_night'])){
                                    array_push($arrSelect,2);
                                }
                                if (!empty($checkBusinessFeeDetail['back_night'])){
                                    array_push($arrSelect,3);
                                }
                                if (!empty($checkBusinessFeeDetail['construction_allowance'])){
                                    array_push($arrSelect,4);
                                }
                                if (!empty($checkBusinessFeeDetail['construction_allowance_province'])){
                                    array_push($arrSelect,5);
                                }
                                if (!empty($checkBusinessFeeDetail['allowance_survey'])){
                                    array_push($arrSelect,6);
                                }
                            }
                            ?>
                            <?php if(!empty($listOvertime)){?>
                                <?php foreach($listOvertime as $key => $value){?>
                                    <option
                                        <?= !empty($arrSelect) ? in_array($value['id'],$arrSelect) ? 'selected' : '' : '' ?>
                                        value="<?= $value['id'] ?>"><?= $value['name'] ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 wrap-type hide">
                    <label for="month" class="control-label bold"><?= lang('Khách hàng - Địa điểm') ?></label>
                    <?php
                    $customer = get_table_where('tblclients',['userid' => !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['customer_id'] : 0],'','row_array');
                    ?>
                    <div class="flex-center">
                        <div class="radio radio-primary mright10">
                            <input type="radio" onchange="changeType(this)" name="type" id="type-1" value="1" <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 1 ? 'checked' : '' : 'checked' ?>>
                            <label for="type-1">Khách hàng</label>
                        </div>
                        <div class="radio radio-primary" style="margin-top: 10px;">
                            <input type="radio" onchange="changeType(this)" name="type" id="type-2" value="2" <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 2 ? 'checked' : '' : '' ?>>
                            <label for="type-2">Địa điểm công ty</label>
                        </div>
                    </div>
                    <div class="wapper-customer <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 1 ? '' : 'hide' : '' ?>">
                        <select <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 1 ? '' : '' : '' ?>  name="rel_id" id="rel_id" class="ajax-sesarch rel_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php
                            if (!empty($customer)) {
                                echo '<option value="' . $checkBusinessFeeDetail['customer_id'] . '"  selected>' . $customer['company'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wapper-customer-text <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 2 ? '' : 'hide' : 'hide' ?>">
                        <input type="text" <?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['type'] == 2 ? '' : '' : '' ?> name="customer_text"  class="form-control customer_text " value="<?= !empty($checkBusinessFeeDetail) ? $checkBusinessFeeDetail['customer_text'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-12 hide" style="margin-top: 10px">
                    <label for="staff_id" class="control-label bold"><?= lang('Nhân viên đi cùng') ?></label>
                    <div class="form-group">
                        <?php
                        $htmlStaff = '';
                        $arrSelect = [];
                        if (!empty($checkBusinessFeeDetail)){
                            $checkBusinessFeeDetailStaff = get_table_where('tbl_business_fee_boiler_overtime_detail_staff',['business_fee_boiler_overtime_detail_id' => $checkBusinessFeeDetail['id']]);
                            if (!empty($checkBusinessFeeDetailStaff)){
                                foreach ($checkBusinessFeeDetailStaff as $kk => $vv){
                                    $arrSelect[] = $vv['staff_id'];
                                }
                            }
                        }
                        foreach ($staffNew as $kk => $val) {
                            $htmlStaff .= '<option ' . ((!empty($arrSelect) && in_array($val['id'],
                                        $arrSelect)) ? 'selected' : '') . ' data-subtext="' . $val['name_department'] . '" value="' . $val['id'] . '">' . $val['fullname'] . '</option>';
                        }
                        ?>
                        <select class="staff_id modal-select2 selectpicker form-control"
                                data-live-search="true"
                                multiple
                                title='<?php echo _l('nhân viên đi cùng'); ?>'
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                name="staff_id[]"
                                id="staff_id">
                            <?= $htmlStaff ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_note', 'note') ?>
                        <textarea name="mnote" id="note" class="form-control note"
                                  rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="timekeepingId" class="form-control" value="<?= $timekeepingId ?>">
            <input type="hidden" name="staffId" class="form-control" value="<?= $personnel_id ?>">
            <input type="hidden" name="timekeeping_detail_id" class="form-control" value="<?= $idTimekeepingDetail ?>">
            <input type="hidden" name="typeTimeKeeping" class="form-control" value="<?= $typeTimeKeeping ?>">

            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default closes" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {
        init_selectpicker();
        $('#reason_id').select2({
            allowClear: true
        });
        $(".closes").click(function() {
            return;
            type = "<?= $typeTimeKeeping ?>";
            type_now = "<?= $type_now ?>";
            if (type_now == 'X') {
                $('.<?= $timekeepingId ?>__<?= $personnel_id ?>__<?= $day ?>__<?= $idTimekeepingDetail ?>')
                    .find('option[value="' + type + '"]').prop('selected', false);
            }
        });

        function task_rel_select() {
            var serverData = {};
            serverData.rel_id = $(".rel_id").val();
            serverData.type = 'customer';
            init_ajax_search('customer', $(".rel_id"), serverData);
        }

        task_rel_select();

        appValidateForm($('#created_overtime'), {
            // 'value': 'required',
        }, custom);

        function custom(form) {
            $('.add').attr('disabled', 'disabled');
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
                    if (data.result) {
                        alert_float('success', data.message);
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled', 15000);
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })

    function changeType(_this){
        value = $(_this).val();
        if (value == 1){
            $(_this).closest('div.wrap-type').find('.wapper-customer').removeClass('hide');
            $(_this).closest('div.wrap-type').find('.wapper-customer-text').addClass('hide');
            $(_this).closest('div.wrap-type').find('.rel_id').attr('required',false);
            $(_this).closest('div.wrap-type').find('.customer_text').attr('required',false);
            $(_this).closest('div.wrap-type').find('.customer_text').val('');
        } else {
            $(_this).closest('div.wrap-type').find('.wapper-customer').addClass('hide');
            $(_this).closest('div.wrap-type').find('.wapper-customer-text').removeClass('hide');
            $(_this).closest('div.wrap-type').find('.rel_id').attr('required',false);
            $(_this).closest('div.wrap-type').find('.customer_text').attr('required',false);
            $(_this).closest('div.wrap-type').find('.rel_id').val('').selectpicker('refresh');
        }
    }
</script>