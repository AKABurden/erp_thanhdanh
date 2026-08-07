<div id="modal_tasks_suggest_outsource" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <style>
            #table-items th {
                white-space: nowrap;
            }
        </style>
        <?php echo form_open(
            'admin/suggest_outsource/update_hand_over_task',
            array('id' => 'suggest_outsource')
        ); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?= $title ?? ''?></h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <input name="id_suggest_outsource" type="hidden" value="<?=$suggest_outsource->id?>">
                        <?php foreach($suggest_outsource_item as $keyItem => $items) {?>
                            <table id="tb-handling-products-stages" class="table dataTable mbot20">
                                <thead>
                                <tr>
                                    <th colspan="5" class="text-center">Phiếu yêu cầu gia công: <?=!empty($suggest_outsource) ? $suggest_outsource->reference_no : 'Chưa tạo'?></th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="width:23%;">Nội dung bàn giao</th>
                                    <th class="text-center" style="width:23%;">Tiêu chuẩn</th>
                                    <th class="text-center" style="width:23%;">Phương thức</th>
                                    <th class="text-center" style="width:15%;">Đạt</th>
                                    <th class="text-center" style="width:15%;">Không đạt</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($items['category_hand_over']->task)) {?>
                                        <input name="id_suggest_outsource_item[<?=$items['id']?>]" type="hidden" value="<?=!empty($items['id']) ? $items['id'] : ''?>">
                                        <input name="category_hand[<?=$items['id']?>]" type="hidden" value="<?=!empty($items['category_hand_over']) ? $items['category_hand_over']->id : ''?>">
                                        <?php foreach($items['category_hand_over']->task as $key => $value) {?>
                                            <?php
                                            $ktHandTasks = false;
                                            $this->db->where('id_suggest_outsource', $suggest_outsource->id);
                                            $this->db->where('id_suggest_outsource_item', $items['id']);
                                            $this->db->where('hand_over_task_id', $value['id']);
                                            $ktHandTasks = $this->db->get('tbl_suggest_outsource_task')->row();
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="hand_over_task_id[<?=$items['id']?>][<?=$key?>]" value="<?=$value['id']?>">
                                                    <?=$value['name']?>
                                                </td>
                                                <td><?=$value['standard']?></td>
                                                <td><?=$value['method']?></td>
                                                <?php if(empty($ktHandTasks)) {?>
                                                    <td class="text-center">
                                                        <div class="checkbox checkbox-info check-data-err">
                                                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$items['id']?>][<?=$key?>]" id="task_hand_over_qualified_<?=$items['id']?>_<?=$key?>" value="1" checked>
                                                            <label for="task_hand_over_qualified_<?=$items['id']?>_<?=$key?>"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="checkbox checkbox-info check-data-err">
                                                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$items['id']?>][<?=$key?>]" id="task_hand_over_un_qualified_<?=$items['id']?>_<?=$key?>" value="2">
                                                            <label for="task_hand_over_un_qualified_<?=$items['id']?>_<?=$key?>"></label>
                                                        </div>
                                                    </td>
                                                <?php } else {?>
                                                    <td class="text-center">
                                                        <div class="checkbox checkbox-info check-data-err">
                                                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$items['id']?>][<?=$key?>]" id="task_hand_over_qualified_<?=$items['id']?>_<?=$key?>" value="1" <?=$ktHandTasks->task_hand_over_qualified == 1 ? 'checked' : ''?>>
                                                            <label for="task_hand_over_qualified_<?=$items['id']?>_<?=$key?>"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="checkbox checkbox-info check-data-err">
                                                            <input type="radio" class="radio_check_hand_over" name="task_hand_over_qualified[<?=$items['id']?>][<?=$key?>]" id="task_hand_over_un_qualified_<?=$items['id']?>_<?=$key?>" value="2"  <?=$ktHandTasks->task_hand_over_qualified == 2 ? 'checked' : ''?>>
                                                            <label for="task_hand_over_un_qualified_<?=$items['id']?>_<?=$key?>"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center hide">
                                                        <?=$ktHandTasks->task_hand_over_qualified == 1 ? 'X' : ''?>
                                                    </td>
                                                    <td class="text-center hide">
                                                        <?=$ktHandTasks->task_hand_over_qualified == 2 ? 'X' : ''?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr><td colspan="5" class="text-danger">Không tìm thấy tiêu chí bàn giao</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                    <button type="submit" class="btn btn-primary add add-finished-stages">Lưu lại</button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal_tasks_suggest_outsource').modal('show');

            appValidateForm($('#suggest_outsource'), {
                reference_no: 'required',
                date: 'required',
                po_id: 'required',
                branch_id: 'required',
                staff_plan: 'required',
            }, db);

            //save db
            function db(form) {
                $('.add').attr('disabled', 'disabled');
                var url = form.action;
                var form = $(form),
                    formData = new FormData(),
                    formParams = form.serializeArray();
                $.each(formParams, function(i, val) {
                    formData.append(val.name, val.value);
                });

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if ($('.radio_check_hand_over[value="2"]:checked').length > 0) {
                                var url = admin_url + `production_report/detail?object_id=${data.id}&object_type=suggest_outsource`;
                                window.open(url, '_blank');
                            }
                            $('#modal_tasks_suggest_outsource').modal('hide');
                            oTable.draw();
                        } else {
                            alert_float('danger', data.message);
                            $('.add').removeAttr('disabled', 'disabled');
                        }
                    })
                    .fail(function() {
                        alert_float('danger', lang_core['errors']);
                        $('.add').removeAttr('disabled', 'disabled');
                    });
                return false;
            }
            $('.radio_check_hand_over').trigger('change');
        });


        $('.radio_check_hand_over').change(function() {
            if ($('.radio_check_hand_over[value="2"]:checked').length > 0) {
                $('.add-finished-stages').text('Cập nhật phiếu bàn giao và tạo báo cáo');
            }
            else {
                $('.add-finished-stages').text('Lưu lại');
            }
        })

    </script>