<div class="text-center uppercase">
    <h2><?= lang('Danh sách đơn hàng hủy') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('customers') ?></b>
        <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('orders') ?></b>
        <input type="text" name="orders" id="orders" style="width: 100%;" data-placeholder="<?= lang('orders') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('start_date') ?></b>
        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('end_date') ?></b>
        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-sales-order" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('Nhóm khách hàng') ?></th>
                <th class="text-center"><?= lang('customers') ?></th>
                <th class="text-center"><?= lang('tnh_orders') ?></th>
                <th class="text-center"><?= lang('Loại đơn hàng') ?></th>
                <th class="text-center"><?= lang('date') ?></th>
                <th class="text-center"><?= lang('Ghi chú hủy') ?></th>
                <th class="text-center"><?= lang('Số lượng') ?></th>
                <th class="text-center"><?= lang('Đơn giá') ?></th>
                <th class="text-center"><?= lang('Tổng tiền(VND)') ?></th>
                <th class="text-center"><?= lang('Tổng tiền(USD)') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <td></td>
                <td></td>
                <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var paramsSalesOrder = {'start_date': '#start_date', 'end_date': '#end_date', 'customers': '#customers', 'orders': '#orders'};
    $(document).ready(function() {
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        init_datepicker();

        oTableSalesOrder = tnhDatatable(
            '#tb-sales-order',
            {
                'order': [[0, 'asc']],
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                'searching': false,
                'ordering': false,
                'dom': "<'row'><'row'<'col-md-7'l><'col-md-5'f>>rt<'row'<'col-md-4'i><'.dt-page-jump'>p>",
                scrollY: true,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getSalesOfOrderCancel') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsSalesOrder) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsSalesOrder[key]).val()
                        });

                        //custom
                        if ($(paramsSalesOrder[key]).data('select2') && $(paramsSalesOrder[key]).val()) {
                            var array_data = $(paramsSalesOrder[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key+'_text_'+index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsSalesOrder[key]).select2('data').text;
                                        aoData.push({
                                            "name": key+'_text',
                                            "value": $(paramsSalesOrder[key]).select2('data').text
                                        });
                                    }
                                });
                            } else {
                                // aoData[key+'_text'] = $(paramsSalesOrder[key]).select2('data').text;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": $(paramsSalesOrder[key]).select2('data').text
                                });
                            }
                        } else if ($(paramsSalesOrder[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsSalesOrder[key]).find('option:selected').text();
                            if (selectedText) {
                                // aoData[key+'_text'] = selectedText;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": selectedText
                                });
                            }
                        }
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': function(response) {
                        $('#tb-sales-order').attr('title_excel', JSON.stringify(response?.title_excel));
                        fnCallback(response);
                    }});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [
                    {"targets": 0, "name": 'customer_group', 'width': '150px', 'searchable': false, 'orderable': false},
                    {"targets": 1, "name": 'customer', 'width': '150px', 'searchable': false, 'orderable': false},
                    {"targets": 2, "name": 'reference_orders', 'width': '130px'},
                    {"targets": 3, "name": 'type_orders', 'width': '100px', 'searchable': false, 'orderable': false},
                    {
                        "render": function(data) { return fld(data); },
                        "targets": 4, "name": 'date_quotes', 'width': '80px', 'searchable': false
                    },
                    {"targets": 5, "name": 'note_cancel', 'width': '130px'},
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 6, "name": 'quantity', 'width': '130px', 'searchable': false, 'orderable': false
                    },
                    {
                        "render": function(data) {
                            if (!data || data == 0) return '';

                            $.each(data, function (index, value) {
                                data[index] = tnhFormatMoney(value);
                            });

                            return '<div class="text-center">'+data.join(', ')+'</div>';
                        },
                        "targets": 7, "name": 'price', 'width': '130px', 'searchable': false, 'orderable': false
                    },
                    {
                        "render": function(data) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 8, "name": 'amount_end', 'width': '120px'
                    },
                    {
                        "render": function(data) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 9, "name": 'amount_end', 'width': '120px'
                    }
                ],
                "fnFooterCallback": function (nRow, aaData) {
                    var total_amount = 0, total_amount_usd = 0, total_quantity = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        total_quantity += intVal(aaData[i][6]);
                        total_amount += intVal(aaData[i][8]);
                        total_amount_usd += intVal(aaData[i][9]);
                    }
                    var nCells = nRow.getElementsByTagName('td');
                    nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(total_quantity)+'</div>';
                    nCells[8].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(total_amount)+'</div>';
                    nCells[9].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(total_amount_usd)+'</div>';
                }
            }, 1
        );

        $('#start_date, #end_date, #customers, #orders').change(function(event) {
            event.preventDefault();
            oTableSalesOrder.draw();
        });
    });
</script>