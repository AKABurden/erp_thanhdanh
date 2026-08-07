<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= base_url('admin/costing/add_costing') ?>" class="btn btn-info pull-right H_action_button">
            	<i class="lnr lnr-plus-circle" aria-hidden="true"></i>
            	<?php echo _l('add'); ?>
            </a>
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
                            <table id="table-costing" class="table dt-tnh table-hover table-condensed table-costing dont-responsive-table dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('start_date') ?></th>
                                        <th><?= lang('end_date') ?></th>
                                        <th><?= lang('name') ?></th>
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
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
	var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {status_table: '#status_table'};
	var oTable = '';
</script>
<script type="text/javascript">
	$(document).ready(function() {
		oTable = tnhDatatable(
            '#table-costing',
            {
                'order': [[1, 'desc'], [2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                // scrollY: height_body,
                // scrollX: true,
                // fixedColumns: {
                //     leftColumns: 4,
                //     rightColumns: 0
                // },
                // stateSave: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/costing/getCosting') ?>',
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
                "drawCallback": function(settings, nRow) {
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                    // setTimeout(function(){ oTable.draw(); }, 1500);
                },
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    // var total_quantity = 0;
                    // for (var i = 0; i < aaData.length; i++) {
                    //     total_quantity+= intVal(aaData[i][7]);
                    // }
                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(total_quantity)+'</div>';
                },
                "columnDefs": [
                    {"targets": 0, "name": 'id', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            return '<div>'+fsd(data)+'</div>';
                        },
                        "targets": 1, "name": 'start_date'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>'+fsd(data)+'</div>';
                        },
                        "targets": 2, "name": 'end_date'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a data-tnh="modal" class="tnh-modal" href="'+site.base_url+'admin/costing/view_costing/'+row[0]+'" data-toggle="modal" data-target="#myModal">'+data+'</a>';
                        },
                        "targets": 3, "name": 'name'
                    },
                    {"targets": 4, "name": 'created_by'},
                    {"targets": 5, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '100px', 'className': 'text-center'},
                ]
            }
        );

        $(document).on('click', '#table-costing_wrapper .btn-dt-reload', function(event) {
            oTable.draw('page');
        });

        $('#table-costing').on('draw.dt', function(e, settings) {
        })
	});
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
