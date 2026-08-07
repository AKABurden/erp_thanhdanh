<div id="modal_view_evaluete" class="modal fade" role="dialog">
    <style>
        .bg-primary .checkbox label {
          color: white;
        }
    </style>
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content" id="view_suggest_check">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mbot10">
                    </div>
                    <div class="col-md-6">
                        <div class="lead-view">
                            <div class="row-contro">
                                <div><?= lang('tnh_date_creted') ?>: </div>
                                <div class="ml-at t-bold"><?= !empty($suggest_test->date) ? _dt($suggest_test->date) : '' ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('Số phiếu yêu cầu đề xuất') ?>: </div>
                                <div class="ml-at t-bold"><?= $suggest_test->code ? $suggest_test->code : '' ?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('Số phiếu yêu cầu đánh giá') ?>: </div>
                                <div class="ml-at t-bold"><?= $suggest_test->code_evaluate ? $suggest_test->code_evaluate : '' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="lead-view">
                            <div class="row-contro">
                                <div><?= lang($type_object['name_object']) ?>: </div>
                                <div class="ml-at t-bold"><?= !empty($suggest_test->company) ?  $suggest_test->company : '-'?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang($type_object['name_po']) ?>: </div>
                                <div class="ml-at t-bold"><?= !empty($suggest_test->code_purchase_order) ?  $suggest_test->code_purchase_order : '-'?></div>
                            </div>
                            <div class="row-contro">
                                <div><?= lang('Ghi chú') ?>: </div>
                                <div class="ml-at t-bold"><?= !empty($suggest_test->note) ? $suggest_test->note : '' ?></div>
                            </div>
    
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="wap-content mtop20">
                            <table class="table dataTable">
                                <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">STT</th>
                                    <th rowspan="2" class="text-center">Danh mục kiểm tra</th>
                                    <th rowspan="2" class="text-center">Tiêu Chuẩn</th>
                                    <th rowspan="2" class="text-center">Công Cụ</th>
                                    <th colspan="5" class="text-center">Kết Quả Mẫu</th>
                                    <th rowspan="2" class="text-center">Kết Quả (Đạt/Không Đạt)</th>
                                    <th rowspan="2" class="text-center">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Mẫu 1</th>
                                    <th class="text-center">Mẫu 2</th>
                                    <th class="text-center">Mẫu 3</th>
                                    <th class="text-center">Mẫu 4</th>
                                    <th class="text-center">Mẫu 5</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $key = 0;?>
                                    <?php if (!empty($suggest_test->detail)){ ?>
                                        <?php foreach($suggest_test->detail as $iItem => $vItem) {?>
                                            <tr class="bg-danger">
                                                <td class="text-center event-plus-one show" data-class="rowID_<?=$vItem['id']?>"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                                                <td colspan="11">
                                                    <b><?=$vItem['name_product']?></b>
                                                </td>
                                            </tr>
                                            <tr class="bg-primary">
                                                <td class="text-center event-plus show" data-class="rowID_<?=$vItem['id']?>_1"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                                                <td colspan="3">
                                                    <b>I. Kiểm tra các tham số chung</b>
                                                </td>
                                                <td>
													<?php $colums = 'sample_one';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $colums = 'sample_two';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $colums = 'sample_three';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $colums = 'sample_four';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $colums = 'sample_five';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $colums = 'sample_is_result';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 1, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <?php foreach ($vItem['list_category'][1] as $k => $value){?>
                                                <tr class="rowID_<?=$vItem['id']?> rowID_<?=$vItem['id']?>_1">
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td><?= $value['name_category'] ?></td>
                                                    <td><?= $value['standard'] ?></td>
                                                    <td><?= $value['tools'] ?></td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_one_<?=$key?>_1" name="check_sample_one_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_one" data-value="1" <?=$value['sample_one'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>,'sample_one', this)">
                                                                <label for="check_sample_one_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_one_<?=$key?>_2" name="check_sample_one_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_one" data-value="2" <?=$value['sample_one'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_one', this)">
                                                                <label for="check_sample_one_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_two_<?=$key?>_1" name="check_sample_two_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_two" data-value="1" <?=$value['sample_two'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>,'sample_two', this)">
                                                                <label for="check_sample_two_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_two_<?=$key?>_2" name="check_sample_two_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_two" data-value="2" <?=$value['sample_two'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_two', this)">
                                                                <label for="check_sample_two_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_three_<?=$key?>_1" name="check_sample_three_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_three" data-value="1" <?=$value['sample_three'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_three', this)">
                                                                <label for="check_sample_three_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_three_<?=$key?>_2" name="check_sample_three_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_three" data-value="2" <?=$value['sample_three'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_three', this)">
                                                                <label for="check_sample_three_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_four_<?=$key?>_1" name="check_sample_four_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_four" data-value="1" <?=$value['sample_four'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_four', this)">
                                                                <label for="check_sample_four_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_four_<?=$key?>_2" name="check_sample_four_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_four" data-value="2" <?=$value['sample_four'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_four', this)">
                                                                <label for="check_sample_four_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_five_<?=$key?>_1" name="check_sample_five_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_five"  data-value="1" <?=$value['sample_five'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_five', this)">
                                                                <label for="check_sample_five_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_five_<?=$key?>_2" name="check_sample_five_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_sample_five"  data-value="2" <?=$value['sample_five'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_five', this)">
                                                                <label for="check_sample_five_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_is_result_<?=$key?>_1" name="check_is_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_is_result" data-value="1" <?=$value['is_result'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'is_result', this)">
                                                                <label for="check_is_result_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_is_result_<?=$key?>_2" name="check_is_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="1_<?=$vItem['id']?>_is_result" data-value="2" <?=$value['is_result'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'is_result', this)">
                                                                <label for="check_is_result_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <textarea class="note form-control note_result" data-id="<?=$value['id']?>"><?=$value['note']?></textarea>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            <tr class="bg-primary">
                                                <td class="text-center event-plus show" data-class="rowID_<?=$vItem['id']?>_2"><i class="fa fa-minus-square-o" aria-hidden="true"></i></td>
                                                <td colspan="3">
                                                    <b>II. Kiểm tra chất lượng ngoại quan NPL</b>
                                                </td>
                                                <td>
													<?php $colums = 'sample_one';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
													<?php $colums = 'sample_two';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
													<?php $colums = 'sample_three';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
													<?php $colums = 'sample_four';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
													<?php $colums = 'sample_five';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
													<?php $colums = 'sample_is_result';?>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" value="1" id="check_<?=$colums?>_all_<?=$key?>_1" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="1" onclick="checkResultGroup(<?=$vItem['id']?>,'<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_1">Đạt</label>
                                                        </div>
                                                        <div class="checkbox checkbox-danger">
                                                            <input type="checkbox" value="2" id="check_<?=$colums?>_all_<?=$key?>_2" name="check_<?=$colums?>_all_<?=$vItem['id']?>" data-id="<?=$vItem['id']?>" data-value="2" onclick="checkResultGroup(<?=$vItem['id']?>, '<?=$colums?>', 2, this)">
                                                            <label for="check_<?=$colums?>_all_<?=$key?>_2">Không Đạt</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <?php foreach ($vItem['list_category'][2] as $k => $value){?>
                                                <tr class="rowID_<?=$vItem['id']?> rowID_<?=$vItem['id']?>_2">
                                                    <td class="text-center"><?= (++$key) ?></td>
                                                    <td><?= $value['name_category'] ?></td>
                                                    <td colspan="2"><?= $value['standard'] ?></td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_one_<?=$key?>_1" name="check_sample_one_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_one" data-value="1" <?=$value['sample_one'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>,'sample_one', this)">
                                                                <label for="check_sample_one_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_one_<?=$key?>_2" name="check_sample_one_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_one" data-value="2" <?=$value['sample_one'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_one', this)">
                                                                <label for="check_sample_one_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_two_<?=$key?>_1" name="check_sample_two_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_two" data-value="1" <?=$value['sample_two'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>,'sample_two', this)">
                                                                <label for="check_sample_two_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_two_<?=$key?>_2" name="check_sample_two_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_two" data-value="2" <?=$value['sample_two'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_two', this)">
                                                                <label for="check_sample_two_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_three_<?=$key?>_1" name="check_sample_three_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_three" data-value="1" <?=$value['sample_three'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_three', this)">
                                                                <label for="check_sample_three_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_three_<?=$key?>_2" name="check_sample_three_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_three" data-value="2" <?=$value['sample_three'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_three', this)">
                                                                <label for="check_sample_three_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_four_<?=$key?>_1" name="check_sample_four_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_four" data-value="1" <?=$value['sample_four'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_four', this)">
                                                                <label for="check_sample_four_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_four_<?=$key?>_2" name="check_sample_four_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_four" data-value="2" <?=$value['sample_four'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_four', this)">
                                                                <label for="check_sample_four_<?=$key?>_1">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_sample_five_<?=$key?>_1" name="check_sample_five_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_five" data-value="1" <?=$value['sample_five'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_five', this)">
                                                                <label for="check_sample_five_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_sample_five_<?=$key?>_2" name="check_sample_five_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_sample_five" data-value="2" <?=$value['sample_five'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'sample_five', this)">
                                                                <label for="check_sample_five_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary">
                                                                <input type="checkbox" value="1" id="check_is_result_<?=$key?>_1" name="check_is_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_is_result" data-value="1" <?=$value['is_result'] == 1 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'is_result', this)">
                                                                <label for="check_is_result_<?=$key?>_1">Đạt</label>
                                                            </div>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" value="2" id="check_is_result_<?=$key?>_2" name="check_is_result_<?=$value['id']?>" data-id="<?=$value['id']?>" data-parent="2_<?=$vItem['id']?>_is_result" data-value="2" <?=$value['is_result'] == 2 ? 'checked' : ''?> onclick="checkResult(<?=$value['id']?>, 'is_result', this)">
                                                                <label for="check_is_result_<?=$key?>_2">Không Đạt</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <textarea class="note form-control note_result" data-id="<?=$value['id']?>"><?=$value['note']?></textarea>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6 pull-right mtop10">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <div><?= lang('tnh_created_by') ?>: <?= get_staff_full_name($suggest_test->create_by) ?></div>
                                    <div><?= lang('tnh_date_creted') ?>: <?= _dt($suggest_test->date_create) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    $('#modal_view_evaluete').modal('show');
    
    $('#view_suggest_check').on('click', '.event-plus', function() {
        var TrClass = $(this).attr('data-class');
        if($(this).hasClass('show')) {
            $(this).removeClass('show')
            $(this).find('i').removeClass('fa-minus-square-o');
            $(this).find('i').addClass('fa-plus-square-o');
            $(`.${TrClass}`).addClass('hide');
        }
        else {
            $(this).addClass('show');
            $(this).find('i').removeClass('fa-plus-square-o');
            $(this).find('i').addClass('fa-minus-square-o');
            $(`.${TrClass}`).removeClass('hide');
        }
    })
    $('#view_suggest_check').on('click', '.event-plus-one', function() {
        var TrClass = $(this).attr('data-class');
        if($(this).hasClass('show')) {
            $(this).removeClass('show')
            $(this).find('i').removeClass('fa-minus-square-o');
            $(this).find('i').addClass('fa-plus-square-o');
            $(`.${TrClass}`).addClass('hide');
            
            $(`.${TrClass}`).find('.event-plus').find('i').removeClass('fa-minus-square-o');
            $(`.${TrClass}`).find('.event-plus').find('i').addClass('fa-plus-square-o');
        }
        else {
            $(this).addClass('show');
            $(this).find('i').removeClass('fa-plus-square-o');
            $(this).find('i').addClass('fa-minus-square-o');
            $(`.${TrClass}`).removeClass('hide');
    
            $(`.${TrClass}`).find('.event-plus').find('i').removeClass('fa-plus-square-o');
            $(`.${TrClass}`).find('.event-plus').find('i').addClass('fa-minus-square-o');
           
        }
    })
    
    
    function checkResult(id, colums, _this) {
        if($(_this).prop('checked')) {
            var result = $(_this).attr('data-value');
            $(`input[name="check_${colums}_${id}"]`).prop('checked', false);
            $(_this).prop('checked', true);
        }
        else {
            result = 0;
        }
        var data = {};
        data['id'] = id;
        data['result'] = result;
        data['colums'] = colums;
        $.get(admin_url + 'suggest_test_item_quality/check_result', data, function(resultData) {
            resultData = JSON.parse(resultData);
            alert_float(resultData.alert_type, resultData.message);
            if(result == 2) {
                $(`.check_production_report_false_${id}`).removeClass('hide');
            }
            else {
                $(`.check_production_report_false_${id}`).addClass('hide');
            }
        })
    }
    
    $('.note_result').change(function () {
        var id = $(this).attr('data-id');
        var note = $(this).val();
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['note'] = note;
        $.post(admin_url + 'suggest_test_item_quality/update_note_result', data, function(resultData) {
            resultData = JSON.parse(resultData);
            alert_float(resultData.alert_type, resultData.message);
        })
    })
    
    function checkResultGroup(id, colums, type, _this) {
        if($(_this).prop('checked')) {
            var result = $(_this).attr('data-value');
            $(`input[name="check_${colums}_all_${id}"]`).prop('checked', false);
            $(_this).prop('checked', true);
        }
        else {
            result = 0;
        }
        if(confirm('Khi bạn check ở đây tất cả kết quả bên dưới sẽ cập nhật theo bạn có chắc chứ?')) {
            if($(_this).prop('checked') == true) {
                $(`input[data-parent="${type}_${id}_${colums}"]`).prop('checked', false);
                var value = $(_this).val();
                $(`input[data-parent="${type}_${id}_${colums}"][data-value="${value}"]`).prop('checked', true);
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['id'] = id;
                data['result'] = value;
                data['colums'] = colums;
                data['type'] = type;
                $.post(admin_url + 'suggest_test_item_quality/check_result_list', data, function(resultData) {
                    resultData = JSON.parse(resultData);
                    alert_float(resultData.alert_type, resultData.message);
                })
            }
        }
    }
</script>