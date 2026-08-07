<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/business_plan/add') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <?= lang('products', 'products_search') ?>
                        <input type="text" name="products_search" id="products_search" class="" style="width: 100%;" value="" data-placeholder="<?= lang('products') ?>">
                    </div>
                    <div class="col-md-3">
                        <?= lang('productions_orders', 'productions_orders_search') ?>
                        <input type="text" name="productions_orders_search" id="productions_orders_search" class="productions_orders_search" value="" style="width: 100%;" data-placeholder="<?= lang('productions_orders') ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-business-plan" class="table dt-tnh table-hover table-condensed table-business-plan" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <!-- <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="business-plan"><label for="mass_select_all"></label></div> -->
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_business_plan') ?></th>
                                        <th><?= lang('tnh_plan_name') ?></th>
                                        <th><?= lang('departments') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_status_productions_plan') ?></th>
                                        <th><?= lang('tnh_items') ?></th>
                                        <th><?= lang('tnh_status_manufactures') ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
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
	var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {
        'products_search': '#products_search',
        productions_orders_search: '#productions_orders_search',
    };
	var oTable = '';

    function loadItemsBusinessPlan(cData) {
        return cData[10];
    }

    $(document).ready(function() {
        ajaxSelectParams('#products_search', 'admin/products/searchProductsSelect2', 0, true, true);
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);

        oTable = tnhDatatable(
            '#table-business-plan',
            {
                'order': [[1, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: "450px",
                // "dom": '<"wrapper"flipt>',
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/business_plan/getBusinessPlan') ?>',
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
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                            // return '<div class="checkbox"><input type="checkbox" name="business_plan_id[]" id="check-item'+data+'" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
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
                        "targets": 1, "name": 'date'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data) return '';
                            data = data.split('__');
                            str = '';
                            if (data[1] > 0) {
                                str = '<div class="text-danger">Dự phòng</div>';
                            }
                            addProductionPlanning = ' | <a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'+site.base_url+'admin/manufactures/add_production_planning/'+row[0]+'"><?= lang('tnh_add_production_planning') ?></a>';

                            actions = '<div class="mtop5"><?= lang('tnh_branch') ?>: '+(row[12] ? row[12] : '')+'</div><div class="row-options">'+
                                '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'+site.base_url+'admin/business_plan/view_business_plan/'+row[0]+'"><?= lang('view') ?></a>'+
                                ' | <a href="'+site.base_url+'admin/business_plan/edit/'+row[0]+'"><?= lang('edit') ?></a>'+
                                ' | <a target="_blank" href="'+site.base_url+'admin/business_plan/pdf/'+row[0]+'"><?= lang('print_vote') ?></a>'+
                                ' | <a class="text-danger delete-confirm-json" href="'+site.base_url+'admin/business_plan/delete/'+row[0]+'"><?= lang('delete') ?></a>'+
                            '</div>';
                            return '<div>'+ data[0] +'</div>'+str+actions;
                        },
                        "targets": 2, "name": 'reference_no'
                    },
                    {"targets": 3, "name": 'plan_name'},
                    {"targets": 4, "name": 'departments_name'},
                    {"targets": 5, "name": 'note'},
                    {"targets": 6, "name": 'created_by'},
                    {
                        "render": function(data, type, row) {
                            str = '';
                            business_plan_id = row[0];
                            if (data == "approved") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[8]+'</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                str = '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' business_plan_id=\''+business_plan_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                str =  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' business_plan_id=\''+business_plan_id+'\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            }

                            status_productions_plan = row[9];
                            if (status_productions_plan != 0) {
                                status_productions_plan = '<div class="mbot10"><span class="label label-primary"><?= lang('tnh_st_productions_plan') ?></span></div>';
                            } else {
                                status_productions_plan = '<div class="mbot10"><span class="label label-warning"><?= lang('tnh_st_un_productions_plan') ?></span></div>';
                            }
                            return row[11]+status_productions_plan+''+str;
                        },
                        "targets": 7, "name": 'status'
                    },
                    {"targets": 8, "name": 'user_status', 'visible': false},
                    {"targets": 9, "name": 'status_productions_plan', 'visible': false},
                    {"targets": 10, "name": 'items', 'visible': false, 'searchable': false, 'sortable': false},
                    {"targets": 11, "name": 'status_manufactures', 'visible': false, 'searchable': false, 'sortable': false},
                    {"targets": 12, "name": 'name_branch', 'visible': false, 'searchable': false, 'sortable': false},
                ]
            }
        );

        $('#table-tools_supplies').on('draw.dt', function() {
        })

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw('page');
        });

        $(document).on('change', '#products_search, #productions_orders_search', function(event) {
            oTable.draw();
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            business_plan_id = $(this).attr('business_plan_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (business_plan_id) {
                $.ajax({
                    url: site.base_url+'admin/business_plan/agree',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        business_plan_id: business_plan_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('page');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('page');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });

        $('#table-business-plan tbody').on('click', 'td .rows-child', function() {
            var tr = $(this).closest('tr');
            var row = oTable.row(tr);
            if (row.child.isShown()) {
                $(this).removeClass('fa-caret-down');
                $(this).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                $(this).removeClass('fa-caret-right');
                $(this).addClass('fa-caret-down');
                row.child( loadItemsBusinessPlan(row.data()) ).show();
                tr.addClass('shown');
            }
        });

        $('#table-business-plan').on('draw.dt', function() {
            $('.rows-child').click();
        });
    });
</script>

