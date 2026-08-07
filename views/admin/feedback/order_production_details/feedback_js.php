
<script>
    var checkActive = [];
    // $(function (e) {
        Dropzone.options.expenseForm = false;
        //init_editor('#<?//=$idComment?>//');
        if($('#dropzoneFeedback<?=$mC?>').length > 0) {
            var expenseDropzone = new Dropzone('#feedback-form<?=$mC?>', appCreateDropzoneOptions({
                paramName: "file",
                autoProcessQueue: false,
                previewsContainer: '.dropzone-previews',
                addRemoveLinks: true,
                maxFiles: 1,
                clickable: '#dropzoneFeedback<?=$mC?>',
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
    // })




    function add_feedback(id) {
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }

        var data_comment = {};
        $('#<?=$idComment?>').mentionsInput('getMentions', function(data_tag) {
            data_comment = JSON.stringify(data_tag);
        });

        var form = $('#feedback-form<?=$mC?>');
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
                    if($('div[data-feed-back-order_production_details="' + data.id + '"]').length == 0) {
                        $('.feedback-<?=$class?>').prepend(data.html);
                    }
                    expenseDropzone.removeAllFiles();
                    $('#<?=$idComment?>').val('');
                    $('#<?=$idComment?>').mentionsInput('reset');
                    //tinymce.get("<?//=$idComment?>//").setContent('');
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
                    $('div[data-feed-back-order_production_details="' + id + '"]').remove();
                }
                alert_float(data.alert_type, data.message);
            })
        }
    }


    // })
</script>
<link href='<?=base_url('assets/mentions_input/')?>assets/style.css' rel='stylesheet' type='text/css'>
<link href='<?=base_url('assets/mentions_input/')?>jquery.mentionsInput.css' rel='stylesheet' type='text/css'>
<script src='<?=base_url('assets/mentions_input/')?>underscore-min.js' type='text/javascript'></script>

<script src='<?=base_url('assets/mentions_input/')?>jquery.mentionsInput.js' type='text/javascript'></script>
<script src='<?=base_url('assets/mentions_input/')?>lib/jquery.events.input.js' type='text/javascript'></script>
<script src='<?=base_url('assets/mentions_input/')?>lib/jquery.elastic.js' type='text/javascript'></script>
<script>

	<?php $staff = getFeedBackStaff_stages();?>
    var staffActive = <?=!empty($staff) ? json_encode($staff) : '[]'?>

    $(function () {
        $('textarea.mention-textarea').mentionsInput({
            onDataRequest:function (mode, query, callback) {
                responseData = staffActive;
                responseData = _.filter(responseData, function(item) {
                    console.log(item.name.toLowerCase().indexOf(query.toLowerCase()))
                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                });
                callback.call(this, responseData);
                // });
            }
        });
    });
</script>