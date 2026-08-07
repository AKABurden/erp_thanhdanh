<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if (has_permission('handover_task', '', 'create')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/hand_over/handling_hand_over_task') ?>" class="btn btn-info H_action_button tnh-modal">
                        <?php echo _l('add'); ?>
                    </a>
                </div>
            <?php } ?>

            <?php if (has_permission('handover_task', '', 'create') || has_permission('handover_task', '', 'edit')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/hand_over/task_modal_excel_import') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                        <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                        <?php echo _l('IMPORT EXCEL'); ?>
                    </a>
                </div>
            <?php } ?>
            <?php if (has_permission('handover_task', '', 'create') || has_permission('handover_task', '', 'edit')) { ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/hand_over/task_modal_excel_import_group') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                        <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                        <?php echo _l('IMPORT EXCEL THEO NHÓM CÔNG ĐOẠN'); ?>
                    </a>
                </div>
            <?php } ?>
            <div class="pull-right mright5 H_border">
                <a onclick="" href="<?= base_url('admin/hand_over/export_machines') ?>" class="btn btn-info pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('EXPORT EXCEL'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('tnh_category_hand_over', 'category_hand_over_search') ?>
                        <select name="category_hand_over_search" id="category_hand_over_search" data-placeholder="<?= lang('tnh_category_hand_over') ?>" class="modal-select2" style="width: 100%;" required="required">
                            <option value=""></option>
                            <?php if (!empty($category_hand_over)) : ?>
                                <?php foreach ($category_hand_over as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-hand_over_task" class="table dt-tnh table-hover table-hand_over_task" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="hand_over_task"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th class="text-center"><?= lang('Công đoạn') ?></th>
                                        <th class="text-center"><?= lang('Mã loại bàn giao') ?></th>
                                        <th class="text-center"><?= lang('Tên loại bàn giao') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_handover_task') ?></th>
                                        <th class="text-center"><?= lang('c_tieu_chuan') ?></th>
                                        <th class="text-center"><?= lang('c_phuong_phap') ?></th>
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
        category_hand_over_search: "#category_hand_over_search",
    };
    var oTable = '';

    $(document).ready(function() {
        $('#category_hand_over_search').select2({
            allowClear: true
        });
        oTable = tnhInitDataTable('#table-hand_over_task', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/hand_over/getHandOverTasks') ?>',
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
                    return `<div class="checkbox checkbox-info">
                            <input type="checkbox" name="hand_over_task[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        </div>`;
                },
                "targets": 0,
                "name": 'id',
                'orderable': false,
                'width': '40px'
            }, ],
        });

        $('#category_hand_over_search').change(function(event) {
            oTable.draw();
        });
    });

</script>