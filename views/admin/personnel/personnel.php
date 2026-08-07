<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a href="<?= base_url('admin/personnel/add_personnel') ?>" class="btn btn-info pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <div class="pull-right mright5 H_border hide">
                <a class="btn btn-info test H_action_button btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><?= lang('tnh_seach_statistical') ?></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12 hide">
                <div id="search-tnh" class="collapse" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('tnh_reference_orders', 'orders_search') ?>
                        <input type="text" name="orders_search" id="orders_search" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_orders') ?>" value="">
                    </div>
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
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-personnel" class="table dt-tnh table-hover table-bordered table-condensed table-personnel">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="personnel"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('tnh_code_personnel') ?></th>
                                        <th><?= lang('tnh_images') ?></th>
                                        <th><?= lang('tnh_fullname') ?></th>
                                        <th><?= lang('tnh_birthday') ?></th>
                                        <th><?= lang('tnh_gender') ?></th>
                                        <th><?= lang('tnh_birthplace') ?></th>
                                        <th><?= lang('tnh_domicile') ?></th>
                                        <th><?= lang('tnh_cmnd_id_passport') ?></th>
                                        <th><?= lang('tnh_date_range') ?></th>
                                        <th><?= lang('tnh_issued_by') ?></th>
                                        <th><?= lang('tnh_marital_status') ?></th>
                                        <th><?= lang('tnh_nationality') ?></th>
                                        <th><?= lang('tnh_nation') ?></th>
                                        <th><?= lang('tnh_account_name') ?></th>
                                        <th><?= lang('tnh_bank') ?></th>
                                        <th><?= lang('tnh_branch') ?></th>
                                        <th><?= lang('tnh_personal_tax_code') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_date_created') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
	// var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {customer_search: "#customer_search", status_table: '#status_table', orders_search: '#orders_search', start_date_search: '#start_date_search', end_date_search: '#end_date_search'};
	var oTable = '';

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-personnel',
            {
                'order': [[1, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: "450px",
                // "dom": '<"wrapper"flipt>',
                fixedColumns:   {
                    leftColumns: 4,
                    rightColumns: 1
                },
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/personnel/getPersonnel') ?>',
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
                            return '<div class="checkbox"><input type="checkbox" name="personnel_id[]" id="check-item'+data+'" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '40px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a href="'+site.base_url+'admin/personnel/view/'+row[0]+'">'+data+'</a>';
                        },
                        "targets": 1, "name": 'code', 'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == null) {
                                images = site.base_url+"assets/images/tnh/no_image.png";
                            } else {
                                data = data.split('__');
                                images = (data[1] != null) ? site.base_url+"uploads/personnel/"+data[0]+'/'+data[1] : site.base_url+"assets/images/tnh/no_image.png";
                            }

                            return '<div class="preview_image" style="width: auto;">\
                                <div class="display-block contract-attachment-wrapper img">\
                                    <div style="width:30px; margin: auto;>\
                                        <a href="'+images+'" data-lightbox="customer-profile" class="display-block mbot5">\
                                            <div class="">\
                                                <img src="'+images+'" style="border-radius: 50%" />\
                                            </div>\
                                        </a>\
                                    </div>\
                                </div>\
                            </div>';
                        },
                        "targets": 2, "name": 'images', 'width': '80px'
                    },
                    {"targets": 3, "name": 'fullname', 'width': '150px'},
                    {
                        "render": function(data, type, row) {
                            return fsd(data);
                        },
                        "targets": 4, "name": 'birthday', 'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == 'male') {
                                return '<div class="text-center"><span class="label label-primary"><?= lang('tnh_male') ?></span></div>';
                            } else if (data == "female") {
                                return '<div class="text-center"><span class="label label-success"><?= lang('tnh_female') ?></span></div>';
                            } else if (data == "other") {
                                return '<div class="text-center"><span class="label label-danger"><?= lang('tnh_other') ?></span></div>';
                            }
                            return '';
                        },
                        "targets": 5, "name": 'gender', 'width': '80px'
                    },
                    {"targets": 6, "name": 'birthplace', 'width': '100px'},
                    {"targets": 7, "name": 'domicile', 'width': '100px'},
                    {"targets": 8, "name": 'cmnd_id_passport', 'width': '180px'},
                    {
                        "render": function(data, type, row) {
                            return fsd(data);
                        },
                        "targets": 9, "name": 'date_range', 'width': '100px'
                    },
                    {"targets": 10, "name": 'issued_by', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            if (data == 'alone') {
                                return '<div class="text-center"><span class="label label-primary"><?= lang('tnh_alone') ?></span></div>';
                            } else if (data == "marriage") {
                                return '<div class="text-center"><span class="label label-success"><?= lang('tnh_marriage') ?></span></div>';
                            } else if (data == "divorce") {
                                return '<div class="text-center"><span class="label label-danger"><?= lang('tnh_divorce') ?></span></div>';
                            }
                            return '';
                        },
                        "targets": 11, "name": 'marital_status', 'width': '120px'
                    },
                    {"targets": 12, "name": 'nationality', 'width': '100px'},
                    {"targets": 13, "name": 'nation', 'width': '100px'},
                    {"targets": 14, "name": 'account_name', 'width': '100px'},
                    {"targets": 15, "name": 'bank', 'width': '100px'},
                    {"targets": 16, "name": 'branch', 'width': '100px'},
                    {"targets": 17, "name": 'personal_tax_code', 'width': '130px'},
                    {
                        "render": function(data, type, row) {
                            var str = '';
                            if (data == 1) {
                                str = '<span class="label btn-success"><?= lang('tnh_working') ?></span>';
                            }
                            return '<div class="text-center">'+str+'</div>';
                        },
                        "targets": 18, "name": 'status', 'width': '100px'
                    },
                    {"targets": 19, "name": 'note', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 20, "name": 'date_created', 'width': '100px'
                    },
                    {"targets": 21, "name": 'created_by', 'width': '100px'},

                    {"targets": 22, "name": 'actions', 'sortable': false, 'searchable': false, 'width': '150px'},
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    // var grand_total = 0;
                    // for (var i = 0; i < aaData.length; i++) {
                    //     grand_total+= intVal(aaData[i][6]);
                    // }
                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[6].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(grand_total)+'</div>';
                }
            }
        );

        $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });



</script>

