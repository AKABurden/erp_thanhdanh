<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="pull-right mright5 H_border">
                <?php if($this->perAddKpiCriteria): ?>
                    <a href="<?= base_url('admin/kpi/handling_criteria') ?>" class="btn btn-info H_action_button tnh-modal">
                        <?php echo _l('add'); ?>
                    </a>
                    <a href="<?= base_url('admin/kpi/modal_import_criteria') ?>" class="btn btn-info H_action_button c_modal">
                        <?php echo _l('Import excel'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12 mbot10">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2 hide">
                        <?= lang('role', 'role_search') ?>
                        <select name="role_search[]" id="role_search" data-none-selected-text="<?= lang('role') ?>" multiple="true" data-actions-box="true" data-live-search="true" class="form-control selectpicker">
                            <?php if(!empty($roles)): ?>
                                <?php foreach($roles as $key => $value): ?>
                                    <option value="<?= $value['roleid'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= lang('staff', 'staff_search') ?>
                            <select name="staff_search[]" id="staff_search" data-actions-box="true" data-live-search="true" data-none-selected-text="<?= lang('staff') ?>" class="form-control selectpicker" multiple>
                                <option value=""></option>
                                <?php if(!empty($staffs)): ?>
                                    <?php foreach($staffs as $key => $value): ?>
                                        <option value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?= lang('department', 'department_search') ?>
                        <select name="department_search[]" id="department_search" data-none-selected-text="<?= lang('department') ?>" multiple="true" data-actions-box="true" data-live-search="true" class="form-control selectpicker">
                            <?php if(!empty($departments)): ?>
                                <?php foreach($departments as $key => $value): ?>
                                    <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('start_date', 'start_date_search') ?>
                            <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" autocomplete="off" id="start_date_search" class="form-control start_date_search datepicker" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?= lang('end_date', 'end_date_search') ?>
                            <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" autocomplete="off" id="end_date_search" class="form-control end_date_search datepicker" value="">
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
                            <table id="table-kpi-criteria" class="table dt-tnh table-hover table-kpi-criteria" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <!-- <div class="checkbox mass_select_all_wrap checkbox-info"><input type="checkbox" id="mass_select_all" data-to-table="hand_over"><label for="mass_select_all"></label>
                                            </div> -->
                                        </th>
                                        <th class="text-center"><?= lang('start_date') ?></th>
                                        <th class="text-center"><?= lang('end_date') ?></th>
                                        <th class="text-center"><?= lang('Mã KPI') ?></th>
                                        <th class="text-center"><?= lang('tnh_criteria') ?></th>
                                        <th class="text-center" style="width: 150px;"><?= lang('staff') ?>/<?= lang('department') ?></th>
                                        <th class="text-center"><?= lang('tnh_unit') ?></th>
                                        <th class="text-center"><?= lang('tnh_target') ?></th>
                                        <th class="text-center"><?= lang('tnh_weight_number') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('actions') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('info') ?></th>
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
        department_search: "#department_search",
        role_search: "#role_search",
        staff_search: "#staff_search",
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    var oTable = '';

    function loadInfo(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[11];
    }

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-kpi-criteria', '', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/kpi/getKpiCriteria') ?>',
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
                        // return `<div class="checkbox checkbox-info">
                        //     <input type="checkbox" name="criteria[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        // </div>`;

                        return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": [9],
                    'orderable': false,
                    'searchable': false,
                },
                {
                    "targets": 10,
                    "name": 'actions',
                    'orderable': false,
                    'searchable': false,
                },
                {
                    "targets": 11,
                    "name": 'info',
                    'orderable': false,
                    'searchable': false,
                    'visible': false,
                }
            ],
        });

        $('#table-kpi-criteria tbody').on('click', 'td .rows-child', function() {
            var tr = $(this).closest('tr');
            var row = oTable.row(tr);
            if (row.child.isShown()) {
                $(this).removeClass('fa-caret-down');
                $(this).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                $(this).removeClass('fa-caret-right');
                $(this).addClass('fa-caret-down');
                row.child(loadInfo(row.data())).show();
                tr.addClass('shown');
            }
        });

        $('#department_search, #role_search, #staff_search, #start_date_search, #end_date_search').change(function(event) {
            oTable.draw();
        });
    });
</script>