<div class="modal fade" id="detail_supplier_price" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('dt_detail_price_supplier'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6  pull-left">
                        <div class="panel panel-success">

                            <div class="panel-heading">
                                <h3 class="panel-title">Thông tin</h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="supplier_price_id"  id="supplier_price_id" value="<?=$supplier_price->id?>">
                                            <div><b><?= _l('Mã nhà cung cấp') ?>: </b><?php echo $supplier_price->code; ?></div>
                                            <div class="code_data"><b><?= _l('ch_name_suppliers') ?>: </b><?php echo $supplier_price->company; ?></div>
                                            <div class="name_data"><b><?= _l('dt_set_name_supplier') ?>: </b><?php echo $supplier_price->name_price; ?></div>
                                            <div><b><?= _l('year') ?>: </b><?php echo $supplier_price->year; ?></div>
                                            <!-- <div><b><?= _l('dt_currency_type') ?>: </b><?php echo $supplier_price->name; ?></div> -->
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 div_table_view">
                        <table class="table detail_supplier">
                            <thead>
                                <th class="text-center"><a class="btn btn-info btn-icon add_item_detail" onclick="createItemsDetail(this)"><i class="fa fa-plus"></i> Thêm</a></th>
                                <th class="text-center"><?php echo _l('Hình ảnh'); ?></th>
                                <th class="text-center"><?php echo _l('Mã hàng'); ?></th>
                                <th class="text-center"><?php echo _l('Tên hàng'); ?></th>
                                <th class="text-center"><?php echo _l('Đơn vị tính'); ?></th>
                                <th class="text-center"><?php echo _l('price'); ?></th>
                                <th class="text-center"><?php echo _l('dt_product_type') ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $key => $value) { ?>
                                    <?php 
                                    // $item = get_full_item($value->product_id, $value->product_type);
                                          $getItem = get_full_item_new($value->product_id, $value->product_type);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $key + 1; ?></td>
                                        <td class="text-center"><?php
                                                                            echo "<img src='" . $getItem->avatar_1 . "' width='50px' height='50px' />";
                                                                            //                                            
                                                                            ?>
                                        </td>
                                        <td><?php echo $getItem->code; ?></td>
                                        <td><?php echo $getItem->name; ?></td>
                                        <td><?php echo $getItem->unit_name_payment; ?></td>
                                        <td class="text-right">
                                            <div class="type_v1">
                                                <?= dt_EditColumSelectInput_pricesupplier(formatNumber($value->price), $value->id, '', '<a class="pointer" id="quantitys_text_v2_' . $value->id . '" target="_blank" >' . formatNumber($value->price) . '</a>', '', admin_url('import_price/quantity/' . $value->id . '/' . $supplier_price->id), 'class="formUpdateDataTable"') ?></div>
                                            <div class="type_v2 hide" data-id="<?= $value->id ?>" class="quantitys_input"><input onkeyup="formatNumBerKeyUpCus(this)" type="text" name="quantitys" id="quantitys" class="height_auto  quantitys H_input align_right" value="<?= formatNumber($value->price) ?>"></div>
                                        </td>
                                        <td class="text-center"><?= format_item_purchases($value->product_type) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
        dtItems = $('.detail_supplier').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "ordering": false,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: '300px',
            // fixedColumns:   {
            //     leftColumns: 4,
            //     rightColumns: 0
            // },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            dom: 'Blfrtip',
	        buttons: [{
                extend: "excel",
                text: app.lang.dt_button_excel,
                footer: !0,
                exportOptions: {
                    columns: [":not(.not-export)"],
                    rows: function (t) {
                        return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                    },
                    format: {
                        body: function(data, row, column, node) {
                            data = $('<p>' + data + '</p>').text();
                            if(column == 4){
                                let trimmedText = data.trim();
                                let noWhiteSpaceText = trimmedText.replace(/\s+/g, " ");
                                let noCommaText = noWhiteSpaceText.replace(/,/g, "");
                                console.log(noCommaText)
                                return noCommaText;
                            }else{
                                return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                            }
                        }
                    }
                },
                customize: function (xlsx) {

                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var mergeCells = $('mergeCells', sheet);
                    var downrows = 0;
                    code = $('.code_data').text();
                    name = $('.name_data').text();

                    var downrows = 2;
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A1:G1'
                        }
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A2:G2'
                        }
                    }));
                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                        attr: {
                            ref: 'A3:G3'
                        }
                    }));
                    var clRow = $('row', sheet);
                    function _createNode(doc, nodeName, opts) {
                        var tempNode = doc.createElement(nodeName);

                        if (opts) {
                            if (opts.attr) {
                                $(tempNode).attr(opts.attr);
                            }

                            if (opts.children) {
                                $.each(opts.children, function (key, value) {
                                    tempNode.appendChild(value);
                                });
                            }

                            if (opts.text !== null && opts.text !== undefined) {
                                tempNode.appendChild(doc.createTextNode(opts.text));
                            }
                        }

                        return tempNode;
                    }
                    //update Row
                    clRow.each(function () {
                        var attr = $(this).attr('r');
                        var ind = parseInt(attr);
                        ind = ind + downrows;
                        $(this).attr("r", ind);
                    });
                    // Update  row > c
                    $('row c ', sheet).each(function () {
                        var attr = $(this).attr('r');
                        var pre = attr.substring(0, 1);
                        var ind = parseInt(attr.substring(1, attr.length));
                        ind = ind + downrows;
                        $(this).attr("r", pre + ind);
                    });

                    function Addrow(index, data) {
                        msg = '<row r="' + index + '">'
                        for (i = 0; i < data.length; i++) {
                            var key = data[i].k;
                            var value = data[i].v;
                            msg += '<c t="inlineStr" r="' + key + index + '" s="2">';
                            msg += '<is>';
                            msg += '<t>' + value + '</t>';
                            msg += '</is>';
                            msg += '</c>';
                        }
                        msg += '</row>';
                        return msg;
                    }
                    var r1 = Addrow(1, [{ k: 'A', v: (code) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                    var r2 = Addrow(2, [{ k: 'A', v: (name) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                    // }
                }
            }],
            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            }
        });
        setTimeout(function() {
            dtItems.draw('page');
        }, 150);
    });
    $('body').on('click', '.editDataTable_ch', function(e) {
        var type = $(this).attr('data-type');
        var client = $(this).attr('data-client');
        var _td = $(this).parent().parent();
        _td.find('.lableScript').addClass('hide');
        _td.find('.inputScript').removeClass('hide');
        appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
    })
    $('body').on('click', '.closeEditData', function(e) {
        var type = $(this).attr('data-type');
        var client = $(this).attr('data-client');
        var _td = $(this).parent().parent().parent();
        _td.find('.lableScript').removeClass('hide');
        _td.find('.inputScript').addClass('hide');
        var id = _td.find('.inputScript').find('input#id_ch').val();
        _td.find('.inputScript').find('input.ChangeDataTable').val($('#quantitys_text_v2_' + id).text());
        appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
    })

    function manage_Udpdatecolum(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form)
            $('#quantitys_text_v2_' + form.id).text(form.total);
            var _td = $('#quantitys_text_v2_' + form.id).parent().parent();
            _td.find('.lableScript').removeClass('hide');
            _td.find('.inputScript').addClass('hide');
            var _tdd = $('#quantitys_text_v2_' + form.id).parent().parent().parent();
            _tdd.find('.type_v2').find('input.quantitys').val(form.total);
            alert_float(form.success, form.messeger);
        }), !1
    }

    var countItems = 0;
    function createItemsDetail(_this) {
        // $(_this).addClass('hide');
        var tr = $(`<tr></tr>`);
        var tdSTT = $(`<td class="text-center">
                                <a class="btn btn-icon btn-success" onclick="saveDetail(this)"><i class="fa fa-floppy-o"></i></a>
                                <a class="btn btn-icon btn-danger" onclick="removeDetail(this)"><i class="fa fa-remove"></i></a>
                        </td>`);
        var tdImage = $(`<td class="text-center"><img class="img_product" src="${site_url}assets/images/preview-not-available.jpg" width="50px" height="50px"></td>`);
        var tdCodeProduct = $(`<td><input type="text" name="items_data[${countItems}][product_id]"  id="items_data_${countItems}" class="items_products" style="width: 100%;" data-placeholder="Thành phẩm" value=""></td>`);
        var tdNameProduct = $(`<td><div class="name_product"></div></td>`);
        var tdUnit = $(`<td></td>`);
        var tdPrice = $(`<td><input type="text" name="items_data[${countItems}][price]" onchange="formatNumBerKeyUpCus(this)" class="form-control price text-right" value=""></td>`);
        var tdType = $(`<td><div class="type_product"></div></td>`);
        tr.append(tdSTT);
        tr.append(tdImage);
        tr.append(tdCodeProduct);
        tr.append(tdNameProduct);
        tr.append(tdUnit);
        tr.append(tdPrice);
        tr.append(tdType);
        $('table.detail_supplier').find('tbody').prepend(tr);
        ajaxSelectParams($(`#items_data_${countItems}`), 'admin/import_price/SearchItems', 0,true,true);
        countItems++;

        $('input.items_products').change(function(data_add) {
            var tr = $(this).parents('tr');
            data = data_add.added;
            if(data.images) {
                tr.find('.img_product').attr('src', site_url + data.images);
            }
            else {
                tr.find('.img_product').attr('src', `${site_url}assets/images/preview-not-available.jpg`);
            }
            tr.find('.name_product').text(data.item_name);
        })
    }

    function saveDetail(_this) {
        var tr = $(_this).parents('tr');
        var items_products = tr.find('input.items_products').val();
        var price = tr.find('input.price').val();
        if(items_products == '') {
            alert_float('danger', 'Sản phẩm là bắt buộc vui lòng chọn');
            return false;
        }
        if(price == '') {
            alert_float('danger', 'Giá là bắt buộc vui lòng nhập');
            return false;
        }

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['items_products'] = items_products;
        data['price'] = price;
        data['supplier_price_id'] = $('#supplier_price_id').val();
        $.post(admin_url + 'import_price/add_items', data, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
            if(result.success) {
                $('.div_table_view').html(result.data);
            }

        })
    }

    function removeDetail(_this) {
        $(_this).parents('tr').remove();
        $('.add_item_detail').removeClass('hide');
    }
</script>