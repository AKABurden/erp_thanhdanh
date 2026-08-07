<?php echo form_open('admin/coupon_invoice/add/' . $id, array('id' => 'tax-bill')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_tax_bill') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y h:i:s')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày nhập misa', 'date_misa') ?>
                        <?php echo form_input('date_misa', (isset($_POST['date_misa']) ? $_POST['date_misa'] : date('d/m/Y')), 'placeholder="' . lang('Ngày nhập misa') . '" id="date_misa"  class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_reference_bill', 'reference_bill') ?>
                        <?php echo form_input('reference_bill', (isset($_POST['reference_bill']) ? $_POST['reference_bill'] : ''), 'placeholder="' . lang('tnh_reference_bill') . '" id="reference_bill" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_customer', 'customer_bill') ?>
                        <input type="text" name="customer_bill" id="customer_bill" style="width: 100%;" class="modal-select2" data-placeholder="<?= lang('tnh_customer') ?>" value="" required="required" pattern="" title="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('h_branch', 'branch_id') ?>
                        <select name="branch_id" id="branch_id" data-placeholder="<?= lang('h_branch') ?>" style="width: 100%;" class="">
                            <option value=""></option>
                            <?php foreach ($branch as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('deliveries', 'orders_bill') ?>
                        <select name="orders_bill[]" id="orders_bill" multiple data-actions-box="1" data-none-selected-text="<?= lang('deliveries') ?>" class="form-control orders_bill selectpicker" required="required">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tổng tiền trước VAT', 'total_before_vat') ?>
                        <div class="text-left div-total_before_vat">0</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Chi phí công thêm', 'additional_costs') ?>
                        <div class="text-left div-additional_costs">0</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_taxs', 'tax_id') ?>
                        <select name="tax_id" id="tax_id" class="tax_id" data-placeholder="<?= lang('tax') ?>" style="width: 100%;">
                            <option value="0"><?= lang('0%') ?></option>
                            <?php foreach ($taxs as $key => $value) : ?>
                                <option data-rate="<?= $value['taxrate'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tổng tiền sau VAT', 'total_after_vat') ?>
                        <div class="text-left div-total_after_vat">0</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <textarea name="note" id="note" placeholder="<?= lang('note') ?>" class="form-control note" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?= lang('tnh_when_you_create_a_tax_invoice_the_total_amount_will_be_changed_according_to_the_tax_you_select') ?></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            <button type="submit" class="btn btn-primary add-bill"><?= lang('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var additional_costs = 0;
    var total_before_vat = 0;

    function loadOrdersBill() {
        customer_bill = $('#customer_bill').val();

        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['customer_bill'] = customer_bill;
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/coupon_invoice/loadOrdersBill',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                var optionsOrdersBill = '';
                $('select[name="orders_bill[]"]').html('');
                $.each(response.orders, function (index, value) { 
                    optionsOrdersBill+= `<option data-additional_costs="${value?.additional_costs}" data-grand_total_before="${value?.grand_total_before}" value="${value.id}">${value.reference_no}</option>`;
                });

                $('select[name="orders_bill[]"]').append(optionsOrdersBill);
                $('select[name="orders_bill[]"]').selectpicker('refresh');
            }
        });
    }

    function totalAfterVAT() {
        additional_costs = 0;
        total_before_vat = 0;
        var orders_bill_id = $('select[name="orders_bill[]"]').val();
        if (orders_bill_id.length > 0) {
            $.each(orders_bill_id, function (index, value) { 
                var dataOption = $('#orders_bill option[value="'+value+'"]');
                var _additional_costs = intVal(dataOption.attr('data-additional_costs'));
                var _grand_total_before = intVal(dataOption.attr('data-grand_total_before'));

                additional_costs+= _additional_costs;
                total_before_vat+= _grand_total_before;
            });
        }

        $('.div-total_before_vat').html(tnhFormatMoney(total_before_vat));
        $('.div-additional_costs').html(tnhFormatMoney(additional_costs));

        var tax_rate = intVal($('select.tax_id').select2().find(":selected").data('rate'));
        var total_after_vat = 0;
        if (total_before_vat > 0) {
            total_after_vat = total_before_vat + (total_before_vat * tax_rate/100) + additional_costs;
        }
        $('.div-total_after_vat').html(tnhFormatMoney(total_after_vat));
    }

    $(document).ready(function() {
        init_datepicker();
        init_selectpicker();
        $('#tax_id').select2();
        $('#branch_id').select2();
        ajaxSelectParams('#customer_bill', 'admin/clients/searchCustomers', 0, true, false);

        $('#customer_bill').change(function(event) {
            loadOrdersBill();
            totalAfterVAT();
        });

        $('select[name="orders_bill[]"]').change(function(event) {
            totalAfterVAT();
        });

        $('select[name="tax_id"]').change(function(event) {
            totalAfterVAT();
        });

        appValidateForm($('#tax-bill'), {
            'date': 'required',
            'reference_bill': 'required',
            'customer_bill': 'required',
            'orders_bill': 'required',
            'tax_id': 'required',
            'branch_id': 'required'
        }, db);

        function db(form) {
            $('.add-bill').attr('disabled', 'disabled');
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

            bootbox.confirm({
                message: '<?= lang('tnh_you_want_save') ?>',
                buttons: {
                    confirm: {
                        label: lang_core['yes'],
                        className: 'btn-success'
                    },
                    cancel: {
                        label: lang_core['no'],
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
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
                                    if (typeof oTable != 'undefined' && oTable != '') {
                                        oTable.draw();
                                    }
                                    $('.modal-dialog .close').trigger('click');
                                } else {
                                    alert_float('danger', data.message);
                                    $('.add-bill').removeAttr('disabled', 'disabled');
                                }
                            })
                            .fail(function() {
                                alert_float('danger', 'error');
                                $('.add-bill').removeAttr('disabled', 'disabled');
                            });
                    } else {
                        $('.add-bill').removeAttr('disabled', 'disabled');
                    }
                }
            });
            return false;
        }
    });
</script>