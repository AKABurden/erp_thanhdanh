<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/categories_maintenance/modal_excel_import_export/' . $type) ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <a href="<?= base_url('admin/categories_maintenance/handling_import_export/0/' . $type) ?>" class="btn btn-info H_action_button tnh-modal">
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
                            <table id="table-standard" class="table table-hover table-standard dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <?php if ($type == 'imported_documents') { ?>
                                            <th class="text-center"><?= lang('Mã Nhập Khẩu') ?></th>
                                            <th class="text-center"><?= lang('Tên Nhập Khẩu') ?></th>
                                        <?php } else { ?>
                                            <th class="text-center"><?= lang('Mã Xuất Khẩu') ?></th>
                                            <th class="text-center"><?= lang('Tên Xuất Khẩu') ?></th>
                                        <?php } ?>
                                        <th class="text-center"><?= lang('Danh Mục') ?></th>
                                        <th class="text-center"><?= lang('Tác Vụ') ?></th>
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
<input type="hidden" name="type_search" id="type_search" class="form-control" value="<?= $type ?>">

<?php $this->load->view('loader') ?>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        type_search: "#type_search",
    };
    var oTable = '';

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-standard', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/categories_maintenance/getImportedExport') ?>',
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
                    $('#table-standard tfoot tr td.quantity_total').html('<div class="text-center"><b>' + tnhFormatNumber(json.quantity_total) + '</b></div>');
                    return json.aaData;
                }
            },
            "columnDefs": [{
                "targets": [0],
                "orderable": false,
            }]
        });
    });
</script>