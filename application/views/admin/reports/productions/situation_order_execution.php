<div class="text-center uppercase">
    <h2><?= lang('report_situation_order_execution') ?></h2>
</div>
<hr>
<?php echo form_open('admin/reports/productions', array('id' => 'situation-order-execution', 'method' => 'GET')); ?>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('start_date') ?></b>
        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="<?= !empty($start_date) ? $start_date : date('d/m/Y') ?>">
    </div>
    <div class="col-md-3">
        <b><?= lang('end_date') ?></b>
        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="<?= !empty($end_date) ? $end_date : date('d/m/Y') ?>">
    </div>
    <div class="col-md-3">
        <input type="hidden" name="search" id="inputSearch" class="form-control" value="situation_order_execution">
        <button type="submit" class="btn btn-default btn-primary mtop20"><?= lang('search') ?></button>
        <a href="<?= base_url('admin/reports/productions') ?>" class="btn btn-danger btn-primary mtop20"><?= lang('un_search') ?></a>
    </div>
</div>
<?php echo form_close(); ?>
<div class="table-responsive">
</div>

<script type="text/javascript">
	var paramsProductionDetailed = {'products': '#products', 'purchase_products': '#purchase_products', 'start_date': '#start_date', 'end_date': '#end_date'};
    var oTableSituationOrderExecution = '';
    var scriptColumnDefs = [
        {"targets": 0, "name": 'id', 'width': '50px', 'class': 'text-center', 'sortable': false, 'visible': false},
        {"targets": 1, "name": 'orders', 'width': '120px', 'sortable': false},
        {"targets": 2, "name": 'productions_orders', 'width': '120px', 'sortable': false},
        {"targets": 3, "name": 'customers', 'width': '120px', 'sortable': false},

        {"targets": 4, "name": 'stage_name', 'width': '150px', 'sortable': false},
        {"targets": 5, "name": 'item_code', 'width': '150px', 'sortable': false},
        {"targets": 6, "name": 'item_name', 'width': '150px', 'sortable': false},
        {"targets": 7, "name": 'unit_name', 'width': '100px', 'class': 'text-center', 'sortable': false},
        {
            "render": function(data, type, row) {
                return data;
            },
            "targets": 8, "name": 'quantity', 'width': '100px', 'class': 'text-center', 'sortable': false
        },
        {
            "render": function(data, type, row) {
                return data;
            },
            "targets": 9, "name": 'date_quantity', 'width': '150px', 'class': 'text-center', 'sortable': false
        }
    ];

    function setScriptColumnDefs()
    {
        return [
            {"targets": 0, "name": 'id', 'width': '50px', 'class': 'text-center', 'sortable': false, 'visible': false},
            {"targets": 1, "name": 'orders', 'width': '120px', 'sortable': false},
            {"targets": 2, "name": 'productions_orders', 'width': '120px', 'sortable': false},
            {"targets": 3, "name": 'customers', 'width': '120px', 'sortable': false},

            {"targets": 4, "name": 'stage_name', 'width': '150px', 'sortable': false},
            {"targets": 5, "name": 'item_code', 'width': '150px', 'sortable': false},
            {"targets": 6, "name": 'item_name', 'width': '150px', 'sortable': false},
            {"targets": 7, "name": 'unit_name', 'width': '100px', 'class': 'text-center', 'sortable': false},
            {
                "render": function(data, type, row) {
                    if (data == 0) return '';
                    return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                },
                "targets": 8, "name": 'quantity', 'width': '100px', 'class': 'text-center', 'sortable': false
            },
            {
                "render": function(data, type, row) {
                    return data;
                },
                "targets": 9, "name": 'date_quantity', 'width': '150px', 'class': 'text-center', 'sortable': false
            }
        ];
    }

    function loadTable(csDays) {
        if (oTableSituationOrderExecution) {
            oTableSituationOrderExecution.destroy();
        }
        iStart = 9;
        scriptColumnDefs = setScriptColumnDefs();
        // for (i = 0; i <= csDays; i++) {
        //     scriptColumnDefs.push({
        //         "render": function(data, type, row) {
        //             if (data == 0) return '';
        //             return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
        //         },
        //         "targets": iStart, "name": iStart, 'width': '100px', 'class': 'text-center', 'sortable': false
        //     });
        //     iStart++;
        // }
        oTableSituationOrderExecution = tnhDatatable(
            '#tb-situation-order-execution',
            {
                destroy: true,
                "ordering": false,
                "searching": false,
                // 'order': [[2, 'asc']],
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
                        title: '<?= lang('report_situation_order_execution') ?>',
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
                    //     title: '<?= lang('report_situation_order_execution') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_situation_order_execution') ?>',
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
                // scrollY: height_body,
                scrollX: true,
                scrollCollapse: true,
                fixedColumns:   {
                    leftColumns: 3,
                    rightColumns: 0
                },
                // stateSave: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getSituationOrderExecution') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionDetailed) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionDetailed[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
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
                "columnDefs": scriptColumnDefs,
                // rowsGroup: [
                //     0, 1, 2, 3, 4
                // ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    // start = 9;
                    // if (!aaData) return;
                    // end = 0;
                    // if (typeof aaData[0] != "undefined")
                    // {
                    //     end = aaData[0].length;
                    // }

                    // arr_id = [];
                    // arr = [];
                    // for (var i = 0; i < aaData.length; i++) {
                    //     for (j = start; j < end; j++)
                    //     {
                    //         if (typeof arr[j] == "undefined")
                    //         {
                    //             arr[j] = 0;
                    //         }
                    //         if (isNaN(parseFloat(aaData[aiDisplay[i]][j])))
                    //         {
                    //             total = 0;
                    //         } else {
                    //             total = parseFloat(aaData[aiDisplay[i]][j]);
                    //         }
                    //         arr[j] = arr[j] + total;
                    //     }
                    // }
                    // for (var i = start; i < arr.length; i++) {
                    //     // if (arr[i] == 0) {
                    //         if (arr_id.indexOf(i) == -1) {
                    //             arr_id.push(i);
                    //         }
                    //     // }
                    // }
                    // oTableSituationOrderExecution.columns(arr_id).visible(false, false);
                }
            }
        );

        $('#tb-situation-order-execution').on( 'page.dt', function () {
            console.log('page');
            var info = oTableSituationOrderExecution.page.info();
            oTableSituationOrderExecution.columns(arr_id).visible(true, true);
            setTimeout(function(){ oTableSituationOrderExecution.columns.adjust().draw(false); }, 3000);
        });

        $('#tb-situation-order-execution_wrapper .btn-dt-reload').click(function(event) {
            oTableSituationOrderExecution.draw();
        });
    }

    function createdTable() {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        if (start_date && end_date) {
            $.ajax({
                url: '<?= base_url('admin/reports/createdColDataTables') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>" : "<?= $this->security->get_csrf_hash() ?>",
                    start_date: start_date,
                    end_date: end_date,
                },
            })
            .done(function(data) {
                $('.table-responsive').html(data.column);
                loadTable(data.days);
            })
            .fail(function() {
                console.log("error");
            });
        }
    }

	$(document).ready(function() {
        ajaxSelectMultipleParams('#products', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams('#purchase_products', 'admin/stock/searchPurchaseProduct', 0, false, true);
        init_datepicker();

        createdTable();

        // $('#products, #purchase_products').change(function(event) {
        //     event.preventDefault();
        //     oTableSituationOrderExecution.draw();
        // });

        // $('#end_date, #start_date').change(function(event) {
        //     createdTable();
        // });
	});
</script>