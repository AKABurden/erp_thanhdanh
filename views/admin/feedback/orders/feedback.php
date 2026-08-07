<?php $folder = 'feedback/orders';?>
<?php $class = 'orders';?>
<?php $urlForm = 'feedback/add_feed_back_orders';?>
<?php $urlRemove = 'feedback/remove_feed_back_orders';?>

<?php $idComment =   'feedback_' . time();?>
<?php echo form_open_multipart(admin_url($urlForm), array(
        'id' => 'feedback-form',
        'class' => 'dropzone dropzone-manual',
        'enctype' => 'multipart/form-data',
        'style' => 'min-height:auto;background-color:#fff;'
)); ?>
<div class="examples">
    <textarea name="comment_feedback" id="feedback_<?=time();?>" placeholder="<?php echo _l('feedback'); ?>" id="feedback_comment" rows="3" class="form-control ays-ignore mention-textarea"></textarea>
</div>

<a data-toggle="collapse" class="pull-left mtop10 mbot10" data-target="#div_upload">File Đính Kèm</a>
<div class="clearfix"></div>
<div id="div_upload" class="collapse">
    <div id="dropzoneFeedback" class="dropzoneDragArea dz-default dz-message feedback-comment-dropzone">
        <span><?php echo _l('drop_files_here_to_upload'); ?></span>
    </div>
</div>
<div class="dropzone-task-comment-previews dropzone-previews"></div>
<button type="button" class="btn btn-info pull-right mbot20" autocomplete="off"  onclick="add_feedback(<?=!empty($order) ? $order['id'] : ''?>);">
    Thêm bình luận
</button>
<?php echo form_close(); ?>
<div class="clearfix"></div>
<hr/>

<div class="clearfix"></div>
<div class="feedback-<?=$class?> data-feed-back-orders-<?=$order['id']?>">
    <?php if(!empty($feedback)) {?>
        <?php foreach($feedback as $key => $value) {
            $this->load->view('admin/' . $folder . '/comment_feedback', ['feedback' => $value]);
        }?>
    <?php } ?>
</div>

<script>
    Dropzone.options.expenseForm = false;
    // init_editor('#<?=$idComment?>');
        if($('#dropzoneFeedback').length > 0){
            var expenseDropzone = new Dropzone('#feedback-form', appCreateDropzoneOptions({
                paramName: "file",
                autoProcessQueue: false,
                previewsContainer: '.dropzone-previews',
                addRemoveLinks: true,
                maxFiles: 1,
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


        function add_feedback(id) {
            for (var i = 0; i < tinymce.editors.length; i++) {
                tinymce.editors[i].save();
            }

            var data_comment = {};
            $('#<?=$idComment?>').mentionsInput('getMentions', function(data_tag) {
                data_comment = JSON.stringify(data_tag);
            });

            var form = $('#feedback-form');
            var formParams = form.serializeArray();
            var url = form.attr('action');
            var formData = new FormData();
            $.each(form.find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(expenseDropzone.files, function(index, value) {
                formData.append('file', value);
            })

            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });

            formData.append('tag_comment', data_comment);
            formData.append('id', id);
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            }).done(function (data) {
                if (data.success) {
                    if(data.html) {
                        if($('div[data-feed-back-orders="' + data.id + '"]').length == 0) {
                            $('.feedback-<?=$class?>').prepend(data.html);
                        }
                        expenseDropzone.removeAllFiles();
                        //tinymce.get("<?//=$idComment?>//").setContent('');
                        $('#<?=$idComment?>').val('');
                        $('#<?=$idComment?>').mentionsInput('reset');
                    }
                }
                alert_float(data.alert_type, data.message);
            })
            .fail(function () {
                alert_float('danger', '<?= lang('tnh_error_please_reload_page') ?>');
                $('.add').removeClass('none-event');
            });
            return false;
        }

        function removeFeedBack(id = "") {
            if(confirm('Xóa feedback sẽ không thể khôi phục bạn có chắc muốn xóa?')) {
                $.get(admin_url + '<?=$urlRemove?>/' + id, function(data) {
                    data = JSON.parse(data);
                    if(data.success) {
                        $('div[data-feed-back-orders="' + id + '"]').remove();
                    }
                    alert_float(data.alert_type, data.message);
                })
            }
        }


    // })
</script>


<link href='<?=base_url('assets/mentions_input/')?>assets/style.css' rel='stylesheet' type='text/css'>
<link href='<?=base_url('assets/mentions_input/')?>jquery.mentionsInput.css' rel='stylesheet' type='text/css'>
<script src='<?=base_url('assets/mentions_input/')?>/underscore-min.js' type='text/javascript'></script>

<script src='<?=base_url('assets/mentions_input/')?>jquery.mentionsInput.js' type='text/javascript'></script>
<script src='<?=base_url('assets/mentions_input/')?>lib/jquery.events.input.js' type='text/javascript'></script>
<script src='<?=base_url('assets/mentions_input/')?>lib/jquery.elastic.js' type='text/javascript'></script>
<script>
	<?php $staff = getFeedBackStaff();?>
    var staffActive = <?=!empty($staff) ? json_encode($staff) : '[]'?>;
    $(function () {
        $('textarea.mention-textarea').mentionsInput({
            onDataRequest:function (mode, query, callback) {
                responseData = staffActive;


                responseData = _.filter(responseData, function(item) {
                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                });
                callback.call(this, responseData);
                // });
            }
        });
    });
</script>