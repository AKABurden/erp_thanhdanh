<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    .text_checklist {
        position: absolute;
        resize: none;
        overflow: hidden;
        left: 25px;
        top: 0;
        font-size: 14px;
        width: 90%;
        border-radius: 3px;
        border: 0;
        outline: 0;
        padding-left: 5px;
    }
    .font-medium-12 {
        font-size: 12px !important;
    }
</style>
<?php echo form_open($this->uri->uri_string(), array('id' => 'form-production_report')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?= lang('info') ?></h3>
					</div>
                    <?php if(!empty($id_delivery_records)) {?>
                            <input type="hidden" name="id_delivery_records" value="<?=$id_delivery_records?>">
					        <?php if(!empty($id_delivery_records_detail)) {?>
                            <input type="hidden" name="id_delivery_records_detail" value="<?=$id_delivery_records_detail?>">
                            <?php } ?>
                    <?php } ?>
					<div class="panel-body">
						<table class="tnh-tb table-bordered table-hover">
							<tbody>
							<tr>
								<td style="width: 15%;"><label for="date">Ngày</label></td>
								<td style="width: 35%;">
									<?php $value = !empty($production_report) ? _dt($production_report->date) : ''?>
									<?= form_input('date', !empty($value) ? $value : date('d/m/Y H:i:s'), 'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
								</td>
                                <td><?= lang('Bộ phận', 'id_departments') ?></td>
                                <td>
                                    <?php $value = !empty($production_report) ? $production_report->id_departments : ''?>
									<?= render_select('id_departments', (!empty($departments) ? $departments : []), ['departmentid', 'name'], '', $value) ?>
                                </td>
							</tr>
                            <tr>
                                <td style="width: 15%;">
                                    <label for="name">Tên phiếu</label>
                                </td>
                                <td style="width: 35%;">
									<?php $value = !empty($production_report) ? $production_report->name_report : ''?>
									<?= form_input('name_report', $value, 'id="name_report" class="form-control" placeholder="' . lang('Tên phiếu') . '" required ') ?>
                                </td>
                                <td style="width: 15%;">
                                    <label for="category_tasks">Mã công việc</label>
                                </td>
                                <td style="width: 35%;">
                                    <?php if(!empty($data_tasks)) {?>
                                        <input type="hidden" name="category_tasks" id="category_tasks" value="<?=$data_tasks->category_tasks?>">
                                        <input type="hidden" name="id_tasks" id="id_tasks" value="<?=$data_tasks->id?>">
                                        <div><b><?=$data_tasks->code_category_tasks?></b></div>
                                        <div><i><?=$data_tasks->content_category_tasks?></i></div>
									<?php } else { ?>
                                        <!-- <a class="mbot10" data-toggle="collapse" data-target="#search_role">Lọc mã công việc theo chức vụ</a> -->
										
                                        <?php $selectedVal = !empty($production_report) ? $production_report->role_id : ''?>
                                        <label for="role_id">Lọc mã công việc theo chức vụ</label>
                                        <div id="search_role" class="collapse in form-group">
                                            <div class="mbot20">
                                                <select id="role_id" name="role_id" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                                    <option></option>
													<?php if(!empty($data_roles)) {
														foreach($data_roles as $key => $value) {?>
                                                            <option <?= ($selectedVal == $value['roleid']) ? 'selected' : '' ?> value="<?=$value['roleid']?>"><?=$value['name']?></option>
														<?php }
													}?>
                                                </select>
                                                <!-- <hr/> -->
                                            </div>
                                            <label for="category_tasks">Mã công việc</label>
                                        </div>

										<?php $value = !empty($production_report) ? $production_report->category_tasks : ''?>
									    <?= render_select('category_tasks', (!empty($category_tasks) ? $category_tasks : []), ['id', 'code', 'content'], '', $value) ?>
										<?php $value = !empty($production_report) ? $production_report->id_tasks : ''?>
                                        <input type="hidden" name="id_tasks" id="id_tasks" value="<?=$value?>">
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Lệnh SX Tổng', 'id_production_orders') ?>
                                    </div>
                                </td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->id_production_orders : (!empty($id_production_orders) ? $id_production_orders : '')?>
                                    <input type="text" name="id_production_orders" id="id_production_orders" class="id_production_orders" data-placeholder="<?= lang('Lệnh SX Tổng') ?>" style="width: 100%;" value="<?=$value?>" title="">
                                </td>
                                <td><?= lang('Công đoạn phát hiện', 'production_stage') ?></td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->production_stage : (!empty($id_stage) ? $id_stage : '')?>
									<?= render_select('production_stage', (!empty($stages) ? $stages : []), ['id', 'name'], '', $value) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Đơn hàng bán', 'id_orders') ?>
                                    </div>
                                </td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->id_orders : ''?>
									<?php $value = !empty($default_value) ? $default_value->id_orders : ''?>
                                    <input type="text" name="id_orders" id="id_orders" class="id_orders" data-placeholder="<?= lang('Đơn hàng bán') ?>" style="width: 100%;" value="<?=$value?>" title="">
                                </td>
                                <td><?= lang('Người theo dõi', 'staff_assigned') ?></td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->staff_assigned : []?>
									<?= render_select('staff_assigned[]', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value, ['multiple' => true], [], '', '', false) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang('Nhà cung cấp', 'suppler_id') ?></td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->suppler_id : ''?>
									<?= render_select('suppler_id', (!empty($suppler) ? $suppler : ''), ['id', 'company', 'code'], '', $value) ?>
                                </td>
                                <td><?= lang('Người xử lý', 'staff_handler') ?></td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->staff_handler : []?>
									<?= render_select('staff_handler[]', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value, ['multiple' => true], [], '', '', false) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= lang('Liên quan đến', 'recommended_list_group_id') ?>
                                </td>
                                <td>
                                    <?php
                                    $dtRecommendedListG = $this->recommended_list_model->getRecommendedListParent([0]);
                                    ?>
                                    <div class="form-group">
                                        <select name="recommended_list_group_id" id="recommended_list_group_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Liên quan đến') ?>" class="selectpicker">
                                            <option value=""></option>
                                            <?php if (!empty($dtRecommendedListG)) : ?>
                                                <?php foreach ($dtRecommendedListG as $key => $value) : ?>
                                                    <option <?= ((!empty($production_report->recommended_list_group_id) && $production_report->recommended_list_group_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?= lang('Chi tiết liên quan', 'recommended_list_id') ?>
                                </td>
                                <td>
                                    <?php
                                    $dtRecommendedList = null;
                                    if (!empty($production_report->recommended_list_group_id)) {
                                        $dtRecommendedList = $this->recommended_list_model->getRecommendedListParent([$production_report->recommended_list_group_id]);
                                    }
                                    ?>
                                    <select name="recommended_list_id" id="recommended_list_id" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98" data-placeholder="<?= lang('Chi tiết liên quan') ?>" class="selectpicker">
                                        <option value=""></option>
                                        <?php if (!empty($dtRecommendedList)) : ?>
                                            <?php foreach ($dtRecommendedList as $key => $value) : ?>
                                                <option <?= ((!empty($production_report->recommended_list_id) && $production_report->recommended_list_id == $value['id']) ? 'selected' : '') ?> data-subtext="<?= $value['name'] ?>" value="<?= $value['id'] ?>"><?= $value['code'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            </tr>
							<tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Sản phẩm', 'it_items') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="name_product"><?=!empty($data_product->list_name_items) ? $data_product->list_name_items : ''?></div>
                                </td>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Số lượng', 'quantity_pcs') ?>
                                    </div>
                                </td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->quantity_pcs : ''?>
									<?= form_input('quantity_pcs', $value, 'id="quantity_pcs" class="form-control" placeholder="' . lang('Số lượng') . '"') ?>
                                </td>
							</tr>
							<tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Sự cố', 'id_trouble') ?>
                                    </div>
                                </td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->id_trouble : ''?>
									<?= render_select('id_trouble', (!empty($trouble) ? $trouble : []), ['id', 'code', 'name'], '', $value) ?>
                                    <div id="violation_point"></div>
                                </td>
                                <td rowspan="3">
                                    <div class="form-group">
                                        <label for="departments"></label>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="type_stage_1" name="type_stage_1" value="1" <?=!empty($production_report->type_stage_1) ? 'checked' : ''?>>
                                            <label for="type_stage_1">Chạy mẫu</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="type_stage_2" name="type_stage_2" value="1" <?=!empty($production_report->type_stage_2) ? 'checked' : ''?>>
                                            <label for="type_stage_2">Chạy hàng + Mẫu</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="type_stage_3" name="type_stage_3" value="1" <?=!empty($production_report->type_stage_3) ? 'checked' : ''?>>
                                            <label for="type_stage_3">Chạy hàng</label>
                                        </div>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" id="type_stage_4" name="type_stage_4" value="1" <?=!empty($production_report->type_stage_4) ? 'checked' : ''?>>
                                            <label for="type_stage_4">Chạy bù hàng</label>
                                        </div>
                                    </div>
                                </td>
							</tr>
                            <tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('Chịu trách nhiệm', 'staff_responsible') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $value = !empty($production_report) ? $production_report->responsible_type : '';
                                        $staffChecked = 'checked';
                                        $departmentChecked = '';
                                        if ($value == 'staff') {
                                            $staffChecked = 'checked';
                                        } else if ($value == 'department') {
                                            $departmentChecked = 'checked';
                                        }
                                    ?>
                                    <div class="col-md-6">
                                        <input type="radio" id="responsible_type_staff" name="responsible_type" value="staff" <?= $staffChecked ?>>
                                        <label for="responsible_type_staff"><?= _l('notify_assigned_user') ?></label>
                                    </div>
                                    <div class="col-md-6" style="padding-left: 0 !important;">
                                        <input type="radio" id="responsible_type_department" name="responsible_type" value="department" <?= $departmentChecked ?>>
                                        <label for="responsible_type_department">Bộ phận chịu trách nhiệm</label>
                                    </div>
                                    <div id="staff_responsible_container">
                                        <?php $value = !empty($production_report) ? $production_report->staff_responsible : '' ?>
                                        <?= render_select('staff_responsible', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname'], 'code'], '', $value) ?>
                                    </div>
                                    <div id="department_responsible_container">
                                        <?php $value = !empty($production_report) ? $production_report->department_responsible : '' ?>
                                        <?= render_select('department_responsible', (!empty($departments) ? $departments : []), ['departmentid', 'name'], '', $value) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display: flex;">
										<?= lang('id_branch', 'id_branch') ?>
                                    </div>
                                </td>
                                <td>
									<?php $value = !empty($production_report) ? $production_report->id_branch : (!empty($data_delivery_records->id_branch) ? $data_delivery_records->id_branch : '')?>
									<?= render_select('id_branch', (!empty($branch) ? $branch : []), ['id', 'name'], '', $value) ?>
                                </td>
                            </tr>
							</tbody>
						</table>
                        <table id="tb-productions-orders" class="table table-hover dataTable" style="width: 100%;margin-bottom: 30px!important;">
                            <thead>
                            <tr>
                                <th class="text-center">Nội dung KPH</th>
                                <th class="text-center">Hành động xử lý lập tức</th>
                                <th class="text-center">Nguyên nhân & khắc phục</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="time_of_recording" class="control-label">Thời điểm ghi nhận</label>
											<?php $value = !empty($production_report->time_of_recording) ? _dt($production_report->time_of_recording) : _dt(date('Y-m-d H:i:s'))?>
                                            <input type="text" id="time_of_recording" name="time_of_recording" class="form-control datetimepicker" value="<?=$value?>">
                                        </div>
                                    </td>
                                    <td rowspan="4">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" id="action_now_1" name="action_now_1" value="1" <?=!empty($production_report->action_now_1) ? 'checked' : ''?>>
                                                <label for="action_now_1">Chấp nhận </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" id="action_now_2" name="action_now_2" value="1" <?=!empty($production_report->action_now_2) ? 'checked' : ''?>>
                                                <label for="action_now_2">Loại bỏ</label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" id="action_now_3" name="action_now_3" value="1" <?=!empty($production_report->action_now_3) ? 'checked' : ''?>>
                                                <label for="action_now_3">Làm lại</label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" id="action_now_4" name="action_now_4" value="1" <?=!empty($production_report->action_now_4) ? 'checked' : ''?>>
                                                <label for="action_now_4">Khác</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td rowspan="2">
                                        <div class="ui-sortable">
                                            <h3 class="bold chk-heading th font-medium font-medium-12"><u>Nguyên phụ liệu (Material)</u></h3>
                                            <div class="div_material">
                                                <?php if(!empty($production_report->material)) {?>
                                                        <?php foreach($production_report->material as $key => $value) { ?>
                                                            <div>
                                                                <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                                    <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                        <input type="checkbox" name="checked[material][<?=$key?>]" value="1" <?=!empty($value['ischeck']) ? 'checked' : ''?>>

                                                                        <label for=""><span class="hide"><?=$value['name']?></span></label>
                                                                        <textarea class="text_checklist" name="items[material][<?=$key?>]" rows="1" style="height: 28px;"><?=$value['name']?></textarea>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="id_list[material][<?=$key?>]" value="<?=$value['id']?>">
                                                            </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="ui-sortable">
                                            <h3 class="bold chk-heading th font-medium font-medium-12"><u>Nhân lực (Man)</u></h3>
                                            <div class="div_man">
												<?php if(!empty($production_report->man)) {?>
													<?php foreach($production_report->man as $key => $value) { ?>
                                                        <div>
                                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                    <input type="checkbox" name="checked[man][<?=$key?>]" value="1"  <?=!empty($value['ischeck']) ? 'checked' : ''?>>
                                                                    <label for=""><span class="hide"><?=$value['name']?></span></label>
                                                                    <textarea class="text_checklist" name="items[man][<?=$key?>]" rows="1" style="height: 28px;"><?=$value['name']?></textarea>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="id_list[man][<?=$key?>]" value="<?=$value['id']?>">
                                                        </div>
													<?php } ?>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="ui-sortable">
                                            <h3 class="bold chk-heading th font-medium font-medium-12"><u>Máy móc (Machine)</u></h3>
                                            <div class="div_machine">
												<?php if(!empty($production_report->machine)) {?>
													<?php foreach($production_report->machine as $key => $value) { ?>
                                                        <div>
                                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                    <input type="checkbox" name="checked[machine][<?=$key?>]" value="1" <?=!empty($value['ischeck']) ? 'checked' : ''?>>
                                                                    <label for=""><span class="hide"><?=$value['name']?></span></label>
                                                                    <textarea class="text_checklist" name="items[machine][<?=$key?>]" rows="1" style="height: 28px;"><?=$value['name']?></textarea>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="id_list[machine][<?=$key?>]" value="<?=$value['id']?>">
                                                        </div>
													<?php } ?>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="ui-sortable">
                                            <h3 class="bold chk-heading th font-medium font-medium-12"><u>Phương pháp (Method)</u></h3>
                                            <div class="div_method">
												<?php if(!empty($production_report->method)) {?>
													<?php foreach($production_report->method as $key => $value) { ?>
                                                        <div>
                                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                    <input type="checkbox" name="checked[method][<?=$key?>]" value="1" <?=!empty($value['ischeck']) ? 'checked' : ''?>>
                                                                    <label for=""><span class="hide"><?=$value['name']?></span></label>
                                                                    <textarea class="text_checklist" name="items[method][<?=$key?>]" rows="1" style="height: 28px;"><?=$value['name']?></textarea>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="id_list[method][<?=$key?>]" value="<?=$value['id']?>">
                                                        </div>
													<?php } ?>
												<?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="code" class="control-label">Mô tả sự KHP</label>
											<?php $value = !empty($production_report->described) ? $production_report->described : (!empty($described) ? $described : '')?>
                                            <textarea name="described" id="described" class="form-control" rows="3"><?=$value?></textarea>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="code" class="control-label">Số lượng</label>
											<?php $value = !empty($production_report->quantity) ? $production_report->quantity : (!empty($quantity_err) ? $quantity_err : '')?>
                                            <input type="text" id="quantity" name="quantity" class="form-control" value="<?=$value?>">
                                        </div>
                                    </td>
                                    <td rowspan="2">
                                        <div class="ui-sortable">
                                            <h3 class="bold chk-heading th font-medium font-medium-12"><u>Khắc phục</u></h3>
                                            <div class="div_procedure">
                                                <?php if(!empty($production_report->procedure)) {?>
													<?php foreach($production_report->procedure as $key => $value) { ?>
                                                        <div>
                                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                                    <input type="checkbox" name="checked[procedure][<?=$key?>]" value="1" <?=!empty($value['ischeck']) ? 'checked' : ''?>>
                                                                    <label for=""><span class="hide"><?=$value['name']?></span></label>
                                                                    <textarea class="text_checklist" name="items[procedure][<?=$key?>]" rows="1" style="height: 28px;"><?=$value['name']?></textarea>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="id_list[procedure][<?=$key?>]" value="<?=$value['id']?>">
                                                        </div>
													<?php } ?>
												<?php } ?>
                                            </div>
                                            <div>
                                                <textarea name="note_fix" id="note_fix" placeholder="<?= lang('Khắc phục') ?>" class="form-control note_fix" rows="3"><?= !empty($production_report->note_fix) ? $production_report->note_fix : '' ?></textarea>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="code" class="control-label">Ghi chú</label>
											<?php $value = !empty($production_report->note) ? $production_report->note : ''?>
                                            <textarea name="note" id="note" class="form-control" rows="3"><?=$value?></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
					</div>
				</div>
                <div class="clearfix"></div>
			</div>
		</div>
		<div class="row">
			<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
				<input type="hidden" name="add" id="" class="form-control" value="1">
				<button type="submit" class="btn btn-info only-save customer-form-submiter add">
					<?php echo _l('submit'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<script type="text/javascript">
    appValidateForm($('#form-production_report'), {
        role_id: 'required',
        date: 'required',
        name: 'required',
        id_departments: 'required',
        category_tasks: 'required',
        id_trouble: 'required',
        id_branch: 'required',
        recommended_list_group_id: 'required',
        // id_production_detail: 'required',
        // production_stage: 'required',
        // quantity_pcs: 'required'
    }, ManageProductionReport);

    function ManageProductionReport(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function(data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                if(data.idtask) {
                    init_task_modal(data.idtask);
                    $("#task-modal").addClass('show_add');
                }
                else {
                    window.location = admin_url + 'production_report';
                }
            }
            else {
                $(form).find('button[type="submit"]').removeAttr('disabled');
            }
        })
        .fail(function(err) {
            alert_float('danger', err.responseText);
            $(form).find('button[type="submit"]').removeAttr('disabled');
        });
        return false;
    }
    // init_editor('textarea[name="constitutive"]');
    // init_editor('textarea[name="note"]');
    // })

    // ajaxSelect2Not('#id_production_detail', admin_url + 'production_report/search_production_detail', $('#id_production_detail').val());
    ajaxSelect2Not('#id_production_orders', admin_url + 'production_report/search_production_orders', $('#id_production_orders').val());

    ajaxSelect2Not('#id_orders', admin_url + 'production_report/search_orders', $('#id_orders').val());

    // $('body').on('change', '#id_production_detail', function (data) {
    //     data_production_detail = data.added;
    //     $('.name_product').text(data_production_detail.items_name);
    //     $('#production_stage').html('<option></option>');
    //     $.get(admin_url + 'production_report/get_stage/' + data_production_detail.id_orders_item, function(data) {
    //         data = JSON.parse(data);
    //         $.each(data, function(index, value) {
    //             $('#production_stage').append(`<option value="${value.id}">${value.name}</option>`);
    //         })
    //         $('#production_stage').selectpicker('refresh');
    //     })
    // })

    $('body').on('change', '#id_production_orders', function (data) {
        data_production_orders = data.added;
        $('.name_product').html(data_production_orders.list_items_name);
    })

    function ajaxSelect2Not(element, url, id, types = '', dataGet = '') {
        if (id != "") {
            var DataSelect = {
                width: 'resolve',
                placeholder: 'Vui lòng chọn',
                allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: url + '/' + $(element).val() + (types ? ('/' + types) : '') + dataGet,
                        dataType: "json",
                        success: function (data) {
                            callback(data.results);
                        }
                    });
                },
                ajax: {
                    url: url + dataGet,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoDontFormat,
                formatSelection: repoDontFormat,
                escapeMarkup: function (m) {
                    return m;
                }
            };


            $(element).val(id).select2(DataSelect);
        } else {
            var DataSelect = {
                width: 'resolve',
                placeholder: 'Vui lòng chọn',
                allowClear: true,
                ajax: {
                    url: url + dataGet,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: repoDontFormat,
                formatSelection: repoDontFormat,
                escapeMarkup: function (m) {
                    return m;
                }
            };


            $(element).select2(DataSelect);
        }
    }

    function repoDontFormat(state) {
        if (!state.id) return state.code;
        var name = '<div><b>'+state.code+'</b></div>';
        if(state.items_code) {
            name += '<div><b>Mã SP:</b> <i>'+state.items_code+'</i></div>';
        }
        if(state.items_name) {
            name += '<div><b>Tên SP:</b><i>'+state.items_name+'</i></div>';
        }
        if(state.list_items_name) {
            name += '<div><b>Sản Phẩm:</b><br/> <i>'+state.list_items_name+'</i></div>';
        }
        if(state.customer_name) {
            name += '<div><b>Khách hàng :</b> <i>'+state.customer_name+'</i></div>';
        }
        return  name;
    }

    $('#id_trouble').change(function() {
        var id_trouble = $(this).val();
        $.get(admin_url + 'trouble/get_trouble/' + id_trouble, function(result) {
            result = JSON.parse(result);
            $('.div_material').html('');
            if(result.material) {
                $.each(result.material, function (index, value) {
                    $('.div_material').append(`<div>
                                                <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                    <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                        <input type="checkbox" name="checked[material][${index}]" value="1">
                                                        <label for=""><span class="hide">${value.name}</span></label>
                                                        <textarea class="text_checklist" name="items[material][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                    </div>
                                                </div>
                                            </div>`);
                })
            }

            $('.div_man').html('');
            $.each(result.man, function(index, value) {
                $('.div_man').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[man][]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[man][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })

            $('.div_machine').html('');
            $.each(result.machine, function(index, value) {
                $('.div_machine').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[machine][${index}]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[machine][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })

            $('.div_method').html('');
            $.each(result.method, function(index, value) {
                $('.div_method').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[method][]" value="1">
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[method][]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })

            $('.div_procedure').html('');
            $.each(result.procedure, function(index, value) {
                $('.div_procedure').append(`<div>
                                            <div class="checklist relative ui-sortable-handle" style="height: 38px;">
                                                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="">
                                                    <input type="checkbox" name="checked[procedure][${index}]" value="1" checked>
                                                    <label for=""><span class="hide">${value.name}</span></label>
                                                    <textarea class="text_checklist" name="items[procedure][${index}]" rows="1" style="height: 28px;">${value.name}</textarea>
                                                </div>
                                            </div>
                                        </div>`);
            })

            if (result.violation_name != null) {
                var violation_color = '#0d6efd';
                if (result.violation_name == 'Nhắc nhở') {
                    violation_color = '#ffc107';
                } else if (result.violation_name == 'Khiển trách') {
                    violation_color = '#fd7e14';
                } else if (result.violation_name == 'Cảnh báo') {
                    violation_color = '#fd7e14';
                }
                $('#violation_point').html('<span class="inline-block label mleft5 mtop5" style="font-size: 12px;color: '+violation_color+';border:1px solid '+violation_color+'">'+result.violation_name+' (trừ '+result.violation_point+' điểm)</span>');
            } else {
                $('#violation_point').html('');
            }
        })
    })

    $("#task-modal").on('hidden.bs.modal', function (e) {
        if($(this).hasClass('show_add')) {
            $("#task-modal").removeClass('show_add');
            window.location = admin_url + 'production_report';
        }
    });


    $('#id_departments').change(function() {
        var id_departments = $(this).val();
        $.get(admin_url + 'production_report/get_list_role/' + id_departments, function(data) {
            data = JSON.parse(data);
            $('#role_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#role_id').append(`<option value="${value.roleid}">${value.name}</option>`);
            })
            $('#role_id').selectpicker('refresh');
            $('#role_id').trigger('change');
        })
    })

    $('#role_id').change(function() {
        var role_id = $('#role_id').val();
        var id_departments = $('#id_departments').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['role_id'] = role_id;
        data['id_departments'] = id_departments;
        $.post(admin_url + 'production_report/get_list_category_tasks', data, function(data) {
            data = JSON.parse(data);
            $('#category_tasks').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#category_tasks').append(`<option value="${value.id}" data-subtext="${value.content}">${value.code}</option>`);
            })
            $('#category_tasks').selectpicker('refresh');
        })

    })

    $('#recommended_list_group_id').change(function() {
        var recommended_list_group_id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['parent_id'] = recommended_list_group_id;

        $.post(admin_url + 'production_report/getRecommendedListByParent', data, function(data) {
            data = JSON.parse(data);
            $('#recommended_list_id').html(`<option></option>`);
            $.each(data, function(index, value) {
                $('#recommended_list_id').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
            })
            $('#recommended_list_id').selectpicker('refresh');
        });
    });

    changeResponsibleType();
    $("[name='responsible_type']").on("change", function() {
        changeResponsibleType();
    });
    function changeResponsibleType() {
        var value = $("input[name='responsible_type']:checked").val();
        if (value == "staff") {
            $("#department_responsible_container").addClass("hide");
            // $("#department_responsible").prop("disabled", true);
            $("#department_responsible").prop("required", false);
            $('#department_responsible').val('default').selectpicker('deselectAll');
            $('#department_responsible').selectpicker('refresh');

            // $("#staff_responsible").prop("disabled", false);
            $("#staff_responsible_container").removeClass("hide");
            $("#staff_responsible").prop("required", true);
        } else if (value == "department") {
            $("#staff_responsible_container").addClass("hide");
            // $("#staff_responsible").prop("disabled", true);
            $("#staff_responsible").prop("required", false);
            $('#staff_responsible').val('default').selectpicker('deselectAll');
            $('#staff_responsible').selectpicker('refresh');

            // $("#department_responsible").prop("disabled", false);
            $("#department_responsible_container").removeClass("hide");
            $("#department_responsible").prop("required", true);
        }
        console.log("Giá trị mới: " + value);
    };
</script>
