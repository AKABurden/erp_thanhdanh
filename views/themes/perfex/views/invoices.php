<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
    .wap-new {
    background: red;
    color: #fff;
    font-weight: 300;
    border-radius: 10px;
    padding: 0px 10px;
    }
    .table-order tbody tr td:nth-child(1){
        white-space: inherit;
        text-align: center;
        max-width: 90px;
    }
    .table-order tbody tr td:nth-child(2){
        white-space: inherit;
        text-align: center;
        max-width: 90px;
    }
    .table-order tbody tr td:nth-child(3){
        white-space: inherit;
        text-align: center;
        max-width: 100px;
    }
    .table-order tbody tr td:nth-child(4){
        white-space: inherit;
        text-align: right;
    }
    .table-order tbody tr td:nth-child(5){
        white-space: inherit;
        text-align: center;
    }
    .table-bordered thead tr th{
        white-space: inherit;
        text-align: center;
    }
    .table-bordered tbody tr td:nth-child(2){
        white-space: inherit;
        text-align: center;
        min-width: 80px;
    }
    .table-bordered tbody tr td:nth-child(3){
        white-space: inherit;
        text-align: left;
        min-width: 120px;
    }
    .table-bordered tbody tr td:nth-child(4){
        white-space: inherit;
        text-align: left;
        min-width: 150px;
    }
    .table-bordered tbody tr td:nth-child(5){
        white-space: inherit;
        text-align: center;
        min-width: 70px;
    }
    .table-bordered tbody tr td:nth-child(6){
        white-space: inherit;
        text-align: center;
        min-width: 70px;
    }
    .table-bordered tbody tr td:nth-child(7){
        white-space: inherit;
        text-align: right;
        min-width: 90px;
    }
    .table-bordered tbody tr td:nth-child(8){
        white-space: inherit;
        text-align: right;
        min-width: 90px;
    }
    .table-bordered tbody tr td:nth-child(9){
        white-space: inherit;
        text-align: center;
        min-width: 90px;
    }
    .table-bordered tbody tr td:nth-child(10){
        white-space: inherit;
        text-align: center;
        min-width: 90px;
    }
    .table-bordered tbody tr td:nth-child(12){
        white-space: inherit;
        text-align: right;
        min-width: 90px;
    }
    .table-bordered tbody tr td:nth-child(13){
        white-space: inherit;
        text-align: left;
        min-width: 100px;
    }   
    td.details-control {
    background: url('./assets/images/tnh/details_open.png') no-repeat center center;
    cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('./assets/images/tnh/details_close.png') no-repeat center center;
    }
    .horizontal-scrollable-tabs .scroller {
    background: 0 0;
    font-weight: 600;
    cursor: pointer;
    color: #50637c;
    border-bottom: 1px solid #f0f0f0;
    border-top: 1px solid #f0f0f0;
    padding: 9px 10px
}

.horizontal-scrollable-tabs .scroller.disabled {
    opacity: .4;
    cursor: not-allowed
}

.firefox .horizontal-scrollable-tabs .horizontal-tabs .nav-tabs-horizontal {
    overflow: -moz-scrollbars-none
}

.horizontal-scrollable-tabs .horizontal-tabs .nav-tabs-horizontal::-webkit-scrollbar {
    width: 0 !important;
    height: 0 !important
}

.horizontal-scrollable-tabs .tabs-submenu-wrapper li {
    position: static
}

.horizontal-scrollable-tabs .tabs-submenu-wrapper {
    position: absolute;
    z-index: 10;
    display: none
}

.firefox .preview-tabs-top {
    margin-bottom: 25px
}

.ribbon + .horizontal-scrollable-tabs .arrow-right {
    z-index: 2;
    border-top: 0;
    position: relative
}
</style>
<div class="panel_s section-heading section-order">
    <div class="panel-body">
        <h4 class="no-margin section-text"><?php echo _l('Lịch sử đơn hàng'); ?></h4>
    </div>
</div>
<div class="panel_s">
 <div class="panel-body">
     <?php get_template_part('invoices_stats'); ?>
     <div class="clearfix"></div>
     <br>
     <div class="horizontal-scrollable-tabs">
                      <div class="horizontal-tabs">
                          <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                            <li class="active pointer">
                                <a class="H_filter" data-id="all">
                                  <?=_l('leads_all')?> (<span class="all">0</span>)
                                </a>
                            </li>
                            <li class="pointer">
                                <a class="H_filter" data-id="un_approved">
                                  <?=_l('dont_approve')?> (<span class="dont_approve">0</span>)
                                </a>
                            </li>
                            <li class="pointer">
                                <a class="H_filter" data-id="approved">
                                  <?=_l('ch_confirm_22')?> (<span class="ch_confirm_22">0</span>)
                                </a>
                            </li>
                          </ul>
                      </div>
                  </div>
                  <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                  
     <table class="table table-order" id="orders">
         <thead>
            <tr>
                <th style="text-align: center" class="th-invoice-number"></th>
                <th style="text-align: center" class="th-invoice-number"><?php echo _l('Ngày'); ?></th>
                <th style="text-align: center" class="th-invoice-date"><?php echo _l('Số đơn hàng'); ?></th>
                <th style="text-align: center" class="th-invoice-duedate"><?php echo _l('Tộng cộng'); ?></th>
                <th style="text-align: center" class="th-invoice-amount"><?php echo _l('Trạng thái'); ?></th>
                <th style="text-align: center" class="th-invoice-amount"><?php echo _l('Địa chỉ giao'); ?></th>
                <th style="text-align: center" class="th-invoice-status"><?php echo _l('Ghi chú'); ?></th>
            </tr>
        </thead>
  
    </table>
</div>
</div>
    
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <link href="https://datatables.net/download/build/dataTables.responsive.nightly.css" rel="stylesheet" type="text/css" />
    <script src="https://datatables.net/download/build/dataTables.responsive.nightly.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url('assets/cjs/css/fixedColumns.dataTables.min.css')?>">
<script type="text/javascript">
$('.H_filter').click(function(e) {
  var target = $(e.currentTarget);
  var value = target.attr('data-id');
  target.parent().parent().find('li').removeClass('active');
  target.parent().addClass('active');
  $('input[name="filterStatus"]').val(value);
  $('input[name="filterStatus"]').change();
});
function initDataTableFixedHeader(table, url, notsearchable, notsortable, fnserverparams, defaultorder, fixedColumns) {
    // alert(table);
    var _table_name = table;
    if ($(table).length == 0) {
        return;
    }
    if (fnserverparams == 'undefined' || typeof(fnserverparams) == 'undefined') {
        fnserverparams = []
    }

    // If not order is passed order by the first column
    if (typeof(defaultorder) == 'undefined') {
        defaultorder = [
        [0, 'ASC']
        ];
    } else {
        if (defaultorder.length == 1) {
            defaultorder = [defaultorder]
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    tables_pagination_limit = parseFloat(tables_pagination_limit);

    if ($.inArray(tables_pagination_limit, length_options) == -1) {
        length_options.push(tables_pagination_limit)
        length_options_names.push(tables_pagination_limit)
    }

    length_options.sort(function(a, b) {
        return a - b;
    });
    length_options_names.sort(function(a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(dt_length_menu_all);

    var table = $('body').find(table).dataTable({
        fixedColumns: fixedColumns || false,
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        "bScrollCollapse": true,
        scrollY:        '80vh',
        scrollCollapse: true,
        "language": dt_lang,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 700,
        "bDeferRender": true,
        "responsive": false,
        "autoWidth": false,
        dom: "<'mbot25'B><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
        "pageLength": tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title)
            $(nRow).attr('data-toggle', aData.Data_Toggle)
        },
        "initComplete": function(settings, json) {
            var _table = $(table);
            var th_last_child = _table.find('thead th:last-child');
            var th_first_child = _table.find('thead th:first-child');
            if (th_last_child.text().trim() == '<?=_l('opption')?>') {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
        },
        "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '';
                        },
                        "className": 'details-control',
                        "targets": 0,
                        "name": 'records',
                        'orderable': false,
                        'width': '5px'
                    },
                ],
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function(d) {
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
            }
        },
    });

    var tableApi = table.DataTable();
    var hiddenHeadings = $(table).find('th.not_visible');
    $.each(hiddenHeadings, function() {
        tableApi.columns(this.cellIndex).visible(false, false);
    });
    // Fix for hidden tables colspan not correct if the table is empty
    if ($(_table_name).is(':hidden')) {
        $(_table_name).find('.dataTables_empty').attr('colspan', $(_table_name).find('thead th').length);
    }

    return tableApi;
}
    var CustomersServerParams = {
      'filterStatus' : '[name="filterStatus"]',
    };
    var tAPI = initDataTableFixedHeader(".table-order", "<?=base_url('clients/table')?>", [1], [1],CustomersServerParams);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
      $(filterItem).on('change', function(){
        tAPI.ajax.reload();
      });
    });
    tAPI.on('draw.dt', function() {
      get_total_limit();
    });
    function format(data,row,tr) {
            $.ajax({
                url:  '<?=base_url('clients/show_daital')?>',
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    data: data,
                },
            })
            .done(function(data) {
                row.child(data).show();
                tr.addClass('shown');
            })
            .fail(function() {
                console.log("error");
            });
        }
    $('#orders tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tAPI.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            format(row.data(),row,tr);
            // row.child( format(row.data()) ).show();
            // tr.addClass('shown');
        }
    } );
        function get_total_limit() {
          dataString = {[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url: "<?=base_url()?>clients/count_all/",
                data: dataString,
                cache: false,
                success: function (data) {
                  data = JSON.parse(data);
                  $('.all').html(data.all);
                  $('.dont_approve').html(data.dont_approve);
                  $('.ch_confirm_22').html(data.ch_confirm_22);         
                  }
            });
        }
</script>
