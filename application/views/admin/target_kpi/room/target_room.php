<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #menu {
        width: 0% !important;
    }
    #wrapper {
        margin-left: 0px !important;
    }
    .width150 {
        width: 150px!important;
    }
    .btn-bottom-toolbar {
        width: 100%;
        margin-right: 0px;
        margin-left: 0px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <a class="btn btn-info H_action_button pull-right c_modal mright5" href="<?=admin_url('target_kpi/import_excel')?>">Nhập Excel</a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?php
                                        $year = [];
                                        for($i = 2018; $i <= date('Y'); $i++){
                                            $year[] = array(
                                                'id' => $i,
                                                'name' => 'Năm ' . $i
                                            );
                                        }
                                        ?>
                                        <?= render_select('year_search', $year, array('id', 'name'), 'Năm', date('Y')); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <?= lang('Phòng ban', 'department_search') ?>
                                            <select name="department_search[]" id="department_search" data-live-search="true" class="form-control selectpicker department_search" multiple data-none-selected-text="<?= lang('Phòng ban') ?>" data-placeholder="<?= lang('Phòng ban') ?>">
                                                <option value="0"></option>
                                                <?php foreach ($dtDepartment as $key => $value){?>
                                                    <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class=""  style="margin-bottom: 100px;">
                                <table id="table-device" class="table dt-tnh table-target_room" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center"><?= lang('STT') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Mã Loại') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Tên Loại') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Mã Chi Phí Cha') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Tên Chi Phí Cha') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Mã Chi Phí') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Tên Chi Phí') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('STT') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Mô Tả') ?></th>
                                            <th rowspan="2" class="text-center"><?= lang('Phòng Ban') ?></th>
                                            <th colspan="12" class="text-center"><?= lang('Năm') ?> <span class="year_active"><?=date('Y')?></span></th>
                                        </tr>
                                        <tr>
                                            <?php for($i = 1; $i <= 12; $i++) {?>
                                                <th class="text-center" style="white-space: nowrap!important;">Tháng <?=$i?></th>
                                            <?php }?>
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
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';
    var CustomersServerParams = {
        'filterStatus' : '[name="filterStatus"]',
        'year_search' : '[name="year_search"]',
        'department_search' : '[name="department_search[]"]',
    };
    oTable = initDataTableCustom('.table-target_room', admin_url + 'target_kpi/getTargetRoom', [0], [0], CustomersServerParams,[[0, 'asc'], [3, 'desc']], fixedColumns = {leftColumns: 0, rightColumns: 0});

    $(document).on('change', '#year_search,#department_search', function(event) {
        event.preventDefault();
        oTable.draw();
    });

    $(document).on('change', '#year_search', function(event) {
        event.preventDefault();
        $('.year_active').text($(this).val());
    });

    function exportExcel() {
        year = $('#year_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/target_kpi/getTargetRoomExcel',
            data: {
                csrf_token_name: hash,
                year: year,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>