<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-category-kpi tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }
    #table-category-kpi tr td:nth-child(5) {
        width: 150px;
        white-space: unset;
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if ($this->perAddDetailTask): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/kpi/import_list_criteria_department') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('Import Excel'); ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="col-md-12">
            <div class="col-md-3">
                <?= lang('Phòng ban', 'department_search') ?>
                <select name="department_search" id="department_search" class="department_search" data-placeholder="<?= lang('Phòng ban') ?>" style="width: 100%;">
                    <?php foreach ($dtDepartment as $key => $value) : ?>
                        <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive table-list-criteria-department" style="width: 100%">
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
    $("#department_search").select2({
        allowClear :true
    })
    $(document).on('change', '#department_search', function(
        event) {
        event.preventDefault();
        loadDataTableListCriteriaDepartment();
    });
    function loadDataTableListCriteriaDepartment(){
        $('#table-list-criteria-department tbody').html('');
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/kpi/loadDataTableListCriteriaDepartment',
            data: {
                csrf_token_name: hash,
                department_search: $("#department_search").val(),
            },
            dataType: "html",
            success: function (response) {
                if (response) {
                    $('.table-list-criteria-department').html(response);
                }

            }
        });
    }
    loadDataTableListCriteriaDepartment();
</script>