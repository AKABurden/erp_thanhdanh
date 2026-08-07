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
            <?php if ($this->perAddProductsListBom): ?>
                <a href="<?= base_url('admin/products/add_bom') ?>" class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
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
                        <div class="">
                            <table id="dt-boms" class="table table-hover table-bordered table-condensed dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="category"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_versions') ?></th>
                                        <th class="text-center"><?= lang('date_start') ?></th>
                                        <th class="text-center"><?= lang('date_end') ?></th>
                                        <th class="text-center"><?= lang('tnh_date_creted') ?></th>
                                        <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    var arr = [];

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#dt-boms',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: height_body,
                // scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/products/getBoms') ?>',
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
                    {"targets": 1, "name": 'versions', 'className': 'text-center'},
                    {
                        "render": function (data, type, row) {
                            return '<div>'+fsd(data)+'</div>';
                        },
                        "targets": 2, "name": 'date_start',
                        'visible': false,
                    },
                    {
                        "render": function (data, type, row) {
                            return '<div>'+fsd(data)+'</div>';
                        },
                        "targets": 3, "name": 'date_end',
                        'visible': false,
                    },
                    {
                        "render": function (data, type, row) {
                            return '<div>'+fld(data)+'</div>';
                        },
                        "targets": 4, "name": 'date_created', 'className': 'text-center'
                    },
                    {"targets": 5, "name": 'created_by', 'className': 'text-center'},
                    {"targets": 6, "name": 'actions', 'orderable': false, 'searchable': false, 'width': '50px'}
                ]
            }
        );

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw();
        });

        $('#table-bom').on('draw.dt', function(e, settings) {
            // if (arr.length > 0) {
            //     console.log(arr);
            //     $.each(arr, function(index, el) {
            //         $('input[name="category_id"][value="'+ el +'"]').closest('td').trigger('click');
            //     });
            // }
        })
    });
</script>
<script type="text/javascript">
    var json = {};
</script>
<script type="text/javascript" src="<?= js('design_bom.js?vs=1.7') ?>"></script>

