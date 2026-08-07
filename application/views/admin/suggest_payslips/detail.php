<div class="modal-dialog modal-lg" style="min-width: 80%;">
    <?php echo form_open(
        admin_url('suggest_payslips/detail/' . $id),
        ['id' => 'suggest_payslips', 'enctype' => 'multipart/form-data']
    ); ?>
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
                                <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('date', 'date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date',
                                set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Người lập phiếu', 'staff_id') ?></td>
                        <td colspan="1">
                            <select name="staff_id" id="staff_id" data-placeholder="<?= lang('Người lập phiếu') ?>" style="width: 100%;" class="">
                                <option value=""></option>
                                <?php foreach ($employees as $key => $value) : ?>
                                    <option <?= !empty($dtData) ? ($dtData['staff_id'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td><?= lang('supplier', 'suppliers_id') ?></td>
                        <td colspan="1">
                            <input type="text" name="suppliers_id" data-placeholder="<?= lang('supplier') ?>" id="suppliers_id" class="suppliers_id" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['suppliers_id'] : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Chi nhánh', 'branch_id') ?></td>
                        <td colspan="1">
                            <?php
                            $branchs = getListBranch();
                            ?>
                            <select name="branch_id" id="branch_id" class="branch_id" data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                                <option value=""></option>
                                <?php if (!empty($branchs)) { ?>
                                    <?php foreach ($branchs as $key => $value) { ?>
                                        <option <?= !empty($dtData) ? ($dtData['branch_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Đính kèm tập tin</td>
                        <td>
                            <input type="file" name="files[]" class="form-control" placeholder='<?= lang('choose') ?>' multiple />
                        </td>
                        <td colspan="2">
                            <?php if (!empty($dtData['list_files'])) {
                                $list_files = json_decode($dtData['list_files']);
                                if (!empty($list_files)) {
                                    foreach ($list_files as $key => $value) { ?>
                                        <?php $type_file = mime_content_type(FCPATH . $value); ?>

                                        <div class="col-md-4 mtop5 item_file">
                                            <div class="item-file">
                                                <?php
                                                if (explode('/', $type_file)[0] == 'image') { ?>
                                                    <a class="pull-left" target="_blank" href="<?= base_url($value) ?>">
                                                        <img src="<?= base_url($value) ?>" class="img-icon-file">
                                                    </a>
                                                    <a class="text-danger pull-right removeItemFile" _href="<?= $value ?>" data-id="<?= $dtData['id'] ?>">
                                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                                    </a>
                                                <?php } else if (explode('/', $type_file)[0] == 'video') { ?>
                                                    <a target="_blank" href="<?= base_url($value) ?>">
                                                        <i class="fa fa-file-video-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="text-danger pull-right removeItemFile" _href="<?= $value ?>" data-id="<?= $dtData['id'] ?>">
                                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <a target="_blank" href="<?= base_url($value) ?>">
                                                        <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="text-danger pull-right removeItemFile" _href="<?= $value ?>" data-id="<?= $dtData['id'] ?>">
                                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                                    </a>
                                                <?php }
                                                ?>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                            <?php }
                                }
                            } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row mtop10">
                <div class="col-md-12">
                    <div style="margin-bottom: 20px">
                        <label for="items_search"><?= lang('Danh mục chi') ?></label>
                        <input type="text" name="category_search" id="category_search" class="category_search" style="width: 100%;" data-placeholder="<?= lang('Danh mục chi') ?>" value="">
                    </div>
                </div>
                <div class="col-md-12">
                    <table id="tb-maintenance-machines" class="table dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px"><?= lang('STT') ?></th>
                                <th class="text-center" style="width: 250px"><?= lang('Nội Dung-Diễn Giải') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('tnh_dvt') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Số Lượng') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Đơn Giá') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Thành Tiền') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Thuế') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Tiền Thuế') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Tổng Thành Tiền') ?></th>
                                <th style="width: 50px;" style="width: 100px"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <?php
                                    $optionUnits = '<option></option>';
                                    if (!empty($dtunits)) {
                                        foreach ($dtunits as $kk => $vv) {
                                            $optionUnits .= '<option ' . ($vv['unitid'] == $value['unit_id'] ? 'selected' : '') . ' value="' . $vv['unitid'] . '">' . $vv['unit'] . '</option>';
                                        }
                                    }
                                    $optiontax = '<option></option>';
                                    if (!empty($dttaxes)) {
                                        foreach ($dttaxes as $kk => $vv) {
                                            $optiontax .= '<option data-taxrate="' . $vv['taxrate'] . '" ' . ($vv['id'] == $value['tax_id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><?= (++$key) ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <input type="hidden" class="counter" name="counter[]" value="<?= $counter ?>">
                                                <input type="hidden" class="suggest_payslips_items_id" name="suggest_payslips_items_id[<?= $counter ?>]" value="<?= $value['id'] ?>">
                                                <input type="hidden" class="category_payslip form-control" name="category_payslip[<?= $counter ?>]" value="<?= $value['category_payslip'].'__'.$value['cost_id'] ?>">
                                                <input type="hidden" class="note_item form-control" name="note_item[<?= $counter ?>]" value="<?= $value['note_item'] ?>">
                                                <div><?= !empty($value['category_payslip']) ? $value['name_category_payslip'] : $value['note_item'] ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="unit_id" id="unit_id_<?= $counter ?>" name="unit_id[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('tnh_dvt') ?>">
                                                    <?= $optionUnits ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-quantity"><input type="text" name="quantity[<?= $counter ?>]" class="quantity form-control number-format" value="<?= formatNumber($value['quantity']) ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-price"><input type="text" name="price[<?= $counter ?>]" class="price form-control number-format" value="<?= formatMoney($value['price']) ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-amount text-right"><?= formatMoney($value['amount']) ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="tax_id" id="tax_id_<?= $counter ?>" name="tax_id[<?= $counter ?>]" style="width: 100%;" data-placeholder="<?= lang('Thuế') ?>">
                                                    <?= $optiontax ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="td-amounttax text-right"></div>
                                        </td>
                                        <td>
                                            <div class="td-total text-right"></div>
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                                <?php $counter++;
                                } ?>
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
    $(document).ready(function (){
        ajaxSelectCallBack($('#category_search'), "<?= base_url('admin/suggest_payslips/searchCategoryPayslip') ?>", 0);
    })
    init_datepicker();
    init_selectpicker('refresh');
    $("#branch_id").select2();
    $("#staff_id").select2();
    var dtunits = <?= !empty($dtunits) ? json_encode($dtunits) : '{}' ?>;
    var dttaxes = <?= !empty($dttaxes) ? json_encode($dttaxes) : '{}' ?>;
    edit = <?= !empty($dtData) ? 1 : 0 ?>;
    counter = <?= !empty($counter) ? $counter : 0 ?>;



    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            $(`#unit_id_${i}`).select2();
            $(`#tax_id_${i}`).select2();
        }
        getTotal();
    }

    function optionUnits(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtunits, function(index, el) {
            selected = selected_id == el.unitid ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.unitid + '">' + el.unit + '</option>';
        });
        return option;
    }

    function optionTaxs(selected_id = 0) {
        option = `<option></option>`;
        $.each(dttaxes, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option data-taxrate="' + el.taxrate + '" ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }
    // function optionResult(selected_id = 0) {
    //     option = `<option></option>`;
    //     $.each(dtResult, function(index, el) {
    //         selected = selected_id == el.id ? 'selected' : '';
    //         option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
    //     });
    //     return option;
    // }

    function loadItem(item = {}) {
        tdStt = `<div class="text-center"></div>`;
        tdName = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="hidden" class="category_payslip form-control" name="category_payslip[${counter}]" value="${item.id}">
            <div>${item.text}</div>
        </div>`;
        tdUnit_id = `<div>
             <select class="unit_id" id="unit_id_${counter}" name="unit_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('tnh_dvt') ?>">
                ${optionUnits()}
            </select>
        </div>`;
        tdQuantity = `<div>
            <input type="text" name="quantity[${counter}]" class="quantity form-control number-format" value="">
        </div>`;
        tdprice = `<div>
            <input type="text" name="price[${counter}]" class="price form-control number-format" value="">
        </div>`;
        tdamount = `<div class="td-amount text-right"></div>`;
        tdTaxid = `<div>
             <select class="tax_id" id="tax_id_${counter}" name="tax_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Thuế') ?>">
                ${optionTaxs()}
            </select>
        </div>`;
        tdamountTax = `<div class="td-amounttax text-right"></div>`;
        tdtotal = `<div class="td-total text-right"></div>`;

        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;
        trItem = `<tr>
            <td class="text-center stt">${tdStt}</td>
            <td>${tdName}</td>
            <td>${tdUnit_id}</td>
            <td>${tdQuantity}</td>
            <td>${tdprice}</td>
            <td>${tdamount}</td>
            <td>${tdTaxid}</td>
            <td>${tdamountTax}</td>
            <td>${tdtotal}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-maintenance-machines").find('tbody').append(trItem);
        $(`#unit_id_${counter}`).select2();
        $(`#tax_id_${counter}`).select2();
        // $(`#unit_id_${counter}`).attr('required', 'required');
        counter++;
        getTotal();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }
    $(document).on('change', '.quantity, .price, .tax_id', function(event) {
        getTotal();
    });

    $("#category_search").change(function() {
        dtItems = $(this).select2('data');
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function getTotal() {
        tb = '#tb-maintenance-machines tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);

            quantity = intVal($(element).find('.quantity').val());
            price = intVal($(element).find('.price').val());
            tax = intVal($(element).find('.tax_id').find('option:selected').attr('data-taxrate'));
            amount = quantity * price;
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
            amounttax = amount * (tax / 100);
            $(element).find('.td-amounttax').html(tnhFormatMoney(amounttax));
            total = amount + amounttax;
            $(element).find('.td-total').html(tnhFormatMoney(total));
        }
    }

    appValidateForm($('#suggest_payslips'), {
        date: 'required',
        reference_no: 'required',
        branch_id: 'required',
        suppliers_id: 'required'
    }, detail);

    function detail(form) {
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
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            }).fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
    // function detail(form) {
    //     $('.add').attr('disabled', 'disabled');
    //     var data = $(form).serialize();
    //     var url = form.action;
    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         dataType: 'JSON',
    //         data: data,
    //     }).done(function(data) {
    //         if (data.result) {
    //             alert_float('success', data.message);
    //             if (typeof oTable != 'undefined') {
    //                 oTable.draw();
    //             }
    //             $('.modal-dialog .close').trigger('click');
    //         } else {
    //             alert_float('danger', data.message);
    //             $('.add').removeAttr('disabled', 'disabled');
    //         }
    //     }).fail(function() {
    //         alert_float('danger', lang_core['errors']);
    //         $('.add').removeAttr('disabled', 'disabled');
    //     });
    //     return false;
    // }

    function ajaxSelectCallBack(element, url, id, types = '') {
        if (id > 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: url + '/' + id + '/' + types,
                        dataType: "json",
                        success: function(data) {
                            callback(data.results[0].children[0]);
                        }
                    });
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            type: $('#type_items').val(),
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    code_client: '',
                                    id: '',
                                    text: 'No Match Found'
                                }]
                            };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) {
                    return m;
                }
            });
        }
    }
    var base_url = '<?= base_url() ?>';

    function repoFormatSelection(state) {
        if (!state.id) return state.text;

        return state.text;
    }
    ajaxSelectCallBack($('#suppliers_id'), "<?= admin_url('suppliers/SearchSupplier') ?>", $('#suppliers_id').val());
</script>