<div class="modal fade" id="modal_view_maintenance" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%;">
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
                            <div class="col-md-12 lead-information-col mbot10">
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Ngày: </span>
                                    <span class="bold font-medium-xs lead-name"><?=_dt($maintenance_ticket['date'])?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Phiếu bảo trì: </span>
                                    <span class="bold font-medium-xs lead-name"><?= $maintenance_ticket['name'] ?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Mã công việc: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($maintenance_ticket['code_category_task']) ? $maintenance_ticket['code_category_task'] : '-'?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Thiết bị: </span>
                                    <span class="bold font-medium-xs lead-name"><?=!empty($machines->name) ? $machines->name : '-'?></span>
                                </div>
                                <?php $html_maintenance = [];?>
                                <?php if(!empty($maintenance_ticket['items_ticket'])) {
                                    foreach($maintenance_ticket['items_ticket'] as $key => $value) {
										$html_maintenance[] = $value['name_maintenance'] .' - '. _dC($value['date']);
                                    }
								} ?>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Bộ phận: </span>
                                    <div class="clearfix"></div>
                                    <span class="bold font-medium-xs lead-name"><div class="col-md-12"><?=implode("<br>", $html_maintenance)?></div></span>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Ngày bảo trì theo lịch trình: </span>
                                    <span class="bold font-medium-xs lead-name"><?=_d($maintenance_ticket['date_maintenance'])?></span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Tổng số lượng: </span>
                                    <span class="bold font-medium-xs lead-name"><?=$maintenance_ticket['quantity_pcs']?></span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">Người tạo: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php $nameStaffCreate = (!empty($maintenance_ticket['create_by']) ? get_staff_full_name($maintenance_ticket['create_by']) : '');?>
										<?php echo staff_profile_image( (!empty($maintenance_ticket['create_by']) ? $maintenance_ticket['create_by'] : ''), [
												'staff-profile-image-small'], 'small',[
												'data-toggle' => 'tooltip',
												'data-title' => $nameStaffCreate
											]) . ' ' . $nameStaffCreate  ?>
                                    </span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">File ghi chú bảo trì: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?php
                                        $tasks = get_table_where('tbltasks', ['rel_type' => 'maintenance_ticket', 'rel_id' => $maintenance_ticket['id']] , '', 'row');
										$getFile = get_table_where('tblfiles', ['rel_type' => 'task', 'rel_id' => $tasks->id]);
										$viewFile = '';
										foreach($getFile as $k => $v) {
											if(!empty($getFile)) {
												if(explode('/', $v['filetype'])[0] == 'image') {
													$viewFile .= '<div class="url_file mtop10" style="margin-bottom:5px; margin-top:5px;">
                                                                                <div class="preview_image" style="width: auto;margin-bottom: 5px; margin-top: 5px;">		
                                                                                    <div class="display-block contract-attachment-wrapper img">
                                                                                        <span class="float-left">
                                                                                            <a href="'.base_url('download/file/taskattachment/' . $v['attachment_key']).'" data-lightbox="customer-profile" class="display-block mbot5 col-md-9">	
                                                                                                <div class="">		                     
                                                                                                   <i class="fa fa-file-image-o" aria-hidden="true"></i> '.$v['file_name'].'
                                                                                                </div>		                             
                                                                                            </a>
                                                                                        </span>	
                                                                                    </div>		           
                                                                                </div>
                                                                                <hr class="mtop5 mbot5"/>
                                                                            </div>';
												}
												else {
													$viewFile .= '<div class="url_file mtop10">
                                                                                <a class="col-md-9" target="_blank" href="' . base_url('download/file/taskattachment/' . $v['attachment_key']) . '"><i class="fa fa-file-archive-o" aria-hidden="true"></i> ' . $v['file_name'] . '</a> 
                                                                                <div class="clearfix"></div>
                                                                                <hr class="mtop5 mbot5"/>
                                                                             </div>';
												}
											}
										}
                                        echo $viewFile;
										?>
                                    </span>
                                </div>
                                <div class="wap-content second">
                                    <span class="text-muted lead-field-heading no-mtop bold">loại bảo trì: </span>
                                    <span class="bold font-medium-xs lead-name">
                                        <?=(!empty($type[$maintenance_ticket['type']]) ? $type[$maintenance_ticket['type']] : '-');?>
                                    </span>
                                </div>
                                <div class="wap-content firt">
                                    <span class="text-muted lead-field-heading no-mtop bold">Ghi chú cách thức bảo trì: </span>
                                    <span class="bold font-medium-xs lead-name" style="white-space: pre-line;">
                                        <?=(!empty($maintenance_ticket['note_main']) ? $maintenance_ticket['note_main'] : '');?>
                                    </span>
                                </div>
                                <div class="wap-content mtop20">
                                    <table class="table tb-view dataTable">
                                        <thead>
                                            <tr style="background: #cedae6;">
                                                <td style="width: 15%;" class="text-center">Mã hạng mục bảo trì</td>
                                                <td style="width: 20%;" class="text-center">Tên hạng mục bảo trì</td>
                                                <td style="width: 15%;" class="text-center">Đạt</td>
                                                <td style="width: 15%;" class="text-center">Không Đạt</td>
                                                <td style="width: 20%;" class="text-center">Nhân viên đánh giá</td>
                                                <td style="width: 15%;" class="text-center">Báo cáo sự cố</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($category_maintenance)) {?>
                                                <?php foreach($category_maintenance as $key => $value) {?>
                                                    <tr>
                                                        <td><?=$value['code']?></td>
                                                        <td><?=$value['name']?></td>
													    <?php if(is_admin() || empty($value['active']) && (!empty($value['active']) && $value['staff_active'] == get_staff_user_id())) {?>
                                                            <td class="text-center">
                                                                <div class="checkbox checkbox-info">
                                                                    <input type="checkbox" class="category_active" data-id="<?=$value['id']?>" name="active[<?=$key?>]" <?=(!empty($value['active'] && $value['active'] == 1) ? 'checked' : '')?> id="category_active_<?=$key?>" value="1">
                                                                    <label for="category_active_<?=$key?>"></label>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="checkbox checkbox-info">
                                                                    <input type="checkbox" class="category_active" data-id="<?=$value['id']?>" name="active[<?=$key?>]"  <?=(!empty($value['active'] && $value['active'] == 2) ? 'checked' : '')?> id="category_unactive_<?=$key?>" value="2">
                                                                    <label for="category_unactive_<?=$key?>"></label>
                                                                </div>
                                                            </td>
                                                        <?php } else {?>
                                                            <td class="text-center">
																<?=$value['active'] == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?>
                                                            </td>
                                                            <td class="text-center">
																<?=$value['active'] == 2 ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?>
                                                            </td>
														<?php } ?>
                                                        <td class="text-center">
                                                            <div class="staff_active"><?=!empty($value['staff_active']) ? get_staff_full_name($value['staff_active']) : '-'?></div>
                                                            <div class="date_active"><?=!empty($value['date_active']) ? _dt($value['date_active']) : ''?></div>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-info btn-icon mbot10" href="<?=admin_url('production_report/detail?maintenance='.$maintenance_ticket['id'].'&id_category='.$value['id_category'])?>" target="_blank">Tạo phiếu báo cáo</a>
                                                        </td>
                                                    </tr>
												<?php } ?>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-default" target="_blank" href="<?=admin_url('maintenance/pdf/' . $maintenance_ticket['id'])?>"><i class="fa fa-print" aria-hidden="true"></i> <?php echo _l('in'); ?></a>
                <button class="btn btn-danger" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    init_selectpicker('refresh');
    $('#modal_view_maintenance').modal('show');

    $('.category_active').change(function() {
        var tr = $(this).parents('tr');

        var id = $(this).data('id');
        active = null;
        if($(this).prop('checked')) {
            var active = $(this).val();
        }
        $(tr).find('.category_active').prop('checked', false);

        if(active) {
            $(tr).find(`.category_active[value="${active}"]`).prop('checked', true);
        }
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['active'] = active;
        $.post(admin_url + 'maintenance/update_maintenance_stick_category', data, function(result) {
            result = JSON.parse(result);
            if(result.success) {
                tr.find('.staff_active').html(result.staff_active);
                tr.find('.date_active').html(result.date_active);
            }
        })
    })
</script>