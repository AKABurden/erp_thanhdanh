<div class="text-center uppercase">
    <h2><?= lang('tnh_nearest_selling_price') ?></h2>
</div>
<hr>
<div class="row mbot10">
</div>
<div class="table-responsive">
    <table id="tb-nearest-selling-price" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th rowspan="1" class="text-center"><?= lang('tnh_category_id') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_item_code') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_item_name') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_type') ?></th>
                <th rowspan="1" class="text-center"><?= lang('tnh_unit') ?></th>
                <th rowspan="1" class="text-center"><?= lang('quantity') ?></th>
                <th rowspan="1" class="text-center"><?= lang('price') ?></th>
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
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var paramsNearestSellingPrice = {'start_date': '#start_date', 'end_date': '#end_date'};
    $(document).ready(function() {
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        init_datepicker();

        oTableNearestSellingPrice = tnhDatatable(
            '#tb-nearest-selling-price',
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
                        title: '<?= lang('tnh_nearest_selling_price') ?>',
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
                    //     title: '<?= lang('tnh_nearest_selling_price') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_nearest_selling_price') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getNearestSellingPrice') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsNearestSellingPrice) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsNearestSellingPrice[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                rowGroup: {
                    dataSrc: 0,
                    startRender: function (rows, group) {
                        if (group != null) {
                            return group;
                        } else {
                            return '<?= lang('tnh_not_categorized') ?>';
                        }
                    }
                },
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
                    {"targets": 0, "name": 'customer', 'width': '150px', 'visible': false},
                    {"targets": 1, "name": 'product_code', 'width': '130px'},
                    {"targets": 2, "name": 'product_name', 'width': '130px'},
                    {
                        "render": function(data, type, row) {
                            if (data == "items") {
                                return '<?= lang('ch_items') ?>';
                            } else {
                                return '<?= lang('products') ?>';
                            }
                        },
                        "targets": 3, "name": 'type', 'width': '130px'
                    },
                    {"targets": 4, "name": 'unit_name', 'width': '50px', 'class': 'text-center'},
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
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity = 0, price = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity+= intVal(aaData[i][5]);
                        price+= intVal(aaData[i][6]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[4].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                    nCells[5].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(price)+'</div>';
                }
            }
        );

        $('#tb-nearest-selling-price_wrapper .btn-dt-reload').click(function(event) {
            oTableNearestSellingPrice.draw();
        });

        $('#start_date, #end_date').change(function(event) {
            event.preventDefault();
            oTableNearestSellingPrice.draw();
        });
    });
</script>