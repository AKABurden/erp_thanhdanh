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
            <a href="<?= base_url('admin/categories/handlingCategoryMachines') ?>" class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
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
                            <table id="table-category-machines" class="table table-hover table-bordered table-condensed dataTable table-category-machines" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="capacity"><label for="mass_select_all"></label></div></th>
                                    <th><?= lang('code') ?></th>
                                    <th><?= lang('name') ?></th>
                                    <th><?= lang('Mã chủng loại') ?></th>
                                    <th><?= lang('Tên chủng loại') ?></th>
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
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    $(document).ready(function() {
        loadAjax();
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script>
    var oTable = '';
    var fnserverparams = {};
    oTable = tnhInitDataTable('#table-category-machines',
        '<?= site_url('admin/categories/getCategoryMachines') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/categories/getCategoryMachines') ?>',
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
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });
</script>
