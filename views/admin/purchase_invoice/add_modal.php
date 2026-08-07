<?php echo form_open('admin/purchase_invoice/add_details/' . $id, array('id' => 'tax-bill')); ?>
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
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y')), 'placeholder="' . lang('date') . '" id="date" required class="form-control input-tip datepicker"'); ?>
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
                        <?= lang('c_tasks_supplier', 'suppliers') ?>
                        <input type="text" name="suppliers" id="suppliers" style="width: 100%;" class="modal-select2" data-placeholder="<?= lang('c_tasks_supplier') ?>" value="" required="required" pattern="" title="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('c_tasks_import', 'imports') ?>
                        <select name="imports[]" id="imports" multiple data-actions-box="1" data-none-selected-text="<?= lang('c_tasks_import') ?>" class="form-control imports selectpicker" required="required">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_items', 'items') ?>
                        <select name="items[]" id="items" multiple data-actions-box="1" data-none-selected-text="<?= lang('tnh_items') ?>" class="form-control items selectpicker" required="required">
                            <option value=""></option>
                        </select>
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

    function loadImports() {
        suppliers = $('#suppliers').val();
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['suppliers'] = suppliers;
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/purchase_invoice/loadImports/' + suppliers,
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                var options = '';
                $('select[name="imports[]"]').html('');
                $.each(response.arrImports, function (index, group) {
                    options += `<optgroup label="${group.date}">`;
                    $.each(group.imports, function (index2, _import) {
                        options+= `<option data-subtext="Tổng tiền: ${_import.total}" value="${_import.id}">${_import.reference_no}</option>`;
                    });
                    // options+= `<option value="">Hi</option>`;

                    options += `</optgroup>`;
                });
                // $.each(response.imports, function (index, value) { 
                //     options+= `<option value="${value.id}">${value.reference_no}</option>`;
                // });

                $('select[name="imports[]"]').append(options);
                $('select[name="imports[]"]').selectpicker('refresh');
            }
        });
        $('select[name="items[]"]').html('');
        $('select[name="items[]"]').selectpicker('refresh');
    }
    function loadItems() {
        arr_import_id = $('#imports').val();
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['arr_import_id'] = arr_import_id;
        $.ajax({
            type: "POST",
            url: site.base_url+'admin/purchase_invoice/loadItems/',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                var options = '';
                $('select[name="items[]"]').html('');
                // alert(response.items);
                $.each(response.items, function (index, value) {
                    options += `<optgroup label="${value.import_code}">`;
                    $.each(value.items, function (index2, value2) {
                        // alert(value2.import_code);die;
                        options+= `<option selected data-subtext="${value2.code_item}" value="${value2.import_item_id}">${value2.name_item}</option>`;
                    });
                    options += `</optgroup>`;
                });

                $('select[name="items[]"]').append(options);
                $('select[name="items[]"]').selectpicker('refresh');
            }
        });
    }

    $(document).ready(function() {
        init_datepicker();
        init_selectpicker();
        $('#tax_id').select2();
        ajaxSelectParams('#suppliers', 'admin/purchase_invoice/loadSuppliers', 0, true, false);

        $('#suppliers').change(function(event) {
            loadImports();
        });
        $('#imports').change(function(event) {
            loadItems();
        });

        appValidateForm($('#tax-bill'), {
            'date': 'required',
            'reference_bill': 'required',
            'suppliers': 'required',
            'imports': 'required',
            'items': 'required',
            'tax_id': 'required',
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