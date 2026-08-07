<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<div style="color: #2b6fa2;font-weight: bold;" class="text-center uppercase">
    <h2><?= lang('tnh_sale_listing') ?></h2>
</div>
<hr>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('customers', 'customers') ?></b>
            <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('start_date', 'start_date') ?></b>
            <input type="text" name="start_date" id="start_date" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('end_date', 'end_date') ?></b>
            <input type="text" name="end_date" id="end_date" autocomplete="off" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <a onclick="view_sale_listing()" class="btn btn-success" href="javascript:void(0)"><i class="fa fa-filter"></i> Lọc</a>
            <a onclick="exportExcel()" class="btn btn-primary" href="javascript:void(0)"><i class="fa fa-file-excel-o"></i> Xuất Excel</a>
        </div>
    </div>
</div>
<div class="view-sale-listing">
    
</div>

<script type="text/javascript">
    var isCost = 'true';

    var paramsReport = {
        'start_date': '#start_date',
        'end_date': '#end_date',
        'customers': '#customers',
    };

    function exportExcel() {
        customer_search = $('#customers').val();

        if (!customer_search) {
            bootbox.alert('Xin vui lòng chọn khách hàng');
            return;
        }

        start_date_search = $('#start_date').val();
        end_date_search = $('#end_date').val();

        if (customer_search) {
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/reports/exportExcelSaleListing',
                data: {
                    customer_search: customer_search,
                    csrf_token_name: hash,
                    start_date_search: start_date_search,
                    end_date_search: end_date_search,
                    export_excel: 1,
                },
                dataType: "json",
                success: function(response) {
                    if (response.result) {
                        alert_float('success', response.message);
                        download(response.filename, response.file);
                    } else {
                        alert_float('danger', response.message);
                    }
                }
            });
        }
    }

    function printSaleListing() {
        customer_search = $('#customers').val();

        if (!customer_search) {
            bootbox.alert('Xin vui lòng chọn khách hàng');
            return;
        }

        start_date_search = $('#start_date').val();
        end_date_search = $('#end_date').val();

        window.open(site.base_url+'admin/reports/print_sale_listing?customer_search='+customer_search+'&start_date_search='+start_date_search+'&end_date_search='+end_date_search);
    }

    function view_sale_listing() {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        customers = $('#customers').val();
        if (customers) {
            $.ajax({
                type: 'POST',
                url: site.base_url+'admin/reports/view_sale_listing',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    start_date: start_date,
                    end_date: end_date,
                    customers: customers
                },
                dataType: "html",
                success: function (response) {
                    $('.view-sale-listing').html(response);
                }
            });
        }
    }

    $(document).ready(function() {
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        init_datepicker();

        oTableReport = tnhDatatable(
            '#tb-reportss', {
                'order': [
                    [0, 'asc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                'searching': false,
                'ordering': false,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getSaleListingByOrder') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsReport) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsReport[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        // 'success': fnCallback
                        success: function(response) {
                            fnCallback(response);
                            if (response) {
                                $('.th-congtienhang').html('<div class="text-right bold">' + tnhFormatMoney(response.total) + '</div>');
                                $('.th-chietkhau').html('<div class="text-right bold">' + tnhFormatMoney(response.totalDiscount) + '</div>');
                                $('.th-thue').html('<div class="text-right bold">' + tnhFormatMoney(response.totalTax) + '</div>');
                                $('.th-phivanchuyen').html('<div class="text-right bold">' + tnhFormatMoney(response.totalDelivery) + '</div>');
                                $('.th-tongcong').html('<div class="text-right bold">' + tnhFormatMoney(response.grand_total) + '</div>');
                            }
                        }
                    });
                },
                "drawCallback": function(aoData, settings) {
                    $('.group-orders').closest('tr').css('background', '#ffff0094');
                    $('.group-orders').closest('tr').addClass('par-group-orders');
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function(tfoot, data, start, end, display) {},
                "columnDefs": [
                ],
                // rowsGroup: [
                //     0, 1, 2, 9, 10, 11
                // ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {}
            }
        );

        $('#tb-report_wrapper .btn-dt-reload').click(function(event) {
            oTableReport.draw();
        });

        $('#start_date, #end_date, #customers').change(function(event) {
            event.preventDefault();
            // view_sale_listing();
            // oTableReport.draw();
        });  
    });
</script>