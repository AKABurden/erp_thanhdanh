<?php echo form_open('admin/orders/convert_delivery/'.$id, array('id' => 'add-order')); ?>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<div class="modal-dialog modal-lg" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('convert_delivery'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y H:i:s')),
                            'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_reference_orders', 'reference_no') ?>
                        <div class="bold">
                            <?= $order['reference_no'] ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('customers', 'customers') ?>
                        <div class="bold">
                            <?= $order['customer_name'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_person_contact', 'person_contact') ?>
                        <input type="text" name="person_contact" data-placeholder="<?= lang('tnh_person_contact') ?>"
                               id="person_contact" class="person_contact modal-select2" style="width: 100%;"
                               value="<?= !empty($order['person_contact_id']) ? 'customers__'.$order['person_contact_id'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('tnh_address_delivery', 'address_delivery') ?>
                        <div class="input-group">
                            <input type="tel" name="address_delivery" id="address_delivery"
                                   data-placeholder="<?= lang('tnh_address_delivery') ?>" class="modal-select2"
                                   value="<?= !empty($order['address_delivery_id']) ? $order['address_delivery_id'] : '' ?>"
                                   style="width: 100%;" value="">
                            <span class="input-group-addon">
                                <a href="<?= base_url('admin/clients/addShipping/'.$customer_id) ?>" class="tnh-modal2"
                                   data-tnh="modal" data-toggle="modal" data-target="#myModal2"><i
                                            class="fa fa-plus"></i></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('staff_admin', 'staff_admin') ?>
                        <select name="staff_admin" id="staff_admin" data-placeholder="<?= lang('staff_admin') ?>"
                                style="width: 100%;" class="modal-select2" required="required">
                            <option value=""></option>
                            <?php foreach ($staff as $key => $value): ?>
                                <option <?= get_option('default_staff_orders') == $value['staffid'] ? 'selected' : '' ?>
                                        value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?> <?= $value['lastname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <?php $value = !empty($order['id_branch']) ? $order['id_branch'] : ''?>
                    <?php echo render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], 'id_branch', $value)?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $order['note']),
                            'placeholder="'.lang('note').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
            </div>
            <div class="row mbot10">
                <div class="col-md-8">
                    <?= lang('tnh_items', 'tnh_items') ?>
                    <select name="" id="items" style="width: 100%;" data-placeholder="<?= lang('chosen') ?>">
                        <option value=""></option>
                        <?php foreach ($items as $key => $value): ?>
                            <option value="<?= $value['id'] ?>"><?= $value['item_code'] ?>(<?= $value['item_name'] ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" style="margin-top: 25px;"
                            class="btn btn-success ev-all"><?= lang('tnh_check_all') ?></button>
                    <button type="button" style="margin-top: 25px;" onclick="refershTable()"
                            class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 2 -->
                        <input type="radio" name="tabset" id="tab2" aria-controls="notifications">
                        <label for="tab2"><?= lang('tnh_notifications') ?></label>

                        <div class="tab-panels">
                            <section id="marzen" class="tab-panel">
                                <div class="table-responsive1">
                                    <table id="tb-convert-delivery"
                                           class="dt-tnh dataTable tnh-table table-bordered table-hover dont-responsive-table"
                                           style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 40px;" class="text-center"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_item_code') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_item_name') ?></th>
                                            <th style="width: 100px;"><?= lang('unit') ?></th>
                                            <th style="width: 200px;"><?= lang('tnh_warehouses') ?><span
                                                        class="text-danger">*</span></th>
                                            <!-- <th style="width: 100px;"><?= lang('tnh_location_warehouse') ?><span class="text-danger">*</span></th> -->
                                            <th style="width: 100px;"><?= lang('quantity') ?></th>
                                            <th style="width: 100px;"><?= lang('quantity_had_delivery') ?></th>
                                            <th style="width: 100px;"><?= lang('quantity_delivery') ?></th>
                                            <th style="width: 100px;"><?= lang('SL loss') ?></th>
                                            <th style="width: 100px;"><?= lang('SL mẫu') ?></th>
                                            <th style="width: 100px;"><?= lang('note') ?></th>
                                            <th style="width: 50px;"><?= lang('actions') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody class="tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                            <section id="notifications" class="tab-panel">
                                <div class="row">
                                    <?php foreach (typeNotificationForm() as $key => $value): ?>
                                        <?php
                                        $notifcations = $this->site_model->getNotificationFormByType($type = $key);
                                        ?>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?= $value ?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <?php
                                                    $selected = '';
                                                    $arrNoti = !empty($order[$key]) ? explode(',', $order[$key]) : [];
                                                    ?>
                                                    <div class="form-group">
                                                        <select name="<?= $key ?>[]" data-placeholder="<?= $value ?>"
                                                                id="<?= $key ?>" style="width: 100%;"
                                                                class="modal-select2" multiple>
                                                            <option value=""></option>
                                                            <?php foreach ($notifcations as $k => $val): ?>
                                                                <option <?= in_array($val['id'],
                                                                    $arrNoti) ? 'selected' : '' ?>
                                                                        value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $('#<?= $key ?>').select2();
                                            });
                                        </script>
                                    <?php endforeach ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <input type="hidden" name="order_id_save" id="order_id_save" class="form-control order_id_save"
                       value="<?= $id ?>">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add" onclick="clickAdd(this)"
                        data-type="1"><?= _l('save') ?></button>
                <button type="submit" class="btn btn-warning add" onclick="clickAdd(this)"
                        data-type="2"><?= _l('Lưu và In') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
        counter = 0;
        type = 1;
        var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
        var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
        var customer_id = 'customers__<?= $order['customer_id'] ?>';
        var count_errors = 0;
        var count_errors_new = 0;
        var order_id = '<?= $id ?>';
        var dataWarehouses = <?= !empty($warehouses) ? json_encode($warehouses) : '{}' ?>;
        var langDelivery = <?= json_encode(['tnh_qty_warehoused' => lang('tnh_qty_warehoused')]) ?>;
        var arrNew = [];
        var arr_info_new = [];
    </script>
    <script type="text/javascript" src="<?= js('convert_delivery.js?vs=3.3') ?>"></script>
    <script>
        $(function () {
            $('#items').select2();
            init_datepicker();
            setTimeout(function () {
                init_selectpicker();
            }, 500);
            formatNumberPlugin();
            formatMoneyPlugin();
            $('#staff_admin').select2({allowClear: true});
            // $('.tax_item').select2();
            // $('.tax_id').select2();

            ajaxSelectParamsCallback('#address_delivery', 'admin/clients/searchAddressDelivery', $('#address_delivery').val(), {'customer_id': '<?= $customer_id ?>'}, true);
            ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', $('#person_contact').val(), {customer_id: customer_id}, true);

            appValidateForm($('#add-order'), {
                'date': 'required',
                'address_delivery': 'required',
                // 'person_contact': 'required',
                'staff_admin': 'required',
                'id_branch': 'required'
            }, convert);

            function convert(form) {
                if (count_errors > 0) {
                    alert_float('danger', lang_orders['tnh_check_quantity_delivery']);
                    return;
                }

                if (count_errors_new > 0) {
                    alert_float('danger', 'Vui lòng kiểm tra lại số lượng chi tiết');
                    return;
                }
                $('.add').attr('disabled', 'disabled');
                var url = form.action;
                // var data = $(form).serialize();
                var form = $(form),
                    formData = new FormData(),
                    formParams = form.serializeArray();

                $.each(form.find('input[type="file"]'), function (i, tag) {
                    $.each($(tag)[0].files, function (i, file) {
                        formData.append(tag.name, file);
                    });
                });

                $.each(formParams, function (i, val) {
                    formData.append(val.name, val.value);
                });

                formData.append('type', type);
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                    .done(function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTable != 'undefined' && oTable != '') {
                                oTable.draw();
                            }
                            $('.modal-dialog .close').trigger('click');
                            if (data.type == 2) {
                                var url = site.base_url + 'admin/releases/print_delivery/' + data.delivery_id;
                                window.open(url, '_blank');
                            }
                            // window.location.href = site.base_url+'admin/releases/export_warehouse_sales/'+data.delivery_id;
                        } else {
                            alert_float('danger', data.message);
                            $('.add').removeAttr('disabled', 'disabled');
                        }
                    })
                    .fail(function () {
                        alert_float('danger', 'error');
                        $('.add').removeAttr('disabled', 'disabled');
                    });
                return false;
            }

            $('.ev-all').trigger('click');
        })

        function clickAdd(_this) {
            type_new = $(_this).attr('data-type');
            type = type_new;
        }
    </script>