<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
    .example button {
        float: left;
        background-color: #4E3E55;
        color: white;
        border: none;
        box-shadow: none;
        font-size: 17px;
        font-weight: 500;
        font-weight: 600;
        border-radius: 3px;
        padding: 15px 35px;
        margin: 26px 5px 0 5px;
        cursor: pointer;
    }
    .example button:focus{
        outline: none;
    }
    .example button:hover{
        background-color: #33DE23;
    }
    .example button:active{
        background-color: #81ccee;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><?= lang('tnh_seach_statistical') ?></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab" value="un_approved_ws_stock" data-toggle="tab"><?= lang('tnh_un_approved_ws_stock') ?>(<span><?= $un_approved_ws_stock ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved_ws_stock" data-toggle="tab"><?= lang('tnh_approved_ws_stock') ?>(<span><?= $approved_ws_stock ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-export-warehouses" class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="export-warehouses-new"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_export_warehouses') ?></th>
                                        <th><?= lang('tnh_reference_deliveries') ?></th>
                                        <th><?= lang('customers') ?></th>
                                        <th><?= lang('tnh_total_quantity') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog modal-lm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_export_quantity_missing');?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript">
	var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {customer_search: "#customer_search", start_date_search: '#start_date_search', end_date_search: '#end_date_search', status_table: '#status_table'};
	var oTable = '';

    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);

        oTable = tnhDatatable(
            '#table-export-warehouses',
            {
                'order': [[1, 'desc'], [2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                fixedColumns:   {
                    leftColumns: 4,
                    rightColumns: 0
                },
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/releases/getExportWarehouses') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "rowCallback": function(row, data) {
                    st = data[8];
                    if (st == "approved")
                    {
                        $(row).find('.ews').addClass('tnh-disabled');
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" name="order_id[]" id="check-item'+data+'" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '40px'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 1, "name": 'date', 'width': '100px', 'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div style="min-width: 150px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="'+site.base_url+'admin/releases/view_export_warehouse/'+row[0]+'" data-toggle="modal" data-target="#myModal">'+ data +'</a>\
                            </div><div class="td-reference-no"></div>';
                        },
                        "targets": 2, "name": 'reference_no', 'width': '100px'
                    },
                    {"targets": 3, "name": 'reference_delivery', 'width': '100px'},
                    {"targets": 4, "name": 'customer_name', 'width': '150px'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-right">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 5, "name": 'total_quantity', 'width': '100px'
                    },
                    {"targets": 6, "name": 'warehouseman_id', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            if(data == 0){
                            return '<a href="javascript:void(0)" onclick="confirm_warehous('+row[0]+')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                            }else
                            {
                            return '<a href="javascript:void(0)" onclick="" class=" btn btn-info btn-icon " data-toggle="tooltip"  data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i>Đã duyệt kho</a>';
                            }
                            // return data;
                        },
                        "targets": 7, "name": 'status', 'width': '100px'
                    },
                    {"targets": 8, "name": 'actions', 'sortable': false, 'searchable': false, 'width': '80px'}
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total+= intVal(aaData[i][5]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[5].innerHTML = '<div class="text-right bold">'+tnhFormatNumber(grand_total)+'</div>';
                }
            }
        );

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('change', '#customer_search, #start_date_search, #end_date_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });
    //hau
    function confirm_warehous(id,warehouseman_id) {
        {
            dataString={id:id,[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url:"<?=admin_url()?>releases/confirm_warehous",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    oTable.draw('page');
                    if(response.success == false)
                    {
                    alert_float(response.message.alert_type, response.message.message);
                    var html ='<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                                <thead>\
                                    <tr>\
                                        <th class="text-center"><?= lang('tnh_items') ?></th>\
                                        <th class="text-center"><?= lang('custom_field_add_edit_type') ?></th>\
                                        <th class="text-center"><?= lang('ch_quantity_missing') ?></th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                        $.each(response.item, function(key,value){
                        html +=     '<tr>\
                                        <th>'+value.item_name+'('+value.item_code+')</th>\
                                        <th class="text-center">'+value.type+'</th>\
                                        <th class="text-center">'+formatNumber(value.quantity_net)+'</th>\
                                    </tr>';
                        });
                        html +=  '</tbody>\
                            </table>';
                            $('#confirm_warehous').modal('show');
                            $('#table_html').html(html);
                    }else
                    {
                    alert_float(response.alert_type, response.message);
                    }
                }
            });
            return false;
        }
    }
</script>

