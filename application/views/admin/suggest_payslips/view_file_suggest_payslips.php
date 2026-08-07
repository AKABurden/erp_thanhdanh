<div id="modal-view-file" class="modal fade" role="dialog">
    <style>
      .img-icon-file {
        height: 20px;
        width: 20px;
      }
      .collapse.save.in {
        display: contents!important;
      }
    </style>
    <div class="modal-dialog" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : '' ?></h4>
            </div>
			<?php echo form_open_multipart(admin_url('suggest_payslips/addFile/' . $items->id), array(
				'id' => 'file-form',
				'class' => 'dropzone dropzone-manual',
				'enctype' => 'multipart/form-data',
				'style' => 'min-height:auto;background-color:#fff;'
			)); ?>
                <div class="modal-body">
                    <?php $folder = 'suggest_payslips';?>
                    <?php if(!empty($items)) { ?>
                        <div class="tc-content task-comment">
                            <div class="media-body">
                                <div class="fild-content mtop10">
                                    <table class="table dataTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center">File</th>
                                                <th class="text-center">Loại</th>
                                                <th class="text-center">Thuộc Tính</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($list_files)) {
                                                foreach($list_files as $keyFile => $valFile) {?>
                                                    <?php $type_file = mime_content_type(FCPATH . $valFile); ?>
                                                        <tr class="item_file">
                                                            <td>
                                                                <?php
                                                                    if(explode('/', $type_file)[0] == 'image') {?>
                                                                        <div class="preview_image" style="width: auto;">
                                                                            <div class="display-block contract-attachment-wrapper img">
                                                                                <div style="width:150px;">
                                                                                    <a href="<?=base_url($valFile)?>" data-lightbox="customer-profile" class="display-block mbot5">
                                                                                        <div class="">
                                                                                            <img src="<?=base_url($valFile)?>" style="max-height: 100px">
                                                                                        </div>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php }
                                                                    else if(explode('/', $type_file)[0] == 'video') {
                                                                        echo "Video";
                                                                    }
                                                                    else {?>
                                                                        <?php $namefile = explode('/', $valFile)?>
                                                                        <a target="_blank" href="<?=base_url($valFile)?>"><i class="fa fa-file-archive-o"></i> <?= $namefile[count($namefile) - 1] ?></a>
                                                                    <?php }
                                                                ?>
                                                            
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                    if(explode('/', $type_file)[0] == 'image') {
                                                                        echo "Hình Ảnh";
                                                                    }
                                                                    else if(explode('/', $type_file)[0] == 'video') {
                                                                        echo "Video";
                                                                    }
                                                                    else {
                                                                        echo "Tập Tin";
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <a class="btn btn-icon btn-danger removeItemFile" _href="<?=$valFile?>" data-id="<?=$items->id?>">
                                                                    <i class="fa fa-remove" aria-hidden="true"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                <?php }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
    
                    <a data-toggle="collapse" class="pull-left mtop10 mbot10" data-target=".div_upload_file">File Đính Kèm</a>
                    <div class="clearfix"></div>
                    <div class="div_upload_file collapse mtop20">
                        <div id="dropzoneFeedback" class="dropzoneDragArea dz-default dz-message feedback-comment-dropzone">
                            <span><?php echo _l('drop_files_here_to_upload'); ?></span>
                        </div>
                    </div>
                    <div class="dropzone-task-comment-previews dropzone-previews"></div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <span class="div_upload_file save collapse mtop30">
                        <button type="button" onclick="add_file()" class="btn btn-info">Lưu</button>
                    </span>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
                <a class="hide c_modal id-active" href="<?=admin_url('suggest_payslips/view_file_suggest_payslips/' . $items->id)?>"></a>
			<?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $('#modal-view-file').modal('show');
    $('.removeItemFile').click(function() {
        if(confirm('File xóa sẽ không thể khôi phục?')) {
            var id = $(this).data('id');
            var _href = $(this).attr('_href');
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            var item_file = $(this).parents('.item_file');
            data['id'] = id;
            data['link_file'] = _href;
            $.post(admin_url + 'suggest_payslips/remove_items_file', data, function(result) {
                result = JSON.parse(result);
                if(result.success) {
                    item_file.remove();
                }
                alert_float(result.alert_type, result.message);
            })
        }
    })
    function add_file(id) {
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
    
        var form = $('#file-form');
        var formParams = form.serializeArray();
        var url = form.attr('action');
        var formData = new FormData();
        
        $.each(expenseDropzone.files, function(index, value) {
            formData.append('files[' + index + ']', value);
        })
        $.each(formParams, function (i, val) {
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
        }).done(function (data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                var url = "<?=admin_url('suggest_payslips/view_file_suggest_payslips/' . $items->id)?>";
                $.get(url, function(result) {
                    $('.modal-backdrop.in').remove();
                    $('#cong_modal').html(result);
                }).error(function (response) {
                    alert_float('danger', response.responseText);
                });
                return false;
            }
        })
        .fail(function () {
            alert_float('danger', '<?= lang('tnh_error_please_reload_page') ?>');
            $('.add').removeClass('none-event');
        });
        return false;
    }

    var checkActive = [];
    Dropzone.options.expenseForm = false;
    if($('#dropzoneFeedback').length > 0){
        var expenseDropzone = new Dropzone('#file-form', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneFeedback',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }
</script>
    