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
            <a href="<?= base_url('admin/notification_form/add') ?>" class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo lang('add'); ?>
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
                        <div class="table-responsive">
                            <table id="table-category" class="table table-hover table-bordered table-condensed dataTable table-category" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="category"><label for="mass_select_all"></label></div></th>
                                        <th><?= lang('code_notification_form') ?></th>
                                        <th><?= lang('name_notification_form') ?></th>
                                        <th><?= lang('type') ?></th>
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

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-category',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/notification_form/getNotificationForm') ?>',
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
                            return '<div>'+ data +'</div>';
                        },
                        "targets": 1,
                        "name": 'code'
                    },
                    {"targets": 2, "name": 'name'},
                    {
                        "render": function(data, type, row) {
                            var str = '';
                            if (data == 'noti_phone') {
                                str = '<?= lang('noti_phone') ?>';
                            } else if (data == 'noti_email') {
                                str = '<?= lang('noti_email') ?>';
                            } else if (data == 'noti_zalo') {
                                str = '<?= lang('noti_zalo') ?>';
                            } else if (data == 'noti_note_other') {
                                str = '<?= lang('noti_note_other') ?>';
                            }
                            return str;
                        },
                        "targets": 3, "name": 'type'
                    },
                    {"targets": 4, "name": 'actions', 'orderable': false, 'searchable': false, 'width': '100px'}
                ]
            }
        );

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>

