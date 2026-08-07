<div class="text-center uppercase">
    <h2><?= lang('tnh_report_returned_goods') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('customers') ?></b>
        <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
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
    <table id="tb-returned-goods" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th rowspan="1" class="text-center"><?= lang('tnh_customer') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_p_returned_goods') ?></th>
                <th rowspan="1" class="text-center"><?= lang('date') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_item_code') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_item_name') ?></th>
                <th rowspan="1" class="text-center"><?= lang('quantity') ?></th>
                <th rowspan="1" class="text-center"><?= lang('price') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_subtotal') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <th></th>
                <th class="text-center"><?= lang('tnh_grand_total') ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var paramsReturnedGoods = {'start_date': '#start_date', 'end_date': '#end_date',  'customers': '#customers'};
    $(document).ready(function() {
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        init_datepicker();

        oTableReturnedGoods = tnhDatatable(
            '#tb-returned-goods',
            {
                'order': [[0, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    {
                        text: 'Excel',
                        title: '<?= lang('tnh_report_returned_goods') ?>',
                        // extend: 'excelHtml5',
                        // autoFilter: true,
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':visible'
                        },
                        // customize: function ( xlsx ){
                        //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        //     $('row c', sheet).attr( 's', '25' );
                        // }
                    },
                    // {
                    //     text: 'Pdf',
                    //     title: '<?= lang('tnh_report_returned_goods') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_report_returned_goods') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'print',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // }
                ],
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                scrollY: true,
                scrollX: true,
                // scrollCollapse: true,
                // fixedColumns: {
                //     leftColumns: 2,
                //     rightColumns: 0
                // },
                // stateSave: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getReturnedGoods') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsReturnedGoods) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsReturnedGoods[key]).val()
                        });

                        //custom
                        if ($(paramsReturnedGoods[key]).data('select2') && $(paramsReturnedGoods[key]).val()) {
                            var array_data = $(paramsReturnedGoods[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key+'_text_'+index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsReturnedGoods[key]).select2('data').text;
                                        aoData.push({
                                            "name": key+'_text',
                                            "value": $(paramsReturnedGoods[key]).select2('data').text
                                        });
                                    }
                                });
                            } else {
                                // aoData[key+'_text'] = $(paramsReturnedGoods[key]).select2('data').text;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": $(paramsReturnedGoods[key]).select2('data').text
                                });
                            }
                        } else if ($(paramsReturnedGoods[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsReturnedGoods[key]).find('option:selected').text();
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
                        $('#tb-returned-goods').attr('title_excel', JSON.stringify(response?.title_excel));
                        fnCallback(response);
                    }});
                },
                // rowGroup: {
                //     dataSrc: 0,
                //     startRender: function (rows, group) {
                //         if (group != null) {
                //             return group;
                //         } else {
                //             return '<?= lang('tnh_not_categorized') ?>';
                //         }
                //     }
                // },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'customer_name', 'width': '150px'},
                    {"targets": 1, "name": 'reference_no', 'width': '130px'},
                    {"targets": 2, "name": 'date', 'width': '130px', 'searchable': false},
                    {"targets": 3, "name": 'item_code', 'width': '130px'},
                    {"targets": 4, "name": 'item_name', 'width': '130px'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 5, "name": 'quantity', 'width': '130px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 6, "name": 'price', 'width': '130px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 7, "name": 'amount', 'width': '130px'
                    },
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity = 0, subtotal = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity+= intVal(aaData[i][5]);
                        subtotal+= intVal(aaData[i][7]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[5].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                    nCells[7].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(subtotal)+'</div>';
                }
            }, 1
        );

        $('#tb-returned-goods_wrapper .btn-dt-reload').click(function(event) {
            oTableReturnedGoods.draw();
        });

        $('#start_date, #end_date, #customers').change(function(event) {
            event.preventDefault();
            oTableReturnedGoods.draw();
        });
    });
</script>