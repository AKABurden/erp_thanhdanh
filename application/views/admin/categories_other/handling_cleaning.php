<div class="modal" id="modal_handling_cleaning">
    <?php echo form_open_multipart('admin/categories_other/handlingCleaning/' . (!empty($id) ? $id : ''), array('id' => 'handling-cleaning')); ?>
    <div class="modal-dialog" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Nhóm khu vực', 'id_code_group') ?>
                            <select id="id_code_group" class="form-control selectpicker id_code_group" data-width="100%" name="id_code_group" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
                                <?php if (!empty($dtCodeGroup)) {
                                    foreach ($dtCodeGroup as $key => $value) { ?>
                                        <option <?= !empty($cleaning) && ($cleaning['id_code_group'] == $value['id']) ? 'selected' : '' ?> data-subtext="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã khu vực', 'id_code') ?>
                            <select id="id_code" class="form-control selectpicker id_code" data-width="100%" name="id_code" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option></option>
                                <?php if (!empty($dtCode)) {
                                    foreach ($dtCode as $key => $value) { ?>
                                        <option <?= !empty($cleaning) && ($cleaning['id_code'] == $value['id']) ? 'selected' : '' ?> data-subtext="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã khu  vực vệ sinh 5S', 'code_group') ?>
                            <input type="text" name="code_group" id="code_group" placeholder="<?= lang('Mã khu  vực vệ sinh 5S') ?>" class="form-control code_group" value="<?= !empty($cleaning) ? $cleaning['code_group'] : '' ?>" required="required">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tên khu  vực vệ sinh 5S', 'name') ?>
                            <input type="text" name="name" id="name" placeholder="<?= lang('Tên khu  vực vệ sinh 5S') ?>" class="form-control name" value="<?= !empty($cleaning) ? $cleaning['name'] : '' ?>" required="required">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ghi chú', 'detail') ?>
                            <textarea name="note" id="note" placeholder="<?= lang('Chi tiết') ?>" class="form-control" rows="3"><?= !empty($cleaning) ? $cleaning['note'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table id="tb-handling_cleaning_detail" class="table table-hover table-cs dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center open" style="width: 50px;">
                                        <a class="hover-svg dropdown-toggle add-row" onclick="addTd()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="true">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th>Hạng Mục Kiểm Tra</th>
                                    <th>Tiêu Chuẩn 5S</th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cleaning['detail'])) {
                                    foreach ($cleaning['detail'] as $key => $value) { ?>
                                        <?php
                                        $viewFile = ViewHtmlImagesDt((!empty($value['img']) ? base_url($value['img']) : ''))
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= ($key + 1) ?></td>
                                            <td>
                                                <input type="hidden" name="detail[<?= $key ?>][id]" value="<?= $value['id'] ?>">
                                                <input type="text" name="detail[<?= $key ?>][name]" required="" class="form-control process" value="<?= $value['name'] ?>" placeholder="Hạng mục kiểm tra">
                                            </td>
                                            <td>
                                                <textarea type="text" name="detail[<?= $key ?>][note]" class="form-control note mbot10" placeholder="Tiêu chuẩn 5s" aria-invalid="false"><?= $value['note'] ?></textarea>
                                                <input type="file" name="detail[<?= $key ?>][file]" class="form-control file_main mbot10" value="" placeholder="File">
                                                <?= $viewFile ?>
                                            </td>
                                            <td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeTD(this)"></i></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <input type="text" name="detail[0][name]" required="" class="form-control process" value="" placeholder="Hạng mục kiểm tra">
                                        </td>
                                        <td>
                                            <textarea type="text" name="detail[0][note]" class="form-control note mbot10" placeholder="Tiêu chuẩn 5s" aria-invalid="false"></textarea>
                                            <input type="file" name="detail[0][file]" class="form-control file_main" value="" placeholder="File">
                                        </td>
                                        <td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeTD(this)"></i></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    $(function() {
        init_selectpicker('refresh');

        $('#modal_handling_cleaning').modal('show');
        appValidateForm($('#handling-cleaning'), {
            name: 'required',
            id_code_group: 'required',
            id_code: 'required',
            code_group: 'required',
        }, handlingData);

        function handlingData(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            var file_data = $('.file_main');
            $.each(file_data, function(infile, valFile) {
                if ($(valFile).prop('files')) {
                    $.each($(valFile).prop('files'), function(index, value) {
                        formData.append($(valFile).attr('name'), value);
                    })
                }
            })

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
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })

    var countItemsMain = <?= !empty($cleaning['detail']) ? count($cleaning['detail']) : 1 ?>;

    function addTd() {
        var tdNumbers = `<td class="text-center td-numbers"></td>`;
        var tdProcess = `<td>
                        <input type="text" name="detail[${countItemsMain}][name]" required class="form-control process" value="" placeholder="<?= lang('Hạng mục kiểm tra') ?>">
                    </td>`;
        var tdFile = `<td>
                     <textarea type="text" name="detail[${countItemsMain}][note]" class="form-control note mbot10" placeholder="Tiêu Chuẩn 5S" aria-invalid="false"></textarea>
                     <input type="file" name="detail[${countItemsMain}][file]" class="form-control file_main" placeholder="<?= lang('File') ?>">
                 </td>`;
        var tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeTD(this)"></i></td>`;
        var trProcess = `<tr>
                        ${tdNumbers}
                        ${tdProcess}
                        ${tdFile}
                        ${tdActions}
                    </tr>`;
        $('#tb-handling_cleaning_detail tbody').append(trProcess);
        countItemsMain++;
        stt_handling_cleaning();
    }

    function stt_handling_cleaning() {
        var tr = $('#tb-handling_cleaning_detail tbody tr');
        $.each(tr, function(index, value) {
            $(value).find('.td-numbers').text((index + 1));
        })
    }

    function removeTD(_this) {
        $(_this).closest('tr').remove();
        stt_handling_cleaning();
    }

    function removeFile(id, _this) {
        if (confirm('Bạn có muốn xóa file này?')) {
            $.get(admin_url + 'categories_other/remove_file/' + id, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.success) {
                    $(_this).parents('.url_file').remove();
                }
            })
        }
    }
</script>