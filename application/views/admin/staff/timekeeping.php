<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('fileinput/fileinput.min.css') ?>">
<?php echo form_open('admin/staff/timekeeping/' . $id, array('id' => 'form-timekeeping')); ?>
<div class="modal-dialog modal-lg" style="width: 40%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" value="<?= (($check_dahahi[0]['result'] == 2) ? $personnel['FaceID'] : '') ?>" name="FacePersionId">
            <table class="table-personnel mtop10">
                <tr>
                    <td rowspan="2" style="width: 30%;">Hình ảnh</td>
                    <td style="width: 70%;">
                        <div class="form-group">
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" id="y_opt_1_disable_languages" name="faceid" value="1">
                                <label for="y_opt_1_disable_languages">Dùng ảnh avatar hiện tại </label>
                                <?php
                                $images = $personnel['profile_image'];
                                if (empty($images)) {
                                    $images = base_url('assets/images/tnh/default-avatar-male.png');
                                } else {
                                    $images = base_url('uploads/staff_profile_images/')  .$personnel['staffid']. '/thumb_'.$personnel['profile_image'];
                                }
                                ?>
                                <div class="preview_image" style="width: auto;">
                                    <div class="display-block contract-attachment-wrapper img">
                                        <div style="width:100px; margin: auto;">
                                            <a href="<?= $images ?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                <div class="">
                                                    <img src="<?= $images ?>" />
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>
                <tr>
                    <td>
                        <div class="radio radio-primary radio-inline">
                            <input type="radio" id="y_opt_2_disable_languages" name="faceid" value="2" checked="">
                            <label for="y_opt_2_disable_languages">Dùng ảnh khác </label><br>
                            <div class="kv-avatar">
                                <div class="file-loading">
                                    <input id="avatar-1" name="images" type="file">
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Chọn thiết bị</td>
                    <td>
                        <?php 
                        $MachineBoxId = !empty($personnel['MachineBoxId']) ? explode(',',$personnel['MachineBoxId']) : [];
                        ?>
                        <?php echo render_select('MachineBoxId[]', $dahahi_getAllMachine, array('MachineBoxId', 'MachineName','MachineCode'), '', $MachineBoxId, array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" class="form-control" value="1">
            <button type="submit" class="btn btn-primary save-ep"><?= _l('save') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript" src="<?= js('fileinput/fileinput.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('fileinput/locales/vi.js') ?>"></script>
<script>
    init_selectpicker();
    $(".file-image").fileinput({
        language: "vi",
        allowedFileTypes: ['image'],
        showUpload: false,
        uploadAsync: false,
        // previewSettings: {
        //     image: {width: "100px", height: "160px"},
        //     other: {width: "100px", height: "160px"}
        // }
    });

    $(".attachments").fileinput({
        showUpload: false,
        language: "vi",
        uploadUrl: "/file-upload-batch/2",
        uploadAsync: false,
        previewFileIcon: '<i class="fas fa-file"></i>',
        allowedPreviewTypes: null, // set to empty, null or false to disable preview for all types
        previewFileIconSettings: {
            'docx': '<i class="fa fa-file-word-o text-primary"></i>',
            'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
            'pptx': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
            'jpg': '<i class="fa fa-file-image-o text-warning"></i>',
            'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
            'zip': '<i class="fa fa-file-zip-o text-muted"></i>',
        }
    });
    // $images = $check_dahahi['Base64Image'];
    var src_img = site.base_url + 'assets/images/tnh/default-avatar-male.png';
    var Base64Image = '<?= (($check_dahahi[0]['result'] == 2) ? $check_dahahi[0]['data']['Base64Image'] : '') ?>';
    if (Base64Image != '') {
        src_img = Base64Image;
    }
    $("#avatar-1").fileinput({
        overwriteInitial: true,
        // maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img style="width:100px; margin: auto;" src="' + src_img + '" alt="Your Avatar">',
        layoutTemplates: {
            main2: '{preview} {remove} {browse}'
        },
        allowedFileTypes: ['image']
        // allowedFileExtensions: ["jpg", "png", "gif"]
    });
    $(document).ready(function() {
        appValidateForm($('#form-timekeeping'), {
            MachineBoxId: "required"
        }, timekeeping);

        function timekeeping(form) {
            $('.save-ep').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();
            $.each(form.find('input[name="images"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

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
                        window.location.href = site.base_url + 'admin/staff/member/' + data.id;
                    } else {
                        alert_float('danger', data.message);
                        $('.save-ep').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.save-ep').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    });
</script>