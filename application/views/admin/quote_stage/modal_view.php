<div id="modal_view_stage_quote" class="modal fade" role="dialog">
    <div class="modal-dialog" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div>Mã bảng giá công đoạn: </div>
                            <div class="ml-at t-bold stage_quote_code"><?= !empty($quote_stage) ? $quote_stage->code : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div>Tên bảng giá công đoạn: </div>
                            <div class="ml-at t-bold stage_quote_name"><?= !empty($quote_stage) ? $quote_stage->name : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Cost of Brand') ?>: </div>
                            <div class="ml-at t-bold stage_quote_code"><?= !empty($quote_stage) ? $quote_stage->cost_of_brand : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Labor cost + Management Cost') ?>: </div>
                            <div class="ml-at t-bold stage_quote_name"><?= !empty($quote_stage) ? $quote_stage->labor_cost : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('Loss Cost') ?>: </div>
                            <div class="ml-at t-bold stage_quote_code"><?= !empty($quote_stage) ? $quote_stage->loss_cost : '' ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('Profit') ?>: </div>
                            <div class="ml-at t-bold stage_quote_name"><?= !empty($quote_stage) ? $quote_stage->profit : '' ?></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 mtop30">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab_modal_stage">Công đoạn</a></li>
                        <li><a data-toggle="tab" href="#tab_modal_client">Khách hàng</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab_modal_stage" class="tab-pane fade in active">
                            <table id="table-items-modal" class="table dont-responsive-table mbot40" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Mã danh mục công đoạn</th>
                                        <th class="text-center">Tên danh mục công đoạn</th>
                                        <th class="text-center">Mã Công đoạn</th>
                                        <th class="text-center">Tên Công đoạn</th>
                                        <th class="text-center">Đơn vị tính</th>
                                        <th class="text-center">Height</th>
                                        <th class="text-center">Width</th>
                                        <th class="text-center">Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($quote_stage->items)) {
                                        foreach ($quote_stage->items as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center"><?= ($key + 1) ?></td>
                                                <td>
                                                    <b><?= $value['code_category'] ?></b>
                                                </td>
                                                <td>
                                                    <b><?= $value['name_category'] ?></b>
                                                </td>
                                                <td>
                                                    <b><?= $value['code_stages'] ?></b>
                                                </td>
                                                <td>
                                                    <b><?= $value['name_stages'] ?></b>
                                                </td>
                                                <td class="text-center"><b><?= $value['unit'] ?></b></td>
                                                <td class="text-right"><?= number_format_data($value['height']) ?></td>
                                                <td class="text-right"><?= number_format_data($value['width']) ?></td>
                                                <td class="text-right"><?= number_format_data($value['price']) ?></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="tab_modal_client" class="tab-pane fade">
                            <table class="table dont-responsive-table mbot40">
                                <thead>
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Mã khách hàng</th>
                                        <th class="text-center">Tên ngắn khách hàng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($quote_stage->items_client)) {
                                        foreach ($quote_stage->items_client as $key => $value) { ?>
                                            <tr>
                                                <td class="text-center"><?= ($key + 1) ?></td>
                                                <td>
                                                    <b><?= $value['zcode'] ?></b>
                                                </td>
                                                <td>
                                                    <b><?= $value['company_short'] ?></b>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#modal_view_stage_quote').modal('show');

    $('#table-items-modal').DataTable({
        "language": app.lang.datatables,
        "pageLength": app.options.tables_pagination_limit,
        "dom": "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row pull-left'<'col-md-4'i>><'row pull-right'<'#colvis'><'.dt-page-jump'>p>",
        buttons: [{
            extend: "excel",
            text: app.lang.dt_button_excel,
            footer: !0,
            exportOptions: {
                columns: [":not(.not-export)"],
                rows: function(t) {
                    return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                },
                format: {
                    body: function(data, row, column, node) {
                        data = $('<p>' + data + '</p>').text();
                        return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                    }
                }
            },
            customize: function(xlsx) {

                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                var mergeCells = $('mergeCells', sheet);
                var downrows = 0;
                stage_quote_code = $('.stage_quote_code').text();
                stage_quote_name = $('.stage_quote_name').text();

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
                            $.each(opts.children, function(key, value) {
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
                clRow.each(function() {
                    var attr = $(this).attr('r');
                    var ind = parseInt(attr);
                    ind = ind + downrows;
                    $(this).attr("r", ind);
                });
                // Update  row > c
                $('row c ', sheet).each(function() {
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
                        msg += '<c t="inlineStr" r="' + key + index + '" s="42">';
                        msg += '<is>';
                        msg += '<t>' + value + '</t>';
                        msg += '</is>';
                        msg += '</c>';
                    }
                    msg += '</row>';
                    return msg;
                }
                var r1 = Addrow(1, [{
                    k: 'A',
                    v: ('Mã bảng giá công đoạn : ' + stage_quote_code)
                }, {
                    k: 'B',
                    v: ""
                }, {
                    k: 'C',
                    v: ""
                }]);
                var r2 = Addrow(2, [{
                    k: 'A',
                    v: ('Tên bảng giá công đoạn : ' + stage_quote_name)
                }, {
                    k: 'B',
                    v: ""
                }, {
                    k: 'C',
                    v: ""
                }]);
                sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                // }
            }
        }],
        "lengthMenu": dataTableLengthMenu(),
        "responsive": true,
        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
        },
        "footerCallback": function(row, data, start, end, display) {}
    });
</script>