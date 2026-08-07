<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a href="<?= base_url('admin/categories/handlingInsurrance/0/add') ?>" class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
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
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-insurrance" class="table table-hover table-bordered table-condensed dataTable table-insurrance" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="insurrance"><label for="mass_select_all"></label></div></th>
                                        <th><?= lang('tnh_hinhthuc') ?></th>
                                        <th><?= lang('code') ?></th>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('tnh_amount_of_money') ?></th>
                                        <th><?= lang('tnh_rate_company') ?></th>
                                        <th><?= lang('tnh_rate_worker') ?></th>
                                        <th><?= lang('note') ?></th>
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
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-insurrance',
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
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/categories/getInsurrance') ?>',
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
                            return '<div class="checkbox"><input type="checkbox" id="check-item" value="'+ data +'"><label for="check-item"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            var str = '';
                            if (data == 1) {
                                str = '<div class="text-center"><span class="label label-primary"><?= lang('tnh_bt') ?></span></div>';
                            } else if (data == 1) {
                                str = '<div class="text-center"><span class="label label-warning"><?= lang('tnh_bg') ?></span></div>';
                            }
                            return str;
                        },
                        "targets": 1, "name": 'ht'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>'+ data +'</div>';
                        },
                        "targets": 2,
                        "name": 'code'
                    },
                    {"targets": 3, "name": 'name'},
                    {
                        "targets": 4, "name": 'money',
                        "render": function(data, type, row) {
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+data+'</div>';
                        },
                        "targets": 5, "name": 'rate_company'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+data+'</div>';
                        },
                        "targets": 6, "name": 'rate_worker'
                    },
                    {"targets": 7, "name": 'note'},
                    {"targets": 8, "name": 'actions', 'orderable': false, 'searchable': false, 'width': '100px'}
                ]
            }
        );
        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });
        loadAjax();
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>

