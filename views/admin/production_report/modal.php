<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="production_report_modal" role="dialog">
    <style>
        #production_report_modal img.staff-profile-image-small {
            height: 20px;
            width: 20px;
        }
    </style>
    <div class="modal-dialog" role="document" style="min-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="title">
                        <?= !empty($title) ? $title : ''?>
                    </span>
                </h4>
            </div>
			<?php
			$imgcheck = '<img style="width:15px;" src="' . base_url('uploads/check.png') . '" width="10" height="10">';
			$imgnocheck = '<span style="font-size: 19px;">◻️</span>';
			?>
            <div class="modal-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-xs-6 lead-information-col">
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Tên phiếu: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['name_report']?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Ngày: </span>
                                    <span class="bold font-medium-xs lead-name"><?=_dt_new($production_report['date'])?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Liên quan đến: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($production_report['group_rl_name']) ? ('<br/>' . $production_report['group_rl_name']) : ''?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Đơn đặt hàng: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['code_orders']?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Lệnh SX: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['reference_no']?> <span style="float: right;">Số lượng <?=number_format_data($production_report['quantity_pcs'])?> pcs</span></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Sản phẩm: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($production_report['list_items_name']) ? ('<br/>' . $production_report['list_items_name']) : '-'?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi tiết liên quan: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($production_report['recommended_list_name']) ? ('<br/>' . $production_report['recommended_list_name']) : ''?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Sự cố: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['code_trouble']?> (<i><?=$production_report['name_trouble']?></i>)</span>
                                </div>
                                <div class="wap-content firt" style="display: flex;justify-content: space-between">
                                    <div>
                                    <span class="text-muted lead-field-heading no-mtop bold"><?= _l('Chịu trách nhiệm') ?>: </span>
                                    <?php
                                        $responsible = '';
                                        if (!empty($production_report['staff_responsible'])) {
                                            $responsible = get_staff_full_name($production_report['staff_responsible']);
                                        } else if (!empty($production_report['department_responsible'])) {
                                            $responsible = get_table_where('tbldepartments', ['departmentid'=>$production_report['department_responsible']], '', 'row_array', '', 'CONCAT(code, " (", name, ")") as department');
                                            if (!empty($responsible['department'])) {
                                                $responsible = $responsible['department'];
                                            } else {
                                                $responsible = '';
                                            }
                                        }
                                    ?>
                                    <span class="bold font-medium-xs lead-name"><?= $responsible ?></span>
                                    </div>
                                    <div>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy mẫu: </span>
                                        <span class="bold font-medium-xs lead-name"><?=(!empty($production_report['type_stage_1']) ? $imgcheck : $imgnocheck)?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy hàng + mẫu: </span>
                                        <span class="bold font-medium-xs lead-name"><?=(!empty($production_report['type_stage_2']) ? $imgcheck : $imgnocheck)?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy hàng: </span>
                                        <span class="bold font-medium-xs lead-name"><?=(!empty($production_report['type_stage_3']) ? $imgcheck : $imgnocheck)?></span>
                                        <br>
                                        <span class="text-muted lead-field-heading no-mtop bold">Chạy bù hàng: </span>
                                        <span class="bold font-medium-xs lead-name"><?=(!empty($production_report['type_stage_4']) ? $imgcheck : $imgnocheck)?></span>
                                    </div>
                                </div>
                                <div class="wap-content second hide">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người tạo: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php $nameStaffCreate = (!empty($production_report['create_by']) ?  $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                                                                            ->get_where('tblstaff', ['staffid' => $production_report['create_by']])->row('fullname') : '');?>
                                        <?php echo staff_profile_image( (!empty($production_report['create_by']) ? $production_report['create_by'] : ''), [
											'staff-profile-image-small'], 'small',[
											'data-toggle' => 'tooltip',
											'data-title' => $nameStaffCreate
										]) . ' ' . $nameStaffCreate  ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6 lead-information-col mbot10">
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Chi nhánh: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($production_report['id_branch']) ? get_table_where('tblbranch', ['id' => $production_report['id_branch']], '', 'row')->name : ''?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Bộ phận: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['name_departments']?></span>
                                </div>
                                <div class="wap-content firt" style="display: flex;align-items: center">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mã công việc: </span>
                                    <span class="bold font-medium-xs lead-name" style="margin-left: 10px">
                                        <div>
                                            <label style="font-weight: 500">Lọc Mã Công Việc Theo Chức Vụ</label><br>
                                            <?=!empty($production_report['name_role']) ? $production_report['name_role'] : '-'?>
                                        </div>
                                        <div>
                                            <label style="font-weight: 500">Mã Công Việc</label><br>
                                            <?=!empty($production_report['code_category_task']) ? $production_report['code_category_task'] : '-'?>
                                        </div>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Công đoạn phát hiện sự cố: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['stage']?></span>
                                </div>

                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người bàn giao: </span>
                                    <span class="bold font-medium-xs lead-name">
                                       <?php
                                       if(!empty($production_report['staff_handover'])) {
                                           echo staff_profile_image($production_report['staff_handover'], array('staff-profile-image-small mright5'), 'small', array(
                                               'data-toggle' => 'tooltip',
                                               'data-title' => (!empty($production_report['staff_handover']) ?  $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                   ->get_where('tblstaff', ['staffid' => $production_report['staff_handover']])->row('fullname') : '')

                                           ));
                                       }
                                       ?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người nhận bàn giao: </span>
                                    <span class="bold font-medium-xs lead-name">
                                       <?php
                                       if(!empty($production_report['handler'])) {
                                           foreach($production_report['handler'] as $key => $value) {
                                               echo staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                                                   'data-toggle' => 'tooltip',
                                                   'data-title' => (!empty($value['staff_id']) ?  $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
                                                       ->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname') : '')

                                               ));
                                           }
                                       }
                                       ?>
                                    </span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người giám sát - báo cáo: </span>
                                    <span class="bold font-medium-xs lead-name">
                                       <?php
									   if(!empty($production_report['assigned'])) {
										   foreach($production_report['assigned'] as $key => $value) {
											   echo staff_profile_image($value['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
												   'data-toggle' => 'tooltip',
												   'data-title' => (!empty($value['staff_id']) ?  $this->db->select('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""), " - ", COALESCE(code, "")) as fullname')
												                                                            ->get_where('tblstaff', ['staffid' => $value['staff_id']])->row('fullname') : '')

											   ));
										   }
									   }
									   ?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">KPIs (lần): </span>
                                    <span class="bold font-medium-xs lead-name"><?=$production_report['quantity_kpi']?></span>
                                </div>
                                <a href="<?=admin_url('production_report/detail/' . $production_report['id'])?>" class="btn btn-info pull-right mtop10">Sửa</a>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table dataTable">
                            <thead>
                            <tr>
                                <th>Nội Dung KPH</th>
                                <th>Hành Động Xử Lý Lập Tức</th>
                                <th>Quy Trình Xử Lý</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="width: 500px;">
                                    <b>Mô tả sự KPH:</b> <?=!empty($production_report['described']) ? $production_report['described'] : '-' ?>
                                    <div class="clearfix"></div>
                                    <div class="row mtop10">
                                        <?php if (!empty($images)) { ?>
                                            <?php foreach($images as $key => $image) {?>
                                                <div class="col-md-4">
                                                    <img src="<?= base_url($image['file_name']); ?>" style="width:100%;height:150px;">
                                                </div>
                                            <?php }?>
                                        <?php } ?>
                                    </div>
                                </td>
<!--                                <td><b>Thời điểm ghi nhận:</b> --><?//=_dt($production_report['time_of_recording'])?><!--</td>-->
                                <td rowspan="3">
                                    <div><b>Chấp nhận :</b> <?=(!empty($production_report['action_now_1']) ? $imgcheck : $imgnocheck)?></div><br/>
                                    <div><b>Loại bỏ :</b> <?=(!empty($production_report['action_now_2']) ? $imgcheck : $imgnocheck)?></div><br/>
                                    <div><b>Làm lại :</b> <?=(!empty($production_report['action_now_3']) ? $imgcheck : $imgnocheck)?></div><br/>
                                    <div><b>Khác :</b> <?=(!empty($production_report['action_now_4']) ? $imgcheck : $imgnocheck)?></div><br/>
                                </td>
                                <td rowspan="1">
                                    <b>Nguyên nhân:</b>
                                    <hr class="mtop5 mbot5"/>
                                    <div class="col-md-12">
                                        <div class="mtop10"><b><u>Nguyên phụ liệu (Material)</u></b></div>
                                        <?php if(!empty($production_report['material'])) {?>
                                            <?php foreach($production_report['material'] as $key => $value) {?>
<!--                                                <div class="mleft10"> --><?//=(!empty($value['ischeck']) ? $imgcheck : $imgnocheck)?><!--<b>--><?//=$value['name']?><!--</b> </div>-->
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="material_<?=$key?>"  onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="material_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="mtop10"><b><u>Nhân lực (Man)</u></b></div>
                                        <?php if(!empty($production_report['man'])) {?>
                                            <?php foreach($production_report['man'] as $key => $value) {?>
<!--                                                <div class="mleft10"> --><?//=(!empty($value['ischeck']) ? $imgcheck : $imgnocheck)?><!--<b>--><?//=$value['name']?><!--</b> </div>-->
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="man_<?=$key?>"  onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="man_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="mtop10"><b><u>Máy móc (Machine)</u></b></div>
                                        <?php if(!empty($production_report['machine'])) {?>
                                            <?php foreach($production_report['machine'] as $key => $value) {?>
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="machine_<?=$key?>" onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="machine_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="mtop10"><b><u>Phương pháp (Method)</u></b></div>
                                        <?php if(!empty($production_report['method'])) {?>
                                            <?php foreach($production_report['method'] as $key => $value) {?>
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="method_<?=$key?>" onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="method_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="mtop10"><b><u>Môi trường (Environment)</u></b></div>
                                        <?php if(!empty($production_report['environment'])) {?>
                                            <?php foreach($production_report['environment'] as $key => $value) {?>
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="environment_<?=$key?>" onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="environment_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Số lượng hàng hư :</b> <?=number_format_data($production_report['quantity'])?></td>
                                <td rowspan="2">
                                    <b>Quy trình khắc phục, phòng ngừa :</b>
                                    <hr class="mtop5 mbot5"/>
                                    <div class="col-md-12">
                                        <?php if(!empty($production_report['procedure'])) {?>
                                            <?php foreach($production_report['procedure'] as $key => $value) {?>
                                                <div class="mleft10 mtop10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" id="procedure_<?=$key?>" onclick="changeStatus(<?=$value['id']?>, this)" value="1" <?=(!empty($value['ischeck']) ? 'checked' : '')?>>
                                                        <label for="procedure_<?=$key?>"><?=$value['name']?></label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?= !empty($production_report['note_fix']) ? $production_report['note_fix'] : '' ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space: break-spaces;"><b>Ghi chú:</b> <?=$production_report['note']?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-default" target="_blank" href="<?=admin_url('production_report/pdf/' . $production_report['id'])?>"><i class="fa fa-print" aria-hidden="true"></i> <?php echo _l('in'); ?></a>
                <button class="btn btn-danger" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#production_report_modal').modal('show');

    function changeStatus(id, _this) {
        var status = 0;
        if($(_this).prop('checked') == true) {
            status = 1;
        }
        $.get(admin_url + 'production_report/changeIscheck/' + id + '/' + status, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
        })
    }

    $('#cproduction_report_modal').on('hidden', function () {
        if(typeof TableData != 'undefined') {
            TableData.draw('page');
        }
    });
</script>
