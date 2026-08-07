<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/work_plan/handling') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                </div>
            </div>
            <div class="col-md-12">
                <?php echo $this->load->view('admin/alert') ?>
                <div class="">
                    <table id="table-work-plan" class="table table-bordered dataTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;"><?= lang('#') ?></th>
                                <th class="text-center"><?= lang('month') ?></th>
                                <th class="text-center"><?= lang('year') ?></th>
                                <th class="text-center"><?= lang('tnh_content') ?></th>
                                <th class="text-center"><?= lang('tnh_date_created') ?></th>
                                <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                <th class="text-center" style="width: 100px;"><?= lang('actions') ?></th>
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
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var lang_orders =
        <?= json_encode(array('tnh_quantity_delivery_less' => lang('tnh_quantity_delivery_less'), 'tnh_check_quantity_delivery' => lang('tnh_check_quantity_delivery'))) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        customer_search: "#customer_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-work-plan', '', {
            'order': [
                [1, 'desc'], [2, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/work_plan/getWorkPlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    // $('#table-orders tfoot tr td:nth-child(7)').html('<div class="text-right">' + tnhFormatMoney(json.grand_total) + '</div>');
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 6],
                    'orderable': false,
                    'searchable': false,
                },
            ],
        });

        // $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search, #items_search', function(
        //     event) {
        //     event.preventDefault();
        //     oTable.draw();
        //     calOrders();
        // });

        // $(document).on('click', '.status-table li a', function(event) {
        //     status_table = $(this).attr('value');
        //     $('#status_table').val(status_table);
        //     oTable.draw();
        // });
    });
</script>