<div id="list_vehicle_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 65%;">
        <?= form_open(admin_url('list_vehicle/detail/' . (!empty($list_vehicle) ? $list_vehicle->id : '')), ['id' => 'from_list_vehicle']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?= !empty($title) ? $title : '' ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td style="width: 15%;">
                            <?= lang('Nhà Vận Chuyển', 'transporters') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="transporters"
                                       class="form-control"
                                       id="transporters"
                                       value="<?= !empty($list_vehicle) ? $list_vehicle->transporters : '' ?>">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('Mã Phương Tiện', 'code_vehicle') ?>
                        </td>
                        <td style="width: 35%;">
                            <input type="text"
                                   name="code_vehicle"
                                   class="form-control"
                                   id="code_vehicle"
                                   value="<?= !empty($list_vehicle) ? $list_vehicle->code_vehicle : '' ?>">
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 15%;">
							<?= lang('Loại Phương Tiện', 'type_vehicle') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="type_vehicle"
                                       class="form-control"
                                       id="type_vehicle"
                                       value="<?= !empty($list_vehicle) ? $list_vehicle->type_vehicle : '' ?>">
                            </div>
                        </td>
                        <td style="width: 15%;">
							<?= lang('Đơn Vị Tính', 'unit_name') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="unit_name"
                                       class="form-control"
                                       id="unit_name"
                                       value="<?= !empty($list_vehicle) ? $list_vehicle->unit_name : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
							<?= lang('Điểm Đi', 'departure_point') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <textarea type="text"
                                       name="departure_point"
                                       class="form-control"
                                          id="departure_point"><?= !empty($list_vehicle) ? $list_vehicle->departure_point : '' ?></textarea>
                            </div>
                        </td>
                        <td style="width: 15%;">
							<?= lang('Điểm Đến', 'destination') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <textarea name="destination"
                                       class="form-control"
                                       id="destination"><?= !empty($list_vehicle) ? $list_vehicle->destination : '' ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
							<?= lang('Số KM', 'number_km') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="number_km"
                                       class="form-control"
                                       id="number_km"
                                       onchange="formatNumBerKeyUpCus(this)"
                                       value="<?= !empty($list_vehicle) ? number_format_data($list_vehicle->number_km) : '' ?>">
                            </div>
                        </td>
                        <td style="width: 15%;">
							<?= lang('Đơn Giá', 'price') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="price"
                                       class="form-control"
                                       id="price"
                                       onchange="formatNumBerKeyUpCus(this)"
                                       value="<?= !empty($list_vehicle) ? number_format_data($list_vehicle->price) : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;"><?= lang('Hình Ảnh', 'img') ?></td>
                        <td style="width: 35%;">
                            <div id="img_review" class="text-center mbot10" style="min-height: 100px;">
                                <?php if (!empty($list_vehicle->img)) { ?>
                                    <img style="width: 200px;height: auto" src="<?= base_url('download/preview_image?path=' . ($list_vehicle->img)) ?>"/>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <input type="file"
                                       name="img"
                                       class="form-control"
                                       id="image"
                                       value="">
                            </div>
                        </td>
                        <td style="width: 15%;">
							<?= lang('Đơn Vị Tiền Tệ', 'currency_unit') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text"
                                       name="currency_unit"
                                       class="form-control"
                                       id="currency_unit"
                                       value="<?= !empty($list_vehicle) ? $list_vehicle->currency_unit : '' ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">
							<?= lang('Ghi Chú', 'note') ?>
                        </td>
                        <td colspan="3">
                            <textarea name="note"
                                      class="form-control note"
                                      rows="6"
                                      id="note"><?= !empty($list_vehicle->note) ? $list_vehicle->note : '' ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('#list_vehicle_modal').modal('show');
    appValidateForm($('#from_list_vehicle'), {
        transporters: 'required',
        code_vehicle: 'required',
        type_vehicle: 'required',
    }, detail_from);

    function detail_from(form) {
    
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var form_data = new FormData();
        if (typeof (csrfData) !== 'undefined') {
            form_data.append(csrfData['token_name'], csrfData['hash']);
        }
        var formParams = $(form).serializeArray();
    
        $.each(formParams, function (i, val) {
            form_data.append(val.name, val.value);
        });
    
        
        var fileimages = $(form).find('input#image').prop('files')[0];
        if(fileimages) {
            form_data.append('img', fileimages);
        }
        
        
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
        }).done(function (data) {
            if (data.success) {
                if (typeof oTable != 'undefined') {
                    oTable.draw();
                }
                $('#list_vehicle_modal').modal('hide');
            } else {
                $('.add').removeAttr('disabled', 'disabled');
            }
            alert_float(data.alert_type, data.message);
        }).fail(function () {
            alert_float('danger', lang_core['errors']);
            $('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }

    $('input[name="img"]').change(function (e) {
        $('#img_review').html('<img style="width: 200px;height: auto" class="img_reiveIMG" src=""/>');
        readURL(e.target, '.img_reiveIMG');
    })
    function readURL(input, thisData) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(thisData).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>