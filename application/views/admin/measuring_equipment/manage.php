<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }
    /*.bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {*/
    /*    width: 200px !important;*/
    /*}*/
    .dt-buttons>.btn-default:nth-child(1){
        /* display: none!important; */
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <div class="line-sp"></div>
            <a href="<?= base_url('admin/measuring_equipment/detail') ?>" class="btn btn-info pull-right H_action_button c_modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <a href="<?= base_url('admin/measuring_equipment/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('IMPORT EXCEL'); ?>
            </a>
            <a href="<?= base_url('admin/measuring_equipment/export_excel') ?>" class="btn btn-info pull-right mright10 H_action_button">
                <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('EXPORT EXCEL'); ?>
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
                            <table id="table-measuring_equipment" class="table table-hover table-bordered table-condensed dataTable table-machines" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center">
                                                <input type="checkbox" id="mass_select_all" data-to-table="measuring_equipment">
                                                <label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('Mã Thiết Bị/Công Việc') ?></th>
                                        <th><?= lang('Tên Thiết Bị/Công Việc') ?></th>
                                        <th><?= lang('Định Mức Năng Suất/Tháng') ?></th>
                                        <th><?= lang('Trạng Thái') ?></th>
                                        <th><?= lang('Thông số kỹ thuật') ?></th>
                                        <th><?= lang('Nhóm công đoạn') ?></th>
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
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var lang_machines = <?= json_encode(status_machine_new()) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {};
    var oTable = '';
    $(document).ready(function() {
        var fnserverparams = {
            'datestart' : '[name="date_start"]',
        };
        oTable = initDataTable('#table-measuring_equipment', admin_url + 'measuring_equipment/table', [0], [0], fnserverparams, [0, 'desc']);

        // $('#table-machines').on('draw.dt', function(e, settings) {
        //     $('.tip').tooltip();
        // });
        //
        // $(document).on('click', '#table-machines_wrapper .btn-dt-reload', function(event) {
        //     oTable.draw();
        // });
        //
        // $(document).on('click', '#table-history-machines_wrapper .btn-dt-reload', function(event) {
        //     oTable_machine.draw();
        // });


    });

    $('body').on('click', '.c_delete', function () {
        if(confirm('Dữ liệu xóa sẽ không thể khôi phục!')) {
            var href = $(this).attr('href');
            var id = $(this).attr('data-id');
            var data = {id: id};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(href, data, function (result) {
                result = JSON.parse(result);
                if (result.success) {
                    oTable.draw("page")
                }
                alert_float(result.alert_type, result.message);
                return false;
            })
        }
        return false;
    })
</script>

