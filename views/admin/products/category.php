<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <?php if ($this->perExportCategory): ?>
                    <li>
                        <a href="<?= base_url('admin/products/export_excel_category') ?>" class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            <?php echo lang('tnh_export_excel'); ?>
                        </a>
                    </li>
                    <?php endif ?>
                    <?php if ($this->perAddCategory): ?>
                        <li>
                            <a href="<?= base_url('admin/products/import_category') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_import_excel'); ?>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if ($this->perDeleteCategory): ?>
                        <li class="not-outside">
                            <?php echo '<a class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                            <button href=\''.base_url('admin/products/delete_category_multiple').'\' class=\'btn btn-danger po-delete-multiple-json\'>'.lang('delete').'</button>
                            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                            "><i class="fa fa-remove"></i> '.lang('delete').'</button>' ?>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
            <?php if ($this->perAddCategory): ?>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/products/add_category') ?>" class="btn btn-info mright5 pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-category" class="table table-hover table-bordered table-condensed dataTable table-category" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="category"><label for="mass_select_all"></label></div></th>
                                        <th></th>
                                        <th><?= lang('tnh_category_code') ?></th>
                                        <th><?= lang('tnh_category_name') ?></th>
                                        <th><?= lang('tnh_customers_groups') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('sub') ?></th>
                                        <th><?= lang('actions') ?></th>
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
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    var arr = [];

    function format(data) {
        tr = data[6] ? data[6] : '';
        tr1 = '\
            <tr class="success">'+
                '<td class="bold"><?= lang('code') ?></td>'+
                '<td class="bold"><?= lang('name') ?></td>'+
                '<td class="bold"><?= lang('tnh_customers_groups') ?></td>'+
                '<td class="bold"><?= lang('note') ?></td>'+
                '<td class="bold" style="width: 100px;"><?= lang('actions') ?></td>'+
            '</tr>';
        tb = '<table class="dt-table tnh-table table-bordered" style="width: 90% !important; float: right;">'+
            '<tbody>'+
            tr1+
            tr+
            '</tbody>'+
        '</table>'
        return tb;
    }

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-category',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // "processing": true,
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/products/getCategory') ?>',
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
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" class="category_id" name="category_id[]" id="check-item'+data+'" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<input type="hidden" name="category_sid" id="category_id" class="form-control" value="'+row[0]+'">';
                        },
                        "targets": 1, "name": 'records', "className": 'details-control', 'width': '30px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>'+ data +'</div>';
                        },
                        "targets": 2,
                        "name": 'code'
                    },
                    {"targets": 3, "name": 'name'},
                    {"targets": 4, "name": 'category_products_customers'},
                    {"targets": 5, "name": 'note'},
                    {"targets": 6, "name": 'sub', 'visible': false},
                    {"targets": 7, "name": 'actions', 'orderable': false, 'searchable': false, 'width': '100px'}
                ]
            }
        );
        // filterCustom('#table-category thead', oTable, [
        //     // {element: '#table-category tfoot th:nth-child(2)', type: "text", label: '<?= lang('code') ?>', data: []},
        //     // {element: '#table-category tfoot th:nth-child(3)', type: "text", label: '<?= lang('name') ?>', data: []},
        //     // {element: '#table-category tfoot th:nth-child(4)', type: "text", label: '<?= lang('note') ?>', data: []},
        //     {element: '#table-category thead tr:eq(1) th:nth-child(2)', type: "text", data: []},
        //     {element: '#table-category thead tr:eq(1) th:nth-child(3)', type: "text", data: []},
        //     {element: '#table-category thead tr:eq(1) th:nth-child(4)', type: "text", data: []},
        // ]);

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });

        $('#table-category').on('draw.dt', function(e, settings) {
            if (arr.length > 0) {
                console.log(arr);
                $.each(arr, function(index, el) {
                    $('input[name="category_sid"][value="'+ el +'"]').closest('td').trigger('click');
                });
            }
        })

        $('#table-category tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var category_id = tr.find('.category_id').val();
            var row = oTable.row( tr );
            if ( row.child.isShown() ) {
                arr = removeArray(arr, category_id);
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                if (!arr.includes(category_id)) {
                    arr.push(category_id);
                }
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );

    });
</script>
<!-- <script type="text/javascript" src="<?= js('core.js?vs=1.1') ?>"></script> -->
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>

