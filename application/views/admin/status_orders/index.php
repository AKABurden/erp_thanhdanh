<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/status_orders/add') ?>" class="btn btn-info H_action_button tnh-modal">
                    <?php echo _l('add'); ?>
                </a>
            </div>
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
                            <table id="table-status_orders" class="table dt-tnh table-hover table-status_orders" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="status_orders"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_code_status_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_status_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_time') ?></th>
                                        <th class="text-center"><?= lang('tnh_colors') ?></th>
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

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-status_orders', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/status_orders/getStatusOrders') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    // if (table.attr('data-last-order-identifier')) {
                    //     d['last_status_orders_identifier'] = table.attr('data-last-order-identifier');
                    // }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return `<div class="checkbox">
                            <input type="checkbox" name="status_orders[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        </div>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
            ],
        });
    });
</script>