<div id="view_modal_records" class="modal fade" role="dialog">
<style>
    .tb-view tbody tr td {
        border-top: 1px solid #cedae6 !important;
    }
</style>
    <div class="modal-dialog modal-lg" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="tb-view table table-hover dataTable">
                            <tbody>
                                <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_info_general') ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;"><?= lang('tnh_reference_no_delivery_records', 'reference_no_delivery_records') ?></td>
                                    <td style="width: 35%;">
                                        <?=(!empty($delivery_records) ? $delivery_records['reference_no'] : '')?>
                                    </td>
                                    <td style="width: 15%;">
                                        <?= lang('tnh_date_delivery_records', 'date_delivery_records') ?>
                                    </td>
                                    <td style="width: 35%;">
                                        <?= !empty($delivery_records) ? _d($delivery_records['date']) : ''?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 15%;"><?= lang('Hạng mục bàn giao', 'Hạng mục bàn giao') ?></td>
                                    <td style="width: 35%;">
                                        <?=(!empty($delivery_records['name_type_object']) ? $delivery_records['name_type_object'] : '')?>
                                    </td>
                                    <td colspan="2"  style="width: 50%;">
                                        <?php if(!empty($delivery_records['records_object'])) {
                                            $arrayValue = [];
                                            foreach($delivery_records['records_object'] as $key => $value) {
												$rel_data = get_relation_data($delivery_records['type_object'], $value['id_object']);
												$rel_val = get_relation_values($rel_data, $delivery_records['type_object']);
                                                $arrayValue[] = $rel_val['name'];
                                            }
                                            echo implode('<br>', $arrayValue);
                                        }?>
                                    </td>
                                </tr>
                                <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_info_staff') ?></td>
                                </tr>
                                <tr>
                                    <?php
                                    $staffc = 0;
                                    $disabled = '';
                                    $module_category_hand_overc = '';
                                    $dtStaff = [];
                                    if ($delivery_records) {
                                        $dtStaff = $this->site_model->getStaffByStaffId($delivery_records['staff']);
                                    } else {
                                        if ($id_import) {
                                            $staffc = get_staff_user_id();
                                            $disabled = 'none-event';
                                            $module_category_hand_overc = 3;
                                        }
                                    }
                                    ?>
                                    <td><?= lang('staff', 'staff_delivery_records') ?></td>
                                    <td>
                                        <?=!empty($delivery_records['staff']) ? get_staff_full_name($delivery_records['staff']) : NULL?>
                                    </td>
                                    <td><?= lang('Người nhận bàn giao', 'receiver') ?></td>
                                    <td class="txt-department">
										<?=!empty($delivery_records['receiver']) ? get_staff_full_name($delivery_records['receiver']) : NULL?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang('id_branch', 'id_branch') ?></td>
                                    <td class="txt-department">
                                        <?=!empty($delivery_records['id_branch']) ? get_table_where('tblbranch', ['id' => $delivery_records['id_branch']], '', 'row')->name : ''?>
                                    </td>
                                </tr>
                                <tr class="text-center bold uppercase">
                                    <td colspan="4"><?= lang('tnh_content_delivery_records') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="1">
                                        <?= lang('Loại bàn giao', 'category_hand') ?>
                                    </td>
                                    <td colspan="3">
                                        <?=get_table_where('tbl_category_hand_over', ['id' => $delivery_records['category_hand']], '', 'row')->name?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="view-hand_over">
                                        <table id="tb-task" class="table tb-view dataTable">
                                            <thead>
                                                <tr style="background: #cedae6;">
                                                    <td style="width: 15%;" class="text-center"><?= lang('Công đoạn') ?></td>
                                                    <td style="width: 15%;" class="text-center"><?= lang('Nội dung bàn giao') ?></td>
                                                    <td style="width: 15%;" class="text-center"><?= lang('Tiêu chuẩn') ?></td>
                                                    <td style="width: 15%;" class="text-center"><?= lang('Phương thức') ?></td>
                                                    <td style="width: 5%;" class="text-center"><?= lang('Đạt') ?></td>
                                                    <td style="width: 5%;" class="text-center"><?= lang('Không đạt') ?></td>
                                                    <td style="width: 15%;" class="text-center"><?= lang('Nhân viên đánh giá') ?></td>
                                                    <td style="width: 15%;" class="text-center"><?= lang('Báo cáo sự cố') ?></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
												$deliveryRecordsTask = $this->hand_over_model->getDeliveryRecordsId($delivery_records['id']);
												$trTask = '';
												$delivery_records_task_id = 0;
												if (!empty($deliveryRecordsTask)) {
													foreach ($deliveryRecordsTask as $kTT => $vTT) {?>
														<tr class="rowTr">
                                                            <td><?=$vTT['code_stage']?></td>
                                                            <td><?=$vTT['name']?></td>
                                                            <td><?=$vTT['standard']?></td>
                                                            <td><?=$vTT['method']?></td>
															<?php if(is_admin() || empty($vTT['task_hand_over_qualified']) || (!empty($vTT['task_hand_over_qualified']) && $vTT['staff_id'] == get_staff_user_id())) {?>
                                                                <td class="text-center">
                                                                    <div class="checkbox checkbox-info">
                                                                        <input type="checkbox" class="radio_check_hand_over" data-id="<?=$vTT['id']?>" name="task_hand_over[<?=$kTT?>]" <?=$vTT['task_hand_over_qualified'] == 1 ? 'checked' : ''?>  id="task_hand_over_qualified_<?=$kTT?>" value="1">
                                                                        <label for="task_hand_over_qualified_<?=$kTT?>"></label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="checkbox checkbox-info">
                                                                        <input type="checkbox" class="radio_check_hand_over" data-id="<?=$vTT['id']?>" name="task_hand_over[<?=$kTT?>]" <?=$vTT['task_hand_over_qualified'] == 2 ? 'checked' : ''?> id="task_hand_over_un_qualified_<?=$kTT?>" value="2">
                                                                        <label for="task_hand_over_un_qualified_<?=$kTT?>"></label>
                                                                    </div>
                                                                </td>
                                                            <?php } else {?>
                                                                <td class="text-center">
																	<?=$vTT['task_hand_over_qualified'] == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?>
                                                                </td>
                                                                <td class="text-center">
																	<?=$vTT['task_hand_over_qualified'] == 2 ? '<i class="fa fa-check" aria-hidden="true"></i>' : ''?>
                                                                </td>
															<?php }?>
                                                            <td>
                                                                <div class="staff_active"><?=(!empty($vTT['staff_id']) ? get_staff_full_name($vTT['staff_id']) : '')?></div>
                                                                <div class="date_active"><?=(!empty($vTT['date_check']) ? _dt($vTT['date_check']) : '')?></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <a class="btn btn-info btn-icon mbot10" href="<?=admin_url('production_report/detail?id_delivery_records='.$delivery_records['id'].'&id_delivery_records_detail='.$vTT['id'])?>" target="_blank">Tạo phiếu báo cáo</a>
                                                                <?php
                                                                    $this->db->where('id_delivery_records', $delivery_records['id']);
                                                                    $this->db->where('id_delivery_records_detail', $vTT['id']);
																    $delivery_records_detail = $this->db->get('tblproduction_report')->result_array();
                                                                    if(!empty($delivery_records_detail)) {
                                                                        foreach($delivery_records_detail as $k => $v) {
                                                                            echo '<span class="label label-info pull-left mbot10" style="padding-top: 1px; padding-bottom: 1px;"><a class="c_modal" href="' . admin_url('production_report/modal/' . $v['id']) . '">' . $v['name_report'] . ' - ' . _dt($v['date']) . '</a></span>';
																		}
																	}
                                                                ?>
                                                            </td>
                                                        </tr>
													<?php }
												}
                                                ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
						<?php if (!empty($files)) { ?>
                            <hr/>
                            <h4 class="mtop30">Tập tin đính kèm</h4>
                            <div class="clearfix"></div>
                            <div class="fild-content mtop10">
								<?php foreach($files as $keyFile => $valFile) {?>
									<?php if(explode('/',$valFile->filetype)[0] == 'image'){ ?>
                                        <div class="mtop5 mbot5 rowData">
                                            <div class="preview_image" style="width: auto;">
                                                <div class="display-block contract-attachment-wrapper img">
                                                    <a class="pull-right text-danger" onclick="removeFile(<?=$valFile->id?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                    <div style="width:150px;">
                                                        <a href="<?=base_url('uploads/delivery_records/'.$delivery_records['id'].'/'.$valFile->file_name)?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                            <div class="">
                                                                <img src="<?=base_url('uploads/delivery_records/'.$delivery_records['id'].'/'.$valFile->file_name)?>" style="max-height: 100px">
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
									<?php } else { ?>
                                        <div class="mtop5 mbot5 rowData">
                                            <a  target="_blank" href="<?=base_url('uploads/delivery_records/' . $delivery_records['id'] . '/'.$valFile->file_name)?>"><i class="fa fa-file-archive-o"></i> <?= $valFile->file_name ?></a>
                                            <a class="pull-right text-danger" onclick="removeFile(<?=$valFile->id?>, this)"><i class="fa fa-times" aria-hidden="true"></i></a>
                                        </div>
									<?php } ?>
								<?php }
								?>
                            </div>
                            <div class="clearfix"></div>
						<?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#view_modal_records').modal('show');

    $('.radio_check_hand_over').change(function() {
        var tr = $(this).parents('tr.rowTr');
        var id = $(this).data('id');
        var task_hand_over_qualified = $(this).val();
        var name = $(this).attr('name');
        if( $(this).prop('checked') == true) {
            $(`input[name="${name}"]`).prop('checked', false);
            $(this).prop('checked', true);
        }
        else {
            task_hand_over_qualified = 0;
        }

        $.get(admin_url + 'hand_over/check_hand_over_qualified/' + id + '/' + task_hand_over_qualified, function(result) {
            result = JSON.parse(result);
            alert_float(result.alert_type, result.message);
            if(result.success) {
                console.log(tr.find('.staff_active'))
                tr.find('.staff_active').html(result.data.fullname);
                tr.find('.date_active').html(result.data.date_check);
            }
        })
    })
    function removeFile(id, _this) {
        if(confirm('Bạn có chắc muốn xóa file?')) {
            $.get(admin_url + 'hand_over/removeFile/' + id, function (result) {
                result = JSON.parse(result);
                if (result.success) {
                    $(_this).parents('.rowData').remove();
                }
            })
        }
    }
</script>