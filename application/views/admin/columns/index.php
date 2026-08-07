<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/columns/handling_columns') ?>" class="btn btn-info H_action_button tnh-modal">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/columns/import') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('Import'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('tnh_code_columns', 'code_columns_search') ?>
                            <input type="text" name="code_columns_search" id="code_columns_search" class="form-control code_columns_search" value="" placeholder="<?= lang('tnh_code_columns') ?>" title="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-columns" class="table dt-tnh table-hover table-columns" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width: 50px;">
                                            <div class="checkbox mass_select_all_wrap text-center checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="columns"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('tnh_code_columns') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_columns') ?></th>
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
        code_columns_search: "#code_columns_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-columns', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/columns/getColumns') ?>',
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
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox checkbox-info"><input type="checkbox" name="columns_id[]" id="check-item' +
                        data + '" value="' + data + '"><label for="check-item' + data +'"></label></div>';
                        // return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": 3,
                    "name": 'actions',
                    'orderable': false,
                    'searchable': false,
                },
            ],
        });

        $('#code_columns_search').change(function(event) {
            oTable.draw();
        });
    });
</script>