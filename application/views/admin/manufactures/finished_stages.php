<?php echo form_open('admin/manufactures_temp/finished_stages/' . $po_id, array('id' => 'handlingFinishedStages')); ?>
<a class="tnh-modal hide activeStick" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?=admin_url('manufactures_temp/finished_stages/' . $po_id)?>"></a>
<div class="modal-dialog modal-lg modal-semi" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?= lang('tnh_reference_productions_orders', 'reference_productions_orders') ?>
                    <?= $productions_orders['reference_no'] ?>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('tnh_warehouses_products', 'warehouses_products') ?>
                        <select name="warehouses_products" data-placeholder="<?= lang('tnh_warehouses_products') ?>" id="warehouses_products" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($warehouses)) : ?>
                                <?php foreach ($warehouses as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <?php
                            $stages = $this->manufactures_model->getStagesPO($po_id);
                            ?>
                            <div class="task-single-col-right" style="max-height: 450px; min-height: 450px;">
                                <h4 class="task-info-heading"><i class="fa fa-info-circle" aria-hidden="true"></i> <?= lang('tnh_cong_doan') ?></h4>
                                <hr class="task-info-separator">
                                <div class="task-info p-0" style="overflow: auto; max-height: 370px;">
                                    <?php if ($stages) : ?>
                                        <?php foreach ($stages as $key => $value) : ?>
                                            <?php
                                            if ($value['id'] == STAGES_MATERIAL) {
                                                continue;
                                            }
                                            ?>
                                            <div class="info-stages" <?= $value['id'] != STAGE_PRINT_BARCODE ? 'onclick="clickActiveStages(this, \'' . $value['id'] . '\')"' : '' ?>><?= $value['name'] ?></div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="div-items-products"></div>
                            <div class="div-items-delivery_records mtop20"></div>
                            <!-- <table id="tb-handling-products-stages" class="table dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 150px;"><?= lang('tnh_product_code') ?></th>
                                        <th class="text-center" style="width: 150px;"><?= lang('tnh_product_name') ?></th>
                                        <th class="text-center" style="width: 80px;" class="text-center"><?= lang('tnh_unit_manufactures') ?></th>
                                        <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_to_enter') ?></th>
                                        <th class="text-center" style="width: 100px;" class="text-center"><?= lang('tnh_quantity_entered') ?></th>
                                        <th class="text-center" style="width: 50px;" class="text-center"><span class="fa fa-trash-o"></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="m_productions_orders_id" id="m_productions_orders_id" class="form-control" value="<?= $po_id ?>">
            <input type="hidden" name="save" id="save" class="form-control" value="1">

            <button type="button" class="btn btn-default pull-right mleft5" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" onclick="checkData()" class="btn btn-primary add-finished-stages pull-right mleft10"><?= _l('save') ?></button>
            <div class="checkbox checkbox-danger pull-right hide">
                <input type="checkbox" class="save_create_production_report" id="save_create_production_report" value="1">
                <label for="save_create_production_report">Tạo phiếu báo cáo</label>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    function checkData() {
        // var hand_orver_false = $('.radio_check_hand_over[value="2"]:checked');
        // if(hand_orver_false.length > 0) {
        //     return false;
        // }
        // else {
        //     return true;
        // }
    }

    function clickActiveStages(_this, c_stage_id) {
        task_info = $(_this).closest('.task-info');
        dataPost = {};
        m_productions_orders_id = $('#m_productions_orders_id').val();
        dataPost[csrfData['token_name']] = csrfData['hash'];
        dataPost['stage_id'] = c_stage_id;
        dataPost['m_productions_orders_id'] = m_productions_orders_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/activeStages',
            data: dataPost,
            dataType: "html",
            success: function(response) {
                $('.div-items-products').html(response);
                task_info.find('.info-stages').removeClass('active');
                $(_this).addClass('active');
                $('.div-items-delivery_records').html('');
                if($('.div-items-products').find('.not-data').length == 0) {
                    $.post(admin_url + 'hand_over/get_table_delivery_records', dataPost, function (result) {
                        $('.div-items-delivery_records').html(result);
                    })
                }
            }
        });


    }

    $(function() {
        $('#warehouses_products').select2();
        appValidateForm($('#handlingFinishedStages'), {
            warehouses_products: 'required'
        }, handlingFinishedStages);

        function handlingFinishedStages(form) {
            $('.add-finished-stages').attr('disabled', 'disabled');
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
                    if(data.success) {
                        var eventClick = $('.info-stages.active').attr('onclick');

                        if(data.id_delivery_records && $('#save_create_production_report').prop('checked')) {
                            window.open(data.href, '_blank');
                        }
                        form.find('.activeStick')[0].click();
                        setTimeout(function() {
                            eventClick;
                            $(`.info-stages[onclick="${eventClick}"]`)[0].click();
                            $(`.info-stages[onclick="${eventClick}"]`).addClass('active')
                        }, 3000)
                        alert_float(data.alert_type, data.message);
                        return false;
                    }

                    if (data.result) {
                        // if(data.id_delivery_records && $('#save_create_production_report').prop('checked')) {
                        //     window.open(admin_url + 'production_report/detail?id_delivery_records=' + data.id_delivery_records, '_blank');
                        // }
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }
                        $('#clickFini').attr('href', site.base_url+'admin/manufactures_temp/finished_stages/<?= $po_id ?>');
                        $('.modal-dialog .close').trigger('click');
                        timeout = setTimeout(function() {
                            $('#clickFini')[0].click();
                        }, 1500);
                    } else {
                        alert_float('danger', data.message);
                        $('.add-finished-stages').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add-finished-stages').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>