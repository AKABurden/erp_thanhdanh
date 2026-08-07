<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if($this->perAddKpi): ?>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/kpi/handling') ?>" class="btn btn-info H_action_button">
                    <?php echo _l('add'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12 mbot10">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2 hide">
                        <?= lang('month', 'month') ?>
                        <select name="month" id="month" class="month" data-placeholder="<?= lang('month') ?>" style="width: 100%;">
                            <?php foreach (getMonth() as $key => $value) : ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 hide">
                        <?= lang('year', 'year') ?>
                        <select name="year" id="year" data-placeholder="<?= lang('year') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php
                            $data = date('Y');
                            for ($i = $data - 5; $i <= $data + 5; $i++) {
                            ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('staff', 'staff') ?>
                        <select name="staff" data-placeholder="<?= lang('staff') ?>" id="staff" class="modal-select2" style="width: 100%;" required="required">
                            <option value=""></option>
                            <?php if (!empty($staffs)) : ?>
                                <?php foreach ($staffs as $key => $value) : ?>
                                    <option <?= !empty($kpi) && $kpi['staff'] == $value['staffid'] ? 'selected' : '' ?> data-department="<?= $value['name_department'] ?>" data-role="<?= $value['name_role'] ?>" value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('department', 'department_search') ?>
                        <select name="department_search" id="department_search" data-none-selected-text="<?= lang('department') ?>" data-actions-box="true" data-live-search="true" class="form-control selectpicker">
                            <option></option>
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
                            <table id="table-list-kpi" class="table dt-tnh table-hover table-list-kpi" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                        </th>
                                        <th class="text-center"><?= lang('start_date') ?></th>
                                        <th class="text-center"><?= lang('end_date') ?></th>
                                        <th class="text-center"><?= lang('tnh_target_reception_time') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_no') ?></th>
                                        <th class="text-center"><?= lang('type') ?></th>
                                        <th class="text-center"><?= lang('staff') ?>/<?= lang('department') ?></th>
                                        <th class="text-center"><?= lang('tnh_point_kpi') ?></th>
                                        <th class="text-center"><?= lang('tnh_result_kpi') ?></th>
                                        <th class="text-center"><?= lang('Những mặt cần khác phục, cố gắng hơn') ?></th>
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
        month: "#month",
        year: "#year",
        staff: "#staff",
        start_date_search: "#start_date_search",
        end_date_search: "#end_date_search",
        department_search: '#department_search'
    };
    var oTable = '';

    function loadInfo(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[10];
    }

    $(document).ready(function() {
        $('#month').select2({allowClear: true});
        $('#year').select2({allowClear: true});
        $('#staff').select2({allowClear: true});
        oTable = tnhInitDataTable('#table-list-kpi', '', {
            'order': [
                [0, 'desc'],
                [1, 'desc'],
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/kpi/getListKpi') ?>',
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
                        //     <input type="checkbox" name="kpi[]" id="check-item${data}" value="${data}"><label for="check-item${data}"></label>
                        // </div>`;
                        // return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                        return '<div class="text-center">'+data+'</div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": 3,
                    'visible': false
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

        $('#table-list-kpi tbody').on('click', 'td .rows-child', function() {
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

        $('#month, #year, #staff, #start_date_search, #end_date_search, #department_search').change(function(event) {
            oTable.draw();
        });
    });
</script>