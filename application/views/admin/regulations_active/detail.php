<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
	.H_action_button i {
      display: contents!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?=admin_url()?>">Trang chủ</a>
                    </li>
                    <li>
                        <a href="<?=admin_url('regulations_active')?>">Quy Chế Hoạt Động Phòng Ban</a>
                    </li>
                    <li class="active">Chi Tiết</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="col-md-9">
                            <h3 class="panel-title in-title">THÔNG TIN</h3>
                        </div>
                        <div class="col-md-3"><a href="<?= admin_url('regulations_active/export_excel/' . (!empty($regulations_active) ? $regulations_active->id : '')) ?>" target="_blank" class="btn btn-success btn-icon mright5 test pull-right"><i class="fa fa-download"></i> Xuất Excel</a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Mã Phòng Ban:</b> <?=$regulations_active->code?></div>
                            </div>
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Tên Phòng Ban:</b> <?=$regulations_active->name?></div>
                            </div>
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Trực Thuộc:</b> <?=$regulations_active->under?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Ngày Ban Hành:</b> <?=_dC($regulations_active->date_issued)?></div>
                            </div>
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Phiên Bản:</b> <?=$regulations_active->version?></div>
                            </div>
                            <div class="div_row">
                                <div style="min-width: 100px"><b>Ngày Cập nhật mới nhất:</b> <?=_dC($regulations_active->date_update)?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body row">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-regulations-active dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2">STT</th>
                                        <th class="text-center" rowspan="2"><?= _l('Chức Năng Phòng') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Nhiệm Vụ Phòng') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Quyền Hạn Phòng') ?></th>
                                        <th class="text-center" colspan="3"><?= _l('Cơ Cấu Tổ Chức') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Công Việc Vị Trí') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Qui Trình') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Mục Tiêu Phòng Ban Năm') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Kết Quả Năm') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Kết Quả KPIs Quý I') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Kết Quả KPIs Quý II') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Kết Quả KPIs Quý III') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('Kết Quả KPIs Quý IV') ?></th>
                                        <th class="text-center" rowspan="2"><?= _l('options') ?></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= _l('Sơ Đồ Trực Thuộc') ?></th>
                                        <th class="text-center"><?= _l('Mã Vị Trí') ?></th>
                                        <th class="text-center"><?= _l('Chức Vụ') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($regulations_active_detail)) {
                                        foreach($regulations_active_detail as $key => $value) {?>
                                                <tr>
                                                    <td><?=($key + 1)?></td>
                                                    <td><?=!empty($value['room_function']) ? $value['room_function'] : ''?></td>
                                                    <td><?=!empty($value['room_tasks']) ? $value['room_tasks'] : ''?></td>
                                                    <td><?=!empty($value['room_powers']) ? $value['room_powers'] : ''?></td>
                                                    <td><?=!empty($value['file_under']) ? $value['file_under'] : ''?></td>
                                                    <td><?=!empty($value['code_locus']) ? $value['code_locus'] : ''?></td>
                                                    <td><?=!empty($value['position']) ? $value['position'] : ''?></td>
                                                    <td><div style="white-space: break-spaces;max-width: 500px;"><?=!empty($value['job_position']) ? $value['job_position'] : ''?></div></td>
                                                    <td><?=!empty($value['name_position']) ? $value['name_position'] : ''?></td>
                                                    <td><?=!empty($value['goals_year']) ? $value['goals_year'] : ''?></td>
                                                    <td><?=!empty($value['result_year']) ? $value['result_year'] : ''?></td>
                                                    <td><?=!empty($value['result_quarter_one']) ? $value['result_quarter_one'] : ''?></td>
                                                    <td><?=!empty($value['result_quarter_two']) ? $value['result_quarter_two'] : ''?></td>
                                                    <td><?=!empty($value['result_quarter_three']) ? $value['result_quarter_three'] : ''?></td>
                                                    <td><?=!empty($value['result_quarter_four']) ? $value['result_quarter_four'] : ''?></td>
                                                    <td><a class="btn btn-icon btn-danger deleteItems" data-href="<?=admin_url('regulations_active/delete_detail/' . $value['id'])?>"><i class="fa fa-remove"></i></a></td>
                                                </tr>
                                        <?php }
									}?>
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
<script>
    $(function(){
        $('.action-menu').click();
        // oTable = initDataTable('.table-regulations-active', admin_url + 'regulations_active/table', [0], [0], filterList, [0, 'desc']);
    })
    
    $('body').on('click', '.deleteItems', function() {
        if(confirm('Dữ liệu xóa không thể khôi phục?')) {
            var href = $(this).attr('data-href');
            var tr = $(this).parents('tr');
            if(href) {
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(href, data, function(data) {
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success) {
                        $(tr).remove();
                    }
                })
            }
        }
    })
</script>
