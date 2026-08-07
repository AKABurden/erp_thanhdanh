<script src="http://malsup.github.com/jquery.form.js"></script> 
<div class="modal fade" id="view_video" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('videos/upload'),array('id'=>'id_videos')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Thêm mới videos'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('title','Tiêu đề video'); ?>
                        <?php echo render_select('type', $type, array('id', 'name'), 'Loại video'); ?>
                        <div class="col-md-12">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <span class="btn btn-default btn-file col-md-12">
                                <span>Choose file videos</span>
                                <input  type="file" name="file" class="mbot10 btn" style="width:100%" id="file"  accept=".AVI, .FLV, .WMV, .MOV, .MP4" />
                            </span>
                        </div>
                        </div>
                        <br>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress">
                          <div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar"
                           aria-valuemin="0" aria-valuemax="100" style="">
                            <div class="progress_ch">0%</div>
                          </div>
                        </div>
                        <?php echo render_textarea('note','Mô tả video','', array(), array(), '', 'tinymce'); ?>
                        <div id="targetLayer" style="display:none;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" id="uploadSubmit" value="Thêm" class="btn btn-info" />
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    $('body').on('hidden.bs.modal', '#view_video', function() {
        $('#view_video_html').html('');
        tinymce.remove();
    }); 
    $(document).ready(function() {
        $('.progress').hide();
        init_editor('textarea[name="note"]');
    });
    appValidateForm($('#id_videos'), {
        title:'required',
        type:'required',
        file:'required',
        },manage_videos);
    function manage_videos(form) {
        var fileInput = document.getElementById('file');
        var filePath = fileInput.value;//lấy giá trị input theo id
        var allowedExtensions = /(\.AVI|\.FLV|\.MOV|\.MP4|\.WMV)$/i;//các tập tin cho phép
        //Kiểm tra định dạng
        if(!allowedExtensions.exec(filePath)){
        alert('Vui lòng upload các file có định dạng: .jpeg/.jpg/.png/.gif only.');
        fileInput.value = '';
        return false;
        }
        if($('#file').val())
        {   
            tinymce.get('note').save();
            var data = $(form).serializeArray();
            var url = form.action;

            var file_data = $('input#file').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('csrf_token_name', csrfData.hash);
            $.each(data, function(key, Val){
                form_data.append(Val.name, Val.value);
            })
            $('#loader-icon').show();
            $('#targetLayer').hide();
            $('.progress').show();
        $.ajax({
                url: url,
                type       : 'POST',
                contentType: false,
                cache      : false,
                processData: false,
                data       : form_data,
                // timeout: 3000,
                xhr        : function ()
                {
                    var jqXHR = null;
                    if ( window.ActiveXObject )
                    {
                        jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
                    }
                    else
                    {
                        jqXHR = new window.XMLHttpRequest();
                    }
                    //Upload progress
                    jqXHR.upload.addEventListener( "progress", function ( evt )
                    {
                        if ( evt.lengthComputable )
                        {
                            var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                            //Do something with upload progress
                            $('.progress_ch').html(percentComplete + '%');
                            $('.progress-bar').animate({
                                width: percentComplete + '%'
                            }, {
                                duration: 10
                            });
                        }
                    }, false );
                    return jqXHR;
                },
                success    : function ( data )
                {
                    data = JSON.parse(data);
                    if($.fn.DataTable.isDataTable('.table-videos')){
                        $('.table-videos').DataTable().ajax.reload();
                    }
                    alert_float(data.alert_type, data.message);
                    $('#view_video').modal('hide');
                }
            });
        return false;
        }
        return false;
    }
//     $(document).ready(function(){
//     $('#uploadImage').submit(function(event){
//         alert(123);
//         if($('#uploadFile').val())
//         {
//             event.preventDefault();
//             $('#loader-icon').show();
//             $('#targetLayer').hide();
//             $(this).ajaxSubmit({
//                 target: '#targetLayer',
//                 beforeSubmit:function(){
//                     $('.progress-bar').width('50%');
//                 },
//                 uploadProgress: function(event, position, total, percentageComplete)
//                 {
//                     $('.progress-bar').animate({
//                         width: percentageComplete + '%'
//                     }, {
//                         duration: 1000
//                     });
//                 },
//                 success:function(){
//                     $('#loader-icon').hide();
//                     $('#targetLayer').show();
//                 },
//                 resetForm: true
//             });
//         }
//         return false;
//     });
// });
</script>