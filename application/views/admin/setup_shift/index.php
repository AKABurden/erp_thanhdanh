<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-setup-shift tr th:nth-child(1),
    #table-setup-shift tr td:nth-child(1) {
        width: 50px !important;
    }

    #table-setup-shift tr th:nth-child(2),
    #table-setup-shift tr td:nth-child(2) {
        width: 200px !important;
    }

    #table-setup-shift tr th:nth-child(3),
    #table-setup-shift tr td:nth-child(3) {
        width: 150px !important;
    }

    #table-setup-shift tr th:nth-child(4),
    #table-setup-shift tr td:nth-child(4) {
        width: 150px !important;
    }

    #table-setup-shift tr th:nth-child(5),
    #table-setup-shift tr td:nth-child(5) {
        width: 120px !important;
    }

    #table-setup-shift tr th:nth-child(6),
    #table-setup-shift tr td:nth-child(6) {
        width: 120px !important;
    }

    #table-setup-shift tr th:nth-child(7),
    #table-setup-shift tr td:nth-child(7) {
        width: 170px !important;
    }
</style>
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddSetUpShift){ ?>
                <a href="<?= base_url('admin/setup_shift/detail') ?>"
                   class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal"
                   data-target="#myModal">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-setup-shift"
                                   class="table table-hover table-bordered table-condensed dataTable table-setup-shift"
                                   style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>
                                        <?= lang('STT') ?>
                                    </th>
                                    <th class="text-center"><?= lang('Ca làm việc') ?></th>
                                    <th class="text-center"><?= lang('Thời gian') ?></th>
                                    <th class="text-center"><?= lang('Thời gian nghỉ trưa') ?></th>
                                    <th class="text-center"><?= lang('Thời gian tính tăng ca') ?></th>
                                    <th class="text-center"><?= lang('Thời gian được tính cơm') ?></th>
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
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    var arr = [];

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-setup-shift', '<?= site_url('admin/setup_shift/getSetupShifts') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/setup_shift/getSetupShifts') ?>',
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function (json) {
                    return json.aaData;
                }
            },
            "columnDefs": []
        });
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
