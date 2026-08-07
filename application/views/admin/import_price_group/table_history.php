<table class="table dataTable table-history-items" style="width: 100%;">
    <thead>
    <tr>
        <th rowspan="2" class="text-center">STT</th>
        <th rowspan="2" class="text-center">Ngày sửa đổi</th>
        <th rowspan="2" class="text-center">Nhân viên sửa đổi</th>
        <th rowspan="2" class="text-center">Hình ảnh</th>
        <th rowspan="2" class="text-center">Mã SP</th>
        <th rowspan="2" class="text-center">Tên SP</th>
        <th colspan="2" class="text-center">MOQ</th>
        <th rowspan="2" class="text-center">Giá</th>
    </tr>
    <tr>
        <th class="text-center">SL từ</th>
        <th class="text-center">SL đến</th>
    </tr>
    </thead>
    <tbody>
        <?php if(!empty($history_data)) {?>
            <?php foreach ($history_data as $key => $value) {?>
                <?php $item = @get_full_item($value->product_id, $value->product_type);
                if(empty($item->id)) continue;?>
                <tr>
                    <td class="text-center">
                        <?php echo $key + 1; ?>
                    </td>
                    <td class="text-center">
                        <?php echo _dt($value->date_create); ?>
                    </td>
                    <td class="text-center">
                        <?php echo !empty($value->create_by) ? get_staff_full_name($value->create_by) : ''; ?>
                    </td>
                    <td class="text-center">
                        <?="<img src='" . $item->avatar_1 . "' width='50px' height='50px' />";?>

                    </td>
                    <td>
                        <div class="mbot5"><?=$item->code?></div>
                        <?= format_item_purchases($value->product_type) ?>
                    </td>
                    <td><?php echo $item->name?></td>
                    <td class="text-center <?=$value->type_event == 'money_start' ? 'bg-danger' : ''?>"><?=number_format_data_four($value->money_start)?></td>
                    <td class="text-center <?=$value->type_event == 'money_end' ? 'bg-danger' : ''?>"><?=number_format_data_four($value->money_end)?></td>
                    <td class="text-right <?=$value->type_event == 'price' ? 'bg-danger' : ''?>"><?=number_format_data_four($value->price)?></td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        dtItemsHistory = $('.table-history-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "ordering": false,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
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
</script>