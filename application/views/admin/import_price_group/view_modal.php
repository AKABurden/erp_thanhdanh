<div class="modal fade" id="detail_price" tabindex="-1" role="dialog">
    <style>
        .items_products {
            max-width: 200px!important;
        }
        #detail_price .select2-container.select2-allowclear .select2-choice .select2-chosen {
            white-space: pre-line!important;
        }
        #detail_price .select2-container .select2-choice {
            height: 100%!important;
        }

        .table-history-items th, .table-history-items td {
            white-space: nowrap!important;
        }
    </style>
    <div class="modal-dialog modal-xl" style="min-width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('c_improt_price_group'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" value="<?=$import_price_group->id?>" id="id_import_price_group">
                <div class="row">
                    <div class="col-md-12  pull-left">
                        <div class="panel panel-success">

                            <div class="panel-heading">
                                <h3 class="panel-title">Thông tin</h3>
                            </div>
                            <div class="panel-body">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="code_data"><b>Khách hàng: </b><?= !empty($import_price_group) ? $import_price_group->company : ''?></div>
                                            <div class="name_data"><b><?= _l('dt_set_name_supplier') ?>: </b><?= $import_price_group->name_price; ?></div>
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_detail_import_price">Chi tiết bảng giá</a></li>
                        <li onclick="loadHistory()"><a data-toggle="tab" href="#tab_history_import_price">Lịch sử thay đổi bảng giá</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_detail_import_price" class="tab-pane fade in active">
                            <div class="div_table_view">
                                <table class="table detail_supplier" id="table-detail_supplier" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center tdSTT"><a class="btn btn-info btn-icon add_item_detail" onclick="createItemsDetail(this)"><i class="fa fa-plus"></i> Thêm</a></th>
                                            <th rowspan="2"  class="text-center tdImage"><?php echo _l('dt_product_image'); ?></th>
                                            <th rowspan="2" class="text-center tdCodeProduct" style="min-width: 200px;"><?php echo _l('dt_product_code'); ?></th>
                                            <th rowspan="2" class="text-center tdNameProduct"><?php echo _l('dt_product_name'); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo _l('Đơn vị tính'); ?></th>
                                            <th rowspan="2" class="text-center"><?php echo _l('Đơn vị tiền tệ'); ?></th>
                                            <th colspan="2" class="text-center"><?php echo _l('MOQ'); ?></th>
                                            <th rowspan="2" style="min-width:150px;" class="text-center tdPrice"><?php echo _l('Giá'); ?></th>
                                            <th rowspan="2"  class="text-center tdType"><?php echo _l('dt_product_type') ?></th>
                                        </tr>
                                        <tr>
                                            <th class="tdQuantityTo" style="min-width:100px;">SL Từ</th>
                                            <th class="tdQuantityFrom" style="min-width:100px;">SL Đến</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($data)) {?>
                                            <?php foreach ($data as $key => $value) {?>
                                                <?php if(!empty($value->id_item_data)) {
													$item = (object)[
                                                        'avatar_1' => $value->avatar,
                                                        'code' => $value->code_item,
                                                        'name' => $value->name_item,
                                                        'unit_name_payment' => $value->unit_name,
                                                    ];
												}
                                                else {?>
                                                    <?php 
                                                    // $item = @get_full_item($value->product_id, $value->product_type);
                                                         $item = get_full_item_new($value->product_id, $value->product_type);

                                                    if(empty($item->id)) continue;?>
                                                <?php }?>
                                                <tr>
                                                    <td width="8%" class="text-center">
                                                        <?php echo $key + 1; ?><br/><br/>
                                                        <a class="btn btn-icon btn-danger" onclick="removeTrDetail(<?=$value->id?>)"><i class="fa fa-remove"></i></a>
                                                    </td>
                                                    <td width="10%" class="text-center">
                                                        <?="<img src='" . $item->avatar_1 . "' width='50px' height='50px' />";?>
                                                    </td>
                                                    <td>
                                                        <b><?=$item->code?></b>
                                                        <div>
                                                            <div class="checkbox checkbox-danger" style="padding-left: 10px;">
                                                                <input type="checkbox" onchange="changeIsLot(this, <?= $value->id ?>)" <?= $value->is_lot == 1 ? 'checked' : '' ?> name="is_lot" class="is_lot" id="is_lot_<?= $value->id ?>" value="1">
                                                                <label for="is_lot_<?= $value->id ?>"><?= lang('tnh_is_lot') ?></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $item->name?></td>
                                                    <td>123</td>
                                                    <td class="text-right" data-id="money_start_text_v2_">
                                                        <div class="type_v1">
                                                            <?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->money_start), $value->id, '', '<a class="pointer" id="money_start_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->money_start) . '</a>', '', admin_url('import_price_group/update_money/start/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                                                        <div class="type_v2 hide" data-id="<?= $value->id ?>" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_start" class="height_auto  money_start H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                                                    </td>
                                                    <td class="text-right" data-id="money_end_text_v2_">
                                                        <div class="type_v1">
                                                            <?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->money_end), $value->id, '', '<a class="pointer" id="money_end_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->money_end) . '</a>', '', admin_url('import_price_group/update_money/end/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                                                        <div class="type_v2 hide" data-id="<?= $value->id ?>" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_end" id="money_end" class="height_auto  money_end H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                                                    </td>
                                                    <td class="text-right" data-id="quantitys_text_v2_">
                                                        <div class="type_v1">
                                                            <?= dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($value->price), $value->id, '', '<a class="pointer" id="quantitys_text_v2_' . $value->id . '" target="_blank" >' . number_format_data_four($value->price) . '</a>', '', admin_url('import_price_group/quantity/' . $value->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"') ?></div>
                                                        <div class="type_v2 hide" data-id="<?= $value->id ?>" class="quantitys_input"><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="quantitys" id="quantitys" class="height_auto  quantitys H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= !empty($value->label_span) ? $value->label_span : format_item_purchases($value->product_type) ?><br/>
                                                        <a href="#" class="btn btn-info btn-icon mtop5" onclick="updatepriceDetail(<?=$import_price_group->id?>, <?=$value->id?>); return false;">Cập nhật giá đơn hàng</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab_history_import_price" class="tab-pane fade view_table_history"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function updatepriceDetail(id, id_detail) {
        if(!confirm('Bạn có chắc muốn cập nhật lại giá các đơn hàng có mặt hàng này')) {
            return false;
        }
        $.get(admin_url + 'import_price_group/UpdatePirceOrderDetail/' + id + '/' + id_detail, function(response) {
            alert_float(response.alert_type, response.message);
        }, 'json');
    }

    function changeIsLot(_this, _id_detail = 0) {
        _is_lot = $(_this).prop('checked');

        var dataPOST = {};
        dataPOST[<?= $this->security->get_csrf_token_name() ?>] = '<?= $this->security->get_csrf_hash() ?>';
        dataPOST['is_lot'] = _is_lot;
        dataPOST['id_detail'] = _id_detail;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/import_price_group/changeIsLot',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    var _dtItems;
    // $(document).ready(function() {
        var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
    
    
        var buttonExport = [{
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
                            return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
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
                }
            }];
    
        _dtItems = tnhInitDataTable('#table-detail_supplier', '', {
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/import_price_group/table_import_group_deail/'.$import_price_group->id) ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [],
            "buttons" : buttonExport
        });
    
    
        
        
        
        //dtItems = $('.detail_supplier').DataTable({
        //    "language": app.lang.datatables,
        //    "pageLength": app.options.tables_pagination_limit,
        //    "ordering": true,
        //    "lengthMenu": [
        //        [10, 25, 50, 100, -1],
        //        [10, 25, 50, 100, "<?//= lang('all') ?>//"]
        //    ],
        //    "serverSide": true,
        //    'sAjaxSource': '<?//= site_url('admin/import_price_group/table_import_group_deail/'.$import_price_group->id) ?>//',
        //    'fnServerData': function (sSource, aoData, fnCallback) {
        //        aoData.push({
        //            "name": "<?//= $this->security->get_csrf_token_name() ?>//",
        //            "value": "<?//= $this->security->get_csrf_hash() ?>//"
        //        });
        //        for (var key in fnserverparams) {
        //            aoData.push({
        //                "name": key,
        //                "value": $(fnserverparams[key]).val()
        //            });
        //        }
        //        $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
        //    },
        //    'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
        
        //    'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        //    "initComplete": function(settings, json) {
        //        var t = this;
        //        t.parents('.table-loading').removeClass('table-loading');
        //        t.removeClass('dt-table-loading');
        //    }
        //});
        //setTimeout(function() {
        //    dtItems.draw('page');
        //}, 150);
    // });
    $('body').on('click', '.editDataTable_ch', function(e) {
        var type = $(this).attr('data-type');
        var client = $(this).attr('data-client');
        var _td = $(this).parent().parent();
        _td.find('.lableScript').addClass('hide');
        _td.find('.inputScript').removeClass('hide');
        appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
    })

    $('body').on('click', '.closeEditData', function(e) {
        var cssID = $(this).parents('td').data('id');
        var type = $(this).attr('data-type');
        var client = $(this).attr('data-client');
        var _td = $(this).parent().parent().parent();
        _td.find('.lableScript').removeClass('hide');
        _td.find('.inputScript').addClass('hide');
        var id = _td.find('.inputScript').find('input#id_ch').val();
        _td.find('.inputScript').find('input.ChangeDataTable').val($('#' + cssID + id).text());
        appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
    })

    function manage_Udpdatecolum(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var cssID = $(form).parents('td').data('id');
        action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form)
            $('#' + cssID + form.id).text(form.total);
            var _td = $('#' + cssID + form.id).parent().parent();
            _td.find('.lableScript').removeClass('hide');
            _td.find('.inputScript').addClass('hide');
            var _tdd = $('#' + cssID + form.id).parent().parent().parent();
            _tdd.find('.type_v2').find('input.quantitys').val(form.total);
            alert_float(form.success, form.messeger);
            _dtItems.draw('page');
            
        }), !1
    }
    var countItems = 0;
     function createItemsDetail(_this) {
         $(_this).addClass('hide');
         var tr = $(`<tr></tr>`);
         var tdSTT = $(`<td class="text-center">
                                <a class="btn btn-icon btn-success" onclick="saveDetail(this)"><i class="fa fa-floppy-o"></i></a>
                                <a class="btn btn-icon btn-danger" onclick="removeDetail(this)"><i class="fa fa-remove"></i></a>
                        </td>`);
         var tdImage = $(`<td class="text-center"><img class="img_product" src="${site_url}assets/images/preview-not-available.jpg" width="50px" height="50px"></td>`);
         var tdCodeProduct = $(`<td><input type="text" name="items_data[${countItems}][product_id]"  id="items_data_${countItems}" class="items_products" style="width: 100%;" data-placeholder="Thành phẩm" value=""></td>`);
         var tdNameProduct = $(`<td><div class="name_product"></div></td>`);
         var tdUnitProduct = $(`<td><div class="unit_product"></div></td>`);
         var tdUnitCurrencies = $(`<td><div class="unit_currencies"></div></td>`);
         var tdQuantityTo = $(`<td><input type="text" name="items_data[${countItems}][money_start]" onchange="formatNumBerKeyUpCusFour(this)" class="form-control money_start text-right" value="0"></td>`);
         var tdQuantityFrom = $(`<td><input type="text" name="items_data[${countItems}][money_end]" onchange="formatNumBerKeyUpCusFour(this)" class="form-control money_end text-right" value="0"></td>`);
         var tdPrice = $(`<td><input type="text" name="items_data[${countItems}][price]" onchange="formatNumBerKeyUpCusFour(this)" class="form-control price text-right" value=""></td>`);
         var tdType = $(`<td><div class="type_product"></div></td>`);
         tr.append(tdSTT);
         tr.append(tdImage);
         tr.append(tdCodeProduct);
         tr.append(tdNameProduct);
         tr.append(tdUnitProduct);
         tr.append(tdUnitCurrencies);
         tr.append(tdQuantityTo);
         tr.append(tdQuantityFrom);
         tr.append(tdPrice);
         tr.append(tdType);
         $('table.detail_supplier').find('tbody').prepend(tr);
         ajaxSelectParams($(`#items_data_${countItems}`), 'admin/products/searchProductsSelect2', 0,true,true);
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
             tr.find('.unit_product').text(data.unit_name);
         })
     }

     function saveDetail(_this) {
         var button = $(_this);
         button.button({loadingText: 'please wait...'});
         button.button('loading');
         var tr = $(_this).parents('tr');
         var items_products = tr.find('input.items_products').val();
         var money_start = tr.find('input.money_start').val();
         var money_end = tr.find('input.money_end').val();
         var price = tr.find('input.price').val();
         if(items_products == '') {
            alert_float('danger', 'Sản phẩm là bắt buộc vui lòng chọn');
             button.button('reset')
            return false;;
         }
         if(money_start== '') {
             alert_float('danger', 'SL Từ là bắt buộc vui lòng nhập');
             button.button('reset');
             return false;
         }
         if(money_end == '') {
             alert_float('danger', 'SL đến là bắt buộc vui lòng nhập');
             button.button('reset');
             return false;
         }
         if(price == '') {
             alert_float('danger', 'Giá là bắt buộc vui lòng nhập');
             button.button('reset');
             return false;
         }

         var data = {};
         if (typeof(csrfData) !== 'undefined') {
             data[csrfData['token_name']] = csrfData['hash'];
         }
         data['items_products'] = items_products;
         data['money_start'] = money_start;
         data['money_end'] = money_end;
         data['price'] = price;
         data['group_price_id'] = $('#id_import_price_group').val();
         $.post(admin_url + 'import_price_group/add_items', data, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
            if(result.success) {
                // $('.div_table_view').html(result.data);
                _dtItems.draw('page');
            }
         }).always(function() {
             button.button('reset')
         });
     }

     function removeDetail(_this) {
         $(_this).parents('tr').remove();
         $('.add_item_detail').removeClass('hide');
     }
     
     $('#table-detail_supplier').on('draw.dt', function () {
         $('.add_item_detail').removeClass('hide');
     })
     
     function removeTrDetail(id) {
         var data = {id : id};
         if (typeof(csrfData) !== 'undefined') {
             data[csrfData['token_name']] = csrfData['hash'];
         }
         if(confirm('Bạn có chắc chắn muốn xóa chi tiết bảng giá này ra khỏi danh sách?')) {
             $.post(admin_url + 'import_price_group/remove_items', data, function(result) {
                 result = JSON.parse(result);
                 alert_float(result.alert_type, result.message);
                 if(result.success) {
                     _dtItems.draw('page');
                     // $('.div_table_view').html(result.data);
                 }
             })
         }
     }

     function loadHistory() {
        var id = $('#id_import_price_group').val();
        $.get(admin_url + 'import_price_group/get_history/' + id, function(result) {
            $('.view_table_history').html(result);
        })
     }

     

</script>