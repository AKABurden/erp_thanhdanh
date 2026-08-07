<div class="modal fade" id="add_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <?php echo form_open(admin_url('gate_pass/add'), array('id' => 'add-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo $title; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hide">
                            <input type="" id="id" name="id" class="form-control" autocomplete="off" value="<?php echo $selected->id ?>">
                        </div>
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                            <tbody>
                                <!-- <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_info_general') ?></td>
                                </tr> -->
                                <tr>
                                    <!-- Mã số -->
                                    <td style="width: 17%;">
                                        <label for="code" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('gp_code'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo form_input('code', $selected->code, 'placeholder="' . lang('intpro_code') . '" id="code" class="form-control input-tip"'); ?>
                                    </td>
                                    <!-- Ngày -->
                                    <td style="width: 17%;">
                                        <label for="date" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('gp_date'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo render_date_input('date', '', $selected->date); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- Loại đối tượng -->
                                    <td style="width: 17%;">
                                        <label for="object_type" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('gp_object_type'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php echo render_select('object_type', $object_type_list, array('id', 'name'), '', ''); ?>
                                    </td>
                                    <!-- Đối tượng -->
                                    <td style="width: 17%;">
                                        <small class="req text-danger">* </small>
                                        <?php echo _l('gp_object'); ?>
                                    </td>
                                    <td class="object">
                                        <?php echo render_select('object', $object_list, array('code', 'name')) ?>
                                        <select class="selectpicker no-margin" data-width="100%" id="object" data-none-selected-text="<?php echo _l('ch_list_code'); ?>" name="object" data-live-search="true">
                                            <option value=""></option>
                                            <?php foreach ($object_list as $product) { ?>
                                                <option <?= (($product['id'] == $items->vouchers_id) ? 'selected' : '') ?> value="<?php echo $product['id']; ?>" total-id="<?= $product['total_import'] ?>" data-subtext=""><?php echo $product['name']; ?> ( <?php echo number_format($product['total_import']) ?> )</option>
                                            <?php
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- Phòng ban -->
                                    <td style="width: 17%;">
                                        <label for="department" class="control-label">
                                            <?php echo _l('department'); ?>
                                        </label>
                                    </td>
                                    <td class="department">
                                    </td>
                                    <!-- Mục đích -->
                                    <td style="width: 17%;">
                                        <small class="req text-danger">* </small>
                                        <?php echo _l('gp_purpose'); ?>
                                    </td>
                                    <td class="purpose">
                                        <?php echo render_input('purpose') ?>
                                    </td>
                                </tr>
                                <!-- <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('Thông tin đề xuất') ?></td>
                                </tr> -->
                                <tr>
                                    <!-- Giờ vào -->
                                    <td style="width: 17%;">
                                        <label for="enter_time" class="control-label">
                                            <small class="req text-danger">* </small>
                                            <?php echo _l('gp_enter_time'); ?>
                                        </label>
                                    </td>
                                    <td>
                                    </td>
                                    <!-- Giờ ra -->
                                    <td style="width: 17%;">
                                        <label for="exit_time" class="control-label">
                                            <?php echo _l('gp_exit_time'); ?>
                                        </label>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- Phương tiện -->
                                    <td style="width: 17%;">
                                        <label for="vehicle" class="control-label">
                                            <?php echo _l('gp_vehicle'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" id="vehicle" name="vehicle" class="form-control " value="">
                                    </td>

                                    <!-- Mang theo -->
                                    <td style="width: 17%;">
                                        <label for="bring" class="control-label">
                                            <?php echo _l('gp_bring'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" id="bring" name="bring" class="form-control " value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    // Chọn loại đối tượng
    $('#object_type').change(function(event) {
        // var select = $("#object_type").select().find(":selected").value;
        var select = document.getElementById("object_type").value;
        $('#object').selectpicker('refresh');

        // alert(test);
    })

    // Chọn Loại đề xuất
    $('#proposal_type').change(function(event) {
        type_name = $("#proposal_type").select().find(":selected").data("type_name");
        // alert(department);
        $('.txt-type_name').html(type_name);
    })
    $(function() {
        appValidateForm($('#add-form'), {
            code: 'required',
            date: 'required',
            staff: 'required',
            proposal_type: 'required'
        }, manage);

        function manage(form) {
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(expenseDropzone.files, function(index, value) {
                formData.append('file[]', value);
            })
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
                .done(function(response) {
                    if (response.success == true) {
                        alert_float('success', response.message);
                    } else {
                        alert_float('danger', response.message);
                    }
                    oTable.draw();
                    $('#add_modal').modal('hide');
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    });
    $(document).on('hide.bs.modal', '#add_modal', function() {
        tinyMCE.remove();
    });
</script>