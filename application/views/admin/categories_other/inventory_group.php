<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/categories_other/handlingInventoryGroup/0/'.$status) ?>" class="btn btn-info H_action_button tnh-modal">
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
                            <table id="table-inventory-group" class="table table-hover table-inventory-group dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="inventory-group"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= $label['code_group'] ?></th>
                                        <th class="text-center"><?= $label['name_group'] ?></th>
                                        <th class="text-center"><?= $label['code'] ?></th>
                                        <th class="text-center"><?= $label['detail'] ?></th>
                                        <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
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
<input type="hidden" name="status_search" id="status_search" class="form-control" value="<?= $status ?>">

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_search: "#status_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-inventory-group', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/categories_other/getInventoryGroup') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false,
                },
                {
                    "targets": [4],
                    "searchable": false,
                    "orderable": false,
                }
            ],
        });
    });
</script>